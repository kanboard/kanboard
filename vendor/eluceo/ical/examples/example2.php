<?php

// use composer autoloader
require_once __DIR__ . '/../vendor/autoload.php';

// set default timezone (PHP 5.4)
date_default_timezone_set('Europe/Berlin');

// 1. Create new calendar
$vCalendar = new \Eluceo\iCal\Component\Calendar('www.example.com');

// 2. Create an event
$vEvent = new \Eluceo\iCal\Component\Event();
$vEvent->setDtStart(new \DateTime('2012-12-24'));
$vEvent->setDtEnd(new \DateTime('2012-12-24'));
$vEvent->setNoTime(true);
$vEvent->setSummary('Summary with some german "umlauten" and a backslash \\: Kinder mögen Äpfel pflücken.');
$vEvent->setCategories(['holidays']);

// Adding Timezone (optional)
$vEvent->setUseTimezone(true);

// 3. Add event to calendar
$vCalendar->addComponent($vEvent);

// 4. Set headers
header('Content-Type: text/calendar; charset=utf-8');
header('Content-Disposition: attachment; filename="cal.ics"');

// 5. Output
echo $vCalendar->render();
