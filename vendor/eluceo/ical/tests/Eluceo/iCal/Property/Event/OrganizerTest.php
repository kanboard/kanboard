<?php
/**
 * Eluceo\iCal\Property\Event\OrganizerTest
 *
 * @author    Giulio Troccoli <giulio@troccoli.it>
 */

namespace Eluceo\iCal\Property\Event;

/**
 * OrganizerTest
 */
class OrganizerTest extends \PHPUnit_Framework_TestCase
{
    public function testOrganizerValueOnly()
    {
        $value    = "MAILTO:name.lastname@example.com";
        $expected = "ORGANIZER:$value";

        $vCalendar = $this->createCalendarWithOrganizer(
            new \Eluceo\iCal\Property\Event\Organizer($value)
        );

        foreach (explode("\n", $vCalendar->render()) as $line)
        {
            if (preg_match('/^ORGANIZER[:;](.*)$/', $line)) {
                $this->assertEquals($expected, trim($line));
            }
        }
    }

    public function testOrganizerValueAndParameter()
    {
        $value    = "MAILTO:name.lastname@example.com";
        $param    = "Name LastName";
        $expected = "ORGANIZER;CN=$param:$value";

        $vCalendar = $this->createCalendarWithOrganizer(
            new \Eluceo\iCal\Property\Event\Organizer($value, array('CN' => $param))
        );

        foreach (explode("\n", $vCalendar->render()) as $line)
        {
            if (preg_match('/^ORGANIZER[:;](.*)$/', $line)) {
                $this->assertEquals($expected, trim($line));
            }
        }

    }

    /**
     * @param Organizer $vOrganizer
     * @return \Eluceo\iCal\Component\Calendar
     */
    private function createCalendarWithOrganizer(\Eluceo\iCal\Property\Event\Organizer $vOrganizer)
    {
        $vCalendar = new \Eluceo\iCal\Component\Calendar('www.example.com');
        $vEvent = new \Eluceo\iCal\Component\Event('123456');
        $vEvent->setOrganizer($vOrganizer);
        $vCalendar->addComponent($vEvent);
        return $vCalendar;
    }
}
