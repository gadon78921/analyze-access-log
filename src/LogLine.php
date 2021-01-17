<?php

declare(strict_types=1);

namespace AnalyzeAccessLogFarpost;

use DateTimeImmutable;

class LogLine
{
    private int               $status;
    private float             $processedTime;
    private DateTimeImmutable $dateTime;

    public function __construct(int $status, float $processedTime, DateTimeImmutable $dateTime)
    {
        $this->status        = $status;
        $this->processedTime = $processedTime;
        $this->dateTime      = $dateTime;
    }

    public function status(): int
    {
        return $this->status;
    }

    public function processedTime(): float
    {
        return $this->processedTime;
    }

    public function dateTime(): DateTimeImmutable
    {
        return $this->dateTime;
    }
}
