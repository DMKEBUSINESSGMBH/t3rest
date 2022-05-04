<?php

namespace DMK\T3rest\Example;

use DMK\Mkunileipzig\AppApi\Transformer\CountryTransformer;
use SJBR\StaticInfoTables\Domain\Repository\CountryRepository;

/***************************************************************
 *  Copyright notice
 *
 * (c) DMK E-BUSINESS GmbH <dev@dmk-ebusiness.de>
 * All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 2 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

/**
 * Class CountriesProvider.
 *
 * @author  Hannes Bochmann
 * @license http://www.gnu.org/licenses/lgpl.html
 *          GNU Lesser General Public License, version 3 or later
 */
class HelloWorldProvider extends \Tx_T3rest_Provider_AbstractProvider
{
    public function prepareRouter(\Tx_T3rest_Router_InterfaceRouter $router): void
    {
        $route = $router->addRoute($router::METHOD_GET, '/hello-world', [$this, 'sayHello']);
        $this->getAuthIpRoutine()->prepareRoute($route);
        $this->getAuthFeUserRoutine()->prepareRoute($route);
    }

    public function sayHello(): \Tx_T3rest_Model_Supplier
    {
        $return = \Tx_T3rest_Utility_Factory::getSupplier();
            return $return->add('greeting', 'Hello World!');
    }
}
