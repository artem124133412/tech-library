<?php

declare(strict_types=1);

namespace App\Command;

use App\Entity\Book;
use App\Entity\Topic;
use App\Enum\BookStatus;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:seed',
    description: 'Заповнює бібліотеку тестовими розділами та книгами',
)]
final class SeedCommand extends Command
{
    public function __construct(
        private readonly EntityManagerInterface $em,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        if ($this->em->getRepository(Topic::class)->count([]) > 0) {
            $io->warning('У базі даних уже є розділи. Пропускаємо.');
            return Command::SUCCESS;
        }

        $backend = (new Topic())->setSlug('backend')->setName('Серверна розробка');
        $networks = (new Topic())->setSlug('networks')->setName('Комп’ютерні мережі');
        $automation = (new Topic())->setSlug('automation')->setName('Автоматизація та ПЛК');
        $databases = (new Topic())->setSlug('databases')->setName('Бази даних');

        foreach ([$backend, $networks, $automation, $databases] as $topic) {
            $this->em->persist($topic);
        }

        $books = [
            [$backend, '978-1491950357', 'Designing Data-Intensive Applications', 'Martin Kleppmann', 2017, BookStatus::Available, 'Фундаментальний посібник з архітектури розподілених систем, реплікації та обробки потоків даних.'],
            [$backend, '978-0134494166', 'Clean Architecture', 'Robert C. Martin', 2017, BookStatus::CheckedOut, 'Принципи проєктування модульних систем, розділення відповідальності та незалежності бізнес-логіки.'],
            [$backend, '978-1098118730', 'PHP 8 Objects, Patterns, and Practice', 'Matt Zandstra', 2021, BookStatus::DigitalOnly, 'Поглиблене вивчення об’єктно-орієнтованого програмування, патернів проєктування та інструментів сучасного PHP.'],
            [$networks, '978-0133594140', 'Computer Networking: A Top-Down Approach', 'James Kurose, Keith Ross', 2020, BookStatus::Available, 'Детальний розбір стеку протоколів TCP/IP, маршрутизації, DNS та принципів роботи прикладного рівня.'],
            [$networks, '978-1587147029', 'CCNA 200-301 Official Cert Guide', 'Wendell Odom', 2019, BookStatus::Available, 'Практичний посібник з налаштування VLAN, статичної та динамічної маршрутизації і безпеки мереж.'],
            [$automation, '978-0128029299', 'Programmable Logic Controllers', 'William Bolton', 2015, BookStatus::Available, 'Архітектура промислових контролерів, мови стандарту IEC 61131-3 (LD, FBD, ST) та опитування датчиків.'],
            [$automation, '978-1119457534', 'Modern Control Engineering', 'Katsuhiko Ogata', 2019, BookStatus::CheckedOut, 'Теорія автоматичного керування, частотний аналіз, критерії стійкості та синтез регуляторів.'],
            [$databases, '978-1492057611', 'High Performance MySQL', 'Silvia Botros, Jeremy Tinley', 2021, BookStatus::DigitalOnly, 'Оптимізація індексів, профілювання запитів, транзакції InnoDB та масштабування баз даних.'],
        ];

        foreach ($books as [$topic, $isbn, $title, $author, $year, $status, $summary]) {
            $book = (new Book())
                ->setTopic($topic)
                ->setIsbn($isbn)
                ->setTitle($title)
                ->setAuthor($author)
                ->setPublishedYear($year)
                ->setStatus($status)
                ->setSummary($summary);

            $this->em->persist($book);
        }

        $this->em->flush();
        $io->success('Тестові розділи та книги успішно додані до каталогу!');

        return Command::SUCCESS;
    }
}