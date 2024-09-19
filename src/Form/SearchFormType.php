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
use App\Entity\Detail;
use App\Entity\Glass;
use App\Entity\Mosquito;
use App\Entity\Status;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class SearchFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder

        ->add('customer', CustomerAutocompleteField::class,[
            'class' => Customer::class,
            'choice_label' => function(Customer $Customer){
                return $Customer->getName();
            },
            'required' => false,
            'attr' => ['class' => 'tom-select customer-autocomplete-field'] // добавяне на клас за стилизиране
        ])
        ->add('type', EntityType::class,[
            'class' => Type::class,
            'choice_label' => 'name',
            'label' => 'Тип',
            'placeholder' => 'всички',
            'required' => false
        ])
        ->add('status', EntityType::class, [
            'class' => Status::class,
            'choice_label' => 'name',
            'label' => 'Сатус',
            'placeholder' => 'всички',
            'required' => false
        ])
        ->add('glass', EntityType::class,[
            'class' => Glass::class,
            'choice_label' => 'name',
            'label' => 'Стъклопакет',
            'placeholder' => 'всички',
            'required' => false
        ])
        ->add('detail', EntityType::class,[
            'class' => Detail::class,
            'choice_label' => 'name',
            'label' => 'Профил',
            'placeholder' => 'всички',
            'required' => false
        ])
        ->add('mosquito', EntityType::class,[
            'class' => Mosquito::class,
            'choice_label' => 'name',
            'label' => 'Комарник',
            'placeholder' => 'всички',
            'required' => false
        ])
        ->add('from_date', DateType::class,[
            'widget' => 'single_text',
            'input'  => 'datetime_immutable'
        ])
        ->add('to_date', DateType::class,[
            'widget' => 'single_text',
            'input' => 'datetime_immutable',
            'data' => new \DateTimeImmutable() // задаваме текущата дата
        ])
        ->add('source', HiddenType::class, [
            'data' => 'filter'
        ]);
        
        
     
    }


    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            
        ]);
    }
}