<?php

namespace App\Controller;

use App\Entity\Order;
use App\Form\OrderType;
use App\Entity\Glass;
use App\Entity\Mosquito;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use Gedmo\Sluggable\Util\Urlizer;
use App\Repository\CustomerRepository;
use App\Entity\Customer;
use App\Entity\GlassHistory;
use App\Entity\StatusHistory;
use App\Entity\MosquitoHistory;
use App\Entity\DetailHistory;
use App\Entity\User;
use App\Repository\DetailRepository;
use App\Repository\OrderRepository;
use App\Repository\GlassRepository;
use App\Repository\MosquitoRepository;
use App\Repository\StatusRepository;
use Pagerfanta\Doctrine\ORM\QueryAdapter;
use Pagerfanta\Pagerfanta;
use Doctrine\ORM\EntityManager;
use phpDocumentor\Reflection\PseudoTypes\True_;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Form\SearchFormType;
use DoctrineExtensions\Query\Mysql\Date;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpParser\Node\Stmt\Echo_;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Knp\Component\Pager\PaginatorInterface;


#[Route('/order')]
class OrderController extends AbstractController
{
    #[Route('/', name: 'app_order')]
public function index(
    EntityManagerInterface $entityManager, 
    OrderRepository $orderRepository, 
    StatusRepository $statusRepository, 
    GlassRepository $glassRepository, 
    MosquitoRepository $mosquitoRepository,
    DetailRepository $detailRepository, 
    Request $request, 
    SessionInterface $session,
    PaginatorInterface $paginator,
): Response {
    // Маркер за начало на изпълнението
    $start = microtime(true);
    
    $user = $this->getUser();
    if (!$user) {
        return new RedirectResponse($this->generateUrl('app_login'));
    }
    if ($this->isGranted('ROLE_USER')) {
        return $this->redirectToRoute('app_customer_user');
    }
    //$template = $request->isXmlHttpRequest() ? '_list.html.twig' : 'index.html.twig';
    $template = $request->query->get('ajax') ? '_list.html.twig' : 'index.html.twig';

    //$isAjax = $request->query->get('ajax') || $request->headers->get('Turbo-Frame') !== null;
    //$template = $isAjax ? '_list.html.twig' : 'index.html.twig';
    
    // Включи логиране, за да провериш кой темплейт се зарежда
    //dump('Is Ajax:', $isAjax);
    //dump('Selected Template:', $template);

   // $isAjax = $request->headers->get('X-Requested-With') === 'XMLHttpRequest';
    //$template = $isAjax ? '_list.html.twig' : 'index.html.twig';
    
    $session = $request->getSession();
    
    /*$parameters = $request->query->all(); // Вземи всички параметри
    $sort = $queryParameters['search_form']['sort'] ?? null; // Вземи 'sort' или null, ако не съществува
    dd($sort);*/
    $sort = $request->query->get('sort', 'id');
    $sortDirection = $request->query->get('sortDirection', 'desc');
    


    $form = $this->createForm(SearchFormType::class, null, [
        'method' => 'GET',
        'csrf_protection' => false,
    ]);
    //dd($sort);
    $form->handleRequest($request);
   //dd($form);
   $parameters = $request->query->all();
   //$request->query->remove('sort');
    //dd($parameters);
    if ($form->isSubmitted() && $form->isValid()) {
        
        //$request->query->set('page', 1); // Принудително задаваме page на 1 при ново търсене
        $data = $form->getData();
       //dd($data);
        // Получаваме стойността на totalPages от GET заявката
        $totalPages = $request->query->get('totalPages', 1); 
        
        
        $customer = $data['customer'];
        $type = $data['type'];
        $status = $data['status'];
        $glass = $data['glass'];
        $detail = $data['detail'];
        $mosquito = $data['mosquito'];
        $source = $data['source'];
        $fromDate = $data['from_date'];
        $toDate = (clone $data['to_date'])->modify('+1 day')->format('Y-m-d');
        
        $queryBuilder = $orderRepository->createQueryBuilder('o')
            ->andWhere('o.createdAt >= :fromDate AND o.createdAt < :toDate')
            ->setParameter('fromDate', $fromDate)
            ->setParameter('toDate', $toDate)
            ->orderBy('o.'. $sort, $sortDirection);
            
        
        if ($customer !== null) {
            $queryBuilder->andWhere('o.customer = :customer')
            ->setParameter('customer', $customer);
        }
        if ($type !== null) {
            $queryBuilder->andWhere('o.type = :type')
            ->setParameter('type', $type);
        }
        if ($status !== null) {
            $queryBuilder->andWhere('o.status = :status')
            ->setParameter('status', $status);
            $checkboxStatusOrder = true;
        } else {
            $checkboxStatusOrder = false;
        }
        if ($glass !== null) {
            $queryBuilder->andWhere('o.glass = :glass')
            ->setParameter('glass', $glass);
            $checkboxStatusGlass = true;
        } else {
            $checkboxStatusGlass = false;
        }
        if ($detail !== null) {
            $queryBuilder->andWhere('o.detail = :detail')
            ->setParameter('detail', $detail);
            $checkboxStatusDetail = true;
        } else {
            $checkboxStatusDetail = false;
        }
        if ($mosquito !== null) {
            $queryBuilder->andWhere('o.mosquito = :mosquito')
            ->setParameter('mosquito', $mosquito);
            $checkboxStatusMosquito = true;
        } else {
            $checkboxStatusMosquito = false;
        }
        if($checkboxStatusOrder || $checkboxStatusGlass || $checkboxStatusDetail || $checkboxStatusMosquito){
            $filter = true;
        } else {
            $filter = false;
        }
        //dd($queryBuilder);
     /*   if ($sort === 'true') {
            $queryBuilder->orderBy('o.price', 'DESC');
        } else {
            // Сортиране по id (по подразбиране)
            $queryBuilder->orderBy('o.id', 'DESC');
        }*/

        $queryBuilderForTotal = clone $queryBuilder;
        $totalSum = $queryBuilderForTotal
            ->select('SUM(o.quadrature)', 'SUM(o.price)', 'SUM(o.paid)', 'COUNT(o.id)')
            ->getQuery()
            ->getResult();
          $orders = $queryBuilder
           ->orderBy('o.'. $sort, $sortDirection)
           ->getQuery()
           ->getResult();
        

        $adapter = new QueryAdapter($queryBuilder);
        $currentPage = $request->query->getInt('page', 1);
        
        $pagerfanta = Pagerfanta::createForCurrentPageWithMaxPerPage(
            $adapter,
            $request->query->get('page', $currentPage),
            15
        );   
        $currentPageResults = $pagerfanta->getCurrentPage();
        //$totalPages = $pagerfanta->getNbPages();  // Общият брой страници при
        //dump($currentPageResults);
        //dump($pagerfanta);

        
       //$session->set('order_search_results', $orders);
        $lastStatus = $this->lastStatus($request, $statusRepository, $glassRepository, $mosquitoRepository, $detailRepository);
        //$orders = $pagerfanta->getCurrentPageResults();
        
        //Маркер
        //$afterQueryExecution = microtime(true);
        //    dump('Query execution time: ' . ($afterQueryExecution - $afterFormHandling) . ' seconds');

        $user = $this->getUser()->getRoles();
        
        return $this->render('order/' . $template, [
            'controller_name' => $user[0],
            'orders' => $pagerfanta,
            'lastStatus' => $lastStatus,
            'showCheckboxes' => $filter,
            'searchForm' => $form->createView(),
            'pager' => $pagerfanta->getCurrentPageResults(), // Използваме само текущите резултати
            'currentPage'=> $currentPage,
            'checkbox_status_order' => $checkboxStatusOrder,
            'checkbox_status_glass' => $checkboxStatusGlass,
            'checkbox_status_detail' => $checkboxStatusDetail,
            'checkbox_status_mosquito' => $checkboxStatusMosquito,
            'total_sum' => $totalSum,
            'totalPages' => $totalPages,
            'sort' => $sort,
            'sortDirection' => $sortDirection
        ]); 
    }
    




    $user = $this->getUser()->getRoles();
    
    $dateObject = new \DateTimeImmutable();
    $fromDate = (clone $dateObject)->modify('-90 days')->format('Y-m-d');
    $toDate = (clone $dateObject)->modify('+1 day')->format('Y-m-d');
    //dd($request);
     
    $queryBuilder = $orderRepository->createQueryBuilderForAllOrders('o')
        ->andWhere('o.createdAt >= :fromDate AND o.createdAt < :toDate')
        ->setParameter('fromDate', $fromDate)
        ->setParameter('toDate', $toDate);

        // Определяме сортиране
        // Ако сортираме по баланс, винаги сортирай в низходящ ред
        if ($sort === 'balance') {
            $queryBuilder->orderBy('o.' . $sort, 'desc'); // Винаги е 'desc' за balance
        } else {
            // За всички други полета сортирай по зададеното поле и посока
            $queryBuilder->orderBy('o.' . $sort, $sortDirection);
        }
        // Ако кликнем на "Номер" или друга колона, трябва да променим посоката
    if ($sort === 'id' && $sortDirection === 'asc') {
        $sortDirection = 'desc';
    } elseif ($sort === 'id' && $sortDirection === 'desc') {
        $sortDirection = 'asc';
    }
    //$request->query->remove('sort');

    $queryBuilderForTotal = clone $queryBuilder;
        $totalSum = $queryBuilderForTotal
            ->select('SUM(o.quadrature)', 'SUM(o.price)', 'SUM(o.paid)', 'COUNT(o.id)')
            ->getQuery()
            ->getResult();
    $adapter = new QueryAdapter($queryBuilder);
    $current = $request->query->get('page', 1);
    $pagerfanta = Pagerfanta::createForCurrentPageWithMaxPerPage(
        $adapter,
        $request->query->get('page', 1),
        15
    );
    $totalPages = $pagerfanta->getNbPages();  // Общият брой страници при първоначалното зареждане
    //dd($pagerfanta);
    $currentPageResults = $pagerfanta->getCurrentPage();
    //dump($currentPageResults);
   $currentPage = $currentPageResults;

    
   
    $orders = $orderRepository->findBy([], ['id' => 'DESC']);
    
        
    
    $session->set('order_search_results', $orders);
    $lastStatus = $this->lastStatus($request, $statusRepository, $glassRepository, $mosquitoRepository, $detailRepository);
    
    
    $response = $this->render('order/' . $template, [
        'controller_name' => $user[0],
        'orders' => $pagerfanta,
        'lastStatus' => $lastStatus,
        'searchForm' => $form->createView(),
        'showCheckboxes' => false,
        'pager' => $pagerfanta,
        'currentPage' => $currentPage,
        'checkbox_status_order' => false,
        'checkbox_status_glass' => false,
        'checkbox_status_detail' => false,
        'checkbox_status_mosquito' => false,
        'totalPages' => $totalPages,  // Подаваме общия брой страници
        'total_sum' => $totalSum,
        'sort' => $sort,
        'sortDirection' => $sortDirection
    ]);
    
    return $response;
}
    /**
     * @Route("/upload", name="upload")
     */
    public function temporaryUploadAction(Request $request)
    {
        /** @var UploadedFile $uploadedFile */
        //dd($request->files->get('image'));
        $uploadedFile = $request->files->get('image');
        $destination = $this->getParameter('kernel.project_dir').'/public/uploads';
        //dd($uploadedFile->move($destination));
        $originalFilename = pathinfo($uploadedFile->getClientOriginalName(), PATHINFO_FILENAME);
        //dd($originalFilename);
        $extension = $uploadedFile->guessExtension();
        $newFilename = md5(uniqid()) . '.' . $extension;
        
       // Вземи името на файла от полето за въвеждане на име, ако е налично
        $filename = $request->request->get('filename');
        //dd($filename);
        if (!$filename) {
            $filename = pathinfo($uploadedFile->getClientOriginalName(), PATHINFO_FILENAME);
            // Генерирай новото име на файла
            $newFilename = $filename.'-'.uniqid().'.'.$uploadedFile->guessExtension();
            $filename = $newFilename;
        }
        // Премести файла към дестинацията с новото име
        $uploadedFile->move($destination, $filename);
        // Връщане на JSON отговор със статус 200
    return new JsonResponse(['message' => 'File uploaded successfully'], 200);
    }

