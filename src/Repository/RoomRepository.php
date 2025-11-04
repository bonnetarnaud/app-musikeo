<?php

namespace App\Repository;

use App\Entity\Organization;
use App\Entity\Room;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Room>
 */
class RoomRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Room::class);
    }

    /**
     * Recherche des salles par organisation avec filtres
     */
    public function findByOrganizationWithSearch(Organization $organization, string $search = '', string $capacity = '', string $availability = ''): array
    {
        $qb = $this->createQueryBuilder('r')
            ->leftJoin('r.lessons', 'l')
            ->where('r.organization = :organization')
            ->setParameter('organization', $organization);

        // Recherche par nom ou localisation
        if (!empty($search)) {
            $qb->andWhere('r.name LIKE :search OR r.location LIKE :search')
               ->setParameter('search', '%' . $search . '%');
        }

        // Filtre par capacité
        if (!empty($capacity)) {
            switch ($capacity) {
                case 'small':
                    $qb->andWhere('r.capacity <= 5');
                    break;
                case 'medium':
                    $qb->andWhere('r.capacity BETWEEN 6 AND 15');
                    break;
                case 'large':
                    $qb->andWhere('r.capacity >= 16');
                    break;
                case 'exact':
                    // Pour une recherche par capacité exacte (à implémenter si nécessaire)
                    break;
            }
        }

        // Filtre par disponibilité
        if (!empty($availability)) {
            switch ($availability) {
                case 'available':
                    // Salles sans leçons ou avec peu de leçons
                    $qb->groupBy('r.id')
                       ->having('COUNT(l.id) < 5');
                    break;
                case 'busy':
                    // Salles avec beaucoup de leçons
                    $qb->groupBy('r.id')
                       ->having('COUNT(l.id) >= 5');
                    break;
                case 'empty':
                    // Salles sans aucune leçon
                    $qb->andWhere('l.id IS NULL');
                    break;
            }
        }

        return $qb->orderBy('r.name', 'ASC')
                  ->getQuery()
                  ->getResult();
    }

    /**
     * Statistiques des salles par organisation
     */
    public function getRoomStatistics(Organization $organization): array
    {
        $qb = $this->createQueryBuilder('r')
            ->select('
                COUNT(r.id) as totalRooms,
                AVG(r.capacity) as averageCapacity,
                MAX(r.capacity) as maxCapacity,
                MIN(r.capacity) as minCapacity
            ')
            ->where('r.organization = :organization')
            ->setParameter('organization', $organization);

        return $qb->getQuery()->getSingleResult();
    }

    /**
     * Salles les plus utilisées
     */
    public function getMostUsedRooms(Organization $organization, int $limit = 5): array
    {
        return $this->createQueryBuilder('r')
            ->select('r, COUNT(l.id) as lessonCount')
            ->leftJoin('r.lessons', 'l')
            ->where('r.organization = :organization')
            ->setParameter('organization', $organization)
            ->groupBy('r.id')
            ->orderBy('lessonCount', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    /**
     * Salles disponibles par capacité minimale
     */
    public function findAvailableRoomsByCapacity(Organization $organization, int $minCapacity): array
    {
        return $this->createQueryBuilder('r')
            ->where('r.organization = :organization')
            ->andWhere('r.capacity >= :minCapacity')
            ->setParameter('organization', $organization)
            ->setParameter('minCapacity', $minCapacity)
            ->orderBy('r.capacity', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Vérifier la disponibilité d'une salle à un moment donné
     */
    public function isRoomAvailable(Room $room, \DateTime $startTime, \DateTime $endTime, ?int $excludeLessonId = null): bool
    {
        $qb = $this->createQueryBuilder('r')
            ->select('COUNT(l.id)')
            ->leftJoin('r.lessons', 'l')
            ->where('r.id = :roomId')
            ->andWhere('l.startDatetime < :endTime')
            ->andWhere('l.endDatetime > :startTime')
            ->setParameter('roomId', $room->getId())
            ->setParameter('startTime', $startTime)
            ->setParameter('endTime', $endTime);

        // Exclure une leçon spécifique (utile pour les modifications)
        if ($excludeLessonId) {
            $qb->andWhere('l.id != :excludeLessonId')
               ->setParameter('excludeLessonId', $excludeLessonId);
        }

        $conflictCount = $qb->getQuery()->getSingleScalarResult();

        return $conflictCount == 0;
    }

    /**
     * Obtenir les créneaux occupés d'une salle pour une date donnée
     */
    public function getRoomScheduleForDate(Room $room, \DateTime $date): array
    {
        return $this->createQueryBuilder('r')
            ->select('l')
            ->leftJoin('r.lessons', 'l')
            ->where('r.id = :roomId')
            ->andWhere('DATE(l.startDatetime) = :date')
            ->setParameter('roomId', $room->getId())
            ->setParameter('date', $date->format('Y-m-d'))
            ->orderBy('l.startDatetime', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
