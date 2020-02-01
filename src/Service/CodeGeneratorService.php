<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\GeneratorTimeProvider;
use App\Entity\SecretString;

class CodeGeneratorService
{
    public const HMAC_ALG = 'sha1';
    public const GENERATED_CODE_LENGTH = 5;

    public function generateUsingSecret(
        SecretString            $secret,
        GeneratorTimeProvider   $now,
        int                     $length = self::GENERATED_CODE_LENGTH
    ): string
    {
        $hash = hash_hmac(self::HMAC_ALG, (string) $now->getCurrentTimeFrame(), $secret->getSecret());
        return mb_strtolower(mb_substr($hash, 0, $length));
    }
}
