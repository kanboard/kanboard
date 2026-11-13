<?php

namespace Kanboard\Core\Security;

use GdImage;

/**
 * Captcha phrase generation, comparison and image rendering
 *
 * Image rendering and bundled fonts are derived from gregwar/captcha (MIT License).
 *
 * @package  security
 * @author   Frederic Guillot
 */
class Captcha
{
    const CHARSET = 'abcdefghijklmnpqrstuvwxyz123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    const FONT_COUNT = 6;
    const MAX_ANGLE = 8;
    const MAX_OFFSET = 5;
    const JPEG_QUALITY = 90;

    public function __construct(
        private readonly int $width = 150,
        private readonly int $height = 40,
    ) {
    }

    /**
     * Generate a random phrase
     *
     * @access public
     * @param  integer $length
     * @return string
     */
    public function generatePhrase(int $length = 5): string
    {
        $phrase = '';
        $max = strlen(self::CHARSET) - 1;

        for ($i = 0; $i < $length; $i++) {
            $phrase .= self::CHARSET[random_int(0, $max)];
        }

        return $phrase;
    }

    /**
     * Compare the expected phrase with the user input (case-insensitive, 0/o and 1/l are equivalent)
     *
     * @access public
     * @param  string $expected
     * @param  string $input
     * @return boolean
     */
    public function comparePhrases(string $expected, string $input): bool
    {
        if ($expected === '') {
            return false;
        }

        return hash_equals($this->normalize($expected), $this->normalize($input));
    }

    /**
     * Render the phrase as a JPEG image
     *
     * @access public
     * @param  string $phrase
     * @return string
     */
    public function render(string $phrase): string
    {
        $image = imagecreatetruecolor($this->width, $this->height);
        $background = imagecolorallocate($image, random_int(200, 255), random_int(200, 255), random_int(200, 255));
        imagefill($image, 0, 0, $background);

        $font = __DIR__.'/fonts/captcha'.random_int(0, self::FONT_COUNT - 1).'.ttf';

        $this->drawLines($image);
        $textColor = $this->writePhrase($image, $phrase, $font);
        $this->drawLines($image, $textColor);

        $image = $this->distort($image, $background);

        ob_start();
        imagejpeg($image, null, self::JPEG_QUALITY);

        return ob_get_clean();
    }

    private function normalize(string $phrase): string
    {
        return strtr(strtolower($phrase), '01', 'ol');
    }

    /**
     * Draw a few random lines, with random colors unless a color is given
     */
    private function drawLines(GdImage $image, ?int $color = null): void
    {
        $area = $this->width * $this->height;
        $count = random_int(intdiv($area, 3000), intdiv($area, 2000));

        for ($i = 0; $i < $count; $i++) {
            $this->drawLine($image, $color);
        }
    }

    private function drawLine(GdImage $image, ?int $color = null): void
    {
        $color ??= imagecolorallocate($image, random_int(100, 255), random_int(100, 255), random_int(100, 255));
        $halfWidth = intdiv($this->width, 2);
        $halfHeight = intdiv($this->height, 2);

        if (random_int(0, 1) === 1) {
            $x1 = random_int(0, $halfWidth);
            $y1 = random_int(0, $this->height);
            $x2 = random_int($halfWidth, $this->width);
            $y2 = random_int(0, $this->height);
        } else {
            $x1 = random_int(0, $this->width);
            $y1 = random_int(0, $halfHeight);
            $x2 = random_int(0, $this->width);
            $y2 = random_int($halfHeight, $this->height);
        }

        imagesetthickness($image, random_int(1, 3));
        imageline($image, $x1, $y1, $x2, $y2, $color);
    }

    /**
     * Write the phrase centered on the image, one letter at a time with a random angle and offset
     *
     * @return integer Text color
     */
    private function writePhrase(GdImage $image, string $phrase, string $font): int
    {
        $color = imagecolorallocate($image, random_int(0, 150), random_int(0, 150), random_int(0, 150));
        $length = mb_strlen($phrase);

        if ($length === 0) {
            return $color;
        }

        $size = $this->width / $length - random_int(0, 3) - 1;
        $box = imagettfbbox($size, 0, $font, $phrase);
        $x = ($this->width - ($box[2] - $box[0])) / 2;
        $y = (int) (($this->height - ($box[1] - $box[7])) / 2 + $size);

        foreach (mb_str_split($phrase) as $letter) {
            $angle = random_int(-self::MAX_ANGLE, self::MAX_ANGLE);
            $offset = random_int(-self::MAX_OFFSET, self::MAX_OFFSET);
            imagettftext($image, $size, $angle, (int) $x, $y + $offset, $color, $font, $letter);

            $box = imagettfbbox($size, 0, $font, $letter);
            $x += $box[2] - $box[0];
        }

        return $color;
    }

    /**
     * Apply a radial ripple and a sine wave, with bilinear interpolation
     */
    private function distort(GdImage $image, int $background): GdImage
    {
        $distorted = imagecreatetruecolor($this->width, $this->height);
        $centerX = random_int(0, $this->width);
        $centerY = random_int(0, $this->height);
        $phase = random_int(0, 10);
        $scale = 1.1 + random_int(0, 10000) / 30000;

        for ($x = 0; $x < $this->width; $x++) {
            for ($y = 0; $y < $this->height; $y++) {
                $vx = $x - $centerX;
                $vy = $y - $centerY;
                $distance = sqrt($vx * $vx + $vy * $vy);

                if ($distance > 0) {
                    $ratio = ($distance + 4 * sin($distance / 30)) / $distance;
                    $sourceX = $centerX + $vx * $ratio;
                    $sourceY = $centerY + $vy * $ratio;
                } else {
                    $sourceX = $centerX;
                    $sourceY = $centerY;
                }

                $sourceY += $scale * sin($phase + $sourceX * 0.2);

                $left = (int) floor($sourceX);
                $top = (int) floor($sourceY);
                $right = (int) ceil($sourceX);
                $bottom = (int) ceil($sourceY);

                $color = $this->interpolate(
                    $sourceX - $left,
                    $sourceY - $top,
                    $this->getColorAt($image, $left, $top, $background),
                    $this->getColorAt($image, $right, $top, $background),
                    $this->getColorAt($image, $left, $bottom, $background),
                    $this->getColorAt($image, $right, $bottom, $background),
                );

                imagesetpixel($distorted, $x, $y, $color === 0 ? $background : $color);
            }
        }

        return $distorted;
    }

    private function interpolate(float $x, float $y, int $nw, int $ne, int $sw, int $se): int
    {
        $color = 0;

        foreach ([16, 8, 0] as $shift) {
            $top = (1 - $x) * (($nw >> $shift) & 0xff) + $x * (($ne >> $shift) & 0xff);
            $bottom = (1 - $x) * (($sw >> $shift) & 0xff) + $x * (($se >> $shift) & 0xff);
            $color |= (int) ((1 - $y) * $top + $y * $bottom) << $shift;
        }

        return $color;
    }

    private function getColorAt(GdImage $image, int $x, int $y, int $background): int
    {
        if ($x < 0 || $x >= $this->width || $y < 0 || $y >= $this->height) {
            return $background;
        }

        return imagecolorat($image, $x, $y);
    }
}
