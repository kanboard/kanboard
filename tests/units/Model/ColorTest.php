<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__.'/../Base.php';

use Kanboard\Model\Color;

class ColorTest extends Base
{
    public function testDefaultColors()
    {
        $c = new Color($this->container);

        $colors = $c->getDefaultColors();
        $this->assertGreaterThanOrEqual(1, count($colors));
        $this->assertTrue(is_array($colors));

        foreach ($colors as $color_id => $color) {
            $this->assertTrue(is_array($color));
            $this->assertArrayHasKey('name', $color);
            $this->assertArrayHasKey('background', $color);
            $this->assertArrayHasKey('border', $color);
        }
    }

    public function testColorNameFinder()
    {
        $c = new Color($this->container);

        $color_id = $c->find('Light Green');
        $this->assertEquals('light_green', $color_id);
    }

    public function testGetBorderColor()
    {
        $c = new Color($this->container);

        $color_code = $c->getBorderColor('yellow');
        $this->assertEquals('rgb(223, 227, 45)', $color_code);
    }

    public function testGetBackgroundColor()
    {
        $c = new Color($this->container);

        $color_code = $c->getBackgroundColor('yellow');
        $this->assertEquals('rgb(245, 247, 196)', $color_code);
    }

    public function testUpdateColors()
    {
        $c = new Color($this->container);

        $colors = $c->getColors();
        $this->assertEquals('Yellow', $colors['yellow']['name']);
        $this->assertEquals('rgb(223, 227, 45)', $colors['yellow']['border']);

        $changes = array(
            'yellow_name' => 'Warning',
            'yellow_is_usable' => true,
            'yellow_background' => '#fff000',
            'yellow_border' => '#fff000',
        );
        $this->assertTrue($c->save($changes));

        $colors = $c->getColors();
        $this->assertEquals('Warning', $colors['yellow']['name']);
        $this->assertEquals('#fff000', $colors['yellow']['border']);
    }

    public function testHideColors()
    {
        $c = new Color($this->container);

        // Get all usable colors, should be all at this point.
        $colors = $c->getColors();
        $this->assertEquals(16, count($colors));

        $changes = array(
            'red_name' => 'Red',
        //  'red_is_usable' => true, // Omitting this is setting it to false.
            'red_background' => '#cc0000',
            'red_border' => '#cc0000',
        );
        $this->assertTrue($c->save($changes,true));

        // Get all usable colors.
        $colors = $c->getColors();
        $this->assertEquals(15, count($colors));

        // Get all colors, even non usable ones.
        $colors = $c->getColors(false);
        $this->assertEquals(16, count($colors));
    }
}
