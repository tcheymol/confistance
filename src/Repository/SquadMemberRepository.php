<?php

namespace App\Repository;

use App\Entity\SquadMember;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method SquadMember|null find($id, $lockMode = null, $lockVersion = null)
 * @method SquadMember|null findOneBy(array $criteria, array $orderBy = null)
 * @method SquadMember[]    findAll()
 * @method SquadMember[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SquadMemberRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SquadMember::class);
    }

    // /**
    //  * @return SquadMember[] Returns an array of SquadMember objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('s.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?SquadMember
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
