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

    public function __construct(EntityManagerInterface $em)
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

    public function getOneByNameOrNull(string $rawName): ?ProtectedMessage
    {
        // Ensure name contains 2 parts
        $parts = explode('.', $rawName);
        if (count($parts) !== 2) {
            return null;
        }

        // Parse name parts
        $name = (string) $parts[0];
        $id = (int) $parts[1];
        if (mb_strlen($name) === 0 || $id <= 0) {
            return null;
        }

        /** @var ProtectedMessage|null $message */
        $message = $this->protectedMessageRepository->findOneBy(
            [ 'id' => $id, 'name' => $name ]
        );

        return $message;
    }
}
