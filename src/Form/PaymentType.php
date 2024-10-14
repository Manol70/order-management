<?php

namespace App\Form;

use App\Entity\Payment;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Customer;
use Doctrine\ORM\Mapping\Entity;

class PaymentType extends AbstractType
{
    
    
        public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('orderId', HiddenType::class);
            /*->add('paymentAmount', NumberType::class, [
                'label' => 'Сум за плащане',
            ])*/
            /*->add('orderPayments', HiddenType::class);*/
            
            /*->add('submit', SubmitType::class, ['label' => 'Submit']);*/
            
            
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => null,
            
        ]);
    }
        
        
    
}
