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
 * tsfe hooks.
 *
 * @author Michael Wagner
 */
class Tx_T3rest_Controller_Json extends Tx_T3rest_Controller_AbstractController
{
    /**
     * register the through routine, to transform the return value to json.
     *
     * @param Tx_T3rest_Router_InterfaceRouter $router
     *
     * @return void
     */
    protected function prepareRoutines(
        Tx_T3rest_Router_InterfaceRouter $router
    ) {
        parent::prepareRoutines($router);

        /* @var $throughJson Tx_T3rest_Routines_Through_Json */
        $throughJson = TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('Tx_T3rest_Routines_Through_Json');
        $throughJson->prepareRouter($router);
    }
}
