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
Tx_T3rest_Utility_Composer::autoload();

/**
 * the Respect/Rest router.
 *
 * @author Michael Wagner
 */
class Tx_T3rest_Router_Respect extends Respect\Rest\Router implements Tx_T3rest_Router_InterfaceRouter
{
    /**
     * disable the auto dispatching!
     *
     * @var bool
     */
    public $isAutoDispatched = false;

    /**
     * register an route.
     *
     * @param string $method
     * @param string $path
     * @param string $class
     * @param array $arguments
     *
     * @return Respect\Rest\Routes\ClassName The route instance
     */
    public function addRoute(
        $method,
        $path,
        $class,
        array $arguments = []
    ) {
        $baseUri = Tx_T3rest_Utility_Config::getRestApiUriPathForSiteLanguage();

        return $this->$method(
            $baseUri.ltrim($path, '/'),
            $class,
            $arguments
        );
    }
}
