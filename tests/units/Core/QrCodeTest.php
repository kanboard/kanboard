<?php

namespace KanboardTests\units\Core;

use InvalidArgumentException;
use KanboardTests\units\Base;
use Kanboard\Core\QrCode;
use ReflectionMethod;

class QrCodeTest extends Base
{
    /**
     * Valid format information strings for each level, indexed by mask (ISO/IEC 18004 Table C.1)
     */
    const FORMAT_STRINGS = [
        'L' => ['111011111000100', '111001011110011', '111110110101010', '111100010011101', '110011000101111', '110001100011000', '110110001000001', '110100101110110'],
        'M' => ['101010000010010', '101000100100101', '101111001111100', '101101101001011', '100010111111001', '100000011001110', '100111110010111', '100101010100000'],
        'Q' => ['011010101011111', '011000001101000', '011111100110001', '011101000000110', '010010010110100', '010000110000011', '010111011011010', '010101111101101'],
        'H' => ['001011010001001', '001001110111110', '001110011100111', '001100111010000', '000011101100010', '000001001010101', '000110100001100', '000100000111011'],
    ];

    public function testInvalidLevel()
    {
        $this->expectException(InvalidArgumentException::class);
        new QrCode('X');
    }

    public function testReedSolomon()
    {
        $method = new ReflectionMethod(QrCode::class, 'computeErrorCorrection');
        $data = [16, 32, 12, 86, 97, 128, 236, 17, 236, 17, 236, 17, 236, 17, 236, 17];

        $this->assertSame([165, 36, 212, 193, 237, 54, 199, 135, 44, 85], $method->invoke(new QrCode(QrCode::LEVEL_M), $data, 10));
    }

    public function testVersionSelection()
    {
        $qrcode = new QrCode;

        $this->assertCount(21, $qrcode->encode(''));
        $this->assertCount(21, $qrcode->encode(str_repeat('a', 17)));
        $this->assertCount(25, $qrcode->encode(str_repeat('a', 18)));
        $this->assertCount(177, $qrcode->encode(str_repeat('a', 2953)));

        $this->assertCount(21, (new QrCode(QrCode::LEVEL_H))->encode(str_repeat('a', 7)));
        $this->assertCount(25, (new QrCode(QrCode::LEVEL_H))->encode(str_repeat('a', 8)));
    }

    public function testDataTooLong()
    {
        $this->expectException(InvalidArgumentException::class);
        (new QrCode)->encode(str_repeat('a', 2954));
    }

    public function testUtf8DataUsesEciHeader()
    {
        $qrcode = new QrCode;

        // The 12-bit ECI header leaves room for 16 bytes in a version 1 symbol instead of 17
        $this->assertCount(21, $qrcode->encode(str_repeat('é', 8)));
        $this->assertCount(25, $qrcode->encode(str_repeat('é', 8).'a'));

        // Invalid UTF-8 is encoded without ECI header
        $this->assertCount(21, $qrcode->encode(str_repeat("\xFF", 17)));
    }

    public function testFunctionPatterns()
    {
        // 150 bytes require a version 7 symbol at level L
        $matrix = (new QrCode)->encode(str_repeat('a', 150));
        $size = count($matrix);
        $this->assertSame(45, $size);

        foreach ([[0, 0], [$size - 7, 0], [0, $size - 7]] as [$left, $top]) {
            for ($y = 0; $y < 7; $y++) {
                for ($x = 0; $x < 7; $x++) {
                    $distance = max(abs($x - 3), abs($y - 3));
                    $this->assertSame($distance !== 2, $matrix[$top + $y][$left + $x]);
                }
            }
        }

        for ($i = 8; $i < $size - 8; $i++) {
            $this->assertSame($i % 2 === 0, $matrix[6][$i]);
            $this->assertSame($i % 2 === 0, $matrix[$i][6]);
        }

        foreach ([[22, 6], [6, 22], [22, 22], [38, 22], [22, 38], [38, 38]] as [$x, $y]) {
            $this->assertTrue($matrix[$y][$x]);
            $this->assertFalse($matrix[$y - 1][$x]);
            $this->assertFalse($matrix[$y][$x + 1]);
            $this->assertTrue($matrix[$y - 2][$x - 2]);
            $this->assertTrue($matrix[$y + 2][$x + 2]);
        }

        $this->assertTrue($matrix[$size - 8][8]);
    }

