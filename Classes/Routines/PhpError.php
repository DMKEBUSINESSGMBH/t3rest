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
 * error routine.
 *
 * @author Michael Wagner
 *
 * @SuppressWarnings("PHPMD.CamelCaseClassName")
 */
class Tx_T3rest_Routines_PhpError implements Tx_T3rest_Routines_InterfaceRouter
{
    /**
     * add the before and after callbacks.
     */
    public function prepareRouter(
        Tx_T3rest_Router_InterfaceRouter $router,
    ): void {
        // register post routine for Respect/Rest
        if ($router instanceof Tx_T3rest_Router_Respect) {
            $router->errorRoute(
                $this->handle(...)
            );
        }
    }

    /**
     * @SuppressWarnings("PHPMD.Superglobals")
     */
    public function handle(array $err): ?string
    {
        $accepted = $GLOBALS['TYPO3_CONF_VARS']['SYS']['errorHandlerErrors'];
        foreach ($err as $k1 => $value) {
            // leave only configured errors!
            if (($value[0] & $accepted) === 0) {
                unset($err[$k1]);
            }

            // unset arguments from err array
            unset($err[$k1][4]);
        }

        if ([] === $err) {
            return null;
        }

        // @todo make configurable
        header(TYPO3\CMS\Core\Utility\HttpUtility::HTTP_STATUS_500);
        Sys25\RnBase\Utility\Logger::fatal(
            'An error occurred during a t3rest request',
            't3rest',
            ['error' => $err]
        );

        return 'Sorry, an error happened.';
    }
}
