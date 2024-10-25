<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\OrderRepository;
use App\Repository\PaymentRepository;


use function PHPSTORM_META\type;

class CustomerUserController extends AbstractController
{
    #[Route('/customer/user', name: 'app_customer_user')]
    /**
     * @Route("/customer_user/index", name="app.customer_user.create", methods={"GET","POST"})
     */
    public function index( OrderRepository $orderRepository,
                          PaymentRepository $paymentRepository, ): Response
    {
       /** @var \App\Entity\User $user */
        $user = $this->getUser();
        $customer = $user->getCustomer();
        $customerId = $customer->getId();
        $orders = $orderRepository->findByCustomerId($customerId);
        //dd($orders);
        $payment = $paymentRepository->findByCustomerId($customerId);
        //dd($payment);
        return $this->render('customer_user/index.html.twig', 
            [
                'orders' => $orders,
            ]
        );
    }
}