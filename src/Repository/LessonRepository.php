<?php

namespace App\Repository;

use App\Entity\Lesson;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Lesson>
 */
class LessonRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Lesson::class);
    }

    /**
     * Find upcoming lessons for a teacher
     */
    public function findUpcomingByTeacher($teacher, int $limit = 5): array
    {
        return $this->createQueryBuilder('l')
            ->join('l.course', 'c')
            ->where('c.teacher = :teacher')
            ->andWhere('l.startDatetime > :now')
            ->setParameter('teacher', $teacher)
            ->setParameter('now', new \DateTime())
            ->orderBy('l.startDatetime', 'ASC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    /**
     * Find recent lessons for a teacher
     */
    public function findRecentByTeacher($teacher, int $limit = 5): array
    {
        return $this->createQueryBuilder('l')
            ->join('l.course', 'c')
            ->where('c.teacher = :teacher')
            ->andWhere('l.endDatetime < :now')
            ->setParameter('teacher', $teacher)
            ->setParameter('now', new \DateTime())
            ->orderBy('l.endDatetime', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    /**
     * Find lessons for this week for a teacher
     */
    public function findThisWeekByTeacher($teacher): array
    {
        $now = new \DateTime();
        $startOfWeek = (clone $now)->modify('monday this week')->setTime(0, 0, 0);
        $endOfWeek = (clone $now)->modify('sunday this week')->setTime(23, 59, 59);

        return $this->createQueryBuilder('l')
            ->join('l.course', 'c')
            ->where('c.teacher = :teacher')
            ->andWhere('l.startDatetime BETWEEN :start AND :end')
            ->setParameter('teacher', $teacher)
            ->setParameter('start', $startOfWeek)
            ->setParameter('end', $endOfWeek)
            ->orderBy('l.startDatetime', 'ASC')
            ->getQuery()
            ->getResult();
    }

    //    /**
    //     * @return Lesson[] Returns an array of Lesson objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('l')
    //            ->andWhere('l.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('l.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Lesson
    //    {
    //        return $this->createQueryBuilder('l')
    //            ->andWhere('l.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
