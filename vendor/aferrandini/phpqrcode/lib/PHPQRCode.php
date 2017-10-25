<?php
/**
 * PHPQRCode.php
 *
 * Created by arielferrandini
 */
$QR_BASEDIR = dirname(__FILE__).DIRECTORY_SEPARATOR;

// Required libs

/*
 * PHP QR Code encoder
 *
 * Config file, feel free to modify
 */

define('QR_CACHEABLE', true);                                                               // use cache - more disk reads but less CPU power, masks and format templates are stored there
define('QR_CACHE_DIR', dirname(__FILE__).DIRECTORY_SEPARATOR.'cache'.DIRECTORY_SEPARATOR);  // used when QR_CACHEABLE === true
define('QR_LOG_DIR', dirname(__FILE__).DIRECTORY_SEPARATOR);                                // default error logs dir

define('QR_FIND_BEST_MASK', true);                                                          // if true, estimates best mask (spec. default, but extremally slow; set to false to significant performance boost but (propably) worst quality code
define('QR_FIND_FROM_RANDOM', false);                                                       // if false, checks all masks available, otherwise value tells count of masks need to be checked, mask id are got randomly
define('QR_DEFAULT_MASK', 2);                                                               // when QR_FIND_BEST_MASK === false

define('QR_PNG_MAXIMUM_SIZE',  1024);


// Supported output formats

define('QR_FORMAT_TEXT', 0);
define('QR_FORMAT_PNG',  1);

/** PHPQRCode root directory */
if (!defined('PHPQRCODE_ROOT')) {
	define('PHPQRCODE_ROOT', dirname(__FILE__) . '/');
	require(PHPQRCODE_ROOT . 'PHPQRCode/Autoloader.php');
}

class PHPQRCode
{

}
