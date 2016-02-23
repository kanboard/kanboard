<?php
namespace Eluceo\iCal\Property\Event;

use Eluceo\iCal\Property;

class Organizer extends Property
{
    const PROPERTY_NAME = 'ORGANIZER';

    /**
     * @param string $name
     * @param string $email
     */
    public function __construct($name, $email = '')
    {
        $name = $name ? array('CN' => $name) : array();
        $email = !$email ?: sprintf('MAILTO:%s', $email);

        return parent::__construct($this->getName(), $email, $name);
    }

    /**
     * @inheritdoc
     */
    public function getName()
    {
        return self::PROPERTY_NAME;
    }
}
