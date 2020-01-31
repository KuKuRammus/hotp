<?php

declare(strict_types=1);

namespace App\Entity\Form;

use Symfony\Component\Validator\Constraints as Assert;

class CodeMessage
{
    /**
     * @Assert\NotBlank()
     * @Assert\Length(min="5", max="255")
     */
    protected string $message;

    public function __construct(string $message = "")
    {
        $this->message = $message;
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function setMessage(string $message): void
    {
        $this->message = $message;
    }
}
