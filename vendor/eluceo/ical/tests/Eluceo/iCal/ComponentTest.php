<?php

namespace Eluceo\iCal;

class ComponentTest extends \PHPUnit_Framework_TestCase
{
    public function testFoldWithMultibyte()
    {
        $input = "x" . str_repeat("あいうえお", 5);

        $vCalendar = new \Eluceo\iCal\Component\Calendar('www.example.com');
        $vEvent    = new \Eluceo\iCal\Component\Event();
        $vEvent->setDtStart(new \DateTime('2014-12-24'));
        $vEvent->setDtEnd(new \DateTime('2014-12-24'));
        $vEvent->setDescription($input);

        $vAlarm = new \Eluceo\iCal\Component\Alarm;
        $vAlarm->setAction(\Eluceo\iCal\Component\Alarm::ACTION_DISPLAY);
        $vAlarm->setDescription($input);
        $vAlarm->setTrigger('PT0S', true);
        $vEvent->addComponent($vAlarm);

        $vCalendar->addComponent($vEvent);

        $output = $vCalendar->render();
        $output = preg_replace('/\r\n /u', '', $output);
        $this->assertContains($input, $output);
    }

    public function testDescriptionWithNewLines()
    {
        $input = "new string \n new line \n new line \n new string";

        $vCalendar = new \Eluceo\iCal\Component\Calendar('www.example.com');
        $vEvent    = new \Eluceo\iCal\Component\Event();
        $vEvent->setDtStart(new \DateTime('2014-12-24'));
        $vEvent->setDtEnd(new \DateTime('2014-12-24'));
        $vEvent->setDescription($input);

        $vCalendar->addComponent($vEvent);

        $output = $vCalendar->render();
        $this->assertContains(str_replace("\n", "\\n", $input), $output);
    }
}
