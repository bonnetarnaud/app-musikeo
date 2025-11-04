<?php

namespace App\Repository;

use App\Entity\Course;
use App\Entity\Organization;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Course>
 */
class CourseRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Course::class);
    }

    /**
     * Find courses by organization with optional search and teacher filter
     */
    public function findByOrganizationWithSearch(Organization $organization, string $search = '', string $teacher = ''): array
    {
        $qb = $this->createQueryBuilder('c')
            ->leftJoin('c.teacher', 't')
            ->leftJoin('c.enrollments', 'e')
            ->where('c.organization = :organization')
            ->setParameter('organization', $organization);

        if (!empty($search)) {
            $qb->andWhere('c.name LIKE :search OR c.description LIKE :search OR t.firstname LIKE :search OR t.lastname LIKE :search')
               ->setParameter('search', '%' . $search . '%');
        }

        if (!empty($teacher)) {
            $qb->andWhere('t.id = :teacher')
               ->setParameter('teacher', $teacher);
        }

        return $qb->orderBy('c.name', 'ASC')
                  ->getQuery()
                  ->getResult();
    }

    /**
     * Find courses with enrollment statistics
     */
    public function findWithStats(Organization $organization): array
    {
        return $this->createQueryBuilder('c')
            ->select('c', 'COUNT(e.id) as enrollmentCount')
            ->leftJoin('c.enrollments', 'e')
            ->where('c.organization = :organization')
            ->setParameter('organization', $organization)
            ->groupBy('c.id')
            ->orderBy('c.name', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Find most popular courses (by enrollment count)
     */
    public function findMostPopular(Organization $organization, int $limit = 5): array
    {
        return $this->createQueryBuilder('c')
            ->select('c', 'COUNT(e.id) as enrollmentCount')
            ->leftJoin('c.enrollments', 'e')
            ->where('c.organization = :organization')
            ->setParameter('organization', $organization)
            ->groupBy('c.id')
            ->orderBy('enrollmentCount', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    //    /**
    //     * @return Course[] Returns an array of Course objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('c')
    //            ->andWhere('c.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('c.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Course
    //    {
    //        return $this->createQueryBuilder('c')
    //            ->andWhere('c.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
