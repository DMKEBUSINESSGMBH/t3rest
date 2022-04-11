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
tx_rnbase::load('Tx_T3rest_Routines_InterfaceRouter');

/**
 * error routine.
 *
 * @author Michael Wagner
 */
class Tx_T3rest_Routines_PhpError implements Tx_T3rest_Routines_InterfaceRouter
{
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
            $router->errorRoute(
                [$this, 'handle']
            );
        }
    }

    /**
     * @param array $err
     *
     * @return string
     */
    public function handle(array $err)
    {
        $accepted = $GLOBALS['TYPO3_CONF_VARS']['SYS']['errorHandlerErrors'];
        foreach ($err as $k1 => $value) {
            // leave only configured errors!
            if (!($value[0] & $accepted)) {
                unset($err[$k1]);
            }
            // unset arguments from err array
            unset($err[$k1][4]);
            continue;
        }

        if (empty($err)) {
            return null;
        }

        // @todo make configurable
        \TYPO3\CMS\Core\Utility\HttpUtility::setResponseCode(
            \TYPO3\CMS\Core\Utility\HttpUtility::HTTP_STATUS_500
        );
        \Sys25\RnBase\Utility\Logger::fatal(
            'An error occurred during a t3rest request',
            't3rest',
            ['error' => var_export($err, true)]
        );

        return 'Sorry, an error happened.';
    }
}
