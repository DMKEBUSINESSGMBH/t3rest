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
 * exception routine.
 *
 * @author Michael Wagner
 */
class Tx_T3rest_Routines_Exception implements Tx_T3rest_Routines_InterfaceRouter
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
            $router->exceptionRoute(
                'Exception',
                [$this, 'handle']
            );
        }
    }

    /**
     * @param Exception $e
     *
     * @return string
     */
    public function handle(Exception $e)
    {
        // @todo make configurable
        header(TYPO3\CMS\Core\Utility\HttpUtility::HTTP_STATUS_500);

        return sprintf(
            'Sorry, error "%1$s" happened: "%2$s"',
            $e->getCode(),
            $e->getMessage()
        );
    }
}
