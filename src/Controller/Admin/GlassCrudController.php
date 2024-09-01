<?php

namespace App\Controller\Admin;

use App\Entity\Glass;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class GlassCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Glass::class;
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
