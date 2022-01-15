<?php

namespace App\Repository;

use App\Entity\Pointages;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Pointages|null find($id, $lockMode = null, $lockVersion = null)
 * @method Pointages|null findOneBy(array $criteria, array $orderBy = null)
 * @method Pointages[]    findAll()
 * @method Pointages[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PointagesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Pointages::class);
    }

    public function findUtilisateurByChantier()
    {
        $qb = $this->createQueryBuilder('p');
        $chantiers = $qb
            ->select('c.id', $qb->expr()->countDistinct('p.utilisateur'))
            ->join('p.chantier', 'c')
            ->groupBy('c.id')
            ->getQuery()
            ->getResult()
        ;
        foreach ($chantiers as $chantier) {
            $data[$chantier['id']] = $chantier[1];
        }

        return $data ?? null;
    }

    public function findDureeByChantier()
    {
        $sql = "SELECT c.id, 
                       SEC_TO_TIME( SUM( TIME_TO_SEC( duree ) ) ) As duree_pointage
                FROM pointages as p
                LEFT JOIN chantiers as c
                    ON c.id = p.chantier_id
                GROUP BY c.id";

        $chantiers = $this->_em->getConnection()
                            ->executeQuery($sql)
                            ->fetchAll(\PDO::FETCH_ASSOC);

        foreach ($chantiers as $chantier) {
            $data[$chantier['id']] = $chantier['duree_pointage'];
        }

        return $data ?? null;
    }
}

    