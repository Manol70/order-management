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




    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('email'),
            TextField::new('password'),
            TextField::new('name'),
            ChoiceField::new('roles')
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
