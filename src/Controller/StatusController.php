<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Status;
use App\Entity\Order;
use App\Entity\StatusHistory;
use App\Repository\GlassRepository;
use App\Repository\OrderRepository;
use App\Repository\MosquitoRepository;
use App\Repository\StatusRepository;
use App\Repository\DetailRepository;
use App\Controller\OrderController;
use App\Entity\Glass;
use Symfony\Component\HttpFoundation\JsonResponse;
use phpDocumentor\Reflection\Types\Null_;

class StatusController extends AbstractController
{
    #[Route('/status/glass', name: 'app_status_glass')]
    public function statusGlass(Request $request, GlassRepository $glassRepository, OrderRepository $orderRepository): Response
    {
        
        $orderId = $request->query->get('orderId');
        $order = $orderRepository->findOneBy(['id'=>$orderId]);
        $numberOrder = $order->getNumber();
        $currentGlassId = $order->getGlass()->getId();
        $currentGlass = $glassRepository->findOneBy(['id' => $currentGlassId]);
        $currentGlassName = $currentGlass->getName();
        // Намераме следващият статус
        $newGlassId = $glassRepository->createQueryBuilder('g')
            ->select('MIN(g.id)')
            ->where('g.id > :currentStatusId')
            ->setParameter('currentStatusId', $currentGlassId)
            ->getQuery()
            ->getSingleScalarResult();
        $newGlass = $glassRepository->findOneBy(['id' => $newGlassId]);
        $newGlassName = $newGlass->getName(); 
        return $this->render('status/glass.html.twig', [
            'orderId' => $orderId,
            'currentGlassName' => $currentGlassName,
            'newGlassName' => $newGlassName,
            'newGlassId' => $newGlassId,
            'numberOrder' => $numberOrder
        ]);
    }

    #[Route('/status/mosquito', name: 'app_status_mosquito')]
    public function statusMosquito(Request $request, MosquitoRepository $mosquitoRepository, OrderRepository $orderRepository): Response
    {
        $orderId = $request->query->get('orderId');
        $order = $orderRepository->findOneBy(['id'=>$orderId]);
        $numberOrder = $order->getNumber();
        $currentMosquitoId = $order->getMosquito()->getId();
        $currentMosquito = $mosquitoRepository->findOneBy(['id' => $currentMosquitoId]);
        $currentMosquitoName = $currentMosquito->getName();
        // Намераме следващият статус
        $newMosquitoId = $mosquitoRepository->createQueryBuilder('m')
            ->select('MIN(m.id)')
            ->where('m.id > :currentMosquitoId')
            ->setParameter('currentMosquitoId', $currentMosquitoId)
            ->getQuery()
            ->getSingleScalarResult();
        $newMosquito = $mosquitoRepository->findOneBy(['id' => $newMosquitoId]);
        $newMosquitoName = $newMosquito->getName(); 
        return $this->render('status/mosquito.html.twig', [
            'orderId' => $orderId,
            'currentMosquitoName' => $currentMosquitoName,
            'newMosquitoName' => $newMosquitoName,
            'newMosquitoId' => $newMosquitoId,
            'numberOrder' => $numberOrder
        ]);
    }

    #[Route('/status/detail', name: 'app_status_detail')]
    public function statusDetail(Request $request, DetailRepository $detailRepository, OrderRepository $orderRepository): Response
    {
        $orderId = $request->query->get('orderId');
        $order = $orderRepository->findOneBy(['id'=>$orderId]);
        $numberOrder = $order->getNumber();
        $currentDetailId = $order->getDetail()->getId();
        $currentDetail = $detailRepository->findOneBy(['id' => $currentDetailId]);
        $currentDetailName = $currentDetail->getName();
        // Намераме следващият статус
        $newDetailId = $detailRepository->createQueryBuilder('d')
            ->select('MIN(d.id)')
            ->where('d.id > :currentDetailId')
            ->setParameter('currentDetailId', $currentDetailId)
            ->getQuery()
            ->getSingleScalarResult();
        $newDetail = $detailRepository->findOneBy(['id' => $newDetailId]);
        $newDetailName = $newDetail->getName();
        return $this->render('status/detail.html.twig', [
            'orderId' => $orderId,
            'currentDetailName' => $currentDetailName,
            'newDetailName' => $newDetailName,
            'newDetailId' => $newDetailId,
            'numberOrder' => $numberOrder
        ]);
    }

