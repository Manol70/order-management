<?php
namespace App\Form;

use App\Entity\Order;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\FormTypeInterface;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use App\Entity\TypeMontage;
use App\Entity\Type;
use App\Entity\Customer;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;

class CustomerCreateFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder

        ->add('name')
        ->add('town')
        ->add('address')
        ->add('phone1')
        ->add('phone2')
        ->add('mail')
        ->add('mosquito')
        ->add('note');
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        
    }
}