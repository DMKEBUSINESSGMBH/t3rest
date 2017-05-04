<?php
/**
 * @package tx_t3rest
 * @subpackage tx_t3rest_mod1
 *
 *  Copyright notice
 *
 *  (c) 2011 das MedienKombinat GmbH <kontakt@das-medienkombinat.de>
 *  All rights reserved
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
 */

tx_rnbase::load('tx_rnbase_mod_ExtendedModFunc');

/**
 * Backend Modul für Auswertung der Logs
 * Sinnvolle Submodule wären die Listenansicht und dann vermutlich paar Statistiken
 *
 * @author René Nitzsche
 * @package tx_t3rest
 * @subpackage tx_t3rest_mod1
 */
class tx_t3rest_mod_Logs extends tx_rnbase_mod_ExtendedModFunc
{

    /**
     * Method getFuncId
     *
     * @return  string
     */
    public function getFuncId()
    {
        return 'logs';
    }
    /**
     * It is possible to overwrite this method and return an array of tab functions
     * @return array
     */
    protected function getSubMenuItems()
    {
        $menuItems = array();
        $menuItems[] = tx_rnbase::makeInstance('tx_t3rest_mod_handler_Overview');
        $menuItems[] = tx_rnbase::makeInstance('tx_t3rest_mod_handler_LogList');
        tx_rnbase_util_Misc::callHook(
            't3rest',
            'modLogs_tabItems',
            array('tabItems' => &$menuItems),
            $this
        );

        return $menuItems;
    }

    /**
     * Liefert false, wenn es keine SubSelectors gibt. sonst ein Array mit den ausgewählten Werten.
     * @param string $selectorStr
     * @return array or false if not needed. Return empty array if no item found
     */
    protected function makeSubSelectors(&$selectorStr)
    {
        return false;
    }
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/t3rest/mod/class.tx_t3rest_mod_Logs.php']) {
    include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/t3rest/mod/class.tx_t3rest_mod_Logs.php']);
}
