<?php

namespace AnalyzeAccessLogFarpost\Test;

use AnalyzeAccessLogFarpost\Parser;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;
use Exception;

class ParserTest extends TestCase
{
    public function testParse()
    {
        $line    = '192.168.32.181 - - [14/06/2017:16:47:02 +1000] "PUT /rest/v1.4/documents?zone=default&_rid=6076537c HTTP/1.1" 200 2 44.510983 "-" "@list-item-updater" prio:0';
        $parser  = new Parser();
        $logLine = $parser->parseLine($line);

        $expectedDateTime = DateTimeImmutable::createFromFormat('d/m/Y:H:i:s O', '14/06/2017:16:47:02 +1000');

        $this->assertEquals(200, $logLine->status());
        $this->assertEquals(44.510983, $logLine->processedTime());
        $this->assertEquals($expectedDateTime, $logLine->dateTime());
    }

    public function testExceptionEmptyLine()
    {
        $line    = '';
        $parser  = new Parser();

        $this->expectException(Exception::class);
        $parser->parseLine($line);
    }

    public function testExceptionUnknownLogFormat()
    {
        $line    = '192.168.32.181 - - [14/06/2017:16:47:02 +1000] "PUT /rest/v1.4/documents?zone=default&_rid=';
        $parser  = new Parser();

        $this->expectException(Exception::class);
        $parser->parseLine($line);
    }
}
