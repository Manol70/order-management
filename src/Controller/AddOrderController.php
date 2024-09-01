<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use App\Form\AddOrderFormType;
use App\Entity\Customer;
use App\Repository\CustomerRepository;
use App\Entity\TypeMontage;
use App\Repository\TypeMontageRepository;
use App\Entity\Order;
use App\Entity\User;

/**
 * @method Customer getCustomer()
 */
class AddOrderController extends AbstractController
{
    #[Route('/add/order', name: 'app_add_order')]
    public function index(EntityManagerInterface $entityManager, Request $request, CustomerRepository $CustomerRepository, TypeMontageRepository $typeMontageRepository): Response
    {
        
        $form = $this->createForm(AddOrderFormType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            //$request->getSession()->set('form_data', $form->getData());
            $data = $form->getData();
            $session = $request->getSession();
            $typeMontage = $session->get('typeMontageName');
            //dd($typeMontage);
            $user = $this->getUser();
            
            
            //dd($userId);
            $typeMontage = $session->get('typeMontage');
            $customer = $session->get('customer');
            $typeMontageId = $typeMontage->getId();
            $customerId = $customer->getId();
           // dd($customerId);
            $typeMontage = $typeMontageRepository->findOneBy(['id' => $typeMontageId]);
            $customer = $CustomerRepository->findOneBy(['id' => $customerId]);
            //dd($customer);
            
            $numberOrder = $session->get('newNumber');
            $type = $data['type'];
            //dd($type);
            $forDate = $data['from_date'];
            $quadrature = $data['quadrature'];
            $price = $data['price'];
            $note = $data['note'];
            $paid = 0;
            //dd($forDate);          
            //dd($session);
            //dd($data);
            
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
            
            
            $entityManager->persist($order);
            
            $entityManager->flush();
            
            //dd($entityManager);
            $this->addFlash('success', 'ПОРЪЧКАТА Е СЪЗДАДЕНА! Knowledge is power!');
            return $this->render('order/index.html.twig');
        }
        return $this->render('add_order/index.html.twig', [
            'addOrder' => $form->createView()
        ]);
    }
}
