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

        $topicRepo = $this->em->getRepository(Topic::class);
        $bookRepo = $this->em->getRepository(Book::class);

        $topicsData = [
            'backend' => 'Серверна розробка',
            'networks' => 'Комп’ютерні мережі',
            'automation' => 'Автоматизація та ПЛК',
            'databases' => 'Бази даних',
            'devops' => 'DevOps та адміністрування',
            'security' => 'Кібербезпека',
            'algorithms' => 'Алгоритми та архітектура ПЗ',
        ];

        $topics = [];
        $addedTopics = 0;

        foreach ($topicsData as $slug => $name) {
            $topic = $topicRepo->findOneBy(['slug' => $slug]);
            if ($topic === null) {
                $topic = (new Topic())->setSlug($slug)->setName($name);
                $this->em->persist($topic);
                $addedTopics++;
            }
            $topics[$slug] = $topic;
        }

        $booksData = [
            ['backend', '978-1491950357', 'Designing Data-Intensive Applications', 'Martin Kleppmann', 2017, BookStatus::Available, 'Фундаментальний посібник з архітектури розподілених систем, реплікації та обробки потоків даних.'],
            ['backend', '978-0134494166', 'Clean Architecture', 'Robert C. Martin', 2017, BookStatus::CheckedOut, 'Принципи проєктування модульних систем, розділення відповідальності та незалежності бізнес-логіки.'],
            ['backend', '978-1098118730', 'PHP 8 Objects, Patterns, and Practice', 'Matt Zandstra', 2021, BookStatus::DigitalOnly, 'Поглиблене вивчення об’єктно-орієнтованого програмування, патернів проєктування та інструментів сучасного PHP.'],
            ['backend', '978-1801070058', 'Symfony 6 Web Development', 'Nicolas Grekas, Fabien Potencier', 2023, BookStatus::Available, 'Побудова сучасних вебзастосунків на Symfony: маршрутизація, Doctrine ORM, безпека та тестування.'],

            ['networks', '978-0133594140', 'Computer Networking: A Top-Down Approach', 'James Kurose, Keith Ross', 2020, BookStatus::Available, 'Детальний розбір стеку протоколів TCP/IP, маршрутизації, DNS та принципів роботи прикладного рівня.'],
            ['networks', '978-1587147029', 'CCNA 200-301 Official Cert Guide', 'Wendell Odom', 2019, BookStatus::Available, 'Практичний посібник з налаштування VLAN, статичної та динамічної маршрутизації і безпеки мереж.'],
            ['networks', '978-1593279325', 'Practical Packet Analysis', 'Chris Sanders', 2017, BookStatus::DigitalOnly, 'Аналіз мережевого трафіку за допомогою Wireshark та діагностика проблем на рівні пакетів.'],

            ['automation', '978-0128029299', 'Programmable Logic Controllers', 'William Bolton', 2015, BookStatus::Available, 'Архітектура промислових контролерів, мови стандарту IEC 61131-3 (LD, FBD, ST) та опитування датчиків.'],
            ['automation', '978-1119457534', 'Modern Control Engineering', 'Katsuhiko Ogata', 2019, BookStatus::CheckedOut, 'Теорія автоматичного керування, частотний аналіз, критерії стійкості та синтез регуляторів.'],
            ['automation', '978-1498737326', 'Industrial Network Security', 'Eric D. Knapp, Joel Thomas Langill', 2015, BookStatus::Available, 'Захист систем SCADA, промислових шин та контролерів у виробничих мережах.'],

            ['databases', '978-1492057611', 'High Performance MySQL', 'Silvia Botros, Jeremy Tinley', 2021, BookStatus::DigitalOnly, 'Оптимізація індексів, профілювання запитів, транзакції InnoDB та масштабування баз даних.'],
            ['databases', '978-1491901632', 'SQL Antipatterns', 'Bill Karwin', 2010, BookStatus::Available, 'Типові помилки проєктування реляційних схем, запитів та способів уникнення деградації продуктивності.'],
            ['databases', '978-1098108304', 'Learning SQL', 'Alan Beaulieu', 2020, BookStatus::CheckedOut, 'Практичний курс із написання складних вибірок, групувань, віконних функцій та транзакцій.'],

            ['devops', '978-1492046530', 'UNIX and Linux System Administration Handbook', 'Evi Nemeth, Garth Snyder', 2017, BookStatus::Available, 'Вичерпний довідник із адміністрування серверів, мережевих служб, прав доступу та автоматизації.'],
            ['devops', '978-1098106225', 'Kubernetes: Up and Running', 'Brendan Burns, Joe Beda', 2022, BookStatus::DigitalOnly, 'Оркестрація контейнерів, розгортання мікросервісів, балансування навантаження та моніторинг кластерів.'],
            ['devops', '978-1492089988', 'Windows Server Administration Fundamentals', 'Crystal Panek', 2020, BookStatus::Available, 'Налаштування Active Directory, групових політик GPO, DNS, DHCP та віддаленого керування через PowerShell.'],

            ['security', '978-1118026472', 'The Web Application Hacker’s Handbook', 'Dafydd Stuttard, Marcus Pinto', 2011, BookStatus::CheckedOut, 'Методологія пошуку та захисту від вразливостей вебзастосунків: SQL-ін’єкцій, XSS, CSRF та помилок автентифікації.'],
            ['security', '978-0470474242', 'Cryptography Engineering', 'Niels Ferguson, Bruce Schneier', 2010, BookStatus::Available, 'Практичне застосування симетричного та асиметричного шифрування, геш-функцій і цифрових підписів.'],
            ['security', '978-1718501126', 'Black Hat Python', 'Justin Seitz, Tim Arnold', 2021, BookStatus::DigitalOnly, 'Автоматизація тестування на проникнення, аналіз мережевих пакетів та створення утиліт безпеки на Python.'],

            ['algorithms', '978-0262046305', 'Introduction to Algorithms', 'Thomas H. Cormen, Charles E. Leiserson', 2022, BookStatus::Available, 'Класичний академічний підручник зі структур даних, графових алгоритмів, динамічного програмування та оцінки складності.'],
            ['algorithms', '978-0134757599', 'Refactoring: Improving the Design of Existing Code', 'Martin Fowler', 2018, BookStatus::CheckedOut, 'Систематичний каталог технік покращення структури коду без зміни його зовнішньої поведінки.'],
            ['algorithms', '978-0135957059', 'The Pragmatic Programmer', 'David Thomas, Andrew Hunt', 2019, BookStatus::Available, 'Практичні поради щодо інженерної культури, проєктування гнучких систем та професійної розробки ПЗ.'],
        ];

        $addedBooks = 0;

        foreach ($booksData as [$topicSlug, $isbn, $title, $author, $year, $status, $summary]) {
            if ($bookRepo->findOneBy(['isbn' => $isbn]) !== null) {
                continue;
            }

            $book = (new Book())
                ->setTopic($topics[$topicSlug])
                ->setIsbn($isbn)
                ->setTitle($title)
                ->setAuthor($author)
                ->setPublishedYear($year)
                ->setStatus($status)
                ->setSummary($summary);

            $this->em->persist($book);
            $addedBooks++;
        }

        $this->em->flush();

        $io->success(sprintf('Додано нових розділів: %d, нових книг: %d.', $addedTopics, $addedBooks));

        return Command::SUCCESS;
    }
}