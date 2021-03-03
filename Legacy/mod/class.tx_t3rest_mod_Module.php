<?php
/**
 *  Copyright notice.
 *
 *  (c) 2012 René Nitzsche <rene@system25.de>
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

/**
 * Backend Modul für t3rest.
 *
 * @author René Nitzsche
 */
class tx_t3rest_mod_Module extends tx_rnbase_mod_BaseModule
{
    /**
     * Initializes the backend module by setting internal variables, initializing the menu.
     *
     * @return void
     */
    public function init()
    {
        parent::init();
        $GLOBALS['LANG']->includeLLFile('EXT:t3rest/Legacy/mod/locallang.xml');
        $GLOBALS['BE_USER']->modAccess($this->MCONF, 1);
    }

    /**
     * Method to get the extension key.
     *
     * @return string Extension key
     */
    public function getExtensionKey()
    {
        return 't3rest';
    }

    /**
     * Method to set the tabs for the mainmenu
     * Umstellung von SelectBox auf Menu.
     */
    protected function getFuncMenu()
    {
        $mainmenu = $this->getFormTool()->showTabMenu($this->getPid(), 'function', $this->getName(), $this->MOD_MENU['function']);

        return $mainmenu['menu'];
    }
}
