<?php

// use composer autoloader
require_once __DIR__ . '/../vendor/autoload.php';

// set default timezone (PHP 5.4)
date_default_timezone_set('Europe/Berlin');

// 1. Create new calendar
$vCalendar = new \Eluceo\iCal\Component\Calendar('www.example.com');

// 2. Create an event
$vEvent = new \Eluceo\iCal\Component\Event();
$vEvent->setDtStart(new \DateTime('2012-11-11 13:00:00'));
$vEvent->setDtEnd(new \DateTime('2012-11-11 14:30:00'));
$vEvent->setSummary('Weekly lunch with Markus');

// Set recurrence rule
$recurrenceRule = new \Eluceo\iCal\Property\Event\RecurrenceRule();
$recurrenceRule->setFreq(\Eluceo\iCal\Property\Event\RecurrenceRule::FREQ_WEEKLY);
$recurrenceRule->setInterval(1);
$vEvent->setRecurrenceRule($recurrenceRule);

// Adding Timezone (optional)
$vEvent->setUseTimezone(true);

// 3. Add event to calendar
$vCalendar->addComponent($vEvent);

// 4. Set headers
header('Content-Type: text/calendar; charset=utf-8');
header('Content-Disposition: attachment; filename="cal.ics"');

// 5. Output
echo $vCalendar->render();
