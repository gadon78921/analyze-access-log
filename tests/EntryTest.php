<?php

namespace AnalyzeAccessLogFarpost\Test;

use PHPUnit\Framework\TestCase;

class EntryTest extends TestCase
{
    public function testOneDay()
    {
        $out = shell_exec("cat tests/_data/access-log-data-one-day | php entry.php -u 90 -t 45");
        $expected = '16:47:22 16:48:25 85.71' . PHP_EOL;
        $this->assertEquals($expected, $out);
    }

    public function testTwoDay()
    {
        $out = shell_exec("cat tests/_data/access-log-data-two-day | php entry.php -u 90 -t 45");
        $expected = '16:47:13 16:48:25 57.14' . PHP_EOL . '16:47:22 16:48:25 85.71' . PHP_EOL;
        $this->assertEquals($expected, $out);
    }
}