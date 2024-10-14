<?php

namespace App\Controller\Admin;

use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use phpDocumentor\Reflection\Types\Integer;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;


class UserCrudController extends AbstractCrudController
{
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
        
    }

    public static function getEntityFqcn(): string
    {
        return User::class;
    }

    public function persistEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        if ($entityInstance instanceof User) {
            //dd($entityInstance);
            //dd($entityInstance->getRoles());
            $entityInstance->setPassword(
                $this->passwordHasher->hashPassword($entityInstance, $entityInstance->getPassword())
            );
        }

        parent::persistEntity($entityManager, $entityInstance);
    }


    public function updateEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        if ($entityInstance instanceof User) {
            
            $currentPassword = $entityInstance->getPassword();
            $originalPassword = $entityManager->getUnitOfWork()->getOriginalEntityData($entityInstance)['password'];

            if ($currentPassword !== $originalPassword) {
                $entityInstance->setPassword(
                    $this->passwordHasher->hashPassword($entityInstance, $currentPassword)
                );
            }
        }

        parent::updateEntity($entityManager, $entityInstance);
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInPlural('Потребители')  // Заглавие за множествено число
            ->setEntityLabelInSingular('Потребител') // Заглавие за единствено число
            ->setPageTitle(Crud::PAGE_NEW, 'Нов потребител') // Промяна на заглавието на страницата за нов клиент
            ->setPageTitle(Crud::PAGE_EDIT, 'Редактирай потребител')
            ->setPageTitle(Crud::PAGE_INDEX, 'Списък с потребители')
            ->setPageTitle(Crud::PAGE_DETAIL, 'Детайли за потребител');         
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            
            ->update(Crud::PAGE_INDEX, Action::NEW, function (Action $action) {
                return $action->setLabel('Нов потребител'); // Задаване на нов текст за бутона
            })
            ->update(Crud::PAGE_INDEX, Action::EDIT, function (Action $action) {
                return $action->setLabel('редактиране'); // Задаване на нов текст за бутона
            })
            ->update(Crud::PAGE_INDEX, Action::DELETE, function (Action $action) {
                return $action->setLabel('изтрий'); // Задаване на нов текст за бутона
            })
            ->update(Crud::PAGE_INDEX, Action::DETAIL, function (Action $action) {
                return $action->setLabel('детайли за потребител'); // Задаване на нов текст за бутона
            })
            ->update(Crud::PAGE_NEW, Action::SAVE_AND_RETURN, function (Action $action) {
                return $action->setLabel('Запиши');
            })
            ->update(Crud::PAGE_NEW, Action::SAVE_AND_ADD_ANOTHER, function (Action $action) {
                return $action->setLabel('Запиши и създай нов потребител');
            })
            ->update(Crud::PAGE_EDIT, Action::SAVE_AND_RETURN, function (Action $action) {
                return $action->setLabel('Запази и върни');
            })
            ->update(Crud::PAGE_EDIT, Action::SAVE_AND_CONTINUE, function (Action $action) {
                return $action->setLabel('Запази и продължи');
            })
            ->update(Crud::PAGE_DETAIL, Action::DELETE, function (Action $action) {
                return $action->setLabel('Изтрий');
            })
            ->update(Crud::PAGE_DETAIL, Action::INDEX, function (Action $action) {
                return $action->setLabel('Обратно към списъка');
            })
            ->update(Crud::PAGE_DETAIL, Action::EDIT, function (Action $action) {
                return $action->setLabel('Редактиране');
            });
    }




    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('email', 'Ел. поща'),
            TextField::new('password', 'Парола'),
            TextField::new('name', 'Име'),
            ChoiceField::new('roles', 'Роля')
                        ->setChoices([
                        'User' => 'ROLE_USER',
                        'Super User' => 'ROLE_SUPER_USER',
                        'Admin' => 'ROLE_ADMIN',
                        ])
                        ->allowMultipleChoices()
                        ->setRequired(true)
                        ->setFormTypeOption('empty_data', ['ROLE_USER']) // Задаване на стойност по подразбиране само ако полето е празно            
                ];
    }
    


    public function deleteEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        
        // Намираме свързания Customer
        $customer = $entityInstance->getCustomer();
        // Ако Customer съществува, актуализирайте полето isUser
        if ($customer) {
            $customer->setIsUser(false);
            
            // Запазете промените в базата данни
            $entityManager->persist($customer);
        }

        // Премахнете User
        $entityManager->remove($entityInstance);

        // Запазете промените в базата данни
        $entityManager->flush();
    }

}
