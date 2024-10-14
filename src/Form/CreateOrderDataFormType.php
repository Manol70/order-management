<?php
namespace App\Form;

use App\Entity\Order;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormTypeInterface;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use App\Entity\TypeMontage;
use App\Entity\Type;
use App\Entity\Customer;
use Doctrine\DBAL\Types\FloatType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Validator\Constraints\GreaterThanOrEqual;
use Symfony\Component\Validator\Constraints\Type as AssertType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Validator\Constraints\PositiveOrZero;

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
                new AssertType(['type' => 'float']),
                new PositiveOrZero(['message' => 'Въведете положително число, или нула!']),
            ],
            'scale' => 2, // до два знака след десетичната запетая
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
        ])
        ->add('for_date', DateType::class,[
            'widget' => 'single_text',
            'label' => 'За дата',
            'input'  => 'datetime_immutable',
            'constraints' => [
            new NotBlank(['message' => 'Моля, въведете дата.']),
            new GreaterThanOrEqual([
            'value' => new \DateTimeImmutable('today'),
            'message' => 'Датата не може да бъде по-стара от днешната.',
        ]),
    ],
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
        ->add('note', TextareaType::class, [
            'label' => 'Забележка',
            'required' => false,
        ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            /*'data_class' => Order::class,*/
        ]);
    }
}