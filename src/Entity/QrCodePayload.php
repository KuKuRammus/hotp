<?php

declare(strict_types=1);

namespace App\Entity;

use JsonSerializable;

final class QrCodePayload implements JsonSerializable
{
    private string $name;
    private string $secret;

    public function getName(): string
    {
        return $this->name;
    }

    public function getSecret(): string
    {
        return $this->secret;
    }

    public static function createFromProtectedMessage(ProtectedMessage $protectedMessage): self
    {
        return new self(
            sprintf("%s.%s", $protectedMessage->getName(), $protectedMessage->getId()),
            (string) $protectedMessage->getSecret()
        );
    }

    public static function createFromJson(string $json): self
    {
        $parsed = json_decode($json, true);
        return new self((string)$parsed['name'], (string)$parsed['secret']);
    }

    private function __construct(string $name, string $secret) {
        $this->name = $name;
        $this->secret = $secret;
    }

    public function jsonSerialize()
    {
        return [
            'name' => $this->name,
            'secret' => $this->secret
        ];
    }
}
