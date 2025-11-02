<?php

namespace App\DataFixtures;

use App\Entity\Admin;
use App\Entity\Student;
use App\Entity\Teacher;
use App\Entity\Instrument;
use App\Entity\InstrumentRental;
use App\Entity\Organization;
use App\Entity\Room;
use App\Entity\Course;
use App\Entity\Lesson;
use App\Entity\Enrollment;
use App\Entity\Payment;
use App\Entity\PreRegistration;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    public function __construct(
        private UserPasswordHasherInterface $passwordHasher
    ) {}

    public function load(ObjectManager $manager): void
    {
        // Clear existing data
        $this->clearData($manager);

        // Create demo organization first
        $organization = $this->createDemoOrganization($manager);
        $manager->flush(); // Ensure organization is persisted

        // Create users
        $users = $this->createUsers($manager, $organization);

        // Create instruments
        $instruments = $this->createInstruments($manager, $organization);

        // Create rooms
        $rooms = $this->createRooms($manager, $organization);

        // Create courses
        $courses = $this->createCourses($manager, $users['teachers'], $instruments, $organization);

        // Create enrollments
        $this->createEnrollments($manager, $users['students'], $courses, $organization);

        // Create lessons
        $this->createLessons($manager, $courses, $rooms, $organization);

        // Create payments
        $this->createPayments($manager, $users['students'], $organization);

        // Create pre-registrations
        $this->createPreRegistrations($manager, $organization);

        $manager->flush();
    }

    private function createDemoOrganization(ObjectManager $manager): Organization
    {
        $organization = new Organization();
        $organization->setName('École de Musique Demo')
                    ->setType('school')
                    ->setEmail('admin@demo-ecole.com')
                    ->setAddress('123 rue de la Musique, 75000 Paris')
                    ->setPhone('01 23 45 67 89')
                    ->setSubscriptionPlan('free')
                    ->setMaxStudents(30)
                    ->setMaxTeachers(3)
                    ->setMaxAdmins(1)
                    ->setActive(true)
                    ->setCreatedAt(new \DateTime())
                    ->setSlug('ecole-demo');
        
        $manager->persist($organization);
        $manager->flush();

        return $organization;
    }

    private function clearData(ObjectManager $manager): void
    {
        // Clean existing data
        $connection = $manager->getConnection();
        
        $connection->executeStatement('SET FOREIGN_KEY_CHECKS = 0');
        $connection->executeStatement('DELETE FROM payment');
        $connection->executeStatement('DELETE FROM lesson');
        $connection->executeStatement('DELETE FROM enrollment');
        $connection->executeStatement('DELETE FROM instrument_rental');
        $connection->executeStatement('DELETE FROM course');
        $connection->executeStatement('DELETE FROM pre_registration');
        $connection->executeStatement('DELETE FROM student');
        $connection->executeStatement('DELETE FROM teacher');
        $connection->executeStatement('DELETE FROM admin');
        $connection->executeStatement('DELETE FROM room');
        $connection->executeStatement('DELETE FROM instrument');
        $connection->executeStatement('DELETE FROM organization');
        $connection->executeStatement('SET FOREIGN_KEY_CHECKS = 1');
    }

    private function createInstruments(ObjectManager $manager, Organization $organization): array
    {
        $instrumentsData = [
            ['Piano Yamaha P-125', 'clavier', 'Piano numérique portable', 'Yamaha', 'P-125', 'SN001234', true, 'excellent'],
            ['Piano Kawai ES110', 'clavier', 'Piano numérique portable', 'Kawai', 'ES110', 'SN001235', true, 'good'],
            ['Guitare Classique', 'cordes', 'Guitare classique taille 4/4', 'Yamaha', 'C40', 'SN002001', true, 'good'],
            ['Guitare Classique Junior', 'cordes', 'Guitare classique taille 3/4', 'Yamaha', 'CS40', 'SN002002', true, 'excellent'],
            ['Guitare Électrique', 'cordes', 'Guitare électrique', 'Fender', 'Stratocaster', 'SN002100', false, 'excellent'],
            ['Violon 4/4', 'cordes', 'Violon taille adulte', 'Stentor', 'Student I', 'SN003001', true, 'good'],
            ['Violon 3/4', 'cordes', 'Violon taille enfant', 'Stentor', 'Student I', 'SN003002', true, 'fair'],
            ['Flûte traversière', 'vents', 'Flûte en métal argenté', 'Pearl', 'PF500', 'SN004001', true, 'excellent'],
            ['Clarinette Sib', 'vents', 'Clarinette en ébène', 'Buffet Crampon', 'E11', 'SN004101', true, 'good'],
            ['Trompette Sib', 'vents', 'Trompette en laiton', 'Bach', 'TR300H2', 'SN004201', true, 'excellent'],
            ['Batterie complète', 'percussions', 'Kit de batterie 5 fûts', 'Pearl', 'Roadshow', 'SN005001', false, 'good'],
            ['Djembé', 'percussions', 'Tambour traditionnel', 'Remo', 'DJ-0012-05', 'SN005101', true, 'excellent'],
        ];

        $instruments = [];
        foreach ($instrumentsData as [$name, $type, $description, $brand, $model, $serialNumber, $isRentable, $condition]) {
            $instrument = new Instrument();
            $instrument->setName($name)
                      ->setType($type)
                      ->setDescription($description)
                      ->setBrand($brand)
                      ->setModel($model)
                      ->setSerialNumber($serialNumber)
                      ->setIsRentable($isRentable)
                      ->setIsCurrentlyRented(false)
                      ->setCondition($condition)
                      ->setOrganization($organization);
            
            $manager->persist($instrument);
            $instruments[$name] = $instrument;
        }

        return $instruments;
    }

    private function createRooms(ObjectManager $manager, Organization $organization): array
    {
        $roomsData = [
            ['Salle Piano 1', 15, 'Rez-de-chaussée, à gauche'],
            ['Salle Piano 2', 15, 'Rez-de-chaussée, à droite'],
            ['Salle Piano 3', 12, '1er étage, bureau 101'],
            ['Studio Batterie', 8, 'Sous-sol, insonorisé'],
            ['Salle Ensemble', 25, '1er étage, grande salle'],
            ['Salle Guitare 1', 12, '1er étage, côté jardin'],
            ['Salle Guitare 2', 10, '1er étage, côté rue'],
            ['Studio Vocal', 10, '2ème étage'],
            ['Salle Cordes', 15, '2ème étage, côté jardin'],
            ['Salle Vents', 12, '2ème étage, côté rue'],
            ['Amphithéâtre', 50, 'Rez-de-chaussée, fond du bâtiment'],
            ['Studio d\'enregistrement', 6, 'Sous-sol, équipé'],
        ];

        $rooms = [];
        foreach ($roomsData as [$name, $capacity, $location]) {
            $room = new Room();
            $room->setName($name)
                 ->setCapacity($capacity)
                 ->setLocation($location)
                 ->setOrganization($organization);
            
            $manager->persist($room);
            $rooms[$name] = $room;
        }

        return $rooms;
    }

    private function createUsers(ObjectManager $manager, Organization $organization): array
    {
        // Admin
        $admin = new Admin();
        $admin->setEmail('admin@musikeo.com')
              ->setFirstname('Jean')
              ->setLastname('Administrateur')
              ->setOrganization($organization)
              ->setPassword($this->passwordHasher->hashPassword($admin, 'password'));
        $manager->persist($admin);

        // Teachers
        $teachers = [];
        
        $teachersData = [
            ['prof.martin@musikeo.com', 'Pierre', 'Martin', '0123456789', 'Professeur de piano depuis 15 ans, diplômé du Conservatoire de Paris.'],
            ['prof.dubois@musikeo.com', 'Sophie', 'Dubois', '0123456788', 'Professeure de guitare et chant, spécialisée en musique moderne.'],
            ['prof.bernard@musikeo.com', 'Michel', 'Bernard', '0123456787', 'Professeur de violon et alto, ancien premier violon de l\'Orchestre de Lyon.'],
            ['prof.rousseau@musikeo.com', 'Claire', 'Rousseau', '0123456786', 'Professeure de flûte et clarinette, diplômée du Conservatoire de Marseille.'],
            ['prof.moreau@musikeo.com', 'Antoine', 'Moreau', '0123456785', 'Professeur de batterie et percussions, musicien de studio professionnel.'],
            ['prof.garcia@musikeo.com', 'Elena', 'Garcia', '0123456784', 'Professeure de chant lyrique et moderne, soprano professionnelle.'],
        ];

        foreach ($teachersData as [$email, $firstname, $lastname, $phone, $bio]) {
            $teacher = new Teacher();
            $teacher->setEmail($email)
                    ->setFirstname($firstname)
                    ->setLastname($lastname)
                    ->setPhone($phone)
                    ->setBiography($bio)
                    ->setOrganization($organization)
                    ->setPassword($this->passwordHasher->hashPassword($teacher, 'password'));
            
            $manager->persist($teacher);
            $teachers[] = $teacher;
        }

        // Students
        $students = [];
        
        $studentsData = [
            ['marie@musikeo.com', 'Marie', 'Dupont', '2005-03-15', '123 rue de la Musique, 75001 Paris', '0123456787'],
            ['paul@musikeo.com', 'Paul', 'Durand', '2008-07-22', '456 avenue des Arts, 75002 Paris', '0123456786'],
            ['sophie@musikeo.com', 'Sophie', 'Bernard', '2010-11-08', '789 boulevard Musical, 75003 Paris', '0123456785'],
            ['lucas@musikeo.com', 'Lucas', 'Moreau', '2006-09-14', '321 rue Beethoven, 75004 Paris', '0123456784'],
            ['emma@musikeo.com', 'Emma', 'Garcia', '2009-01-30', '654 avenue Mozart, 75005 Paris', '0123456783'],
            ['thomas@musikeo.com', 'Thomas', 'Rousseau', '2007-12-05', '987 rue Chopin, 75006 Paris', '0123456782'],
            ['lea@musikeo.com', 'Léa', 'Petit', '2011-06-18', '147 boulevard Bach, 75007 Paris', '0123456781'],
            ['hugo@musikeo.com', 'Hugo', 'Roux', '2005-04-27', '258 rue Vivaldi, 75008 Paris', '0123456780'],
            ['chloe@musikeo.com', 'Chloé', 'Blanc', '2010-08-13', '369 avenue Debussy, 75009 Paris', '0123456779'],
            ['maxime@musikeo.com', 'Maxime', 'Noir', '2008-02-19', '741 rue Ravel, 75010 Paris', '0123456778'],
        ];

        foreach ($studentsData as [$email, $firstname, $lastname, $birthDate, $address, $phone]) {
            $student = new Student();
            $student->setEmail($email)
                    ->setFirstname($firstname)
                    ->setLastname($lastname)
                    ->setDateOfBirth(new \DateTime($birthDate))
                    ->setAddress($address)
                    ->setPhone($phone)
                    ->setOrganization($organization)
                    ->setPassword($this->passwordHasher->hashPassword($student, 'password'));
            
            $manager->persist($student);
            $students[] = $student;
        }

        return [
            'admin' => $admin,
            'teachers' => $teachers,
            'students' => $students
        ];
    }

    private function createCourses(ObjectManager $manager, array $teachers, array $instruments, Organization $organization): array
    {
        $coursesData = [
            ['Piano Débutant', 'Apprentissage des bases du piano pour débutants', 0],
            ['Piano Intermédiaire', 'Perfectionnement technique et répertoire varié', 0],
            ['Guitare Classique Débutant', 'Initiation à la guitare classique', 1],
            ['Guitare Électrique Rock', 'Apprentissage du rock à la guitare électrique', 1],
            ['Violon Débutant', 'Découverte du violon et technique de base', 2],
            ['Violon Avancé', 'Perfectionnement et répertoire complexe', 2],
            ['Flûte Traversière', 'Technique de souffle et doigtés', 3],
            ['Batterie Rock/Pop', 'Rythmes modernes et coordination', 4],
            ['Chant Lyrique', 'Technique vocale classique', 5],
            ['Chant Moderne', 'Interprétation de variété et jazz', 5],
            ['Éveil Musical 3-6 ans', 'Découverte ludique de la musique pour petits', 1],
            ['Solfège Débutant', 'Lecture de notes et théorie musicale', 2],
            ['Ensemble de Cordes', 'Pratique collective pour instruments à cordes', 2],
            ['Atelier Jazz', 'Improvisation et standards jazz', 5],
            ['Formation Musicale', 'Dictées, rythmes et analyse', 3],
        ];

        $courses = [];
        foreach ($coursesData as [$name, $description, $teacherIndex]) {
            $course = new Course();
            $course->setName($name)
                   ->setDescription($description)
                   ->setTeacher($teachers[$teacherIndex])
                   ->setOrganization($organization);
            
            $manager->persist($course);
            $courses[] = $course;
        }

        return $courses;
    }

    private function createEnrollments(ObjectManager $manager, array $students, array $courses, Organization $organization): void
    {
        $statuses = [Enrollment::STATUS_PENDING, Enrollment::STATUS_VALIDATED, Enrollment::STATUS_CANCELLED];
        
        // Create random enrollments
        for ($i = 0; $i < 25; $i++) {
            $enrollment = new Enrollment();
            $enrollment->setStudent($students[array_rand($students)])
                      ->setCourse($courses[array_rand($courses)])
                      ->setDateEnrolled(new \DateTime('-' . rand(1, 90) . ' days'))
                      ->setStatus($statuses[array_rand($statuses)])
                      ->setOrganization($organization);
            
            $manager->persist($enrollment);
        }
    }

    private function createLessons(ObjectManager $manager, array $courses, array $rooms, Organization $organization): void
    {
        $roomsArray = array_values($rooms);
        
        // Create lessons for the next 4 weeks
        foreach ($courses as $courseIndex => $course) {
            for ($week = 0; $week < 4; $week++) {
                for ($day = 1; $day <= 5; $day++) { // Monday to Friday
                    if (rand(0, 100) < 70) { // 70% chance of having a lesson
                        $lesson = new Lesson();
                        
                        $startDate = new \DateTime("next monday +{$week} weeks +{$day} days");
                        $startDate->setTime(9 + rand(0, 8), [0, 30][rand(0, 1)]); // 9:00-17:30
                        
                        $endDate = clone $startDate;
                        $endDate->add(new \DateInterval('PT1H')); // +1 hour
                        
                        $lesson->setCourse($course)
                              ->setStartDatetime($startDate)
                              ->setEndDatetime($endDate)
                              ->setRoom($roomsArray[array_rand($roomsArray)])
                              ->setOrganization($organization);
                        
                        $manager->persist($lesson);
                    }
                }
            }
        }
    }

    private function createPayments(ObjectManager $manager, array $students, Organization $organization): void
    {
        $methods = [Payment::METHOD_CARD, Payment::METHOD_CHECK, Payment::METHOD_TRANSFER, Payment::METHOD_CASH];
        $amounts = ['50.00', '75.00', '100.00', '120.00', '150.00', '200.00'];
        $descriptions = [
            'Cours mensuel', 'Inscription annuelle', 'Stage intensif', 
            'Cours particulier', 'Matériel pédagogique', 'Examen final'
        ];
        
        // Create payments for the last 6 months
        for ($i = 0; $i < 40; $i++) {
            $payment = new Payment();
            $payment->setStudent($students[array_rand($students)])
                    ->setAmount($amounts[array_rand($amounts)])
                    ->setDate(new \DateTime('-' . rand(1, 180) . ' days'))
                    ->setMethod($methods[array_rand($methods)])
                    ->setDescription($descriptions[array_rand($descriptions)])
                    ->setOrganization($organization);
            
            $manager->persist($payment);
        }
    }

    private function createPreRegistrations(ObjectManager $manager, Organization $organization): void
    {
        $preRegistrationsData = [
            [
                'Alice', 'Martin', 'alice.martin@email.com', '0123456790', '2010-05-15',
                '15 rue des Fleurs, 75001 Paris', 
                'Marie Martin', 'marie.martin@email.com', '0123456791',
                'piano', 'beginner', 'Ma fille aimerait apprendre le piano. Elle écoute beaucoup de musique classique.',
                PreRegistration::STATUS_PENDING, -2
            ],
            [
                'Thomas', 'Dubois', 'thomas.dubois@email.com', '0123456792', '2008-03-22',
                '42 avenue Mozart, 75002 Paris',
                'Jean Dubois', 'jean.dubois@email.com', '0123456793',
                'guitar', 'beginner', 'Thomas voudrait apprendre la guitare pour jouer du rock.',
                PreRegistration::STATUS_CONTACTED, -5
            ],
            [
                'Emma', 'Rousseau', 'emma.rousseau@email.com', '0123456794', '2009-11-08',
                '8 boulevard Saint-Germain, 75003 Paris',
                'Claire Rousseau', 'claire.rousseau@email.com', '0123456795', 
                'violin', 'beginner', 'Emma souhaite découvrir le violon après avoir vu un concert.',
                PreRegistration::STATUS_PENDING, -1
            ],
            [
                'Maxime', 'Lefebvre', 'maxime.lefebvre@email.com', '0123456796', '1995-07-12',
                '23 rue de Rivoli, 75004 Paris',
                null, null, null,
                'drums', 'intermediate', 'J\'ai déjà joué dans un groupe amateur, je souhaite me perfectionner.',
                PreRegistration::STATUS_ENROLLED, -10
            ],
            [
                'Sofia', 'Garcia', 'sofia.garcia@email.com', '0123456797', '2011-01-30',
                '67 rue de la Paix, 75005 Paris',
                'Carlos Garcia', 'carlos.garcia@email.com', '0123456798',
                'voice', 'beginner', 'Sofia adore chanter et voudrait prendre des cours de chant.',
                PreRegistration::STATUS_CONTACTED, -7
            ],
            [
                'Lucas', 'Bernard', 'lucas.bernard@email.com', '0123456799', '2007-09-14',
                '134 avenue des Champs, 75006 Paris',
                'Michel Bernard', 'michel.bernard@email.com', '0123456800',
                'flute', 'beginner', 'Lucas joue déjà un peu de flûte à bec et voudrait passer à la flûte traversière.',
                PreRegistration::STATUS_PENDING, -3
            ],
        ];

        foreach ($preRegistrationsData as [$firstname, $lastname, $email, $phone, $birthDate, $address, $parentName, $parentEmail, $parentPhone, $instrument, $level, $message, $status, $daysAgo]) {
            $preRegistration = new PreRegistration();
            $preRegistration->setFirstname($firstname)
                          ->setLastname($lastname)
                          ->setEmail($email)
                          ->setPhone($phone)
                          ->setDateOfBirth(new \DateTime($birthDate))
                          ->setAddress($address)
                          ->setParentName($parentName)
                          ->setParentEmail($parentEmail)
                          ->setParentPhone($parentPhone)
                          ->setInterestedInstrument($instrument)
                          ->setLevel($level)
                          ->setMessage($message)
                          ->setStatus($status)
                          ->setOrganization($organization)
                          ->setCreatedAt(new \DateTime("{$daysAgo} days"));

            if ($status === PreRegistration::STATUS_CONTACTED) {
                $preRegistration->setContactedAt(new \DateTime("{$daysAgo} days +1 day"));
            }

            $manager->persist($preRegistration);
        }
    }
}
