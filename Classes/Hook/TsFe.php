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

/**
 * tsfe hooks
 *
 * @package TYPO3
 * @subpackage Tx_T3rest
 * @author Michael Wagner
 */
class Tx_T3rest_Hook_TsFe
{

	/**
	 * in this hook we check for an mobile redirect.
	 *
	 * @param array &$params
	 * @param \TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController $tsfe
	 * @return NULL;
	 */
	public function checkAndRunRestApi(&$params, $tsfe)
	{
		if (!$this->isApiCall()) {
			return NULL;
		}

		tx_rnbase::load('Tx_T3rest_Utility_Composer');
		Tx_T3rest_Utility_Composer::autoload();

		$router = new \Respect\Rest\Router();
		$router->isAutoDispatched = false;

		// @TODO: create routing config

		exit('API not implementet yet');

		$out = $router->run();

		if ($out) {
			echo $out;
		}

		// prevent typo3 rendering, the output should be done by the rest api.
		die();
	}

	/**
	 * is there are a api call?
	 *
	 * @return boolean
	 */
	protected function isApiCall()
	{
		// the hook is not enabled
		if (!Tx_T3rest_Utility_Config::isRestHookEnabled()) {
			return FALSE;
		}
		// check the request uri for the api uri segment
		$requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
		$apiSegment = Tx_T3rest_Utility_Config::getRestApiUriPath();
		return strpos($requestUri, $apiSegment) === 0;
	}
}
