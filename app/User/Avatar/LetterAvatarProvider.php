<?php

namespace Kanboard\User\Avatar;

use Kanboard\Core\Base;
use Kanboard\Core\User\Avatar\AvatarProviderInterface;

/**
 * Letter Avatar Provider
 *
 * The color hash algorithm is backported from the Javascript library color-hash
 * Source: https://github.com/zenozeng/color-hash
 * Author: Zeno Zeng (MIT License)
 *
 * @package  avatar
 * @author   Frederic Guillot
 */
class LetterAvatarProvider extends Base implements AvatarProviderInterface
{
    protected $lightness = array(0.35, 0.5, 0.65);
    protected $saturation = array(0.35, 0.5, 0.65);

    /**
     * Render avatar html
     *
     * @access public
     * @param  array $user
     * @param  int   $size
     * @return string
     */
    public function render(array $user, $size)
    {
        $initials = $this->helper->user->getInitials($user['name'] ?: $user['username']);
        $rgb = $this->getBackgroundColor($user['name'] ?: $user['username']);
        $name = $this->helper->text->e($user['name'] ?: $user['username']);

        return sprintf(
            '<div class="avatar-letter" style="background-color: rgb(%d, %d, %d)" title="%s" role="img" aria-label="%s">%s</div>',
            $rgb[0],
            $rgb[1],
            $rgb[2],
            $name,
            $name,
            $this->helper->text->e($initials)
        );
    }

    /**
     * Determine if the provider is active
     *
     * @access public
     * @param  array $user
     * @return boolean
     */
    public function isActive(array $user)
    {
        return true;
    }

    /**
     * Get background color based on a string
     *
     * @param  string $str
     * @return array
     */
    public function getBackgroundColor($str)
    {
        $hsl = $this->getHSL($str);
        return $this->getRGB($hsl[0], $hsl[1], $hsl[2]);
    }

    /**
     * Convert HSL to RGB
     *
     * @access protected
     * @param  integer  $hue         Hue ∈ [0, 360)
     * @param  integer  $saturation  Saturation ∈ [0, 1]
     * @param  integer  $lightness   Lightness ∈ [0, 1]
     * @return array
     */
    protected function getRGB($hue, $saturation, $lightness)
    {
        $hue /= 360;
        $q = $lightness < 0.5 ? $lightness * (1 + $saturation) : $lightness + $saturation - $lightness * $saturation;
        $p = 2 * $lightness - $q;

        return array_map(function ($color) use ($q, $p) {
            if ($color < 0) {
                $color++;
            }

            if ($color > 1) {
                $color--;
            }

            if ($color < 1/6) {
                $color = $p + ($q - $p) * 6 * $color;
            } else if ($color < 0.5) {
                $color = $q;
            } else if ($color < 2/3) {
                $color = $p + ($q - $p) * 6 * (2/3 - $color);
            } else {
                $color = $p;
            }

            return round($color * 255);
        }, array($hue + 1/3, $hue, $hue - 1/3));
    }

    /**
     * Returns the hash in [h, s, l].
     * Note that H ∈ [0, 360); S ∈ [0, 1]; L ∈ [0, 1];
     *
     * @access protected
     * @param  string $str
     * @return array
     */
    protected function getHSL($str)
    {
        $hash = $this->hash($str);
        $hue = $hash % 359;

        $hash = intval($hash / 360);
        $saturation = $this->saturation[$hash % count($this->saturation)];

        $hash = intval($hash / count($this->saturation));
        $lightness = $this->lightness[$hash % count($this->lightness)];

        return array($hue, $saturation, $lightness);
    }

    /**
     * BKDR Hash (modified version)
     *
     * @access protected
     * @param  string $str
     * @return integer
     */
    protected function hash($str)
    {
        $seed = 131;
        $seed2 = 137;
        $hash = 0;

        // Make hash more sensitive for short string like 'a', 'b', 'c'
        $str .= 'x';
        $max = intval(PHP_INT_MAX / $seed2);

        for ($i = 0, $ilen = mb_strlen($str, 'UTF-8'); $i < $ilen; $i++) {
            if ($hash > $max) {
                $hash = intval($hash / $seed2);
            }

            $hash = $hash * $seed + $this->getCharCode(mb_substr($str, $i, 1, 'UTF-8'));
        }

        return $hash;
    }

    /**
     * Backport of Javascript function charCodeAt()
     *
     * @access protected
     * @param  string $c
     * @return integer
     */
    protected function getCharCode($c)
    {
        list(, $ord) = unpack('N', mb_convert_encoding($c, 'UCS-4BE', 'UTF-8'));
        return $ord;
    }
}
