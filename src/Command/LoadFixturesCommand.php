<?php

namespace App\Command;

use App\Entity\Admin;
use App\Entity\Student;
use App\Entity\Teacher;
use App\Entity\Instrument;
use App\Entity\Room;
use App\Entity\Course;
use App\Entity\Lesson;
use App\Entity\Enrollment;
use App\Entity\Payment;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AsCommand(
    name: 'app:load-fixtures',
    description: 'Load test data for Musikeo',
)]
class LoadFixturesCommand extends Command
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private UserPasswordHasherInterface $passwordHasher
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $io->title('Chargement des données de test Musikeo');

        // Nettoyer les données existantes
        $this->clearData($io);

        // Créer les instruments
        $instruments = $this->createInstruments($io);

        // Créer les salles
        $rooms = $this->createRooms($io);

        // Créer les utilisateurs
        $users = $this->createUsers($io);

        // Associer instruments aux utilisateurs
        $this->associateInstruments($users, $instruments, $io);

        // Créer les cours
        $courses = $this->createCourses($users['teachers'], $instruments, $io);

        // Créer les inscriptions
        $enrollments = $this->createEnrollments($users['students'], $courses, $io);

        // Créer les leçons
        $this->createLessons($courses, $rooms, $io);

        // Créer les paiements
        $this->createPayments($users['students'], $io);

        $this->entityManager->flush();

        $io->success('Données de test créées avec succès !');
        $io->section('Comptes de test créés :');
        $io->table(['Type', 'Email', 'Mot de passe'], [
            ['Admin', 'admin@musikeo.com', 'password'],
            ['Professeur', 'prof.martin@musikeo.com', 'password'],
            ['Professeur', 'prof.dubois@musikeo.com', 'password'],
            ['Étudiant', 'marie@musikeo.com', 'password'],
            ['Étudiant', 'paul@musikeo.com', 'password'],
            ['Étudiant', 'sophie@musikeo.com', 'password'],
        ]);

        return Command::SUCCESS;
    }

    private function clearData(SymfonyStyle $io): void
    {
        $io->section('Nettoyage des données existantes...');
        
        // Supprimer dans l'ordre des dépendances
        $this->entityManager->createQuery('DELETE FROM App\Entity\Payment')->execute();
        $this->entityManager->createQuery('DELETE FROM App\Entity\Lesson')->execute();
        $this->entityManager->createQuery('DELETE FROM App\Entity\Enrollment')->execute();
        $this->entityManager->createQuery('DELETE FROM App\Entity\Course')->execute();
        $this->entityManager->createQuery('DELETE FROM App\Entity\Student')->execute();
        $this->entityManager->createQuery('DELETE FROM App\Entity\Teacher')->execute();
        $this->entityManager->createQuery('DELETE FROM App\Entity\Admin')->execute();
        $this->entityManager->createQuery('DELETE FROM App\Entity\Room')->execute();
        $this->entityManager->createQuery('DELETE FROM App\Entity\Instrument')->execute();
    }

    private function createInstruments(SymfonyStyle $io): array
    {
        $io->section('Création des instruments...');

        $instrumentsData = [
            ['Piano', 'clavier', 'Instrument à clavier avec cordes frappées'],
            ['Guitare', 'cordes', 'Instrument à cordes pincées'],
            ['Violon', 'cordes', 'Instrument à cordes frottées'],
            ['Flûte', 'vents', 'Instrument à vent en bois'],
            ['Batterie', 'percussions', 'Ensemble d\'instruments de percussion'],
            ['Saxophone', 'vents', 'Instrument à vent en cuivre'],
            ['Chant', 'vocal', 'Technique vocale et interprétation'],
        ];

        $instruments = [];
        foreach ($instrumentsData as [$name, $type, $description]) {
            $instrument = new Instrument();
            $instrument->setName($name)
                      ->setType($type)
                      ->setDescription($description);
            
            $this->entityManager->persist($instrument);
            $instruments[$name] = $instrument;
        }

        return $instruments;
    }

    private function createRooms(SymfonyStyle $io): array
    {
        $io->section('Création des salles...');

        $roomsData = [
            ['Salle Piano 1', 15, 'Rez-de-chaussée, à gauche'],
            ['Salle Piano 2', 15, 'Rez-de-chaussée, à droite'],
            ['Studio Batterie', 8, 'Sous-sol, insonorisé'],
            ['Salle Ensemble', 25, '1er étage, grande salle'],
            ['Salle Guitare', 12, '1er étage, côté jardin'],
            ['Studio Vocal', 10, '2ème étage'],
        ];

        $rooms = [];
        foreach ($roomsData as [$name, $capacity, $location]) {
            $room = new Room();
            $room->setName($name)
                 ->setCapacity($capacity)
                 ->setLocation($location);
            
            $this->entityManager->persist($room);
            $rooms[$name] = $room;
        }

        return $rooms;
    }

    private function createUsers(SymfonyStyle $io): array
    {
        $io->section('Création des utilisateurs...');

        // Admin
        $admin = new Admin();
        $admin->setEmail('admin@musikeo.com')
              ->setFirstname('Jean')
              ->setLastname('Administrateur')
              ->setPassword($this->passwordHasher->hashPassword($admin, 'password'));
        $this->entityManager->persist($admin);

        // Professeurs
        $prof1 = new Teacher();
        $prof1->setEmail('prof.martin@musikeo.com')
              ->setFirstname('Pierre')
              ->setLastname('Martin')
              ->setPhone('0123456789')
              ->setBiography('Professeur de piano depuis 15 ans, diplômé du Conservatoire de Paris.')
              ->setPassword($this->passwordHasher->hashPassword($prof1, 'password'));
        $this->entityManager->persist($prof1);

        $prof2 = new Teacher();
        $prof2->setEmail('prof.dubois@musikeo.com')
              ->setFirstname('Sophie')
              ->setLastname('Dubois')
              ->setPhone('0123456788')
              ->setBiography('Professeure de guitare et chant, spécialisée en musique moderne.')
              ->setPassword($this->passwordHasher->hashPassword($prof2, 'password'));
        $this->entityManager->persist($prof2);

        // Étudiants
        $student1 = new Student();
        $student1->setEmail('marie@musikeo.com')
                 ->setFirstname('Marie')
                 ->setLastname('Dupont')
                 ->setDateOfBirth(new \DateTime('2005-03-15'))
                 ->setAddress('123 rue de la Musique, 75001 Paris')
                 ->setPhone('0123456787')
                 ->setPassword($this->passwordHasher->hashPassword($student1, 'password'));
        $this->entityManager->persist($student1);

        $student2 = new Student();
        $student2->setEmail('paul@musikeo.com')
                 ->setFirstname('Paul')
                 ->setLastname('Durand')
                 ->setDateOfBirth(new \DateTime('2008-07-22'))
                 ->setAddress('456 avenue des Arts, 75002 Paris')
                 ->setPhone('0123456786')
                 ->setPassword($this->passwordHasher->hashPassword($student2, 'password'));
        $this->entityManager->persist($student2);

        $student3 = new Student();
        $student3->setEmail('sophie@musikeo.com')
                 ->setFirstname('Sophie')
                 ->setLastname('Bernard')
                 ->setDateOfBirth(new \DateTime('2010-11-08'))
                 ->setAddress('789 boulevard Musical, 75003 Paris')
                 ->setPhone('0123456785')
                 ->setPassword($this->passwordHasher->hashPassword($student3, 'password'));
        $this->entityManager->persist($student3);

        return [
            'admin' => $admin,
            'teachers' => [$prof1, $prof2],
            'students' => [$student1, $student2, $student3]
        ];
    }

    private function associateInstruments(array $users, array $instruments, SymfonyStyle $io): void
    {
        $io->section('Association des instruments...');

        // Professeurs
        $users['teachers'][0]->addInstrumentsTaught($instruments['Piano']);
        $users['teachers'][0]->addInstrumentsTaught($instruments['Chant']);

        $users['teachers'][1]->addInstrumentsTaught($instruments['Guitare']);
        $users['teachers'][1]->addInstrumentsTaught($instruments['Chant']);

        // Étudiants
        $users['students'][0]->addInstrument($instruments['Piano']);
        $users['students'][0]->addInstrument($instruments['Chant']);

        $users['students'][1]->addInstrument($instruments['Guitare']);

        $users['students'][2]->addInstrument($instruments['Piano']);
    }

    private function createCourses(array $teachers, array $instruments, SymfonyStyle $io): array
    {
        $io->section('Création des cours...');

        $courses = [];

        $course1 = new Course();
        $course1->setName('Piano Débutant')
                ->setInstrument($instruments['Piano'])
                ->setTeacher($teachers[0]);
        $this->entityManager->persist($course1);
        $courses[] = $course1;

        $course2 = new Course();
        $course2->setName('Guitare Moderne')
                ->setInstrument($instruments['Guitare'])
                ->setTeacher($teachers[1]);
        $this->entityManager->persist($course2);
        $courses[] = $course2;

        $course3 = new Course();
        $course3->setName('Technique Vocale')
                ->setInstrument($instruments['Chant'])
                ->setTeacher($teachers[1]);
        $this->entityManager->persist($course3);
        $courses[] = $course3;

        return $courses;
    }

    private function createEnrollments(array $students, array $courses, SymfonyStyle $io): array
    {
        $io->section('Création des inscriptions...');

        $enrollments = [];

        // Marie s'inscrit au piano et chant
        $enrollment1 = new Enrollment();
        $enrollment1->setStudent($students[0])
                   ->setCourse($courses[0]) // Piano
                   ->setStatus(Enrollment::STATUS_VALIDATED);
        $this->entityManager->persist($enrollment1);
        $enrollments[] = $enrollment1;

        $enrollment2 = new Enrollment();
        $enrollment2->setStudent($students[0])
                   ->setCourse($courses[2]) // Chant
                   ->setStatus(Enrollment::STATUS_VALIDATED);
        $this->entityManager->persist($enrollment2);
        $enrollments[] = $enrollment2;

        // Paul s'inscrit à la guitare
        $enrollment3 = new Enrollment();
        $enrollment3->setStudent($students[1])
                   ->setCourse($courses[1]) // Guitare
                   ->setStatus(Enrollment::STATUS_VALIDATED);
        $this->entityManager->persist($enrollment3);
        $enrollments[] = $enrollment3;

        // Sophie s'inscrit au piano (en attente)
        $enrollment4 = new Enrollment();
        $enrollment4->setStudent($students[2])
                   ->setCourse($courses[0]) // Piano
                   ->setStatus(Enrollment::STATUS_PENDING);
        $this->entityManager->persist($enrollment4);
        $enrollments[] = $enrollment4;

        return $enrollments;
    }

    private function createLessons(array $courses, array $rooms, SymfonyStyle $io): void
    {
        $io->section('Création des leçons...');

        $roomsArray = array_values($rooms);

        // Quelques leçons pour la semaine prochaine
        foreach ($courses as $index => $course) {
            for ($day = 1; $day <= 3; $day++) {
                $lesson = new Lesson();
                $startDate = new \DateTime("next monday +{$day} days");
                $startDate->setTime(10 + $index * 2, 0); // Heures différentes
                
                $endDate = clone $startDate;
                $endDate->add(new \DateInterval('PT1H')); // +1 heure

                $lesson->setCourse($course)
                      ->setStartDatetime($startDate)
                      ->setEndDatetime($endDate)
                      ->setRoom($roomsArray[$index % count($roomsArray)]);

                $this->entityManager->persist($lesson);
            }
        }
    }

    private function createPayments(array $students, SymfonyStyle $io): void
    {
        $io->section('Création des paiements...');

        // Quelques paiements d'exemple
        $payment1 = new Payment();
        $payment1->setStudent($students[0])
                 ->setAmount('120.00')
                 ->setMethod(Payment::METHOD_CARD)
                 ->setDescription('Cours de piano - Octobre 2024');
        $this->entityManager->persist($payment1);

        $payment2 = new Payment();
        $payment2->setStudent($students[1])
                 ->setAmount('100.00')
                 ->setMethod(Payment::METHOD_CHECK)
                 ->setDescription('Cours de guitare - Octobre 2024');
        $this->entityManager->persist($payment2);

        $payment3 = new Payment();
        $payment3->setStudent($students[0])
                 ->setAmount('80.00')
                 ->setMethod(Payment::METHOD_TRANSFER)
                 ->setDescription('Cours de chant - Octobre 2024');
        $this->entityManager->persist($payment3);
    }
}