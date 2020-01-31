<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\ProtectedMessage;
use Doctrine\ORM\EntityRepository;

class ProtectedMessageRepository extends EntityRepository
{
    public function save(ProtectedMessage $protectedMessage): void
    {
        $this->getEntityManager()->persist($protectedMessage);
    }
}
