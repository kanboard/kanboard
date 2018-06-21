<?php
/**
 * FrameFiller.php
 *
 * Created by arielferrandini
 */

namespace PHPQRCode;

class FrameFiller {

    public $width;
    public $frame;
    public $x;
    public $y;
    public $dir;
    public $bit;

    //----------------------------------------------------------------------
    public function __construct($width, &$frame)
    {
        $this->width = $width;
        $this->frame = $frame;
        $this->x = $width - 1;
        $this->y = $width - 1;
        $this->dir = -1;
        $this->bit = -1;
    }

    //----------------------------------------------------------------------
    public function setFrameAt($at, $val)
    {
        $this->frame[$at['y']][$at['x']] = chr($val);
    }

    //----------------------------------------------------------------------
    public function getFrameAt($at)
    {
        return ord($this->frame[$at['y']][$at['x']]);
    }

    //----------------------------------------------------------------------
    public function next()
    {
        do {

            if($this->bit == -1) {
                $this->bit = 0;
                return array('x'=>$this->x, 'y'=>$this->y);
            }

            $x = $this->x;
            $y = $this->y;
            $w = $this->width;

            if($this->bit == 0) {
                $x--;
                $this->bit++;
            } else {
                $x++;
                $y += $this->dir;
                $this->bit--;
            }

            if($this->dir < 0) {
                if($y < 0) {
                    $y = 0;
                    $x -= 2;
                    $this->dir = 1;
                    if($x == 6) {
                        $x--;
                        $y = 9;
                    }
                }
            } else {
                if($y == $w) {
                    $y = $w - 1;
                    $x -= 2;
                    $this->dir = -1;
                    if($x == 6) {
                        $x--;
                        $y -= 8;
                    }
                }
            }
            if($x < 0 || $y < 0) return null;

            $this->x = $x;
            $this->y = $y;

        } while(ord($this->frame[$y][$x]) & 0x80);

        return array('x'=>$x, 'y'=>$y);
    }

} ;