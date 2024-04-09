<?php

namespace Tests\Twig;

use Humbrain\Framework\extensions\TimeExtension;
use PHPUnit\Framework\TestCase;

class TimeExtensionTest extends TestCase
{

    /**
     * @var TimeExtension
     */
    private $timeExtension;

    public function setUp(): void
    {
        $this->timeExtension = new TimeExtension();
    }

    public function testDateFormat()
    {
        $date = new \DateTime();
        $format = 'd/m/Y H:i';
        $result = "<time class='timeago' datetime='"
            . $date->format(\DateTime::ATOM) .
            "'>" . $date->format($format) . '</time>';
        $this->assertEquals($result, $this->timeExtension->ago($date));
    }

}