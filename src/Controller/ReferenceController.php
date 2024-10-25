<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\OrderRepository;
use App\Repository\PaymentRepository;
use App\Repository\StatusHistoryRepository;
use App\Repository\GlassHistoryRepository;
use App\Repository\DetailHistoryRepository;
use App\Repository\MosquitoHistoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use App\Form\ReferenceDateStatusFormType;
use App\Repository\StatusRepository;
use App\Form\SearchFormType;

class ReferenceController extends AbstractController
{
    #[Route('/reference/order', name: 'app_reference_order')]
    public function referenceOrder(OrderRepository $orderRepository, PaymentRepository $paymentRepository,
                                    StatusHistoryRepository $statusHistoryRepository,
                                    GlassHistoryRepository $glassHistoryRepository,
                                    DetailHistoryRepository $detailHistoryRepository,
                                    MosquitoHistoryRepository $mosquitoHistoryRepository, 
                                   EntityManagerInterface $entityManager, Request $request): Response
    {
        $orderId = $request->query->get('orderId');
        $order = $orderRepository->getOrderById($orderId);
        $orderId = $order->getId();
        $payments = $paymentRepository->findByOrderId($orderId);
        $statuses = $statusHistoryRepository -> findByOrderId($orderId);
        $glass = $glassHistoryRepository ->findByOrderId($orderId);
        $detail = $detailHistoryRepository->findByOrderId($orderId);
        $mosquito = $mosquitoHistoryRepository->findByOrderId($orderId);
        return $this->render('reference/order.html.twig', [
            'order' => $order,
            'payments' => $payments,
            'statuses' => $statuses,
            'glass' => $glass,
            'detail'=> $detail,
            'mosquito' => $mosquito
        ]);
    }

    #[Route('/reference/customer', name: 'app_reference_customer')]
    public function referenceCustomer(OrderRepository $orderRepository, PaymentRepository $paymentRepository,
                                    EntityManagerInterface $entityManager, Request $request, SessionInterface $session): Response
    {
        $customerId = $request->query->get('customerId');
        $orders = $orderRepository->getOrdersByCustomerId($customerId);
        $customer = $orders[0]['customer'];

        $session->set('order_reference_customer', $orders);
        return $this->render('reference/customer.html.twig', [
            'orders' => $orders,
            'customer' => $customer
        ]);
    }
    
    #[Route('/export/reference/customer', name: 'export_reference_customer')]
    public function export(SessionInterface $session): Response
    {
        // Вземаме данните от сесията
        $orders = $session->get('order_reference_customer', []);
        $date = $orders[0]['createdAt'];
        $formattedDate = $date->format('d.m.y');
            
        if (empty($orders)) {
            throw new \Exception('No orders found in session.');
        }
        // Създаваме нов Spreadsheet обект
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        // Задаваме заглавията на колоните
        $sheet->setCellValue('A1', 'Номер');
        $sheet->setCellValue('B1', 'Тип');
        $sheet->setCellValue('C1', 'Дата');
        $sheet->setCellValue('D1', 'За Дата');
        $sheet->setCellValue('E1', 'Квадратура');
        $sheet->setCellValue('F1', 'Цена');
        $sheet->setCellValue('G1', 'Платено');
        $sheet->setCellValue('H1', 'дължима сума');
        
        // Примерни данни
        $data = [];
        // Добавяне на редове с данни
        foreach ($orders as $order) {
            $data[] = [
                $order['number'],
                $order['type']['name'],
                $order['createdAt']->format('d.m.y'),
                $order['for_date']->format('d.m.y'),
                $order['quadrature'],
                $order['price'],
                $order['paid'],
                $order['price']-$order['paid'],
            ];
        }
        // Запълваме клетките с данни
        $row = 2; // Започваме от втория ред, защото първият ред са заглавията
        foreach ($data as $record) {
            $sheet->setCellValue('A' . $row, $record[0]);
            $sheet->setCellValue('B' . $row, $record[1]);
            $sheet->setCellValue('C' . $row, $record[2]);
            $sheet->setCellValue('D' . $row, $record[3]);
            $sheet->setCellValue('E' . $row, $record[4]);
            $sheet->setCellValue('F' . $row, $record[5]);
            $sheet->setCellValue('G' . $row, $record[6]);
            $sheet->setCellValue('H' . $row, $record[7]);
            $row++;
        }
        
        // Създаваме Writer обект и записваме в паметта
        $writer = new Xlsx($spreadsheet);
        $fileName = 'exportReferenceCustomer.xlsx';
        $temp_file = tempnam(sys_get_temp_dir(), $fileName);
        $writer->save($temp_file);
        
        // Връщаме файла като отговор
        return $this->file($temp_file, $fileName, ResponseHeaderBag::DISPOSITION_INLINE);
    }

