<?php

namespace App\Controller;

use App\Entity\Payment;
use App\Repository\OrderRepository;
use App\Repository\CustomerRepository;
use App\Form\PaymentType;
use App\Repository\PaymentRepository;
use App\Entity\Order;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/payment')]
class PaymentController extends AbstractController
{
    #[Route('/', name: 'app_payment_index', methods: ['GET'])]
    public function index(PaymentRepository $paymentRepository): Response
    {
        return $this->render('payment/index.html.twig', [
            'payments' => $paymentRepository->findAll(),
        ]);
    }
    
    #[Route('/payables', name: 'app_payment_payables', methods: ['GET', 'POST'])]
    public function payables(Request $request, EntityManagerInterface $entityManager, OrderRepository $orderRepository, CustomerRepository $customerRepository): Response
    {
        $scrollPosition = $request->request->get('scrollPosition');
        if ($scrollPosition) {
            $request->getSession()->set('scrollPosition', $scrollPosition);
        }
        $customer_id = $request->query->get('customerId');
        $orders = $orderRepository->getUnpaidOrdersByCustomerId($customer_id);
        $order = $orderRepository->findOneBy(['customer' => $customer_id]);
        $customer = $order->getCustomer();
        $customerName = $order->getCustomer()->getName();
         
        $form = $this->createForm(PaymentType::class,);
        $form->handleRequest($request);
            
        if ($form->isSubmitted() && $form->isValid()) {
            $orderPaymentsJson = $request->request->get('orderPaymentsJson');
            $orderPayments = json_decode($orderPaymentsJson, true);
            $data = $form->getData();

            $this->processPayments($orderPayments, $entityManager, $customer, $orderRepository);
            // Редирект към основната страница
            return $this->redirectToRoute('app_order');
        } else {
            // Връщане на JSON отговор при невалидна форма
            return new JsonResponse(['success' => false, 'errors' => (string) $form->getErrors(true, false)]);
        }

        return $this->render('payment/payables.html.twig', [
            'orders' => $orders, 'customer' => $customerName, 'form' => $form->createView()
        ]);
    }
    
    private function processPayments(array $orderPayments, EntityManagerInterface $entityManager, $customer, $orderRepository): void
    {
        //dd($orderPayments);
        // Начало на транзакцията
        $entityManager->beginTransaction();

        try {
            // Обработка на плащанията
            foreach ($orderPayments as $orderPayment) {
                
                $user = $this->getUser();
                $orderId = $orderPayment['orderId'];
                $order = $orderRepository->getOrderById($orderId);
                $paymentAmount = $orderPayment['paymentAmount'];
                $orderNumber = $orderPayment['orderNumber'];
                $paymentDoc = $orderPayment['paymentDoc'];
                $docNumber = $orderPayment['docNumber'];
                
                // Създаване на нов запис в таблицата Payment
                $payment = new Payment();
                $payment->setNumberOrder($orderNumber);
                $payment->setPaid($paymentAmount);
                $payment->setDocument($paymentDoc);
                $payment->setNumberDoc($docNumber);
                $payment->setUser($user);
                $payment->setCustomer($customer);
                $payment->setOrder($order);
                
                // Запазване на записа в базата данни
                $entityManager->persist($payment);

                // Актуализация на записа в таблицата Order
                $order = $entityManager->getRepository(Order::class)->find($orderId);
                $order->setPaid($order->getPaid() + $paymentAmount);

                // Запазване на записа в базата данни
                $entityManager->persist($order);
            }

            // Комитиране на транзакцията
            $entityManager->flush();
            $entityManager->commit();
        } catch (\Exception $e) {
            // Ако възникне грешка, отмени транзакцията
            $entityManager->rollback();
            throw $e;
        }
    }






    #[Route('/new', name: 'app_payment_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, OrderRepository $orderRepository): Response
    { 
        $payment = new Payment();
        $user = $this->getUser();
        $form = $this->createForm(PaymentType::class, $payment);
        dd($request);
        $order_id = $request->query->get('orderId');
        $session = $request->getSession();
        
        $session->set('orderId', $order_id);
        $order = $orderRepository->findOneBy(['id' => $order_id]);
        
        $order_number = $order->getNumber();
        $customer = $order->getCustomer();
        $typeMontage = $order->getTypeMontage();
        $payment->setNumberOrder($order_number);
        $payment->setCustomer($customer);
        $payment->setUser($user);
        $payment->setTypeMontage($typeMontage);
        $payment->setOrder($order);
   
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            //dd($data);
            $paid = $order->getPaid();
            $paid = $paid + $data->getPaid();
            $order -> setPaid($paid);
             
            
            $entityManager->persist($payment);
            $entityManager->flush();

            

            return $this->redirectToRoute('app_payment_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('payment/new.html.twig', [
            'payment' => $payment,
            'form' => $form,
            'orderId' => $order_id
        ]);
    }

}