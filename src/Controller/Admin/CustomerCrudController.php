<?php

namespace App\Controller\Admin;

use App\Entity\Customer;
use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FieldCollection;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FilterCollection;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;


class CustomerCrudController extends AbstractCrudController
{
    private $entityManager;
    private UserPasswordHasherInterface $passwordHasher;
    private MailerInterface $mailer;

    public function __construct(EntityManagerInterface $entityManager,
                                UserPasswordHasherInterface $passwordHasher,
                                MailerInterface $mailer)
    {
        $this->entityManager = $entityManager;
        $this->passwordHasher = $passwordHasher;
        $this->mailer = $mailer;
        
    }

    public static function getEntityFqcn(): string
    {
        return Customer::class;
    }

    
    public function configureFields(string $pageName): iterable
    {
        


        return [
            //IdField::new('id'),
            TextField::new('name'),
            TextField::new('town'),
            TextField::new('address'),
            TextField::new('phone1'),
            TextField::new('phone2'),
            TextField::new('mail', 'ел. поща'),
            TextEditorField::new('note'),
            BooleanField::new('isUser', 'Потребител')
            ->renderAsSwitch(false)
            ->setHelp('Маркирай, ако този клиент ще има достъп като потребител'),
           
        ];
    }

    // Метод за създаване на нов клиент
    public function createEntity(string $entityFqcn)
    {
        $customer = new Customer();
        
        return $customer;
    }

    public function persistEntity(EntityManagerInterface $entityManager, $entity): void
    {
        // Извикване на родителския метод
        parent::persistEntity($entityManager, $entity);
        //dd($entity);
        // Проверка дали entity е Customer
        if ($entity instanceof Customer) {
            // Логика за създаване на User, ако 'isUser' е true
            if ($entity->getIsUser()) {
                //dd($entity);
                $user = new User();
                $plainPassword = $this->generateRandomPassword();

                $user->setEmail($entity->getMail());
                $user->setPassword($this->passwordHasher->hashPassword($user, $plainPassword)); // Временна парола
                $user->setName($entity->getName());
                $user->setCustomer($entity);
                

                $user->setRoles(['ROLE_USER']);

                // Изпращаме имейл с паролата
                //dd($user);
            //$this->sendPasswordEmail($user->getEmail(), $plainPassword);
                
                // Запазване на новия User
                $this->entityManager->persist($user);
            }
            // Запазване на данните в базата
            $this->entityManager->flush();
        }
    }

    private function generateRandomPassword($length = 4)
    {
        return bin2hex(random_bytes($length / 2)); // Генерира парола от 4 символа
    }

    private function sendPasswordEmail(string $email, string $plainPassword): void
    {
        
        $emailMessage = (new Email())
            ->from('manolvelikov@gmail.com')
            ->to($email)
            ->subject('Вашата парола за PVC RUSE')
            ->text("Вашата нова парола е: $plainPassword");

        $this->mailer->send($emailMessage);
    }

    public function updateEntity(EntityManagerInterface $entityManager, $entityInstance): void
{
    if ($entityInstance instanceof Customer) {
        // Намери свързания потребител
        $user = $entityInstance->getUser();
        
        // Ако потребителят съществува, актуализирай данните
        if ($user) {
            $user->setName($entityInstance->getName());
            $user->setEmail($entityInstance->getMail());
         } elseif ($entityInstance->getIsUser() == true){
            $user = new User();
                $plainPassword = $this->generateRandomPassword();

                $user->setEmail($entityInstance->getMail());
                $user->setPassword($this->passwordHasher->hashPassword($user, $plainPassword)); // Временна парола
                $user->setName($entityInstance->getName());
                $user->setCustomer($entityInstance);
                $user->setRoles(['ROLE_USER']);

                // Изпращаме имейл с паролата
                //dd($user);
                //$this->sendPasswordEmail($user->getEmail(), $plainPassword);
                
                // Запазване на новия User
                $this->entityManager->persist($user);
            }
            //Изтриване на съществуващ потребител, ако е променена настройката за user на customer
            if($user && $entityInstance->getIsUser() == false){
                // Първо премахваме връзката между Customer и User
                $entityInstance->setUser(null);
                // Премахване на потребителя от EntityManager
                $this->entityManager->remove($user);
            }
       // Запазване на данните в базата
        $entityManager->flush();
    }

    parent::updateEntity($entityManager, $entityInstance);
}

    
}
