<?php

/*
 * Copyright notice
 *
 * (c) DMK E-BUSINESS GmbH <dev@dmk-ebusiness.de>
 * All rights reserved
 *
 * This file is part of the "t3rest" Extension for TYPO3 CMS.
 *
 * This script is part of the TYPO3 project. The TYPO3 project is
 * free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * GNU Lesser General Public License can be found at
 * www.gnu.org/licenses/lgpl.html
 *
 * This script is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * This copyright notice MUST APPEAR in all copies of the script!
 */

/**
 * base controller.
 *
 * @author Michael Wagner
 *
 * @SuppressWarnings("PHPMD.CamelCaseClassName")
 */
class Tx_T3rest_Controller_AbstractController implements Tx_T3rest_Controller_InterfaceController
{
    /**
     * execute the request.
     *
     * @SuppressWarnings("PHPMD.ExitExpression")
     */
    public function execute(): void
    {
        $router = $this->getRouter();
        $this->prepareRouter($router);

        $out = $router->run();

        if ($out) {
            echo $out;
        }

        // else ?

        // prevent typo3 rendering
        exit;
    }

    /**
     * get the router.
     */
    protected function getRouter(): Tx_T3rest_Router_InterfaceRouter
    {
        return Tx_T3rest_Utility_Factory::getRespectRestRouter();
    }

    /**
     * find all providers.
     *
     * @TODO: add caching!
     *
     * @return array:Tx_T3rest_Model_Provider
     */
    protected function getProviders()
    {
        $providerRepo = Tx_T3rest_Utility_Factory::getProviderRepository();

        return $providerRepo->findAll();
    }

    /**
     * prepare the router.
     */
    private function prepareRouter(
        Tx_T3rest_Router_InterfaceRouter $router,
    ): void {
        $this->prepareRouterByProviders($router);
        $this->prepareRoutines($router);
    }

    /**
     * prepare the router by providers.
     *
     * @return void
     */
    protected function prepareRouterByProviders(
        Tx_T3rest_Router_InterfaceRouter $router,
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
     * @return void
     */
    protected function prepareRoutines(
        Tx_T3rest_Router_InterfaceRouter $router,
    ) {
        /* @var $exceptions Tx_T3rest_Routines_Exception */
        $exceptions = TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('Tx_T3rest_Routines_Exception');
        $exceptions->prepareRouter($router);

        /* @var $error Tx_T3rest_Routines_PhpError */
        $error = TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('Tx_T3rest_Routines_PhpError');
        $error->prepareRouter($router);

        /* @var $timeTrack Tx_T3rest_Routines_Log_TimeTrack */
        $timeTrack = TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('Tx_T3rest_Routines_Log_TimeTrack');
        $timeTrack->prepareRouter($router);

        /* @var $memTrack Tx_T3rest_Routines_Log_MemTrack */
        $memTrack = TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('Tx_T3rest_Routines_Log_MemTrack');
        $memTrack->prepareRouter($router);
    }
}
