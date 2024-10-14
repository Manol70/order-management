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
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Validator\Constraints\Type as AssertType;
use Symfony\Component\Validator\Constraints\PositiveOrZero;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use App\Form\DataTransformer\EmptyStringToNullTransformer;

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
            'label' => 'Тип',
            'choice_label' => function ( Type $type ) {
                return $type->getName ();
            }
        ])
        ->add('quadrature', NumberType::class, [
            'label' => 'Квадратура',
            'required' => false,
            'constraints' => [
                new NotBlank(['message' => 'Моля, въведете квадратура.']),
               /* new AssertType(['type' => 'float']),*/
                new PositiveOrZero(['message' => 'Въведете положително число, или нула!']),
            ],
            'scale' => 2, // до два знака след десетичната запетая
            'empty_data' => 0.00 //задава нула когато се изпрати празно поле
        ])
        ->add('price', NumberType::class, [
            'label' => 'Цена',
            'required' => false,
            'constraints' => [
                new NotBlank(['message' => 'Моля, въведете цена.']),
                new AssertType(['type' => 'float']),
                new PositiveOrZero(['message' => 'Въведете положително число, или нула!']),
            ],
            'scale' => 2, // до два знака след десетичната запетая
            'empty_data' => 0.00
        ])
        ->add('for_date', DateType::class,[
            'label' => 'Готова на',
            'widget' => 'single_text',
            'label' => 'За дата',
           /* 'constraints' => [], // премахва всякакви глобални констрейнти
            'validation_groups' => ['edit'], // използва се само при редакция */
            
        ]) 
        ->add('glass', CheckboxType::class, array(
            'label' => 'СТЪКЛОПАКЕТ',
            'required' => false,
            'disabled' => true,
            'data' => $options['glass_value'],
        ))
        ->add('newGlass', CheckboxType::class, [
            'mapped' => false,
            'label' => 'Промени състоянието на СТЪКЛОПАКЕТ',
            'required' => false,
            'data' => $options['glass_value']
        ])
        ->add('detail', CheckboxType::class, array(
            'label' => 'ПРОФИЛ',
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
            'label' => 'КОМАРНИК',
            'required' => false,
            'disabled' => true,
            'data' => $options['mosquito_value'],
        ))
        ->add('newMosquito', CheckboxType::class, [
            'mapped' => false,
            'label' => 'Промени състоянието на КОМАРНИК',
            'required' => false,
            'data' => $options['mosquito_value']
        ])
        ->add('note', TextareaType::class, [
            'label' => 'Забележка',
            'required' => false,
            'empty_data' => null, //празните стойности да се третират като null
        ])
        
        ->add('scheme', null, [
            'label' => 'Схема',
            'disabled' => true,
            'attr' => ['readonly' => true],
        ])
        ->add('schemeFile', FileType::class, [
            'label' => 'Избери файл',
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
            'label' => 'Име на файла',
            'mapped' => false,
            'required' => false, // Полето не е задължително
            'attr' => ['placeholder' => 'Enter filename']  
        ])
        ->add('removeScheme', CheckboxType::class, [
            'mapped' => false, // Полето не е свързано директно с обекта
            'required' => false, // Полето не е задължително
            'label' => 'Изтрий схемата', // Надпис за чекбокса
        ]);

    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'glass_value' => false,
            'mosquito_value' => false,
            'detail_value' => false,
            'data_class' => Order::class,
            
        ]);
    }
} 