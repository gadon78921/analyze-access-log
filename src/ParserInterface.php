<?php

declare(strict_types=1);

namespace AnalyzeAccessLogFarpost;

interface ParserInterface
{
    public function parseLine(string $line): LogLine;
}
