<?php

namespace Eluceo\iCal;

class PropertyBagTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @todo Use Mocks instead of a real object!
     */
    public function testPropertyAlreadyExistsOnAddingProperty()
    {
        $this->setExpectedException('\\Exception', "Property with name 'propName' already exists");

        $propertyBag = new PropertyBag();
        $propertyBag->add(new Property('propName', ''));
        $propertyBag->add(new Property('propName', ''));
    }
}
