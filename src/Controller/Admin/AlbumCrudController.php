<?php

namespace App\Controller\Admin;

use App\Entity\Album;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\UrlField;

class AlbumCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Album::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm(), // Cache l'ID lors de la création/modification
            TextField::new('title', 'Titre de l\'album'),
            UrlField::new('cover_url', 'Lien de la jaquette (Image)'),
            DateField::new('release_date', 'Date de sortie')->setRequired(false),
            
            // Crée automatiquement une liste déroulante liée à tes artistes SQL
            AssociationField::new('artist', 'Artiste lié')
        ];
    }
}