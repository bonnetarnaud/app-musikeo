<?php

namespace App\Repository;

use App\Entity\Student;
use App\Entity\Organization;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Student>
 */
class StudentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Student::class);
    }

    /**
     * Find students by organization with optional search and status filter
     */
    public function findByOrganizationWithSearch(Organization $organization, string $search = '', string $status = ''): array
    {
        $qb = $this->createQueryBuilder('s')
            ->leftJoin('s.enrollments', 'e')
            ->leftJoin('s.instrumentRentals', 'ir')
            ->leftJoin('s.payments', 'p')
            ->where('s.organization = :organization')
            ->setParameter('organization', $organization);

        if (!empty($search)) {
            $qb->andWhere('s.firstname LIKE :search OR s.lastname LIKE :search OR s.email LIKE :search OR s.phone LIKE :search')
               ->setParameter('search', '%' . $search . '%');
        }

        if (!empty($status)) {
            switch ($status) {
                case 'active_rentals':
                    $qb->andWhere('ir.status = :rental_status')
                       ->setParameter('rental_status', 'active');
                    break;
                case 'active_enrollments':
                    $qb->andWhere('e.status = :enrollment_status')
                       ->setParameter('enrollment_status', 'valide');
                    break;
                case 'recent_payments':
                    $qb->andWhere('p.date >= :recent_date')
                       ->setParameter('recent_date', new \DateTime('-30 days'));
                    break;
            }
        }

        return $qb->orderBy('s.lastname', 'ASC')
                  ->addOrderBy('s.firstname', 'ASC')
                  ->getQuery()
                  ->getResult();
    }

    /**
     * Find students with enrollment statistics
     */
    public function findWithEnrollmentStats(Organization $organization): array
    {
        return $this->createQueryBuilder('s')
            ->select('s', 'COUNT(e.id) as enrollmentCount')
            ->leftJoin('s.enrollments', 'e')
            ->where('s.organization = :organization')
            ->setParameter('organization', $organization)
            ->groupBy('s.id')
            ->orderBy('s.lastname', 'ASC')
            ->addOrderBy('s.firstname', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Find students with active rentals
     */
    public function findWithActiveRentals(Organization $organization): array
    {
        return $this->createQueryBuilder('s')
            ->innerJoin('s.instrumentRentals', 'ir')
            ->where('s.organization = :organization')
            ->andWhere('ir.status = :status')
            ->setParameter('organization', $organization)
            ->setParameter('status', 'active')
            ->orderBy('s.lastname', 'ASC')
            ->addOrderBy('s.firstname', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Find students by age range
     */
    public function findByAgeRange(Organization $organization, int $minAge = null, int $maxAge = null): array
    {
        $qb = $this->createQueryBuilder('s')
            ->where('s.organization = :organization')
            ->setParameter('organization', $organization);

        if ($minAge !== null) {
            $maxBirthDate = new \DateTime();
            $maxBirthDate->modify("-{$minAge} years");
            $qb->andWhere('s.dateOfBirth <= :maxBirthDate')
               ->setParameter('maxBirthDate', $maxBirthDate);
        }

        if ($maxAge !== null) {
            $minBirthDate = new \DateTime();
            $minBirthDate->modify("-{$maxAge} years");
            $qb->andWhere('s.dateOfBirth >= :minBirthDate')
               ->setParameter('minBirthDate', $minBirthDate);
        }

        return $qb->orderBy('s.dateOfBirth', 'DESC')
                  ->getQuery()
                  ->getResult();
    }

    //    /**
    //     * @return Student[] Returns an array of Student objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('s')
    //            ->andWhere('s.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('s.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Student
    //    {
    //        return $this->createQueryBuilder('s')
    //            ->andWhere('s.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
