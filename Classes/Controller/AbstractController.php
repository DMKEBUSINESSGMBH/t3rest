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
class Tx_T3rest_Controller_AbstractController implements Tx_T3rest_Controller_InterfaceController
{

    /**
     * execute the request
     *
     * @return void
     */
    public function execute()
    {
        //TODO: in T3 >= 9 this should be checked in the middleware
        if (!$this->isApiCall()) {
            return;
        }

        $router = $this->getRouter();
        $this->prepareRouter($router);

        $out = $router->run();

        if ($out) {
            echo $out;
        }
        // else ?

        // prevent typo3 rendering
        die();
    }

    /**
     * get the router.
     *
     * @return Tx_T3rest_Router_InterfaceRouter
     */
    protected function getRouter()
    {
        return Tx_T3rest_Utility_Factory::getRespectRestRouter();
    }

    /**
     * find all providers
     *
     * @TODO: add caching!
     * @return array:Tx_T3rest_Model_Provider
     */
    protected function getProviders()
    {
        $providerRepo = Tx_T3rest_Utility_Factory::getProviderRepository();

        return $providerRepo->findAll();
    }

    /**
     * prepare the router.
     *
     * @param Tx_T3rest_Router_InterfaceRouter $router
     * @return void
     */
    private function prepareRouter(
        Tx_T3rest_Router_InterfaceRouter $router
    ) {
        $this->prepareRouterByProviders($router);
        $this->prepareRoutines($router);
    }

    /**
     * prepare the router by providers.
     *
     * @param Tx_T3rest_Router_InterfaceRouter $router
     * @return void
     */
    protected function prepareRouterByProviders(
        Tx_T3rest_Router_InterfaceRouter $router
    ) {
        /* @var $provider Tx_T3rest_Model_Provider */
        foreach ($this->getProviders() as $provider) {
            $providerInstance = $provider->getProviderInstance();
            if (!$providerInstance instanceof Tx_T3rest_Provider_InterfaceProvider) {
                continue;
            }
            $providerInstance->prepareRouter($router);
        }
    }

    /**
     * initializes the routines for the router.
     * for excample it can be used to register a throu routine
     * for data transformation to json.
     *
     * @param Tx_T3rest_Router_InterfaceRouter $router
     * @return void
     */
    protected function prepareRoutines(
        Tx_T3rest_Router_InterfaceRouter $router
    ) {
        /* @var $exceptions Tx_T3rest_Routines_Exception */
        $exceptions = tx_rnbase::makeInstance('Tx_T3rest_Routines_Exception');
        $exceptions->prepareRouter($router);

        /* @var $error Tx_T3rest_Routines_PhpError */
        $error = tx_rnbase::makeInstance('Tx_T3rest_Routines_PhpError');
        $error->prepareRouter($router);

        /* @var $timeTrack Tx_T3rest_Routines_Log_TimeTrack */
        $timeTrack = tx_rnbase::makeInstance('Tx_T3rest_Routines_Log_TimeTrack');
        $timeTrack->prepareRouter($router);

        /* @var $memTrack Tx_T3rest_Routines_Log_MemTrack */
        $memTrack = tx_rnbase::makeInstance('Tx_T3rest_Routines_Log_MemTrack');
        $memTrack->prepareRouter($router);
    }

    /**
     * is there are a api call?
     *
     * @return bool
     */
    protected function isApiCall()
    {
        // check the request uri for the api uri segment
        $requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $apiSegment = Tx_T3rest_Utility_Config::getRestApiUriPath();

        return strpos($requestUri, $apiSegment) === 0;
    }
}
