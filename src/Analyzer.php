<?php

declare(strict_types=1);

namespace AnalyzeAccessLogFarpost;

use DateInterval;
use DateTimeImmutable;
use Throwable;

class Analyzer
{
    private const HTTP_INTERNAL_SERVER_ERROR = 500;

    private ParserInterface $parser;
    private float           $minAvailabilityLevel;
    private float           $maxResponseTime;
    private int             $measureInterval;

    public function __construct(ParserInterface $parser, float $minAvailabilityLevel, float $maxResponseTime, int $measureInterval)
    {
        $this->parser               = $parser;
        $this->minAvailabilityLevel = $minAvailabilityLevel;
        $this->maxResponseTime      = $maxResponseTime;
        $this->measureInterval      = $measureInterval;
    }

    public function analyze(): iterable
    {
        $interval = new MeasureInterval();

        while (false !== ($line = fgets(STDIN))) {
            try {
                $currentLogLine = $this->parser->parseLine($line);
            } catch (Throwable $exception) {
                fwrite(STDERR, $exception->getMessage() . "\r\n");
                continue;
            }

            if ($this->isRejectLine($currentLogLine)) {
                $start = $interval->getStart();
                null === $start && $interval->setBounds($currentLogLine->dateTime(), $this->getEndOfMeasureInterval($currentLogLine));
                null === $start && $interval->setTotalCountLine(0);
                $interval->incrementRejectedCountInMeasureInterval();
            }

            $interval->incrementTotalCountLineInInterval();
            $interval->setLastLogLineDateTime($currentLogLine->dateTime());
            $interval->canCalculateAvailabilityLevel($currentLogLine) && $interval->calculateAndSetAvailabilityLevel();

            $availability = $interval->getAvailabilityLevel();
            if (null !== $availability && $availability < $this->minAvailabilityLevel) {
                yield $interval;

                $interval = new MeasureInterval();
            }
        }
    }

    private function isRejectLine(LogLine $logLine): bool
    {
        return $logLine->status() >= self::HTTP_INTERNAL_SERVER_ERROR || $logLine->processedTime() > $this->maxResponseTime;
    }

    private function getEndOfMeasureInterval(LogLine $logLine): DateTimeImmutable
    {
        return $logLine->dateTime()->add(DateInterval::createFromDateString($this->measureInterval . ' seconds'));
    }
}
