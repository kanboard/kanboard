<?php

/**
 * example to show how to create an ICal calendar which
 * provides a full timezone definition
 */

// use composer autoloader
require_once __DIR__ . '/../vendor/autoload.php';

// set default timezone (PHP 5.4)
$tz  = 'Europe/Berlin';
$dtz = new \DateTimeZone($tz);
date_default_timezone_set($tz);

// 1. Create new calendar
$vCalendar = new \Eluceo\iCal\Component\Calendar('www.example.com');

// 2. Create timezone rule object for Daylight Saving Time
$vTimezoneRuleDst = new \Eluceo\iCal\Component\TimezoneRule(\Eluceo\iCal\Component\TimezoneRule::TYPE_DAYLIGHT);
$vTimezoneRuleDst->setTzName('CEST');
$vTimezoneRuleDst->setDtStart(new \DateTime('1981-03-29 02:00:00', $dtz));
$vTimezoneRuleDst->setTzOffsetFrom('+0100');
$vTimezoneRuleDst->setTzOffsetTo('+0200');
$dstRecurrenceRule = new \Eluceo\iCal\Property\Event\RecurrenceRule();
$dstRecurrenceRule->setFreq(\Eluceo\iCal\Property\Event\RecurrenceRule::FREQ_YEARLY);
$dstRecurrenceRule->setByMonth(3);
$dstRecurrenceRule->setByDay('-1SU');
$vTimezoneRuleDst->setRecurrenceRule($dstRecurrenceRule);

// 3. Create timezone rule object for Standard Time
$vTimezoneRuleStd = new \Eluceo\iCal\Component\TimezoneRule(\Eluceo\iCal\Component\TimezoneRule::TYPE_STANDARD);
$vTimezoneRuleStd->setTzName('CET');
$vTimezoneRuleStd->setDtStart(new \DateTime('1996-10-27 03:00:00', $dtz));
$vTimezoneRuleStd->setTzOffsetFrom('+0200');
$vTimezoneRuleStd->setTzOffsetTo('+0100');
$stdRecurrenceRule = new \Eluceo\iCal\Property\Event\RecurrenceRule();
$stdRecurrenceRule->setFreq(\Eluceo\iCal\Property\Event\RecurrenceRule::FREQ_YEARLY);
$stdRecurrenceRule->setByMonth(10);
$stdRecurrenceRule->setByDay('-1SU');
$vTimezoneRuleStd->setRecurrenceRule($stdRecurrenceRule);

// 4. Create timezone definition and add rules
$vTimezone = new \Eluceo\iCal\Component\Timezone($tz);
$vTimezone->addComponent($vTimezoneRuleDst);
$vTimezone->addComponent($vTimezoneRuleStd);
$vCalendar->setTimezone($vTimezone);

// 5. Create an event
$vEvent = new \Eluceo\iCal\Component\Event();
$vEvent->setDtStart(new \DateTime('2012-12-24', $dtz));
$vEvent->setDtEnd(new \DateTime('2012-12-24', $dtz));
$vEvent->setSummary('Summary with some german "umlauten" and a backslash \\: Kinder mögen Äpfel pflücken.');

// 6. Adding Timezone
$vEvent->setUseTimezone(true);

// 7. Add event to calendar
$vCalendar->addComponent($vEvent);

// 8. Set headers
header('Content-Type: text/calendar; charset=utf-8');
header('Content-Disposition: attachment; filename="cal.ics"');

// 9. Output
echo $vCalendar->render();
