<?php
namespace App\Form;

use App\Entity\Order;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\FormTypeInterface;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use App\Entity\TypeMontage;
use App\Entity\Type;
use App\Entity\Customer;
use Symfony\Component\Validator\Constraints\File;

class OrderType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
       //dd($options);
        $builder
        
        ->add('customer', EntityType::class, [
            'class' => Customer::class,
            'label' => 'Клиент',
            'choice_label' => 'name', // Или друго поле, което искате да покажете, като име, например
            //'disabled' => true, // За да направите полето непроменяемо
        ])
        ->add('type', EntityType::class,[
            'class' => Type::class,
            'choice_label' => function ( Type $type ) {
                return $type->getName ();
            }
        ])
        ->add('quadrature')
        ->add('price')
        ->add('for_date')
        ->add('glass', CheckboxType::class, array(
            'label' => 'стъклопакет',
            'required' => false,
            'disabled' => true,
            'data' => $options['glass_value'],
        ))
        ->add('newGlass', CheckboxType::class, [
            'mapped' => false,
            'label' => 'Промени състоянието на glass',
            'required' => false,
            'data' => $options['glass_value']
        ])
        ->add('detail', CheckboxType::class, array(
            'label' => 'профил',
            'required' => false,
            'disabled' => true,
            'data' => $options['detail_value'],
        ))
        ->add('newDetail', CheckboxType::class, [
            'mapped' => false,
            'label' => 'Промени състоянието на ПРОФИЛ',
            'required' => false,
            'data' => $options['detail_value']
        ])
        ->add('mosquito', CheckboxType::class, array(
            'label' => 'комарник',
            'required' => false,
            'disabled' => true,
            'data' => $options['mosquito_value'],
        ))
        ->add('newMosquito', CheckboxType::class, [
            'mapped' => false,
            'label' => 'Промени състоянието на комарник',
            'required' => false,
            'data' => $options['mosquito_value']
        ])
        ->add('note')
        ->add('scheme', null, [
            'disabled' => true,
            'attr' => ['readonly' => true],
        ])
        ->add('schemeFile', FileType::class, [
            'mapped' => false,
            'required' => false,
            'constraints' => [
                new File([
                    //'maxSize' => '1024k',
                    'extensions' => ['pdf'],
                    //'extensionsMessage' => 'Невалиден формат на файла',
                ])
            ]
        ])
        
        ->add('filename', TextType::class, [
            'mapped' => false,
            'required' => false, // Полето не е задължително
            'attr' => ['placeholder' => 'Enter filename']  
        ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'glass_value' => false,
            'mosquito_value' => false,
            'detail_value' => false
        ]);
    }
}