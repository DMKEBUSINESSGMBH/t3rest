<?php
/**
 *
 *  Copyright notice
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
 * benötigte Klassen einbinden
 */

unset($MCONF);
require_once('conf.php');
require_once($BACK_PATH.'init.php');
require_once($BACK_PATH.'template.php');

require_once tx_rnbase_util_Extensions::extPath('rn_base', 'class.tx_rnbase.php');

$LANG->includeLLFile('EXT:t3rest/mod/locallang.xml');
$BE_USER->modAccess($MCONF,1);	// This checks permissions and exits if the users has no permission for entry.
	// DEFAULT initialization of a module [END]

tx_rnbase::load('tx_rnbase_configurations');
tx_rnbase::load('tx_rnbase_mod_BaseModule');

/**
 * Backend Modul für t3rest
 *
 * @author René Nitzsche
 * @package tx_t3rest
 * @subpackage tx_t3rest_mod1
 */
class  tx_t3rest_mod_Module extends tx_rnbase_mod_BaseModule {

	/**
	 * Method to get the extension key
	 *
	 * @return	string Extension key
	 */
	public function getExtensionKey() {
		return 't3rest';
	}

//	public function getBasePid() {
//		return tx_rnbase_configurations::getExtensionCfgValue('t3rest', 'memberPID');
//	}

	/**
	 * Method to set the tabs for the mainmenu
	 * Umstellung von SelectBox auf Menu
	 */
	protected function getFuncMenu() {
		$mainmenu = $this->getFormTool()->showTabMenu($this->getPid(), 'function', $this->getName(), $this->MOD_MENU['function']);
		return $mainmenu['menu'];
	}
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/t3rest/mod/index.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/t3rest/mod/index.php']);
}

// Make instance:
$SOBE = t3lib_div::makeInstance('tx_t3rest_mod_Module');
$SOBE->init();

// Include files?
foreach($SOBE->include_once as $INC_FILE)	include_once($INC_FILE);

$SOBE->main();
$SOBE->printContent();