    public function testFormatInformation()
    {
        foreach (array_keys(self::FORMAT_STRINGS) as $level) {
            $matrix = (new QrCode($level))->encode('otpauth://totp/admin?secret=JBSWY3DPEHPK3PXP&issuer=Kanboard');
            $size = count($matrix);
            $first = '';
            $second = '';

            for ($i = 14; $i >= 0; $i--) {
                if ($i < 6) {
                    $first .= (int) $matrix[$i][8];
                } elseif ($i < 8) {
                    $first .= (int) $matrix[$i + 1][8];
                } elseif ($i === 8) {
                    $first .= (int) $matrix[8][7];
                } else {
                    $first .= (int) $matrix[8][14 - $i];
                }

                $second .= (int) ($i < 8 ? $matrix[8][$size - 1 - $i] : $matrix[$size - 15 + $i][8]);
            }

            $this->assertSame($first, $second);
            $this->assertContains($first, self::FORMAT_STRINGS[$level]);
        }
    }

    public function testVersionInformation()
    {
        $matrix = (new QrCode)->encode(str_repeat('a', 150));
        $size = count($matrix);
        $topRight = '';
        $bottomLeft = '';

        for ($i = 17; $i >= 0; $i--) {
            $topRight .= (int) $matrix[intdiv($i, 3)][$size - 11 + $i % 3];
            $bottomLeft .= (int) $matrix[$size - 11 + $i % 3][intdiv($i, 3)];
        }

        $this->assertSame('000111110010010100', $topRight);
        $this->assertSame('000111110010010100', $bottomLeft);
    }

    public function testRenderSvg()
    {
        $url = 'otpauth://totp/admin?secret=JBSWY3DPEHPK3PXP&issuer=Kanboard';
        $size = count((new QrCode)->encode($url));
        $svg = (new QrCode)->renderSvg($url);
        $xml = simplexml_load_string($svg);

        $this->assertNotFalse($xml);
        $this->assertSame('svg', $xml->getName());
        $this->assertSame((string) (($size + 8) * 6), (string) $xml['width']);
        $this->assertSame((string) (($size + 8) * 6), (string) $xml['height']);
        $this->assertSame('0 0 '.($size + 8).' '.($size + 8), (string) $xml['viewBox']);
        $this->assertStringStartsWith('M4 4h7v1h-7z', (string) $xml->path['d']);
        $this->assertStringNotContainsString('JBSWY3DPEHPK3PXP', $svg);

        $xml = simplexml_load_string((new QrCode)->renderSvg($url, 2, 0));
        $this->assertSame((string) ($size * 2), (string) $xml['width']);
        $this->assertStringStartsWith('M0 0h7v1h-7z', (string) $xml->path['d']);
    }

    public function testDecodeWithZbar()
    {
        $zbarimg = trim((string) shell_exec('command -v zbarimg 2>/dev/null'));

        if ($zbarimg === '') {
            $this->markTestSkipped('zbarimg is not installed');
        }

        $inputs = [
            QrCode::LEVEL_L => 'otpauth://totp/admin%40example.org?secret=JBSWY3DPEHPK3PXP&issuer=Kanboard',
            QrCode::LEVEL_M => 'otpauth://totp/Jos%C3%A9?secret=JBSWY3DPEHPK3PXP&issuer=Kanboard',
            QrCode::LEVEL_Q => 'otpauth://totp/José%20Müller?secret=JBSWY3DPEHPK3PXP&issuer=Kanboard',
            QrCode::LEVEL_H => str_repeat('Kanboard ', 100),
        ];

        foreach ($inputs as $level => $data) {
            $file = tempnam(sys_get_temp_dir(), 'qrcode');
            $this->writePng((new QrCode($level))->encode($data), $file);
            $output = shell_exec(escapeshellarg($zbarimg).' --raw -q -Sdisable -Sqrcode.enable '.escapeshellarg($file));
            unlink($file);

            $this->assertSame($data, rtrim((string) $output, "\n"));
        }
    }

    private function writePng(array $matrix, string $file): void
    {
        $scale = 4;
        $margin = 4;
        $pixels = (count($matrix) + 2 * $margin) * $scale;
        $image = imagecreate($pixels, $pixels);
        imagecolorallocate($image, 255, 255, 255);
        $black = imagecolorallocate($image, 0, 0, 0);

        foreach ($matrix as $y => $row) {
            foreach ($row as $x => $dark) {
                if ($dark) {
                    $left = ($x + $margin) * $scale;
                    $top = ($y + $margin) * $scale;
                    imagefilledrectangle($image, $left, $top, $left + $scale - 1, $top + $scale - 1, $black);
                }
            }
        }

        imagepng($image, $file);
    }
}
