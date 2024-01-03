<?php
/**
 * Copyright notice.
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

use TYPO3\CMS\Core\Context\Context;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * this routine authenticates an fe user
 * by session cookie or basioc auth.
 *
 * @TODO: migrate to context aspects!
 * GeneralUtility::makeInstance(Context::class)->getAspect('frontend.user')
 *
 * @author Michael Wagner
 */
class Tx_T3rest_Routines_Auth_FeUser implements Tx_T3rest_Routines_InterfaceRouter, Tx_T3rest_Routines_InterfaceRoute, Tx_T3rest_Routines_Auth_InterfaceAuth
{
    /**
     * the required fe groups to access a route.
     *
     * @var string
     */
    private $feGroups = 0;

    /**
     * constructor.
     *
     * @param string $feGroups
     *
     * @return void
     */
    public function __construct($feGroups = 0)
    {
        $this->feGroups = $feGroups;
    }

    /**
     * add the before and after callbacks.
     *
     * @param Tx_T3rest_Router_InterfaceRouter $router
     *
     * @return void
     */
    public function prepareRouter(
        Tx_T3rest_Router_InterfaceRouter $router
    ) {
        // register post routine for Respect/Rest
        if ($router instanceof Tx_T3rest_Router_Respect) {
            $router->always(
                'By',
                [$this, 'byInitUserRespect']
            );
        }
    }

    /**
     * add the before and after callbacks.
     *
     * @param array|\Respect\Rest\Routes\AbstractRoute $route
     *
     * @return void
     */
    public function prepareRoute($route)
    {
        // iterate over multiple routes
        if (is_array($route)) {
            foreach ($route as $r) {
                $this->prepareRoute($r);
            }
        } // register post routine for Respect/Rest
        elseif ($route instanceof \Respect\Rest\Routes\AbstractRoute) {
            $route->by([$this, 'byLoginRespect']);
        }
    }

    /**
     * was called before a provider is called and initializes the user.
     *
     * @return bool
     */
    public function byInitUserRespect()
    {
        return true;
    }

    /**
     * was called before a provider is called and checks the access.
     *
     * @return string
     */
    public function byLoginRespect()
    {
        // all right, grant access!
        if ($this->checkAccess()) {
            return true;
        }

        // no access, don't process the route!
        if (Tx_T3rest_Utility_Config::isBasicAuthHeaderEnabled()) {
            header('WWW-Authenticate: Basic realm="'.$GLOBALS['TYPO3_CONF_VARS']['SYS']['sitename'].'"');
        }
        header(\TYPO3\CMS\Core\Utility\HttpUtility::HTTP_STATUS_401);

        return false;
    }

    /**
     * Check user access to a route.
     *
     * @return bool
     */
    public function checkAccess()
    {
        $hasAccess = GeneralUtility::makeInstance(Context::class)->getAspect('frontend.user')->get('isLoggedIn');
        if ($this->feGroups) {
            $userAspect = GeneralUtility::makeInstance(Context::class)->getAspect('frontend.user');
            $pageGroupList = explode(',', $this->feGroups ?: 0);
            $hasAccess = count(array_intersect($userAspect->getGroupIds(), $pageGroupList)) > 0;
        }

        return $hasAccess;
    }

    /**
     * @return TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController
     */
    protected function getFrontendController()
    {
        return $GLOBALS['TSFE'];
    }

    public function initUser(): void
    {
    }
}