    #[Route('status/order', name: 'app_status_order')]
    public function statusOrder(Request $request, StatusRepository $statusRepository, OrderRepository $orderRepository,
                                OrderController $orderController, GlassRepository $glassRepository,
                                MosquitoRepository $mosquitoRepository, DetailRepository $detailRepository): Response
    {
        $currentPage = $request->query->get('currentPage');
        $orderId = $request->query->get('orderId');
        $order = $orderRepository->findOneBy(['id'=>$orderId]);
        $numberOrder = $order->getNumber();
        $currentStatusGlass = $order->getGlass();
        $currentStatusMosquito = $order->getMosquito();
        $currentStatusDetail = $order->getDetail();
        
        $lastStatus = $orderController->lastStatus($request, $statusRepository, $glassRepository, $mosquitoRepository, $detailRepository);
        $lastStatusOrder = $lastStatus['lastStatusOrder'];
        $lastStatusGlass = $lastStatus['lastStatusGlass'];
        $lastStatusGlassId = $lastStatus['lastStatusGlass']->getId();
        $lastStatusMosquito = $lastStatus['lastStatusMosquito'];
        $lastStatusMosquitoId = $lastStatus['lastStatusMosquito']->getId();
        $lastStatusDetail = $lastStatus['lastStatusDetail'];
        $lastStatusDetailId = $lastStatus['lastStatusDetail']->getId();
        $penultStatus = $orderController->penultStatus($request, $statusRepository, $glassRepository, $mosquitoRepository, $detailRepository);
        $penultStatusGlass = $penultStatus['penultStatusGlass'];
        $penultStatusMosquito = $penultStatus['penultStatusMosquito'];
        $penultStatusDetail = $penultStatus['penultStatusDetail'];
        $statusId = $order->getStatus()->getId();
        $currentStatusName = $statusRepository->findOneBy(['id' => $statusId]);
        $currentStatusName = $currentStatusName->getName();
        // Намераме следващият статус
        $newStatusId = $statusRepository->createQueryBuilder('s')
            ->select('MIN(s.id)')
            ->where('s.id > :currentStatusId')
            ->setParameter('currentStatusId', $statusId)
            ->getQuery()
            ->getSingleScalarResult();
        $newStatus = $statusRepository->findOneBy(['id' => $newStatusId]);
        
        $newStatusName = $newStatus->getName();
        $newStatusGlassId = '';
        $newStatusMosquitoId = '';
        $newStatusDetailId = '';
        $glassChange = false;
        $mosquitoChange = false;
        $detailChange = false;
        $messageGlass = '';
        $messageMosquito = '';
        $messageDetail = '';
        if($lastStatusOrder == $newStatus){
            if ($currentStatusGlass == $penultStatusGlass){
                $glassChange = true;
                $newStatusGlassId = $lastStatusGlassId;
                $messageGlass = 'Към поръчката има СТЪКЛОПАКЕТ.Отбележете, ако искате да промените статуса на "ВЗЕТ"';
            } elseif ($currentStatusGlass !== null 
                      AND $currentStatusGlass !== $penultStatusGlass
                      AND $currentStatusGlass !== $lastStatusGlass){
                $messageGlass = 'Към поръчката има СТЪКЛОПАКЕТ, който НЕ Е готов за предаване и остава с текущия статус';
            }
            if ($currentStatusMosquito == $penultStatusMosquito){
                $mosquitoChange = true;
                $newStatusMosquitoId = $lastStatusMosquitoId;
                $messageMosquito = 'Към поръчката има КОМАРНИК.Отбележете, ако искате дапромените статуса на "ВЗЕТ"';
            } elseif ($currentStatusMosquito !== null 
                      AND $currentStatusMosquito !== $penultStatusMosquito
                      AND $currentStatusMosquito !== $lastStatusMosquito){
                $messageMosquito = 'Към поръчката има КОМАРНИК, който НЕ Е готов за предаване и остава с текущия статус';
            }
            if ($currentStatusDetail == $penultStatusDetail){
                $detailChange = true;
                $newStatusDetailId = $lastStatusDetailId;
                $messageDetail = 'Към поръчката има ДОП. ПРОФИЛ.Отбележете, ако искате да промените статуса на "ВЗЕТ"';
            } elseif ($currentStatusDetail !== null 
                      AND $currentStatusDetail !== $penultStatusDetail
                      AND $currentStatusDetail !== $lastStatusDetail){
                $messageDetail = 'Към поръчката има ДОП. ПРОФИЛ, който НЕ Е готов за предаване и остава с текущия статус';
            }     
        }
        return $this->render('status/order.html.twig', [
            'orderId' => $orderId,
            'currentStatus' => $currentStatusName,
            'newStatusName' => $newStatusName,
            'newStatusId' => $newStatusId,
            'newGlassId' => $newStatusGlassId,
            'glass' => $glassChange,
            'messageGlass' => $messageGlass,
            'newMosquitoId' => $newStatusMosquitoId,
            'mosquito' => $mosquitoChange,
            'messageMosquito' => $messageMosquito,
            'newDetailId' => $newStatusDetailId,
            'detail' => $detailChange,
            'messageDetail' => $messageDetail,
            'numberOrder' => $numberOrder,
            'currentPage' => $currentPage
        ]);
    }
    #[Route('status/orderBulk', name: 'app_status_orderBulk')]
    public function changeStatusBulk(Request $request, OrderRepository $orderRepository,
                                    StatusRepository $statusRepository, OrderController $orderController,
                                    GlassRepository $glassRepository, MosquitoRepository $mosquitoRepository,
                                    DetailRepository $detailRepository,
                                    EntityManagerInterface $em): Response
    {
        // Получаване на ID на избраните поръчки и новия статус от заявката
        $selectedOrders = $request->get('selectedOrders');
        $currentStatusId = $request->request->get('statusId');
        $lastStatusId = $statusRepository->lastStatusOrder();
        $newStatusId = $statusRepository->determineNewStatus($currentStatusId);
        if ($newStatusId == $lastStatusId){
            foreach ($selectedOrders as $orderId) {
                //dd($orderId);
                $this->statusOrder($request, $statusRepository, $orderRepository, 
                                $orderController, $glassRepository, 
                                $mosquitoRepository, $detailRepository, $orderId);
            }
        }
        $newStatus = $statusRepository->find($newStatusId);
        if ($newStatus) {
            $em->beginTransaction();
            try {
                foreach ($selectedOrders as $orderId) {
                    // Намери поръчката по ID
                    $order = $orderRepository->find($orderId);
                    if ($order) {
                        // Актуализирай статуса на поръчката
                        $order->setStatus($newStatus);
                        $em->persist($order);

                        // Създаване на запис в StatusHistory
                        $statusHistory = new StatusHistory();
                        $statusHistory->setOrder($order);
                        $statusHistory->setUser($this->getUser()); 
                        $statusHistory->setStatus($newStatus);
                        $statusHistory->setNumberOrder($order->getNumber());
                        $em->persist($statusHistory);
                    }
                }
                // Запази промените в базата данни
                $em->flush();
                $em->commit();
            } catch (\Exception $e) {
                $em->rollback();
                throw $e; // Прехвърляй грешката нагоре
            }
        }
        // Добавяне на flash съобщение
        $this->addFlash('success', 'Статусите бяха променени успешно.');
        // Пренасочване към страницата с поръчките
        return $this->redirectToRoute('app_order',[
            'fromStatus' => true
        ]);
    }
}
