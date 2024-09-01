<?php

namespace App\Controller;

use App\Entity\Order;
use App\Entity\Customer;
use App\Entity\TypeMontage;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\FormTypeInterface;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\OrderRepository;
use App\Form\CreateOrderDataFormType;
use App\Form\AddOrderFormType;
use App\Repository\TypeMontageRepository;
use App\Repository\GlassRepository;
use App\Repository\DetailRepository;
use App\Repository\MosquitoRepository;
use App\Repository\StatusRepository;

use function PHPSTORM_META\type;

class CreateOrderController extends AbstractController
{
    #[Route('/create/order', name: 'app_create_order')]
    /**
     * @Route("/add/order", name="app.add.order", methods={"GET","POST"})
     */
    public function index(EntityManagerInterface $entityManager, OrderRepository $orderRepository, TypeMontageRepository $typeMontageRepository,
                          GlassRepository $glassRepository, DetailRepository $detailRepository, MosquitoRepository $mosquitoRepository,
                          StatusRepository $statusRepository, Request $request): Response
    {
        $form = $this->createForm(CreateOrderDataFormType::class);
            $form->handleRequest($request);
           ($form); 
        if ($form->isSubmitted() && $form->isValid()) {
            /*$request->getSession()->set('form_data', $form->getData());
            $data = $form->getData();
            $typeMontage = $data['typemontage'];
            $typeMontageId = $typeMontage->getId();
            $typeMontageName = $typeMontage->getName();
            $customer = $data['customer'];
            $customerId = $customer->getId();
            $customerName = $customer->getName();
            
            $lastNumber = $orderRepository->findOneBy(['type_montage'=>$typeMontageId], ['number' => 'desc']);
                if ($lastNumber == NULL){
                    $newNumber = 1;
                } else{
                    $newNumber = $lastNumber->getNumber() + 1;
                }
            
            //$request->getSession()->set('type', $type);
            $session = $request->getSession();
            $session->set('typeMontage', $typeMontage);
            $session->set('typeMontageName', $typeMontageName);
            $session->set('customer', $customer);
            $session->set('customerName', $customerName);
            $session->set('newNumber', $newNumber);
            
            
            $or = $entityManager -> getRepository(Order::class);
            
           // $orde = $or->findBy(['type_montage_id'=>1]);
           
            //$lastOne = $orderRepository->findOneBy(['type_montage_id'=>1], ['number' => 'desc']);
            //dd($lastOne->getNumber());
            */
            $user = $this->getUser();
            $lastNumber = $orderRepository->findOneBy([],['number' => 'desc']);
            
                if ($lastNumber == NULL){
                    $newNumber = 1;
                } else{
                    $newNumber = $lastNumber->getNumber() + 1;
                }
            
            $data = $form->getData();
            //$customer = $data['customer'][0];
            //$cus = $customer[0];
            //dd($customer);
            //dd($data);
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
            }
            if($data['detail'] == 'true'){
                $order->setDetail($detail);
            }
            if($data['mosquito'] == 'true'){
                $order->setMosquito($mosquito);
            }
            
           
            
            $entityManager->persist($order);
            
            $entityManager->flush();

            if ($request->isXmlHttpRequest()) {
                return new Response(null, 204);
            }
            
            //dd($entityManager);
            $orders = $orderRepository->findall();
            $this->addFlash('success', 'ПОРЪЧКАТА Е СЪЗДАДЕНА! Knowledge is power!');
            
            return $this->redirectToRoute('app_order', ['orders' => $orders]);

        }
        return $this->render('create_order/index.html.twig', [
            'createOrderData' => $form->createView()
        ], new Response(
            null,
            $form->isSubmitted() && !$form->isValid() ? 422 : 200,
        ));
    }
}
