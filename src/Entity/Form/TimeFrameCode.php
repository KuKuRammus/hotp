<?php

declare(strict_types=1);

namespace App\Entity\Form;

use Symfony\Component\Validator\Constraints as Assert;

class TimeFrameCode
{
    /**
     * @Assert\NotBlank()
     * @Assert\Length(min=5, max=5)
     */
    protected string $code;

    public function __construct(string $code = "")
    {
        $this->code = $code;
    }

    public function getCode(): string
    {
        return mb_strtolower($this->code);
    }

    public function setCode(string $code): void
    {
        $this->code = $code;
    }

}
