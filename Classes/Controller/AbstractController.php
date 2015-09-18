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

tx_rnbase::load('Tx_T3rest_Controller_InterfaceController');

/**
 * base controller
 *
 * @package TYPO3
 * @subpackage Tx_T3rest
 * @author Michael Wagner
 */
class Tx_T3rest_Controller_AbstractController
	implements Tx_T3rest_Controller_InterfaceController
{

	/**
	 * execute the request
	 *
	 * @return string
	 */
	public function execute()
	{
		if (!$this->isApiCall()) {
			return FALSE;
		}

		tx_rnbase::load('Tx_T3rest_Utility_Composer');
		Tx_T3rest_Utility_Composer::autoload();

		$router = $this->getRouter();
		$this->prepareRouterByProviders($router);


		$router->always(
			'Through',
			function(){
				return array($this, 'transformReturnValue');
			}
		);

		$out =$router->run();

		if ($out) {
			echo $out;
		}
		// else ?

		// prevent typo3 rendering
		die();
	}

	/**
	 * was called after provider returns his value.
	 * this method can be extended by child classes
	 *
	 * @param mixed $data
	 * @return string
	 */
	public function transformReturnValue($data)
	{
		return (string) $data;
	}

	/**
	 *
	 * @return Tx_T3rest_Router_Respect
	 */
	protected function getRouter()
	{
		$router = Tx_T3rest_Utility_Factory::getRespectRestRouter();
		$router->isAutoDispatched = false;

		return $router;
	}

	/**
	 *
	 * @return array:Tx_T3rest_Model_Provider
	 */
	protected function getProviders()
	{
		$providerRepo = Tx_T3rest_Utility_Factory::getProviderRepository();
		return $providerRepo->findAll();
	}

	/**
	 *
	 * @param Tx_T3rest_Router_Respect $router
	 */
	protected function prepareRouterByProviders(
		Tx_T3rest_Router_Respect $router
	) {
		/* @var $provider Tx_T3rest_Model_Provider */
		foreach ($this->getProviders() as $provider) {
			$route = $provider
				->getProviderInstance()
				->prepareRouter($router)
			;
		}
	}

	/**
	 * is there are a api call?
	 *
	 * @return boolean
	 */
	protected function isApiCall()
	{
		// check the request uri for the api uri segment
		$requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
		$apiSegment = Tx_T3rest_Utility_Config::getRestApiUriPath();
		return strpos($requestUri, $apiSegment) === 0;
	}
}
