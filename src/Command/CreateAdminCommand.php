<?php

declare(strict_types=1);

namespace App\Command;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AsCommand(
    name: 'app:admin:create',
    description: 'Створює обліковий запис адміністратора з роллю ROLE_ADMIN',
)]
final class CreateAdminCommand extends Command
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly UserPasswordHasherInterface $passwordHasher,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $email = (string) $io->ask('Введіть email адміністратора', 'admin@library.test');
        $plainPassword = (string) $io->askHidden('Введіть пароль адміністратора');

        if (mb_strlen($plainPassword) < 6) {
            $io->error('Пароль має містити щонайменше 6 символів.');
            return Command::FAILURE;
        }

        $repo = $this->em->getRepository(User::class);
        $user = $repo->findOneBy(['email' => $email]) ?? new User();

        $user->setEmail($email);
        $user->setRoles(['ROLE_ADMIN']);
        $user->setPassword($this->passwordHasher->hashPassword($user, $plainPassword));

        $this->em->persist($user);
        $this->em->flush();

        $io->success(sprintf('Адміністратора "%s" успішно створено!', $email));

        return Command::SUCCESS;
    }
}