    #[Route('/reference/date', name: 'app_reference_date')]
    public function referenceDate(StatusRepository $statusRepository,
                                StatusHistoryRepository $statusHistoryRepository,
                                EntityManagerInterface $entityManager, Request $request ): Response
    {
        // Конвертираме стринга обратно в DateTime обект
        // Вземаме датата от GET параметъра
        $date = $request->request->get('date');
        if ($date) {
            $dateObject = \DateTimeImmutable::createFromFormat('d.m.Y', $date);
        } else {
            $dateObject = new \DateTimeImmutable();
        }
        // Извличане на статуса "Готова" от базата данни
        $defaultStatus = $statusRepository->findOneBy(['name' => 'Готова']);
        $statusId = $defaultStatus->getId();
        $form = $this->createForm(ReferenceDateStatusFormType::class, null, ['default_date' => $dateObject, 'default_status' => $defaultStatus]);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $newDate = $data['for_date']->format('d.m.Y');
            $dateObject = $data['for_date'];
            $type = $data['type'];
            $status = $data['status'];
                
            if ($type == null and $status == null){
                $statuses = $statusHistoryRepository->findByDateAllTypeStatus($dateObject);
                $totalQuadrature = 0;
                foreach ($statuses as $status) {
                    $totalQuadrature += $status[0]['_order']['quadrature'];
                }
                return $this->render('reference/date.html.twig', [
                    'date' => $newDate,
                    'statuses' => $statuses,
                    'totalQuadrature' => $totalQuadrature,
                    'referenceDateStatusForm' => $form->createView()
                ]);
            } elseif($type != null and $status == null){
                $typeId = $type->getId();
                $statuses = $statusHistoryRepository->findByDateAndTypeId($dateObject, $typeId);
                $totalQuadrature = 0;
                foreach ($statuses as $status) {
                    $totalQuadrature += $status[0]['_order']['quadrature'];
                }
                return $this->render('reference/date.html.twig', [
                    'date' => $newDate,
                    'statuses' => $statuses,
                    'totalQuadrature' => $totalQuadrature,
                    'referenceDateStatusForm' => $form->createView()
                ]);
            } elseif($type != null and $status != null){
                $typeId = $type->getId();
                $statusId = $status->getId();
                $statuses = $statusHistoryRepository->findByDateTypeIdStatusId($dateObject, $typeId, $statusId);
                $totalQuadrature = 0;
                foreach ($statuses as $status) {
                    $totalQuadrature += $status[0]['_order']['quadrature'];
                }
                return $this->render('reference/date.html.twig', [
                    'date' => $newDate,
                    'statuses' => $statuses,
                    'totalQuadrature' => $totalQuadrature,
                    'referenceDateStatusForm' => $form->createView()
                ]); 
            } elseif($type == null and $status != null){
                $statusId = $status->getId();
                $statuses = $statusHistoryRepository->findByDateAndStatusId($dateObject, $statusId);
                $totalQuadrature = 0;
                    foreach ($statuses as $status) {
                        $totalQuadrature += $status[0]['_order']['quadrature'];
                    }
                return $this->render('reference/date.html.twig', [
                    'date' => $newDate,
                    'statuses' => $statuses,
                    'totalQuadrature' => $totalQuadrature,
                    'referenceDateStatusForm' => $form->createView()
                ]);
            }
            } else{
                $statuses = $statusHistoryRepository->findByDate($dateObject, $statusId);
            }
            $totalQuadrature = 0;
                foreach ($statuses as $status) {
                    $totalQuadrature += $status[0]['_order']['quadrature'];
                }
            return $this->render('reference/date.html.twig', [
                'date' => $dateObject,
                'statuses' => $statuses,
                'totalQuadrature' => $totalQuadrature,
                'referenceDateStatusForm' => $form->createView()
            ]);
    }

    #[Route('/reference/index', name: 'app_reference_index')]
    public function referenceIndex(OrderRepository $orderRepository, EntityManagerInterface $entityManager,
                                Request $request): Response
    {
        $session = $request->getSession();
        $form = $this->createForm(SearchFormType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $fromDate = (clone $data['from_date']) ->format('d.m.Y');
            $toDate = (clone $data['to_date'])->modify('+1 day')->format('d.m.Y');
            $totalOrders = $orderRepository->getTotalOrders($data['from_date'], $data['to_date']);
            $totalQuadrature = $orderRepository->getTotalQuadrature($data['from_date'], $data['to_date']);
            $totalAmount = $orderRepository->getTotalAmount($data['from_date'], $data['to_date']);
            $totalPaid = $orderRepository->getTotalPaid($data['from_date'], $data['to_date']);
            $topTurnover = $orderRepository->getTopTurnover($entityManager, $data['from_date'], $data['to_date']);
            $topQuadratureByCustomer = $orderRepository->getTopQuadratureByCustomer($entityManager, $data['from_date'], $data['to_date']);
            $topCountOrderByCustomer = $orderRepository->getTopCountOrderByCustomer($entityManager, $data['from_date'], $data['to_date']);

            $response = $this->render('reference/index.html.twig' , [
                'searchForm' => $form->createView(),
                'fromDate' => $fromDate,
                'toDate' => $toDate,
                'totalOrders' => $totalOrders,
                'totalQuadrature' => $totalQuadrature,
                'totalAmount' => $totalAmount,
                'totalPaid' => $totalPaid,
                'topTurnover' => $topTurnover,
                'topQuadratureByCustomer' => $topQuadratureByCustomer,
                'topCountOrderByCustomer' => $topCountOrderByCustomer            
    
            ]);
            return $response;
        }

        $response = $this->render('reference/index.html.twig' , [
            'searchForm' => $form->createView(),
            'fromDate' => null,
            'toDate' => null,            

        ]);
        return $response;
        
    }

}
