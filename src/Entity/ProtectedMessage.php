<?php

declare(strict_types=1);

namespace App\Entity;

use App\Entity\Form\ProtectedMessageContent;
use Doctrine\ORM\Mapping as ORM;
use DateTimeInterface;

/**
 * Class ProtectedMessage
 * @package App\Entity
 *
 * @ORM\Entity(
 *     readOnly=true,
 *     repositoryClass="App\Repository\ProtectedMessageRepository"
 * )
 */
class ProtectedMessage
{
    /**
     * @var int|null Message ID
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @ORM\Column(name="id", type="integer")
     */
    private ?int $id = null;

    /**
     * @var string Generated name
     * @ORM\Column(type="string", length=255, nullable=false, name="name")
     */
    private string $name;

    /**
     * @var string Random string (used as a secret)
     * @ORM\Column(type="string", length=255, nullable=false, name="secret")
     */
    private string $secret;

    /**
     * @var string Message content
     * @ORM\Column(type="string", length=255, nullable=false, name="content")
     */
    private string $content;

    public function __construct(
        RandomName                  $name,
        SecretString                $secret,
        ProtectedMessageContent     $content
    )
    {
        $this->name = $name->getName();
        $this->secret = $secret->getSecret();
        $this->content = $content->getContent();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): RandomName
    {
        return new RandomName($this->name);
    }

    public function getSecret(): SecretString
    {
        return new SecretString(SecretString::DEFAULT_LENGTH, $this->secret);
    }

    public function getContent(): ProtectedMessageContent
    {
        return new ProtectedMessageContent($this->content);
    }
}
