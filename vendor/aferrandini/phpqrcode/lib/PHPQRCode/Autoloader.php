<?php
/**
 * Autoloader
 *
 * Copyright (c) 2006 - 2011 PHPExcel
 *
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 2.1 of the License, or (at your option) any later version.
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with this library; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301  USA
 *
 * @category   PHPQRCode
 * @package    PHPQRCode
 */

namespace PHPQRCode;

class Autoloader
{
	public static function register()
    {
		spl_autoload_register(array(new self, 'autoload'));
    }

	public static function autoload($class)
    {
		if ((class_exists($class)) || (strpos($class, 'PHPQRCode') !== 0)) {
			return false;
		}

		$file =	dirname(__FILE__) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR
            . str_replace(array('\\', "\0"), array('/', ''), $class).'.php';

		if (is_file($file)) {
			require $file;
		}
    }

}