<?php

namespace App\Repository;

use App\Entity\Site;
use App\Entity\UserSite;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<UserSite>
 *
 * @method UserSite|null find($id, $lockMode = null, $lockVersion = null)
 * @method UserSite|null findOneBy(array $criteria, array $orderBy = null)
 * @method UserSite[]    findAll()
 * @method UserSite[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserSiteRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserSite::class);
    }

    public function save(UserSite $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(UserSite $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function getUserSiteBySite(Site $site): array
    {
        return $this->createQueryBuilder('us')
            ->orderBy('us.id', 'DESC')
            ->andWhere('us.site = :site')->setParameter('site', $site)
            ->getQuery()
            ->getResult();
    }
}
