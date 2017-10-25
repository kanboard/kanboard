<?php

namespace Eluceo\iCal\Component;

class CalendarIntegrationTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @coversNothing
     */
    public function testExample3()
    {
        $timeZone = new \DateTimeZone('Europe/Berlin');

        // 1. Create new calendar
        $vCalendar = new \Eluceo\iCal\Component\Calendar('www.example.com');

        // 2. Create an event
        $vEvent = new \Eluceo\iCal\Component\Event('123456');
        $vEvent->setDtStart(new \DateTime('2012-12-31', $timeZone));
        $vEvent->setDtEnd(new \DateTime('2012-12-31', $timeZone));
        $vEvent->setNoTime(true);
        $vEvent->setIsPrivate(true);
        $vEvent->setSummary('New Year’s Eve');

        // Set recurrence rule
        $recurrenceRule = new \Eluceo\iCal\Property\Event\RecurrenceRule();
        $recurrenceRule->setFreq(\Eluceo\iCal\Property\Event\RecurrenceRule::FREQ_YEARLY);
        $recurrenceRule->setInterval(1);
        $vEvent->setRecurrenceRule($recurrenceRule);

        // Adding Timezone (optional)
        $vEvent->setUseTimezone(true);

        // 3. Add event to calendar
        $vCalendar->addComponent($vEvent);

        $lines = array(
            '/BEGIN:VCALENDAR/',
            '/VERSION:2\.0/',
            '/PRODID:www\.example\.com/',
            '/X-PUBLISHED-TTL:P1W/',
            '/BEGIN:VEVENT/',
            '/UID:123456/',
            '/DTSTART;TZID=Europe\/Berlin;VALUE=DATE:20121231/',
            '/SEQUENCE:0/',
            '/TRANSP:OPAQUE/',
            '/DTEND;TZID=Europe\/Berlin;VALUE=DATE:20121231/',
            '/SUMMARY:New Year’s Eve/',
            '/CLASS:PRIVATE/',
            '/RRULE:FREQ=YEARLY;INTERVAL=1/',
            '/X-MICROSOFT-CDO-ALLDAYEVENT:TRUE/',
            '/DTSTAMP:20\d{6}T\d{6}Z/',
            '/END:VEVENT/',
            '/END:VCALENDAR/',
        );

        foreach (explode("\n", $vCalendar->render()) as $key => $line)
        {
            $this->assertTrue(isset($lines[$key]), 'Too many lines... ' . $line);

            $this->assertRegExp($lines[$key], $line);
        }
    }
}
