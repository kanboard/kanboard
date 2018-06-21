<?php
/**
 * Constants.php
 *
 * Created by arielferrandini
 */

namespace PHPQRCode;

class Constants
{
    const QR_CACHEABLE = false;
    const QR_CACHE_DIR = ''; //dirname(__FILE__) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'cache' . DIRECTORY_SEPARATOR;
    const QR_LOG_DIR   = '/tmp/qrcode_log/';

    const QR_FIND_BEST_MASK = true;
    const QR_FIND_FROM_RANDOM = false;
    const QR_DEFAULT_MASK = 2;

    const QR_PNG_MAXIMUM_SIZE =  1024;

    // Encoding modes
	const QR_MODE_NUL = -1;
	const QR_MODE_NUM = 0;
	const QR_MODE_AN = 1;
	const QR_MODE_8 = 2;
	const QR_MODE_KANJI = 3;
	const QR_MODE_STRUCTURE = 4;

	// Levels of error correction.
	const QR_ECLEVEL_L = 0;
	const QR_ECLEVEL_M = 1;
	const QR_ECLEVEL_Q = 2;
	const QR_ECLEVEL_H = 3;

	// Supported output formats
	const QR_FORMAT_TEXT = 0;
	const QR_FORMAT_PNG =  1;

    const QR_IMAGE =  true;

    const STRUCTURE_HEADER_BITS =  20;
    const MAX_STRUCTURED_SYMBOLS = 16;

    // Maks
    const N1 = 3;
	const N2 = 3;
	const N3 = 40;
	const N4 = 10;

    const QRSPEC_VERSION_MAX = 40;
    const QRSPEC_WIDTH_MAX =   177;

    const QRCAP_WIDTH =        0;
    const QRCAP_WORDS =        1;
    const QRCAP_REMINDER =     2;
    const QRCAP_EC =           3;
}
