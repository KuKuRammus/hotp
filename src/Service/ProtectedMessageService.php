<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Form\ProtectedMessageContent;
use App\Entity\ProtectedMessage;
use App\Entity\RandomName;
use App\Entity\SecretString;
use App\Repository\ProtectedMessageRepository;
use Doctrine\ORM\EntityManagerInterface;

class ProtectedMessageService
{
    private ProtectedMessageRepository $protectedMessageRepository;
    private EntityManagerInterface $em;

    public function __construct(
        EntityManagerInterface      $em
    )
    {
        $this->protectedMessageRepository = $em->getRepository(ProtectedMessage::class);
        $this->em = $em;
    }

    public function create(ProtectedMessageContent $content): ProtectedMessage
    {
        $protectedMessage = new ProtectedMessage(new RandomName(), new SecretString(), $content);
        $this->protectedMessageRepository->save($protectedMessage);
        $this->em->flush();

        return $protectedMessage;
    }
}
