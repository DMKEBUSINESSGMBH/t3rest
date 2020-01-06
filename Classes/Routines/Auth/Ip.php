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
 * This routine ahtuenticates a route by the source IP of the request.
 *
 * @author          Hannes Bochmann
 * @license         http://www.gnu.org/licenses/lgpl.html
 *                  GNU Lesser General Public License, version 3 or later
 */
class Tx_T3rest_Routines_Auth_Ip implements Tx_T3rest_Routines_InterfaceRouter, Tx_T3rest_Routines_InterfaceRoute
{
    /**
     * @var array
     */
    protected $allowedIps;

    /**
     * @param array $allowedIps
     */
    public function __construct(array $allowedIps)
    {
        $this->allowedIps = $allowedIps;
    }

    /**
     * {@inheritdoc}
     *
     * @see Tx_T3rest_Routines_InterfaceRouter::prepareRouter()
     */
    public function prepareRouter(
        Tx_T3rest_Router_InterfaceRouter $router
    ) {
        // register post routine for Respect/Rest
        if ($router instanceof Tx_T3rest_Router_Respect) {
            $router->always(
                'By',
                [$this, 'checkRemoteIp']
            );
        }
    }

    /**
     * {@inheritdoc}
     *
     * @see Tx_T3rest_Routines_InterfaceRoute::prepareRoute()
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
            $route->by([$this, 'checkRemoteIp']);
        }
    }

    /**
     * {@inheritdoc}
     *
     * @see Tx_T3rest_Routines_Auth_InterfaceAuth::checkAccess()
     */
    public function checkRemoteIp()
    {
        $hasAccess = tx_rnbase_util_Network::cmpIP(
            tx_rnbase_util_Misc::getIndpEnv('REMOTE_ADDR'),
            join(',', $this->allowedIps)
        );

        if (!$hasAccess) {
            echo 'IP not allowed';
            \TYPO3\CMS\Core\Utility\HttpUtility::setResponseCode(
                \TYPO3\CMS\Core\Utility\HttpUtility::HTTP_STATUS_401
            );
        }

        return $hasAccess;
    }
}
