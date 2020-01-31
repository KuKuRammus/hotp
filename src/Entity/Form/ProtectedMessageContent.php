<?php

declare(strict_types=1);

namespace App\Entity\Form;

use Symfony\Component\Validator\Constraints as Assert;

class ProtectedMessageContent
{
    /**
     * @Assert\NotBlank()
     * @Assert\Length(min="5", max="255")
     */
    protected string $content;

    public function __construct(string $message = "")
    {
        $this->content = $message;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function setContent(string $content): void
    {
        $this->content = $content;
    }
}
