<?php

namespace App\Controller;

use App\Entity\Order;
use App\Entity\Customer;
use App\Entity\TypeMontage;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\FormTypeInterface;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\OrderRepository;
use App\Form\CustomerCreateFormType;
use App\Form\AddOrderFormType;
use App\Repository\TypeMontageRepository;
use App\Repository\GlassRepository;
use App\Repository\DetailRepository;
use App\Repository\MosquitoRepository;
use App\Repository\StatusRepository;
use App\Repository\PaymentRepository;


use function PHPSTORM_META\type;

class CustomerUserController extends AbstractController
{
    #[Route('/customer/user', name: 'app_customer_user')]
    /**
     * @Route("/customer_user/index", name="app.customer_user.create", methods={"GET","POST"})
     */
    public function index(EntityManagerInterface $entityManager, OrderRepository $orderRepository, TypeMontageRepository $typeMontageRepository,
                          GlassRepository $glassRepository, DetailRepository $detailRepository, MosquitoRepository $mosquitoRepository,
                          PaymentRepository $paymentRepository, Request $request): Response
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
            ]);
    }
}