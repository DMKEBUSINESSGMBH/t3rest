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

tx_rnbase::load('Tx_T3rest_Routines_InterfaceRouter');
tx_rnbase::load('Tx_T3rest_Routines_InterfaceRoute');
tx_rnbase::load('Tx_T3rest_Routines_Auth_InterfaceAuth');

/**
 * this routine authenticates an fe user
 * by session cookie or basioc auth.
 *
 * @package TYPO3
 * @subpackage Tx_T3rest
 * @author Michael Wagner
 */
class Tx_T3rest_Routines_Auth_FeUser
	implements
		Tx_T3rest_Routines_InterfaceRouter,
		Tx_T3rest_Routines_InterfaceRoute,
		Tx_T3rest_Routines_Auth_InterfaceAuth
{
	/**
	 * the required fe groups to access a route
	 *
	 * @var string
	 */
	private $feGroups = 0;

	/**
	 * constructor
	 *
	 * @param string $feGroups
	 * @return void
	 */
	public function __construct($feGroups = 0) {
		$this->feGroups = $feGroups;
	}

	/**
	 * add the before and after callbacks
	 *
	 * @param Tx_T3rest_Router_InterfaceRouter $router
	 * @return void
	 */
	public function prepareRouter(
		Tx_T3rest_Router_InterfaceRouter $router
	) {
		// register post routine for Respect/Rest
		if ($router instanceof Tx_T3rest_Router_Respect) {
			$router->always(
				'By',
				array($this, 'byInitUserRespect')
			);
		}
	}

	/**
	 * add the before and after callbacks
	 *
	 * @param \Respect\Rest\Routes\AbstractRoute $route
	 * @return void
	 */
	public function prepareRoute($route) {
		// register post routine for Respect/Rest
		if ($route instanceof \Respect\Rest\Routes\AbstractRoute) {
			$route->by(array($this, 'byLoginRespect'));
		}
	}

	/**
	 * was called before a provider is called and initializes the user.
	 *
	 * @return boolean
	 */
	public function byInitUserRespect()
	{
		$this->initUser();

		return TRUE;
	}

	/**
	 * was called before a provider is called and checks the access
	 *
	 * @return string
	 */
	public function byLoginRespect()
	{
		$this->initUser();

		// all right, grant access!
		if ($this->checkAccess()) {
			return TRUE;
		}

		// no access, don't process the route!
		header('WWW-Authenticate: Basic realm="' . $GLOBALS['TSFE']->TYPO3_CONF_VARS['SYS']['sitename'] . '"');
		\TYPO3\CMS\Core\Utility\HttpUtility::setResponseCode(
			\TYPO3\CMS\Core\Utility\HttpUtility::HTTP_STATUS_401
		);
		return FALSE;
	}

	/**
	 * log in a user
	 *
	 * @return void
	 */
	public function initUser()
	{
		// there is allready a user, skip multiple init calls.
		if (is_array($this->fe_user->user) && $this->fe_user->user['uid']) {
			return;
		}

		// auth nach redirect herstellen
		if (isset($_SERVER['REDIRECT_HTTP_AUTHORIZATION'])) {
			list($_SERVER['PHP_AUTH_USER'], $_SERVER['PHP_AUTH_PW']) =
				explode(':' , base64_decode(substr($_SERVER['REDIRECT_HTTP_AUTHORIZATION'], 6)));
		}

		// there are user and pwd, so we has to reauth by this data
		if (!empty($_SERVER['PHP_AUTH_USER']) && !empty($_SERVER['PHP_AUTH_PW'])) {
			$_POST['user'] = $_SERVER['PHP_AUTH_USER'];
			$_POST['pass'] = $_SERVER['PHP_AUTH_PW'];
			$_POST['logintype'] = 'login';
			$_POST['pid'] = Tx_T3rest_Utility_Config::getAuthUserStoragePid();
		}

		/* @var $tsFe \TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController */
		$tsFe = $GLOBALS['TSFE'];

		// init fe user
		if (!is_object($tsFe->fe_user)) {
			$tsFe->initFEuser();
		}
		// init groups, if required
		if ($this->feGroups && !$tsFe->gr_list) {
			$tsFe->initUserGroups();
		}
	}

	/**
	 * check user access to a route
	 *
	 * @return boolean
	 */
	public function checkAccess()
	{
		/* @var $tsFe \TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController */
		$tsFe = $GLOBALS['TSFE'];

		$hasAccess = $tsFe->checkPageGroupAccess(
			array('fe_group' => $this->feGroups)
		);

		return $hasAccess;
	}
}
