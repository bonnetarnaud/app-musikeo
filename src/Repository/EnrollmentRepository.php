<?php

namespace App\Repository;

use App\Entity\Enrollment;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Enrollment>
 */
class EnrollmentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Enrollment::class);
    }

    /**
     * Count unique students for a teacher
     */
    public function countUniqueStudentsByTeacher($teacher): int
    {
        return $this->createQueryBuilder('e')
            ->select('COUNT(DISTINCT e.student)')
            ->join('e.course', 'c')
            ->where('c.teacher = :teacher')
            ->andWhere('e.status = :status')
            ->setParameter('teacher', $teacher)
            ->setParameter('status', Enrollment::STATUS_VALIDATED)
            ->getQuery()
            ->getSingleScalarResult();
    }

    //    /**
    //     * @return Enrollment[] Returns an array of Enrollment objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('e')
    //            ->andWhere('e.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('e.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Enrollment
    //    {
    //        return $this->createQueryBuilder('e')
    //            ->andWhere('e.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
