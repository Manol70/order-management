<?php
namespace App\Form;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\FormTypeInterface;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use App\Entity\Type;
use Symfony\Component\OptionsResolver\OptionsResolver;
use App\Entity\Customer;
use DateTime;
use Symfony\Component\DomCrawler\Form;



class AddOrderFormType extends AbstractType
{
     /**
     * {@inheritdoc}
    */
    
    public function buildForm(FormBuilderInterface $builder, array $options,)
    {   
        
        $typeMontageId = $options['datas'];
        //dd($options);
            $builder
            
            ->add('type', EntityType::class,[
                'class' => Type::class,
                'choice_label' => function ( Type $type ) {
                    return $type->getName ();
                }
            ])
            ->add('quadrature')
            ->add('price')
            ->add('from_date', DateType::class,[
                'widget' => 'single_text',
                'input'  => 'datetime_immutable'
            ])
            ->add('note');
    }
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'datas' => null, 'name' => null
        ]);
    }

}