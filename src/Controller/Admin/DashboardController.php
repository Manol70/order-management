<?php

namespace App\Controller\Admin;

use App\Entity\Customer;
use App\Entity\Glass;
use App\Entity\Type;
use App\Entity\TypeMontage;
use App\Entity\User;
use App\Entity\Order;
use COM;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class DashboardController extends AbstractDashboardController
{
    
    #[IsGranted('ROLE_ADMIN')]
    #[Route('/admin', name: 'admin')]
    
    public function index(): Response
    {
        //return parent::index();
        return $this->render('admin/index.html.twig', [
            'controller_name' => 'DashboardController',
        ]);

        // Option 1. You can make your dashboard redirect to some common page of your backend
        //
        // $adminUrlGenerator = $this->container->get(AdminUrlGenerator::class);
        // return $this->redirect($adminUrlGenerator->setController(OneOfYourCrudController::class)->generateUrl());

        // Option 2. You can make your dashboard redirect to different pages depending on the user
        //
        // if ('jane' === $this->getUser()->getUsername()) {
        //     return $this->redirect('...');
        // }

        // Option 3. You can render some custom template to display a proper dashboard with widgets, etc.
        // (tip: it's easier if your template extends from @EasyAdmin/page/content.html.twig)
        //
        // return $this->render('some/path/my-dashboard.html.twig');
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('АДМИН ПАНЕЛ');
    }

    public function configureMenuItems(): iterable
    {
        //yield MenuItem::linkToDashboard('Dashboard', 'fa fa-home');
        yield MenuItem::linkToRoute('Начало', 'fas fa-home', 'app_order');
        yield MenuItem::linkToCrud('Потребители', 'fa fa-user-circle', User::class);
        yield MenuItem::linkToCrud('Клиенти', 'fa fa-customer-circle', Customer::class);
        //yield MenuItem::linkToCrud('Glass','fa fa-glass-circle', Glass::class);
        //yield MenuItem::linkToCrud('Type', 'fa fa-type-circle', Type::class);
        //yield MenuItem::linkToCrud('TypeMontage', 'fa fa-type_montage', TypeMontage::class);
        //yield MenuItem::linkToCrud('Order', 'fa fa-order-circle', Order::class);
        

        // yield MenuItem::linkToCrud('The Label', 'fas fa-list', EntityClass::class);
    }

    public function configureActions(): Actions 
    {
        return parent::configureActions()
        ->add(Crud::PAGE_INDEX, Action::DETAIL);
    }

    public function configureCrud(): Crud
    {
        return Crud::new()
            // Заместваш оригиналния crud/index темплейт с твоя персонализиран
            ->overrideTemplates([
                'crud/index' => 'admin/crud/index.html.twig',
            ]);
            
    }

}
