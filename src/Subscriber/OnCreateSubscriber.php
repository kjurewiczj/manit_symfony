<?php

namespace App\Subscriber;

use App\Interfaces\Entity\CreatedAtInterface;
use App\Interfaces\Entity\UserCreatedInterface;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Event\PreFlushEventArgs;
use Doctrine\ORM\Events;
use Doctrine\ORM\UnitOfWork;
use Symfony\Bundle\SecurityBundle\Security;

class OnCreateSubscriber implements EventSubscriber
{
    private EntityManager $em;
    private UnitOfWork $uow;

    public function __construct(
        private readonly Security $security,
    ){}

    public function getSubscribedEvents(): array
    {
        return [
            Events::preFlush,
        ];
    }

    public function preFlush(PreFlushEventArgs $args)
    {
        $this->em = $args->getObjectManager();
        $this->uow = $this->em->getUnitOfWork();
        foreach ($this->uow->getScheduledEntityInsertions() as $entity) {
            $this->onCreated($entity);
        }
    }

    public function onCreated($entity)
    {
        if ($entity instanceof CreatedAtInterface) {
            $entity->setCreatedAt(new \DateTimeImmutable());
        }

        if ($entity instanceof UserCreatedInterface) {
            $entity->setUserCreated($this->security->getUser());
        }
    }
}
