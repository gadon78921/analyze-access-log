<?php

declare(strict_types=1);

use AnalyzeAccessLogFarpost\Analyzer;
use AnalyzeAccessLogFarpost\Parser;
use AnalyzeAccessLogFarpost\MeasureInterval;

require __DIR__ . '/vendor/autoload.php';

$options              = getopt('u:t:i:') ?: [];
$minAvailabilityLevel = (float) $options['u'];
$maxResponseTime      = (int) $options['t'];
$measureInterval      = (int) ($options['i'] ?? 60);
validateInput($options);

$parser   = new Parser();
$analyzer = new Analyzer($parser, $minAvailabilityLevel, $maxResponseTime, $measureInterval);

$generator = $analyzer->analyze();

$result = [];
foreach ($generator as $rejectedInterval) {
    /** @var MeasureInterval $rejectedInterval */
    $result[$rejectedInterval->getStart()->format('H:i:s')] = $rejectedInterval;
}

ksort($result);
foreach ($result as $rejectedInterval) {
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

    if ((int) $options['t'] <= 0) {
        throw new Exception('Wrong option for maximum response time. Need int above 0. Pass: ' . $options['t']);
    }

    if (isset($options['i']) && ((int) $options['i']) <= 0) {
        throw new Exception('Wrong option for measure interval. Need int above 0. Pass: ' . $options['i']);
    }
}
