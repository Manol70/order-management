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

class CreateOrderDataFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder

        ->add('customer', CustomerAutocompleteField::class,[
            'class' => Customer::class,
            'choice_label' => function(Customer $Customer){
                return $Customer->getName();
            },
            'attr' => ['class' => 'customer-autocomplete-field'] // добавяне на клас за стилизиране
        ])
        ->add('type', EntityType::class,[
            'class' => Type::class,
            'choice_label' => function ( Type $type ) {
                return $type->getName ();
            }
        ])
        ->add('quadrature')
        ->add('price')
        ->add('for_date', DateType::class,[
            'widget' => 'single_text',
            'input'  => 'datetime_immutable'
        ])
        ->add('glass', CheckboxType::class, array(
            'label' => 'стъклопакет',
            'required' => false
        ))
        ->add('detail', CheckboxType::class, array(
            'label' => 'Допълнителен детайл',
            'required' => false
        ))
        ->add('mosquito', CheckboxType::class, array(
            'label' => 'Комарник',
            'required' => false
        ))
        ->add('note');
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        
    }
}