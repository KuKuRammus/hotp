<?php

declare(strict_types=1);

namespace App\Entity;

use RuntimeException;

class SecretString
{
    public const DEFAULT_LENGTH = 127;

    private string $secret;

    public function __construct(int $length = self::DEFAULT_LENGTH, ?string $secret = null)
    {
        if ($length <= 0) {
            throw new RuntimeException('Secret string cannot be lower or equal to zero');
        }

        if ($secret === null) {
            $secret = '';
            while (($generatedLength = strlen($secret)) < $length) {
                $size = $length - $generatedLength;
                $bytes = random_bytes($size);
                $secret .= substr(str_replace(['/', '+', '='], '', base64_encode($bytes)), 0, $size);
            }
        }
        $this->secret = $secret;
    }

    public function getSecret(): string
    {
        return $this->secret;
    }

    public function __toString(): string
    {
        return $this->getSecret();
    }
}
