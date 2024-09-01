<?php
namespace App\Form;

use App\Entity\Status;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use App\Entity\Type;

class ReferenceDateStatusFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
       //dd($options);
       $defaultDate = $options['default_date'] ?? null;
       $defaultStatus = $options['default_status'] ?? null;
       
        $builder
        ->add('for_date', DateType::class,[
            'label' => 'Избери друга дата',
            'widget' => 'single_text',
            'input'  => 'datetime_immutable',
            'data' => $defaultDate
        ])
        ->add('type', EntityType::class,[
            'placeholder' => 'всички',
            'required' => false,
            'label' => 'Тип',
            'class' => Type::class,
            'choice_label' => function ( Type $type ) {
                return $type->getName ();
            }
        ])
        ->add('status', EntityType::class,[
            'placeholder' => 'всички',
            'required' => false,
            'label' => 'Статус',
            'class' => Status::class,
            'choice_label' => function ( Status $status ){
                return $status->getName();
            },
            'data' => $defaultStatus
        ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'default_date' => null,
            'default_status' => null,
            'data_class' => null,
        ]);
    }
}