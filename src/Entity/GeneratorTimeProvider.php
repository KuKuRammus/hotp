<?php

declare(strict_types=1);

namespace App\Entity;

final class GeneratorTimeProvider
{
    public const TIME_FRAME_DURATION = 30;

    private int $timestamp = 0;
    private int $currentTimeFrame = 0;
    private int $timeTillNextFrame = 0;

    public function __construct(int $timestamp, int $timeFrameDuration = self::TIME_FRAME_DURATION)
    {
        $this->timestamp = $timestamp;
        $this->currentTimeFrame = (int) ($timestamp / $timeFrameDuration);
        $this->timeTillNextFrame = (int) (
            $timeFrameDuration - ($timestamp - ($this->currentTimeFrame * $timeFrameDuration))
        );
    }

    public function getTimestamp(): int
    {
        return $this->timestamp;
    }

    public function getCurrentTimeFrame(): int
    {
        return $this->currentTimeFrame;
    }

    public function getTimeTillNextFrame(): int
    {
        return $this->timeTillNextFrame;
    }

}
