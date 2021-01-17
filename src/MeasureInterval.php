<?php

declare(strict_types=1);

namespace AnalyzeAccessLogFarpost;

use DateTimeImmutable;

class MeasureInterval
{
    private int $totalCountLine       = 0;
    private int $rejectedCount        = 0;
    private ?float $availabilityLevel = null;

    private ?DateTimeImmutable $start               = null;
    private ?DateTimeImmutable $end                 = null;
    private ?DateTimeImmutable $lastLogLineDateTime = null;


    public function getTotalCountLine(): int
    {
        return $this->totalCountLine;
    }

    public function setTotalCountLine(int $totalCountLine): void
    {
        $this->totalCountLine = $totalCountLine;
    }

    public function getRejectedCount(): int
    {
        return $this->rejectedCount;
    }

    public function setRejectedCount(int $rejectedCount): void
    {
        $this->rejectedCount = $rejectedCount;
    }

    public function getStart(): ?DateTimeImmutable
    {
        return $this->start;
    }

    public function setStart(?DateTimeImmutable $start): void
    {
        $this->start = $start;
    }

    public function getEnd(): ?DateTimeImmutable
    {
        return $this->end;
    }

    public function setEnd(?DateTimeImmutable $end): void
    {
        $this->end = $end;
    }

    public function getLastLogLineDateTime(): ?DateTimeImmutable
    {
        return $this->lastLogLineDateTime;
    }

    public function setLastLogLineDateTime(?DateTimeImmutable $lastLogLineDateTime): void
    {
        $this->lastLogLineDateTime = $lastLogLineDateTime;
    }

    public function getAvailabilityLevel(): ?float
    {
        return $this->availabilityLevel;
    }

    public function setAvailabilityLevel(?float $availabilityLevel): void
    {
        $this->availabilityLevel = $availabilityLevel;
    }

    public function incrementTotalCountLineInInterval(): void
    {
        $this->totalCountLine++;
    }

    public function incrementRejectedCountInMeasureInterval(): void
    {
        $this->rejectedCount++;
    }

    public function setBounds(DateTimeImmutable $startOfMeasureInterval, DateTimeImmutable $endOfMeasureInterval): void
    {
        $this->start = $startOfMeasureInterval;
        $this->end   = $endOfMeasureInterval;
    }

    public function canCalculateAvailabilityLevel(LogLine $logLine): bool
    {
        return null !== $this->start && null !== $this->end && $logLine->dateTime()->getTimestamp() > $this->end->getTimestamp();
    }

    public function calculateAndSetAvailabilityLevel(): void
    {
        $this->availabilityLevel = $this->calculateAvailability();
    }

    private function calculateAvailability(): float
    {
        if ($this->totalCountLine === 0) {
            return 100;
        }

        return round(100 - ((100 * $this->rejectedCount) / $this->totalCountLine), 2);
    }
}
