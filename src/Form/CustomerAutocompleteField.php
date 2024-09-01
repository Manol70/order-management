<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Form;

use App\Entity\Customer;
use App\Repository\CustomerRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Count;
use Symfony\UX\Autocomplete\Form\AsEntityAutocompleteField;
use Symfony\UX\Autocomplete\Form\BaseEntityAutocompleteType;
use Doctrine\ORM\QueryBuilder;

#[AsEntityAutocompleteField]
class CustomerAutocompleteField extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'class' => Customer::class,
            'attr' => ['data-controller' => 'custom-autocomplete',],
            'placeholder' => 'Избери клиент',
            // 'searchable_fields' => ['name'],
            'label' => 'Въведи клиент',
            'choice_label' => 'name',
            //'multiple' => true,
            'max_characters' => 1,
            //'required' => false,
           /* 'constraints' => [
                new Count(min: 1, minMessage: 'We need to eat *something*'),
            ],*/
            'filter_query' => function(QueryBuilder $qb, string $searchString, CustomerRepository $repository) {
                if (!$searchString) {
                    return;
                }

                $qb->andWhere('entity.name LIKE :name')
                    ->setParameter('name', $searchString.'%', );
            },
        ]);
    }

    public function getParent(): string
    {
        return BaseEntityAutocompleteType::class;
    }
}
