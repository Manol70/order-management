<?php

namespace App\Controller;

use App\Entity\Order;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\OrderRepository;
use App\Form\CreateOrderDataFormType;
use App\Repository\TypeMontageRepository;
use App\Repository\GlassRepository;
use App\Repository\DetailRepository;
use App\Repository\MosquitoRepository;
use App\Repository\StatusRepository;
use App\Entity\StatusHistory;
use App\Entity\GlassHistory;
use App\Entity\DetailHistory;
use App\Entity\MosquitoHistory;
use Doctrine\DBAL\LockMode;


use function PHPSTORM_META\type;

class CreateOrderController extends AbstractController
{
    #[Route('/create/order', name: 'app_create_order')]
    
    public function index(EntityManagerInterface $entityManager, OrderRepository $orderRepository,
                        TypeMontageRepository $typeMontageRepository, 
                        GlassRepository $glassRepository,
                        DetailRepository $detailRepository,
                        MosquitoRepository $mosquitoRepository,
                        StatusRepository $statusRepository, 
                        Request $request): Response
    {
        
        $lastOrders = $orderRepository->findBy([],['number' => 'desc'], 5);
        $lastOrder = $lastOrders[0];
        $lastNumber = $lastOrder->getNumber();
        $lastOrderYear = $lastOrder->getCreatedAt()->format('Y'); 
        $currentYear = (new \DateTime())->format('Y'); // Текущата година
        if ($lastOrder == null){
            $newNumber = 1;
        } else{
            if ($lastOrderYear === $currentYear) {
                // Ако годината на последната поръчка е текущата, продължаваме номерацията
                $newNumber = $lastNumber + 1;
            } else {
                // Ако годината на последната поръчка е различна от текущата, започваме от 1
                $newNumber = 1;
            }
        }

        $form = $this->createForm(CreateOrderDataFormType::class);
            $form->handleRequest($request);
           ($form); 

        if ($form->isSubmitted() && $form->isValid()) {
            $orderData = $form->getData(); // Вземане на данните от формата
            //dd($orderData);
            $entityManager->beginTransaction(); // Започваме транзакцията
            try {
                // Проверка за дублиране на номера преди записа
                $startOfYear = new \DateTime('first day of January ' . $currentYear);
                $endOfYear = new \DateTime('last day of December ' . $currentYear);
                //Правим заявка, която проверява номера на поръчката и съвпадението на годината чрез диапазон от дати
                $existingOrder = $orderRepository->createQueryBuilder('o')
                    ->where('o.number = :number')
                    ->andWhere('o.createdAt BETWEEN :startOfYear AND :endOfYear')
                    ->setParameter('number', $newNumber)
                    ->setParameter('startOfYear', $startOfYear)
                    ->setParameter('endOfYear', $endOfYear)
                    ->getQuery()
                    ->getOneOrNullResult();
                    
                 
                if ($existingOrder!==null) {
                     //съобщение при дублирани номера
                    
                     $this->addFlash('error', sprintf('ВНИМАНИЕ! НЕУСПЕШЕН ЗАПИС НА ПОРЪЧКА № %s ПОРАДИ ДУБЛИРАНИ НОМЕРА. ПРОВЕРЕТЕ ДАННИТЕ ЗА ПОРЪЧКАТА В ТАБЛИЦАТА! ПРИ НЕОБХОДИМОСТ ЗАПОЧНЕТЕ СЪЗДАВАНЕТО НА НОВА ПОРЪЧКА С ГЕНЕРИРАН НОВ НОМЕР!', $newNumber));

                    // Рендиране на формата
                    return $this->redirectToRoute('app_order');
                }
                
                // Заключваме, за да предотвратим състезание
                $entityManager->lock($lastOrder, LockMode::PESSIMISTIC_WRITE);

                $user = $this->getUser();
                $data = $form->getData();
                $customer = $data['customer'];
                $numberOrder = $newNumber;
                $type = $data['type'];
                $forDate = $data['for_date'];
                $quadrature = $data['quadrature'];
                $typeMontage = $typeMontage = $typeMontageRepository->findOneBy(['id' => 1]);
                $price = $data['price'];
                $status = $statusRepository->findOneBy(['id' => 1]);
                $glass = $glassRepository->findOneBy(['id' => 1]);
                $detail = $detailRepository->findOneBy(['id' => 2]);
                $mosquito = $mosquitoRepository->findOneBy(['id' => 1]);
                $note = $data['note'];
                $paid = 0;
            
            
                $order = new Order;
                $order->setCustomer($customer);
                $order->setNumber($numberOrder);
                $order->setQuadrature($quadrature);
                $order->setForDate($forDate);
                $order->setPrice($price);
                $order->setPaid($paid);
                $order->setNote($note);
                $order->setType($type);
                $order->setTypeMontage($typeMontage);
                $order->setUser($user);
                $order->setStatus($status);
                if($data['glass'] == 'true'){
                $order->setGlass($glass);
                //създаване на клас GlassHistory за запазване в БД
                $glassHistory = new GlassHistory();
                $glassHistory->setOrder($order);
                $glassHistory->setUser($user);
                $glassHistory->setGlass($glass);
                $glassHistory->setNumberOrder($numberOrder);
                $entityManager->persist($glassHistory);
                }
                if($data['detail'] == 'true'){
                    $order->setDetail($detail);
                //създаване на клас DetailHistory за запазване в БД
                $detailHistory = new DetailHistory();
                $detailHistory->setOrder($order);
                $detailHistory->setUser($user);
                $detailHistory->setDetail($detail);
                $detailHistory->setNumberOrder($numberOrder);
                $entityManager->persist($detailHistory);
                }
                if($data['mosquito'] == 'true'){
                    $order->setMosquito($mosquito);
                //създаване на клас MosquitoHistory за запазване в БД
                $mosquitoHistory = new MosquitoHistory();
                $mosquitoHistory->setOrder($order);
                $mosquitoHistory->setUser($user);
                $mosquitoHistory->setMosquito($mosquito);
                $mosquitoHistory->setNumberOrder($numberOrder);
                $entityManager->persist($mosquitoHistory);
                }
                //създаване на клас StatusHistory за запазване в БД
                $statusHistory = new StatusHistory();
                $statusHistory->setOrder($order);
                $statusHistory->setUser($user);
                $statusHistory->setStatus($status);
                $statusHistory->setNumberOrder($numberOrder);
                $entityManager->persist($statusHistory);
    
                $entityManager->persist($order);
                $entityManager->flush();
                $entityManager->commit(); // Записваме транзакцията
            } catch (\Exception $e) {
                $entityManager->rollback(); // Отменяме транзакцията
               // Flash съобщение за глобална грешка
                $this->addFlash('error', 'Грешка при създаване на поръчката: ' . $e->getMessage());

                // Връщане на формата с грешката и запазените данни
                return $this->render('create_order/index.html.twig', [
                    'createOrderData' => $form->createView(),
                    'numberOrder' => $newNumber
                ]);
            }
            if ($request->isXmlHttpRequest()) {
                return new Response(null, 204);
            }
            //dd($entityManager);
            $orders = $orderRepository->findall();
            $this->addFlash('success', 'ПОРЪЧКАТА Е СЪЗДАДЕНА!');
            
            return $this->redirectToRoute('app_order', ['orders' => $orders]);

        }
        
        return $this->render('create_order/index.html.twig', [
            'createOrderData' => $form->createView(),
            'numberOrder' => $newNumber,
            'orders' => $lastOrders
        ], new Response(
                null,
                $form->isSubmitted() && !$form->isValid() ? 422 : 200,
            ));
        
    }
}
