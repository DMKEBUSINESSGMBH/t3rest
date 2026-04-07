<?php

declare(strict_types=1);

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
 * This routine ahtuenticates a route by the source IP of the request.
 *
 * @author          Hannes Bochmann
 * @license         http://www.gnu.org/licenses/lgpl.html
 *                  GNU Lesser General Public License, version 3 or later
 *
 * @SuppressWarnings("PHPMD.CamelCaseClassName")
 */
class Tx_T3rest_Routines_Auth_Ip implements Tx_T3rest_Routines_InterfaceRouter, Tx_T3rest_Routines_InterfaceRoute
{
    public function __construct(protected array $allowedIps)
    {
    }

    /**
     * @see Tx_T3rest_Routines_InterfaceRouter::prepareRouter()
     */
    public function prepareRouter(
        Tx_T3rest_Router_InterfaceRouter $router,
    ): void {
        // register post routine for Respect/Rest
        if ($router instanceof Tx_T3rest_Router_Respect) {
            $router->always(
                'By',
                [$this, 'checkRemoteIp']
            );
        }
    }

    /**
     * @see Tx_T3rest_Routines_InterfaceRoute::prepareRoute()
     */
    public function prepareRoute($route): void
    {
        // iterate over multiple routes
        if (is_array($route)) {
            foreach ($route as $r) {
                $this->prepareRoute($r);
            }
        } // register post routine for Respect/Rest
        elseif ($route instanceof Respect\Rest\Routes\AbstractRoute) {
            $route->by($this->checkRemoteIp(...));
        }
    }

    /**
     * @see Tx_T3rest_Routines_Auth_InterfaceAuth::checkAccess()
     */
    public function checkRemoteIp()
    {
        $hasAccess = Sys25\RnBase\Utility\Network::cmpIP(
            Sys25\RnBase\Utility\Misc::getIndpEnv('REMOTE_ADDR'),
            implode(',', $this->allowedIps)
        );

        if (!$hasAccess) {
            echo 'IP not allowed';
            header(TYPO3\CMS\Core\Utility\HttpUtility::HTTP_STATUS_401);
        }

        return $hasAccess;
    }
}
