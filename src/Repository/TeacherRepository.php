<?php

namespace App\Repository;

use App\Entity\Teacher;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Teacher>
 */
class TeacherRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Teacher::class);
    }

    /**
     * Find teachers by organization with optional search and status filter
     */
    public function findByOrganizationWithSearch($organization, string $search = '', string $status = ''): array
    {
        $qb = $this->createQueryBuilder('t')
            ->andWhere('t.organization = :organization')
            ->setParameter('organization', $organization)
            ->orderBy('t.lastname', 'ASC')
            ->addOrderBy('t.firstname', 'ASC');

        if (!empty($search)) {
            $qb->andWhere(
                $qb->expr()->orX(
                    $qb->expr()->like('LOWER(t.firstname)', ':search'),
                    $qb->expr()->like('LOWER(t.lastname)', ':search'),
                    $qb->expr()->like('LOWER(t.email)', ':search')
                )
            )
            ->setParameter('search', '%' . strtolower($search) . '%');
        }

        // For status filtering, we can check roles since we don't have a dedicated status field
        if ($status === 'active') {
            $qb->andWhere('t.roles LIKE :role')
               ->setParameter('role', '%"ROLE_TEACHER"%');
        } elseif ($status === 'inactive') {
            $qb->andWhere('t.roles NOT LIKE :role OR t.roles IS NULL')
               ->setParameter('role', '%"ROLE_TEACHER"%');
        }

        return $qb->getQuery()->getResult();
    }

    //    /**
    //     * @return Teacher[] Returns an array of Teacher objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('t')
    //            ->andWhere('t.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('t.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Teacher
    //    {
    //        return $this->createQueryBuilder('t')
    //            ->andWhere('t.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
