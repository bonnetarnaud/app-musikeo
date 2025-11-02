<?php

namespace App\Repository;

use App\Entity\PreRegistration;
use App\Entity\Organization;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<PreRegistration>
 */
class PreRegistrationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PreRegistration::class);
    }

    public function findByOrganization(Organization $organization): array
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.organization = :organization')
            ->setParameter('organization', $organization)
            ->orderBy('p.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function findByOrganizationAndStatus(Organization $organization, string $status): array
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.organization = :organization')
            ->andWhere('p.status = :status')
            ->setParameter('organization', $organization)
            ->setParameter('status', $status)
            ->orderBy('p.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function countByOrganization(Organization $organization): int
    {
        return $this->createQueryBuilder('p')
            ->select('COUNT(p.id)')
            ->andWhere('p.organization = :organization')
            ->setParameter('organization', $organization)
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function countByOrganizationAndStatus(Organization $organization, string $status): int
    {
        return $this->createQueryBuilder('p')
            ->select('COUNT(p.id)')
            ->andWhere('p.organization = :organization')
            ->andWhere('p.status = :status')
            ->setParameter('organization', $organization)
            ->setParameter('status', $status)
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function findRecentByOrganization(Organization $organization, int $limit = 10): array
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.organization = :organization')
            ->setParameter('organization', $organization)
            ->orderBy('p.createdAt', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    public function findByInstrument(Organization $organization, string $instrument): array
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.organization = :organization')
            ->andWhere('p.interestedInstrument = :instrument')
            ->setParameter('organization', $organization)
            ->setParameter('instrument', $instrument)
            ->orderBy('p.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }
}