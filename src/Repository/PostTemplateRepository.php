<?php

namespace App\Repository;

use App\Entity\PostTemplate;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<PostTemplate>
 *
 * @method PostTemplate|null find($id, $lockMode = null, $lockVersion = null)
 * @method PostTemplate|null findOneBy(array $criteria, array $orderBy = null)
 * @method PostTemplate[]    findAll()
 * @method PostTemplate[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PostTemplateRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PostTemplate::class);
    }

    public function save(PostTemplate $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(PostTemplate $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}
