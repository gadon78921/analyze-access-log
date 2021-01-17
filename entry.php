<?php

declare(strict_types=1);

use AnalyzeAccessLogFarpost\Analyzer;
use AnalyzeAccessLogFarpost\Parser;
use AnalyzeAccessLogFarpost\Domain\MeasureInterval;

require __DIR__ . '/vendor/autoload.php';

$options              = getopt('u:t:s::v') ?: [];
$minAvailabilityLevel = (float) $options['u'];
$maxResponseTime      = (int) $options['t'];
validateInput($options);

$parser   = new Parser();
$analyzer = new Analyzer($parser, $minAvailabilityLevel, $maxResponseTime, 60);

$generator = $analyzer->analyze();

foreach ($generator as $rejectedInterval) {
    /** @var MeasureInterval $rejectedInterval */
    $start        = $rejectedInterval->getStart()->format('H:i:s');
    $end          = $rejectedInterval->getLastLogLineDateTime()->format('H:i:s');
    $availability = $rejectedInterval->getAvailabilityLevel();

    echo  $start . ' ' . $end . ' ' . $availability . PHP_EOL;
}

function validateInput(array $options): void
{
    if (((float) $options['u']) <= 0.0) {
        throw new Exception('Wrong option for minimum availability level. Need float above 0.0. Pass: ' . $options['u']);
    }

    if ((float) $options['t'] <= 0) {
        throw new Exception('Wrong option for maximum response time. Need int above 0. Pass: ' . $options['t']);
    }
}
