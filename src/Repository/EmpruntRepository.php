<?php

namespace App\Repository;

use App\Entity\Emprunt;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Emprunt>
 */
class EmpruntRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Emprunt::class);
    }

    /**
     * Retourne la liste des emprunts qui ne sont pas encore rendus
     */
    public function findEmpruntsEnCours(): array
    {
        return $this->createQueryBuilder('e')
            ->join('e.book', 'b')
            ->join('e.user', 'u')
            ->addSelect('b', 'u')
            ->andWhere('e.statut = :statut')
            ->setParameter('statut', 'en_cours')
            ->orderBy('e.dateEmprunt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function countByAuteur(): array
    {
        return $this->createQueryBuilder('e')
            ->select('b.auter, COUNT(e.id) as total')
            ->join('e.book', 'b')
            ->groupBy('b.auter')
            ->getQuery()
            ->getResult();
    }

    public function findByUser(int $userId): array
    {
        return $this->createQueryBuilder('e')
            ->join('e.user', 'u')
            ->join('e.book', 'b')
            ->addSelect('u', 'b')
            ->andWhere('u.id = :userId')
            ->setParameter('userId', $userId)
            ->orderBy('e.dateEmprunt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function findTop5DernierMois(): array
    {
        $debut = new \DateTimeImmutable('-30 days');

        return $this->createQueryBuilder('e')
            ->select('b.id, b.titre, b.image, COUNT(e.id) as total')
            ->join('e.book', 'b')
            ->andWhere('e.dateEmprunt >= :debut')
            ->setParameter('debut', $debut)
            ->groupBy('b.id')
            ->orderBy('total', 'DESC')
            ->setMaxResults(5)
            ->getQuery()
            ->getResult();
    }
}
