<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Entity\Book;
use App\Enum\BookStatus;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

final class BookCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Book::class;
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id')->hideOnForm();
        yield AssociationField::new('topic', 'Розділ');
        yield TextField::new('isbn', 'ISBN');
        yield TextField::new('title', 'Назва книги');
        yield TextField::new('author', 'Автор');
        yield IntegerField::new('publishedYear', 'Рік видання');
        yield ChoiceField::new('status', 'Статус')
            ->setChoices([
                'Доступна' => BookStatus::Available,
                'На руках' => BookStatus::CheckedOut,
                'Електронна' => BookStatus::DigitalOnly,
            ]);
        yield TextareaField::new('summary', 'Анотація')->hideOnIndex();
    }
}