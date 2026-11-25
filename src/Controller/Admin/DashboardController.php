<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminDashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[AdminDashboard(routePath: '/admin', routeName: 'admin')]
#[IsGranted('ROLE_ADMIN')]
final class DashboardController extends AbstractDashboardController
{
    public function index(): Response
    {
        return $this->redirectToRoute('admin_topic_index');
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('Tech Library · Адмін-панель');
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToDashboard('Головна', 'fa fa-home');
        yield MenuItem::section('Бібліотека');
        yield MenuItem::linkTo(TopicCrudController::class, 'Розділи', 'fa fa-folder');
        yield MenuItem::linkTo(BookCrudController::class, 'Книги', 'fa fa-book');
        yield MenuItem::section('Навігація');
        yield MenuItem::linkToUrl('На публічний сайт', 'fa fa-globe', '/');
    }
}