    #[Route('/{id}/edit', name: 'app_order_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Order $order, EntityManagerInterface $entityManager,
                        GlassRepository $glassRepository, MosquitoRepository $mosquitoRepository,
                        DetailRepository $detailRepository): Response
    {       
        //dd($order);
        // Вземаме стойността на полето for_date от обекта Order
        $forDate = $order->getForDate();
    
        // Проверяваме дали датата е по-стара от днешната
        if ($forDate < new \DateTimeImmutable('today')) {
            // Ако датата е в миналото, показваме съобщение и прекратяваме обработката
            $this->addFlash('error', 'Не можете да редактирате поръчки с минала дата.');
        
            // Може да направите пренасочване към страница или друга логика
        return $this->redirectToRoute('app_order'); // или друга страница
        }
        $user = $this->getUser();
        $numberOrder = $order->getNumber();
       if ($order->getGlass() !== null ) {
            $glassValue = true;
        } else{
            $glassValue = false;
        }
        $mosquitoValue = $order->getMosquito() !== null ? true : false;
        $detailValue = $order->getDetail() !== null ? true : false;
        //dd($mosquitoValue);
        //добавяне допълнителни параметри(glass_valuе, mosquito_value и т.н.) към $options за изпращане към формата
        $form = $this->createForm(OrderType::class, $order, [
            'glass_value' => $glassValue,
            'mosquito_value' => $mosquitoValue,
            'detail_value' => $detailValue,
        ]);
        $form->handleRequest($request);
        //dd($form);
 
        //проверяваме какво се връща от формата след субмит/update/
        if ($request->getMethod() === 'POST') {
            $formData = $request->request->all(); // Вземаме всички POST данни
            //dd($form->getData()->getForDate());
           
            //$glassValue = $formData['order']['glass'];
            //dd($glassValue);
            $newGlassValue = isset($formData['order']['glass']) ? (bool) $formData['order']['glass'] : false;
            //dd($newGlassValue);
            $currentGlassValue = $order->getGlass() !== null ? true : false;
           // $formData['order']['glass']=$newGlassValue;
            //dd($formData);
            
            
            
        }
        
        if ($form->isSubmitted() && $form->isValid()) {
            //dd($form->all());
            if ($form['removeScheme']->getData() == true){
                if($form->getData()->getScheme()){
                    $filePath = $this->getParameter('kernel.project_dir') .'/public/uploads'. '/' . $form->getData()->getScheme();
                    if (file_exists($filePath)) {
                        unlink($filePath);
                        $order->setScheme(null);
                    }
                }
            }
            
            
            /** @var UploadedFile $uploadedFile */
            $uploadedFile = $form['schemeFile']->getData();
            
            if ($uploadedFile) {
                $destination = $this->getParameter('kernel.project_dir').'/public/uploads';
                 // Вземи името на файла от полето за въвеждане на име, ако е налично
                $filename = $form['filename']->getData();
                //dd($filename);
                if (!$filename) {
                $filename = pathinfo($uploadedFile->getClientOriginalName(), PATHINFO_FILENAME);
                }
                $number = $order->getNumber();
                
                // Генерирай новото име на файла
                // $newFilename = $filename.'-'.uniqid().'.'.$uploadedFile->guessExtension();
                $newFilename = $filename;
                // Премести файла към дестинацията с новото име
                $uploadedFile->move($destination, $newFilename);
                $order->setScheme($newFilename);

            }
            //dd($form->all());
            $glass = $form['glass']->getData();
            $newGlass = $form['newGlass']->getData();
            //dd($newGlass);
            if ($newGlass !== $glass ) {
                if ($newGlass == true ) {
                    $newGlassId = $glassRepository->createQueryBuilder('g')
                    ->select('MIN(g.id)')
                    ->getQuery()
                    ->getSingleScalarResult();
                    $newGlass = $glassRepository->findOneBy(['id' => $newGlassId]);
                    $order->setGlass($newGlass);
                    //създаване на клас GlassHistory за запазване в БД
                $glassHistory = new GlassHistory();
                $glassHistory->setOrder($order);
                $glassHistory->setUser($user);
                $glassHistory->setGlass($newGlass);
                $glassHistory->setNumberOrder($numberOrder);
                $entityManager->persist($glassHistory);
                } else {
                    $order->setGlass(Null);
                    //създаване на клас GlassHistory за запазване в БД
                $glassHistory = new GlassHistory();
                $glassHistory->setOrder($order);
                $glassHistory->setUser($user);
                $glassHistory->setGlass(Null);
                $glassHistory->setNumberOrder($numberOrder);
                $entityManager->persist($glassHistory);
                }
            }
            $mosquito = $form['mosquito']->getData();
            $newMosquito = $form['newMosquito']->getData();
            if ($newMosquito !== $mosquito) {
                if ($newMosquito == true) {
                    $newMosquitoId = $mosquitoRepository->createQueryBuilder('g')
                    ->select('MIN(g.id)')
                    ->getQuery()
                    ->getSingleScalarResult();
                    $newMosquito = $mosquitoRepository->findOneBy(['id' => $newMosquitoId]);
                    $order->setMosquito($newMosquito);
                    //създаване на клас MosquitoHistory за запазване в БД
                    $mosquitoHistory = new MosquitoHistory();
                    $mosquitoHistory->setOrder($order);
                    $mosquitoHistory->setUser($user);
                    $mosquitoHistory->setMosquito($newMosquito);
                    $mosquitoHistory->setNumberOrder($numberOrder);
                    $entityManager->persist($mosquitoHistory);
                } else {
                    $order->setMosquito(Null);
                    //създаване на клас MosquitoHistory за запазване в БД
                    $mosquitoHistory = new MosquitoHistory();
                    $mosquitoHistory->setOrder($order);
                    $mosquitoHistory->setUser($user);
                    $mosquitoHistory->setMosquito(Null);
                    $mosquitoHistory->setNumberOrder($numberOrder);
                    $entityManager->persist($mosquitoHistory);
                }
            }
            $detail = $form['detail']->getData();
            $newDetail = $form['newDetail']->getData();
            if ($newDetail !== $detail) {
                if ($newDetail == true) {
                    $newDetailId = $detailRepository->createQueryBuilder('d')
                    ->select('MIN(d.id)')
                    ->getQuery()
                    ->getSingleScalarResult();
                    $newDetail = $detailRepository->findOneBy(['id' => $newDetailId]);
                    $order->setDetail($newDetail);
                    //създаване на клас DetailHistory за запазване в БД
                    $detailHistory = new DetailHistory();
                    $detailHistory->setOrder($order);
                    $detailHistory->setUser($user);
                    $detailHistory->setDetail($newDetail);
                    $detailHistory->setNumberOrder($numberOrder);
                    $entityManager->persist($detailHistory);
                } else {
                    $order->setDetail(Null);
                    //създаване на клас DetailHistory за запазване в БД
                    $detailHistory = new DetailHistory();
                    $detailHistory->setOrder($order);
                    $detailHistory->setUser($user);
                    $detailHistory->setDetail(Null);
                    $detailHistory->setNumberOrder($numberOrder);
                    $entityManager->persist($detailHistory);
                }
            }
            
            
            $entityManager->flush();

            return $this->redirectToRoute('app_order', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('order/edit.html.twig', [
            'form' => $form,
            'order' => $order,  // Предаваме order на Twig, въпреки че данните вече ги има във $form, но така ще можем да покажем номера в twig
        ]);
    }

    #[Route('/update', name: 'order_update', methods: ['POST'])]
    public function update(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        // Извлечение на данните от заявката
        $orderId = $request->request->get('orderId');
        $newGlassId = $request->request->get('newGlassId');

        // Актуализация на базата данни
    $order = $entityManager->getRepository(Order::class)->find($orderId);
    $glass = $entityManager->getRepository(Glass::class)->find($newGlassId);

    if (!$order || !$glass) {
        return new JsonResponse(['message' => 'Order or Glass not found'], 404);
    }

    $order->setGlass($glass); // Ако полето 'glassId' е свързано с обект от класа 'Glass'
    $entityManager->flush();

    return new JsonResponse(['message' => 'Order updated successfully'], 200);
    }

    #[Route('/status/glass', name: 'app_order_status_glass')]
    public function statusGlass(Request $request, GlassRepository $glassRepository, OrderRepository $orderRepository,
                                EntityManagerInterface $entityManager): Response
    {
        //dd($request);
        //начало на транзаакцията
        $entityManager->beginTransaction();
        try {
            $orderId = $request->query->get('orderId');
            $order = $orderRepository->findOneBy(['id'=>$orderId]);
            $numberOrder = $order->getNumber();
            $user = $this->getUser();
            $newGlassId = $request->query->get('newGlassId');
            $glass = $glassRepository->findOneBy(['id'=>$newGlassId]);

            //създаване на клас GlassHistory за запазване в БД
            $glassHistory = new GlassHistory();
            $glassHistory->setOrder($order);
            $glassHistory->setUser($user);
            $glassHistory->setGlass($glass);
            $glassHistory->setNumberOrder($numberOrder);

            //UPDATE class Order
            $order->setGlass($glass);
            $entityManager->persist($order);
            $entityManager->persist($glassHistory);

            // Комитиране на транзакцията
            $entityManager->commit();
            $entityManager->flush();

            // echo "Received message: " . $message;
            return new JsonResponse(['message' => 'Order updated successfully'], 200);
        } catch (\Exception $e) {
            // Ако възникне грешка, отмени транзакцията
            $entityManager->rollback();
            throw $e;
        }
    }

    #[Route('/status/mosquito', name: 'app_order_status_mosquito')]
    public function statusMosquito(Request $request, MosquitoRepository $mosquitoRepository, OrderRepository $orderRepository, EntityManagerInterface $entityManager): Response
    {
        //dd($request);
        //начало на транзаакцията
        $entityManager->beginTransaction();
        try {
            $orderId = $request->query->get('orderId');
            $order = $orderRepository->findOneBy(['id'=>$orderId]);
            $numberOrder = $order->getNumber();
            $newMosquitoId = $request->query->get('newMosquitoId') ;
            $mosquito = $mosquitoRepository->findOneBy(['id'=>$newMosquitoId]);
            $user = $this->getUser();
            //създаване на клас MosquitoHistory за запазване в БД
            $mosquitoHistory = new MosquitoHistory();
            $mosquitoHistory->setOrder($order);
            $mosquitoHistory->setUser($user);
            $mosquitoHistory->setMosquito($mosquito);
            $mosquitoHistory->setNumberOrder($numberOrder);

            //UPDATE class Order
            $order->setMosquito($mosquito);
            $entityManager->persist($order);
            $entityManager->persist($mosquitoHistory);
            
            // Комитиране на транзакцията
            $entityManager->commit();
            $entityManager->flush();          
        // echo "Received message: " . $message;
       return new JsonResponse(['message' => 'Order updated successfully'], 200);
        } catch (\Exception $e) {
            // Ако възникне грешка, отмени транзакцията
            $entityManager->rollback();
            throw $e;
        }
    }

    #[Route('/status/detail', name: 'app_order_status_detail')]
    public function statusDetail(Request $request, DetailRepository $detailRepository, OrderRepository $orderRepository,
                                EntityManagerInterface $entityManager): Response
    {
        //dd($request);
        //начало на транзаакцията
        $entityManager->beginTransaction();
        try {
            $orderId = $request->query->get('orderId');
            $order = $orderRepository->findOneBy(['id'=>$orderId]);
            $numberOrder = $order->getNumber();
            $user = $this->getUser();
            $newDetailId = $request->query->get('newDetailId');
            $detail = $detailRepository->findOneBy(['id'=>$newDetailId]);

            //създаване на клас DetailHistory за запазване в БД
            $detailHistory = new DetailHistory();
            $detailHistory->setOrder($order);
            $detailHistory->setUser($user);
            $detailHistory->setDetail($detail);
            $detailHistory->setNumberOrder($numberOrder);

            //UPDATE class Order
            $order->setDetail($detail);
            $entityManager->persist($order);
            $entityManager->persist($detailHistory);

            // Комитиране на транзакцията
            $entityManager->commit();
            $entityManager->flush();

            // echo "Received message: " . $message;
            return new JsonResponse(['message' => 'Order updated successfully'], 200);
        } catch (\Exception $e) {
            // Ако възникне грешка, отмени транзакцията
            $entityManager->rollback();
            throw $e;
        }
    }


    #[Route('/status/', name: 'app_order_status')]
    public function statusOrder(Request $request, StatusRepository $statusRepository, OrderRepository $orderRepository,
                                CustomerRepository $customerRepository, GlassRepository $glassRepository,
                                MosquitoRepository $mosquitoRepository, DetailRepository $detailRepository,
                                EntityManagerInterface $entityManager): Response
    {
        //dd($request);
        //начало на транзаакцията
        $entityManager->beginTransaction();
        try {
            $orderId = $request->query->get('orderId');
            $newStatusId = $request->query->get('newStatusId');
            $order = $orderRepository->findOneBy(['id'=>$orderId]);
            $numberOrder = $order->getNumber();
            $customerId = $order->getCustomer()->getId();
            //$customer = $customerRepository->findOneBy(['id'=>$customerId]);
            $user = $this->getUser();
            //$statusId = $order->getStatus()->getId();
            $status = $statusRepository->findOneBy(['id'=>$newStatusId]);
            //създаване на клас StatusHistory за запазване в БД
            $statusHistory = new StatusHistory();
            $statusHistory->setOrder($order);
            $statusHistory->setUser($user);
            $statusHistory->setStatus($status);
            $statusHistory->setNumberOrder($numberOrder);

            //UPDATE class Order
            $order->setStatus($status);
            $entityManager->persist($order);
            $entityManager->persist($statusHistory);

            if ($request->query->get('glass') == 'on') {
                
                $this->handleGlassUpdate($request, $order, $numberOrder, $user, $glassRepository, $entityManager);
            }
            if ($request->query->get('mosquito') == 'on') {
                
                $this->handleMosquitoUpdate($request, $order, $numberOrder, $user, $mosquitoRepository, $entityManager);
            }
            if ($request->query->get('detail') == 'on') {
                
                $this->handleDetailUpdate($request, $order, $numberOrder, $user, $detailRepository, $entityManager);
            }

            // Комитиране на транзакцията
            $entityManager->commit();
            $entityManager->flush();

            // echo "Received message: " . $message;
            return new JsonResponse(['message' => 'Order updated successfully'], 200);
        } catch (\Exception $e) {
            // Ако възникне грешка, отмени транзакцията
            $entityManager->rollback();
            throw $e;
        }
    }

    //намиране на последните записи от статусите
    public function lastStatus(Request $request, StatusRepository $statusRepository, GlassRepository $glassRepository,
                               MosquitoRepository $mosquitoRepository, DetailRepository $detailRepository)
    {
        $lastStatus = [];
        $lastStatusOrderId = $statusRepository->createQueryBuilder('s')
        ->select('MAX(s.id)')
        ->getQuery()
        ->getSingleScalarResult();
        $lastStatusOrder = $statusRepository->findOneBy(['id' => $lastStatusOrderId]);
        $lastStatus['lastStatusOrder'] = $lastStatusOrder;
        
        $lastStatusGlassId = $glassRepository->createQueryBuilder('s')
        ->select('MAX(s.id)')
        ->getQuery()
        ->getSingleScalarResult();
        $lastStatusGlass = $glassRepository->findOneBy(['id' => $lastStatusGlassId]);
        $lastStatus['lastStatusGlass'] = $lastStatusGlass;

        $lastStatusMosquitoId = $mosquitoRepository->createQueryBuilder('s')
        ->select('MAX(s.id)')
        ->getQuery()
        ->getSingleScalarResult();
        $lastStatusMosquito = $mosquitoRepository->findOneBy(['id' => $lastStatusMosquitoId]);
        $lastStatus['lastStatusMosquito'] = $lastStatusMosquito;

        $lastStatusDetailId = $detailRepository->createQueryBuilder('s')
        ->select('MAX(s.id)')
        ->getQuery()
        ->getSingleScalarResult();
        $lastStatusDetail = $detailRepository->findOneBy(['id' => $lastStatusDetailId]);
        $lastStatus['lastStatusDetail'] = $lastStatusDetail;
       
        return $lastStatus;
    }

    public function penultStatus(Request $request, StatusRepository $statusRepository, GlassRepository $glassRepository,
                               MosquitoRepository $mosquitoRepository, DetailRepository $detailRepository)
    {
        $penultStatus = [];
        // Получаване на последното id
        $lastStatusOrderId = $statusRepository->createQueryBuilder('s')
            ->select('MAX(s.id)')
            ->getQuery()
            ->getSingleScalarResult();
        // Получаване на предпоследното id
        $penultStatusOrderId = $statusRepository->createQueryBuilder('s')
            ->select('MAX(s.id)')
            ->where('s.id < :maxId')
            ->setParameter('maxId', $lastStatusOrderId)
            ->getQuery()
            ->getSingleScalarResult();
        $penultStatusOrder = $statusRepository->findOneBy(['id' => $penultStatusOrderId]);
        $penultStatus['penultStatusOrder'] = $penultStatusOrder;
        
        $lastStatusGlassId = $glassRepository->createQueryBuilder('s')
            ->select('MAX(s.id)')
            ->getQuery()
            ->getSingleScalarResult();
        $penultStatusGlassId = $glassRepository->createQueryBuilder('s')
            ->select('MAX(s.id)')
            ->where('s.id < :maxId')
            ->setParameter('maxId', $lastStatusGlassId)
            ->getQuery()
            ->getSingleScalarResult();
        $penultStatusGlass = $glassRepository->findOneBy(['id' => $penultStatusGlassId]);
        $penultStatus['penultStatusGlass'] = $penultStatusGlass;

        $lastStatusMosquitoId = $mosquitoRepository->createQueryBuilder('s')
            ->select('MAX(s.id)')
            ->getQuery()
            ->getSingleScalarResult();
        $penultStatusMosquitoId = $mosquitoRepository->createQueryBuilder('s')
            ->select('MAX(s.id)')
            ->where('s.id < :maxId')
            ->setParameter('maxId', $lastStatusMosquitoId)
            ->getQuery()
            ->getSingleScalarResult();
        $penultStatusMosquito = $mosquitoRepository->findOneBy(['id' => $penultStatusMosquitoId]);
        $penultStatus['penultStatusMosquito'] = $penultStatusMosquito;

        $lastStatusDetailId = $detailRepository->createQueryBuilder('s')
            ->select('MAX(s.id)')
            ->getQuery()
            ->getSingleScalarResult();
        $penultStatusDetailId = $detailRepository->createQueryBuilder('s')
            ->select('MAX(s.id)')
            ->where('s.id < :maxId')
            ->setParameter('maxId', $lastStatusDetailId)
            ->getQuery()
            ->getSingleScalarResult();
        $penultStatusDetail = $detailRepository->findOneBy(['id' => $penultStatusDetailId]);
        $penultStatus['penultStatusDetail'] = $penultStatusDetail;
       
        return $penultStatus;
    }

    private function handleGlassUpdate(Request $request, Order $order, $numberOrder, $user, GlassRepository $glassRepository, EntityManagerInterface $entityManager)
    {
        $newGlassId = $request->query->get('newGlassId');
        //dd($newGlassId);
        $glass = $glassRepository->findOneBy(['id' => $newGlassId]);

        $glassHistory = new GlassHistory();
        $glassHistory->setOrder($order);
        $glassHistory->setUser($user);
        $glassHistory->setGlass($glass);
        $glassHistory->setNumberOrder($numberOrder);

        $order->setGlass($glass);
        $entityManager->persist($order);
        $entityManager->persist($glassHistory);
    }

    private function handleMosquitoUpdate(Request $request, Order $order, $numberOrder, $user, MosquitoRepository $mosquitoRepository, EntityManagerInterface $entityManager)
    {
        $newMosquitoId = $request->query->get('newMosquitoId');
        $mosquito = $mosquitoRepository->findOneBy(['id' => $newMosquitoId]);

        $mosquitoHistory = new MosquitoHistory();
        $mosquitoHistory->setOrder($order);
        $mosquitoHistory->setUser($user);
        $mosquitoHistory->setMosquito($mosquito);
        $mosquitoHistory->setNumberOrder($numberOrder);

        $order->setMosquito($mosquito);
        $entityManager->persist($order);
        $entityManager->persist($mosquitoHistory);
    }

    private function handleDetailUpdate(Request $request, Order $order, $numberOrder, $user, DetailRepository $detailRepository, EntityManagerInterface $entityManager)
    {
        $newDetailId = $request->query->get('newDetailId');
        $detail = $detailRepository->findOneBy(['id' => $newDetailId]);

        $detailHistory = new DetailHistory();
        $detailHistory->setOrder($order);
        $detailHistory->setUser($user);
        $detailHistory->setDetail($detail);
        $detailHistory->setNumberOrder($numberOrder);

        $order->setDetail($detail);
        $entityManager->persist($order);
        $entityManager->persist($detailHistory);
    }



    #[Route('/export', name: 'export_table')]
    public function export(SessionInterface $session): Response
    {
        // Вземаме данните от сесията
    $orders = $session->get('order_search_results', []);
        //dd($session);
    // Логваме съдържанието на сесията
    //dump($session->all());
        // Вместо да връщаш файла, временно върни нормален отговор
    //return new Response('Check the profiler for dump output');
    if (empty($orders)) {
        throw new \Exception('No orders found in session.');
    }
        // Създаваме нов Spreadsheet обект
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        // Задаваме заглавията на колоните
        $sheet->setCellValue('A1', 'Номер');
        $sheet->setCellValue('B1', 'Клиент');
        $sheet->setCellValue('C1', 'Тип');
        $sheet->setCellValue('D1', 'м2');
        $sheet->setCellValue('E1', 'дата');
        $sheet->setCellValue('F1', 'цена');
        $sheet->setCellValue('G1', 'платено');
        $sheet->setCellValue('H1', 'дължима сума');
        $sheet->setCellValue('I1', 'статус');
        
        // Примерни данни
        $data = [];
        // Добавяне на редове с данни
    foreach ($orders as $order) {
        $data[] = [
            $order->getNumber(),
            $order->getCustomer()->getName(),
            $order->getType()->getName(),
            $order->getQuadrature(),
            $order->getCreatedAt()->format('d.m.y'),
            $order->getPrice(),
            $order->getPaid(),
            $order->getPrice()-$order->getPaid(),
           // $order->getStatus()->getName(),
        ];
    }
        //dd($data);
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
        $fileName = 'export.xlsx';
        $temp_file = tempnam(sys_get_temp_dir(), $fileName);
        $writer->save($temp_file);
        
        // Връщаме файла като отговор
        return $this->file($temp_file, $fileName, ResponseHeaderBag::DISPOSITION_INLINE);
    }

//Метод за пълната версия
#[Route('/full', name: 'order_full')]
    public function fullList(
        EntityManagerInterface $entityManager, 
        OrderRepository $orderRepository, 
        StatusRepository $statusRepository, 
        GlassRepository $glassRepository, 
        MosquitoRepository $mosquitoRepository,
        DetailRepository $detailRepository, 
        Request $request, 
        SessionInterface $session
    ): Response {
        $user = $this->getUser();
        
        if (!$user) {
            return new RedirectResponse($this->generateUrl('app_login'));
        }
        
        $template = '_list.html.twig'; // Шаблон за пълната версия

        $session = $request->getSession();
    

        $form = $this->createForm(SearchFormType::class);
            $form->handleRequest($request);
          //dd($form); 
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            //dd($data);
            $customer = $data['customer'];
            $type = $data['type'];
            $status = $data['status'];
            $glass = $data['glass'];
            $source = $data['source'];
            $fromDate = $data['from_date'];
            if ($status == Null){
                $source = false;
            }
           // $fromDate = $fromDate->format('Y-m-d'); // Превръщаме го във формат само за дата
        $toDate = $data['to_date'];
            //$fromDate = $fromDate->format('Y-m-d'); // Превръщаме го във формат само за дата   
        $toDate = (clone $toDate)->modify('+1 day')->format('Y-m-d'); // Плюс един ден за да включим следващия ден
        //dd($toDate);
        $queryBuilder = $orderRepository->createQueryBuilder('o')
            ->andWhere('o.createdAt >= :fromDate AND o.createdAt < :toDate')
            ->setParameter('fromDate', $fromDate)
            ->setParameter('toDate', $toDate);

            // Условие за customer
        if ($customer !== null) {
            $queryBuilder->andWhere('o.customer = :customer')
            ->setParameter('customer', $customer);
        }
            // Условия за тип
        if ($type !== null) {
            $queryBuilder->andWhere('o.type = :type')
            ->setParameter('type', $type);
        }
        if ($status !== null) {
            $queryBuilder->andWhere('o.status = :status')
            ->setParameter('status', $status);
        }
        if ($glass !== null) {
            $queryBuilder->andWhere('o.glass = :glass')
            ->setParameter('glass', $glass);
        }

        $orders = $queryBuilder
            ->orderBy('o.id', 'DESC')
            ->getQuery()
            ->getResult();
            //dd($orders);
        
        // Запазваме резултатите в сесията
        $session->set('order_search_results', $orders);    
        // Логваме съдържанието на сесията
        //dump($session->all());
        $lastStatus = $this->lastStatus($request, $statusRepository, $glassRepository, $mosquitoRepository, $detailRepository);
            //dd($orders);
        $user = $this->getUser()->getRoles();
            return $this->render('order/' . $template, [
                'controller_name' => $user[0],
                'orders' => $orders,
                'lastStatus' => $lastStatus,
                'showCheckboxes' => $source === 'filter',
                'searchForm' => $form->createView(),
                
            ]);
        }
        //$user1 = $this->getUser();
        
        $user = $this->getUser()->getRoles();
        
        //$ordersRepository = $entityManager->getRepository(Order::class);
        $queryBuilder = $orderRepository->createQueryBuilderForAllOrders();
        //dd($queryBuilder);
        $adapter = new QueryAdapter($queryBuilder);
        $pagerfanta = Pagerfanta::createForCurrentPageWithMaxPerPage(
            $adapter,
            $request->query->get('page', 1),
            9
        );
        //dd($pagerfanta);
        $orders = $orderRepository->findBy([], ['id' => 'DESC']);
        // Запазваме резултатите в сесията
        $session->set('order_search_results', $orders);  
        //dd($orders[0]);
        $lastStatus = $this->lastStatus($request, $statusRepository, $glassRepository, $mosquitoRepository, $detailRepository);
        
       
        
        return $this->render('order/' . $template, [
            'controller_name' => $user[0],
            'orders' => $orders,
            'lastStatus' => $lastStatus,
            'searchForm' => $form->createView(),
            'showCheckboxes' => false,
            'pager' => $pagerfanta,
            
        ]);
    }





// Нов метод за съкратената версия
    #[Route('/short', name: 'order_short')]
    public function listshort(EntityManagerInterface $entityManager, OrderRepository $orderRepository, 
        StatusRepository $statusRepository, GlassRepository $glassRepository, MosquitoRepository $mosquitoRepository,
        DetailRepository $detailRepository, Request $request, SessionInterface $session): Response
    {
        $user = $this->getUser();
        
        if (!$user) {
            // Ако няма активен потребител, пренасочи към страницата за логин
            return new RedirectResponse($this->generateUrl('app_login'));
        }
        
        $template = '_short_list.html.twig'; // Шаблон за съкратената версия
        //dd($orderRepository->getOrdersFullyLoad());
        
        //phpinfo();
        $session = $request->getSession();
    

        $form = $this->createForm(SearchFormType::class);
            $form->handleRequest($request);
          //dd($form); 
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            //dd($data);
            $customer = $data['customer'];
            $type = $data['type'];
            $status = $data['status'];
            $glass = $data['glass'];
            $source = $data['source'];
            $fromDate = $data['from_date'];
            if ($status == Null){
                $source = false;
            }
           // $fromDate = $fromDate->format('Y-m-d'); // Превръщаме го във формат само за дата
        $toDate = $data['to_date'];
            //$fromDate = $fromDate->format('Y-m-d'); // Превръщаме го във формат само за дата   
        $toDate = (clone $toDate)->modify('+1 day')->format('Y-m-d'); // Плюс един ден за да включим следващия ден
        //dd($toDate);
        $queryBuilder = $orderRepository->createQueryBuilder('o')
            ->andWhere('o.createdAt >= :fromDate AND o.createdAt < :toDate')
            ->setParameter('fromDate', $fromDate)
            ->setParameter('toDate', $toDate);

            // Условие за customer
        if ($customer !== null) {
            $queryBuilder->andWhere('o.customer = :customer')
            ->setParameter('customer', $customer);
        }
            // Условия за тип
        if ($type !== null) {
            $queryBuilder->andWhere('o.type = :type')
            ->setParameter('type', $type);
        }
        if ($status !== null) {
            $queryBuilder->andWhere('o.status = :status')
            ->setParameter('status', $status);
        }
        if ($glass !== null) {
            $queryBuilder->andWhere('o.glass = :glass')
            ->setParameter('glass', $glass);
        }

        $orders = $queryBuilder
            ->orderBy('o.id', 'DESC')
            ->getQuery()
            ->getResult();
            //dd($orders);
        
        // Запазваме резултатите в сесията
        $session->set('order_search_results', $orders);    
        // Логваме съдържанието на сесията
        //dump($session->all());
        $lastStatus = $this->lastStatus($request, $statusRepository, $glassRepository, $mosquitoRepository, $detailRepository);
            //dd($orders);
        $user = $this->getUser()->getRoles();
            return $this->render('order/' . $template, [
                'controller_name' => $user[0],
                'orders' => $orders,
                'lastStatus' => $lastStatus,
                'showCheckboxes' => $source === 'filter',
                'searchForm' => $form->createView(),
                
            ]);
        }
        //$user1 = $this->getUser();
        
        $user = $this->getUser()->getRoles();
        
        //$ordersRepository = $entityManager->getRepository(Order::class);
        $queryBuilder = $orderRepository->createQueryBuilderForAllOrders();
        //dd($queryBuilder);
        $adapter = new QueryAdapter($queryBuilder);
        $pagerfanta = Pagerfanta::createForCurrentPageWithMaxPerPage(
            $adapter,
            $request->query->get('page', 1),
            9
        );
        //dd($pagerfanta);
        $orders = $orderRepository->findBy([], ['id' => 'DESC']);
        // Запазваме резултатите в сесията
        $session->set('order_search_results', $orders);  
        //dd($orders[0]);
        $lastStatus = $this->lastStatus($request, $statusRepository, $glassRepository, $mosquitoRepository, $detailRepository);
        
       
        
        return $this->render('order/' . $template, [
            'controller_name' => $user[0],
            'orders' => $orders,
            'lastStatus' => $lastStatus,
            'searchForm' => $form->createView(),
            'showCheckboxes' => false,
            'pager' => $pagerfanta,
            
        ]);
    }
    
    
}
