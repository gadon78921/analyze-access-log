<?php

declare(strict_types=1);

namespace AnalyzeAccessLogFarpost;

use DateTimeImmutable;
use Exception;

class Parser implements ParserInterface
{
    private const PATTERN = '/.+\s-\s-\s\[(?<dateTime>\d{2}\/\d{2}\/\d{4}:\d{2}:\d{2}:\d{2}\s\+\d{4})]\s".+"\s(?<status>\d+)\s.+\s(?<processedTime>\d+\.\d+)\s".*"\s".*"\s.*/';

    public function parseLine(string $line): LogLine
    {
        if (empty($line)) {
            throw new Exception('Empty string');
        }

        if ((bool) preg_match(self::PATTERN, $line, $matches) === false) {
            throw new Exception('Unknown log format for line: ' . $line);
        }

        $dateTime = DateTimeImmutable::createFromFormat('d/m/Y:H:i:s O', $matches['dateTime']);

        return new LogLine((int) $matches['status'], (float) $matches['processedTime'], $dateTime);
    }
}
