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
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;


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

    

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInPlural('Клиенти')  // Заглавие за множествено число
            ->setEntityLabelInSingular('Клиент') // Заглавие за единствено число
            ->setPageTitle(Crud::PAGE_NEW, 'Нов клиент') // Промяна на заглавието на страницата за нов клиент
            ->setPageTitle(Crud::PAGE_EDIT, 'Редактирай клиент')
            ->setPageTitle(Crud::PAGE_INDEX, 'Списък с клиенти')
            ->setPageTitle(Crud::PAGE_DETAIL, 'Детайли за клиента');         
    }
    public function configureActions(Actions $actions): Actions
{
    return $actions
        ->update(Crud::PAGE_INDEX, Action::NEW, function (Action $action) {
            return $action->setLabel('Нов клиент'); // Задаване на нов текст за бутона
        })
        ->update(Crud::PAGE_INDEX, Action::EDIT, function (Action $action) {
            return $action->setLabel('редактиране'); // Задаване на нов текст за бутона
        })
        ->update(Crud::PAGE_INDEX, Action::DELETE, function (Action $action) {
            return $action->setLabel('изтрий'); // Задаване на нов текст за бутона
        })
        ->update(Crud::PAGE_INDEX, Action::DETAIL, function (Action $action) {
            return $action->setLabel('детайли за клиента'); // Задаване на нов текст за бутона
        })
        ->update(Crud::PAGE_NEW, Action::SAVE_AND_RETURN, function (Action $action) {
            return $action->setLabel('Запиши');
        })
        ->update(Crud::PAGE_NEW, Action::SAVE_AND_ADD_ANOTHER, function (Action $action) {
            return $action->setLabel('Запиши и създай нов клиент');
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
            //IdField::new('id'),
            TextField::new('name', 'Име'),
            TextField::new('town', 'Град'),
            TextField::new('address', 'Адрес'),
            TextField::new('phone1', 'Телефон1'),
            TextField::new('phone2', 'Телефон2'),
            TextField::new('mail', 'Ел. поща'),
            TextEditorField::new('note', 'Забележка'),
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
                //код за стартиране на работника за мейли от конзолата:  php bin/console messenger:consume async
            $this->sendPasswordEmail($user->getEmail(), $plainPassword);
                
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
            ->from('noreply@pvcruse.com')
            ->to($email)
            ->subject('Вашата парола за PVC RUSE')
            ->html("Вашата парола, за да можете да следите поръчките си в PVC РУСЕ е: <strong>$plainPassword</strong><br><br>Можете да влезете в профила си тук: <a href='https://yourdomain.com/login'>Вход в профила</a>");

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
                $this->sendPasswordEmail($user->getEmail(), $plainPassword);
                
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
