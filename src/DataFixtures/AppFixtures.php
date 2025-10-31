<?php

namespace App\DataFixtures;

use App\Entity\Admin;
use App\Entity\Student;
use App\Entity\Teacher;
use App\Entity\Instrument;
use App\Entity\Room;
use App\Entity\Course;
use App\Entity\Lesson;
use App\Entity\Enrollment;
use App\Entity\Payment;
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

        // Create instruments
        $instruments = $this->createInstruments($manager);

        // Create rooms
        $rooms = $this->createRooms($manager);

        // Create users
        $users = $this->createUsers($manager);

        // Associate instruments to users
        $this->associateInstruments($users, $instruments);

        // Create courses
        $courses = $this->createCourses($manager, $users['teachers'], $instruments);

        // Create enrollments
        $this->createEnrollments($manager, $users['students'], $courses);

        // Create lessons
        $this->createLessons($manager, $courses, $rooms);

        // Create payments
        $this->createPayments($manager, $users['students']);

        $manager->flush();
    }

    private function clearData(ObjectManager $manager): void
    {
        // Clean existing data
        $connection = $manager->getConnection();
        
        $connection->executeStatement('SET FOREIGN_KEY_CHECKS = 0');
        $connection->executeStatement('DELETE FROM payment');
        $connection->executeStatement('DELETE FROM lesson');
        $connection->executeStatement('DELETE FROM enrollment');
        $connection->executeStatement('DELETE FROM course');
        $connection->executeStatement('DELETE FROM student_instrument');
        $connection->executeStatement('DELETE FROM teacher_instrument');
        $connection->executeStatement('DELETE FROM student');
        $connection->executeStatement('DELETE FROM teacher');
        $connection->executeStatement('DELETE FROM admin');
        $connection->executeStatement('DELETE FROM room');
        $connection->executeStatement('DELETE FROM instrument');
        $connection->executeStatement('SET FOREIGN_KEY_CHECKS = 1');
    }

    private function createInstruments(ObjectManager $manager): array
    {
        $instrumentsData = [
            ['Piano', 'clavier', 'Instrument à clavier avec cordes frappées'],
            ['Guitare', 'cordes', 'Instrument à cordes pincées'],
            ['Guitare électrique', 'cordes', 'Guitare amplifiée électroniquement'],
            ['Basse', 'cordes', 'Instrument à cordes graves'],
            ['Violon', 'cordes', 'Instrument à cordes frottées'],
            ['Alto', 'cordes', 'Instrument à cordes, plus grave que le violon'],
            ['Violoncelle', 'cordes', 'Instrument à cordes de la famille des violons'],
            ['Flûte traversière', 'vents', 'Instrument à vent en bois'],
            ['Clarinette', 'vents', 'Instrument à vent en bois à anche'],
            ['Saxophone', 'vents', 'Instrument à vent en cuivre'],
            ['Trompette', 'vents', 'Instrument à vent en cuivre'],
            ['Trombone', 'vents', 'Instrument à vent en cuivre à coulisse'],
            ['Batterie', 'percussions', 'Ensemble d\'instruments de percussion'],
            ['Djembé', 'percussions', 'Tambour d\'origine africaine'],
            ['Chant', 'vocal', 'Technique vocale et interprétation'],
            ['Ukulélé', 'cordes', 'Petite guitare hawaïenne'],
        ];

        $instruments = [];
        foreach ($instrumentsData as [$name, $type, $description]) {
            $instrument = new Instrument();
            $instrument->setName($name)
                      ->setType($type)
                      ->setDescription($description);
            
            $manager->persist($instrument);
            $instruments[$name] = $instrument;
        }

        return $instruments;
    }

    private function createRooms(ObjectManager $manager): array
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
                 ->setLocation($location);
            
            $manager->persist($room);
            $rooms[$name] = $room;
        }

        return $rooms;
    }

    private function createUsers(ObjectManager $manager): array
    {
        // Admin
        $admin = new Admin();
        $admin->setEmail('admin@musikeo.com')
              ->setFirstname('Jean')
              ->setLastname('Administrateur')
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

    private function associateInstruments(array $users, array $instruments): void
    {
        // Teachers - instruments taught
        $users['teachers'][0]->addInstrumentsTaught($instruments['Piano']);
        $users['teachers'][0]->addInstrumentsTaught($instruments['Chant']);
        
        $users['teachers'][1]->addInstrumentsTaught($instruments['Guitare']);
        $users['teachers'][1]->addInstrumentsTaught($instruments['Guitare électrique']);
        $users['teachers'][1]->addInstrumentsTaught($instruments['Ukulélé']);
        
        $users['teachers'][2]->addInstrumentsTaught($instruments['Violon']);
        $users['teachers'][2]->addInstrumentsTaught($instruments['Alto']);
        
        $users['teachers'][3]->addInstrumentsTaught($instruments['Flûte traversière']);
        $users['teachers'][3]->addInstrumentsTaught($instruments['Clarinette']);
        
        $users['teachers'][4]->addInstrumentsTaught($instruments['Batterie']);
        $users['teachers'][4]->addInstrumentsTaught($instruments['Djembé']);
        
        $users['teachers'][5]->addInstrumentsTaught($instruments['Chant']);

        // Students - instruments learned
        $instrumentsList = array_values($instruments);
        foreach ($users['students'] as $index => $student) {
            // Each student learns 1-3 instruments
            $numInstruments = rand(1, 3);
            $studentInstruments = array_rand($instrumentsList, $numInstruments);
            
            if (is_array($studentInstruments)) {
                foreach ($studentInstruments as $instrumentIndex) {
                    $student->addInstrument($instrumentsList[$instrumentIndex]);
                }
            } else {
                $student->addInstrument($instrumentsList[$studentInstruments]);
            }
        }
    }

    private function createCourses(ObjectManager $manager, array $teachers, array $instruments): array
    {
        $coursesData = [
            ['Piano Débutant', 'Piano', 0],
            ['Piano Intermédiaire', 'Piano', 0],
            ['Guitare Débutant', 'Guitare', 1],
            ['Guitare Électrique', 'Guitare électrique', 1],
            ['Violon Débutant', 'Violon', 2],
            ['Violon Avancé', 'Violon', 2],
            ['Flûte Débutant', 'Flûte traversière', 3],
            ['Batterie Rock', 'Batterie', 4],
            ['Chant Lyrique', 'Chant', 5],
            ['Chant Moderne', 'Chant', 5],
            ['Ukulélé Débutant', 'Ukulélé', 1],
            ['Ensemble de Cordes', 'Violon', 2],
        ];

        $courses = [];
        foreach ($coursesData as [$name, $instrumentName, $teacherIndex]) {
            $course = new Course();
            $course->setName($name)
                   ->setInstrument($instruments[$instrumentName])
                   ->setTeacher($teachers[$teacherIndex]);
            
            $manager->persist($course);
            $courses[] = $course;
        }

        return $courses;
    }

    private function createEnrollments(ObjectManager $manager, array $students, array $courses): void
    {
        $statuses = [Enrollment::STATUS_PENDING, Enrollment::STATUS_VALIDATED, Enrollment::STATUS_CANCELLED];
        
        // Create random enrollments
        for ($i = 0; $i < 25; $i++) {
            $enrollment = new Enrollment();
            $enrollment->setStudent($students[array_rand($students)])
                      ->setCourse($courses[array_rand($courses)])
                      ->setDateEnrolled(new \DateTime('-' . rand(1, 90) . ' days'))
                      ->setStatus($statuses[array_rand($statuses)]);
            
            $manager->persist($enrollment);
        }
    }

    private function createLessons(ObjectManager $manager, array $courses, array $rooms): void
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
                              ->setRoom($roomsArray[array_rand($roomsArray)]);
                        
                        $manager->persist($lesson);
                    }
                }
            }
        }
    }

    private function createPayments(ObjectManager $manager, array $students): void
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
                    ->setDescription($descriptions[array_rand($descriptions)]);
            
            $manager->persist($payment);
        }
    }
}
