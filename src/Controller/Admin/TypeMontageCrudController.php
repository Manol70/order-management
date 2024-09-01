<?php

namespace App\Controller\Admin;

use App\Entity\TypeMontage;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class TypeMontageCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return TypeMontage::class;
    }

    /*
    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id'),
            TextField::new('title'),
            TextEditorField::new('description'),
        ];
    }
    */
}
