<?php
/**
 * Copyright notice
 *
 * (c) 2015 DMK E-Business GmbH <dev@dmk-ebusiness.de>
 * All rights reserved
 *
 * This script is part of the TYPO3 project. The TYPO3 project is
 * free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * The GNU General Public License can be found at
 * http://www.gnu.org/copyleft/gpl.html.
 *
 * This script is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * This copyright notice MUST APPEAR in all copies of the script!
 */

tx_rnbase::load('Tx_T3rest_Controller_AbstractController');
tx_rnbase::load('tx_rnbase_util_Json');

/**
 * tsfe hooks
 *
 * @package TYPO3
 * @subpackage Tx_T3rest
 * @author Michael Wagner
 */
class Tx_T3rest_Controller_Json
	extends Tx_T3rest_Controller_AbstractController
{

	/**
	 * was called after provider returns his value.
	 * this method can be extended by child classes
	 *
	 * @param mixed $data
	 * @return string
	 */
	public function transformReturnValue($data)
	{
		$converter = tx_rnbase_util_Json::getInstance();
		$json = $converter->encode($data);

		// find a better way to set the headers
		header('Cache-Control: no-cache, must-revalidate');
		header('Content-type: application/json');

		return $json;
	}

}
