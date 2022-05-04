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
        $this->initUser();

        return true;
    }

    /**
     * was called before a provider is called and checks the access.
     *
     * @return string
     */
    public function byLoginRespect()
    {
        $this->initUser();

        // all right, grant access!
        if ($this->checkAccess()) {
            return true;
        }

        // no access, don't process the route!
        if (Tx_T3rest_Utility_Config::isBasicAuthHeaderEnabled()) {
            header('WWW-Authenticate: Basic realm="'.$GLOBALS['TYPO3_CONF_VARS']['SYS']['sitename'].'"');
        }
        \TYPO3\CMS\Core\Utility\HttpUtility::setResponseCode(
            \TYPO3\CMS\Core\Utility\HttpUtility::HTTP_STATUS_401
        );

        return false;
    }

    /**
     * Log in a user.
     *
     * @return void
     */
    public function initUser()
    {
        $tsFe = $this->getFrontendController();

        // there is already a user, skip multiple init calls.
        if (is_object($tsFe->fe_user) && is_array($tsFe->fe_user->user) && $tsFe->fe_user->user['uid']) {
            if (empty($tsFe->fe_user->groupData['uid'])) {
                $tsFe->initUserGroups();
            }

            return;
        }

        // auth nach redirect herstellen
        if (isset($_SERVER['REDIRECT_HTTP_AUTHORIZATION'])) {
            list($_SERVER['PHP_AUTH_USER'], $_SERVER['PHP_AUTH_PW']) =
                explode(':', base64_decode(substr($_SERVER['REDIRECT_HTTP_AUTHORIZATION'], 6)));
        }

        // there are user and pwd, so we has to reauth by this data
        if (!empty($_SERVER['PHP_AUTH_USER']) && !empty($_SERVER['PHP_AUTH_PW'])) {
            $_POST['user'] = $_SERVER['PHP_AUTH_USER'];
            $_POST['pass'] = $_SERVER['PHP_AUTH_PW'];
            $_POST['logintype'] = 'login';
            $_POST['pid'] = Tx_T3rest_Utility_Config::getAuthUserStoragePid();
        }

        // init fe user
        if (!is_object($tsFe->fe_user)) {
            $tsFe->fe_user = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(
                \TYPO3\CMS\Frontend\Authentication\FrontendUserAuthentication::class
            );
            if (Tx_T3rest_Utility_Config::getAuthUserStoragePid()) {
                $tsFe->fe_user->checkPid_value = Tx_T3rest_Utility_Config::getAuthUserStoragePid();
            }
            $tsFe->fe_user->start();
        }

        // init groups, if required
        if ($this->feGroups && !$tsFe->gr_list || empty($tsFe->fe_user->groupData['uid'])
        ) {
            $tsFe->initUserGroups();
        }
    }

    /**
     * Check user access to a route.
     *
     * @return bool
     */
    public function checkAccess()
    {
        $tsFe = $this->getFrontendController();

        // check if fe user auth has failed and that an user exists.
        $hasAccess = (true !== $tsFe->fe_user->loginFailure && null !== $tsFe->fe_user->user);
        if ($this->feGroups) {
            $hasAccess = $tsFe->checkPageGroupAccess(
                ['fe_group' => $this->feGroups]
            );
        }

        return $hasAccess;
    }

    /**
     * @return \TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController
     */
    protected function getFrontendController()
    {
        return $GLOBALS['TSFE'];
    }
}
