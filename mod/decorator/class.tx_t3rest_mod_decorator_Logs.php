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

tx_rnbase::load('tx_rnbase_mod_IModule');

/**
 *
 * @author René Nitzsche
 */
class tx_t3rest_mod_decorator_Logs {
	public function __construct(tx_rnbase_mod_IModule $mod) {
		$this->mod = $mod;
	}

	public function format($value,$columnName, $record, $item) {
		$ret = $value;
		switch ($columnName) {
			case 'actions':
				$ret = $this->getActions($item, array('edit' => '','hide' => '','remove' => '',));
			break;
			case 'address':
				$ret = $record['street'];
			break;
			case 'city':
				$ret = $record['zip'] . ' ' .$record['city'];
			break;
			case 'lastname':
				$ret = $record['lastname'] . ', ' . $record['firstname'];
				if($record['honorary'])
					$ret .= ' <a href="javascript:void(0);" title="###LABEL_HONORARY###">*</a>';
				if($record['hidden'])
					$ret = '<strike>'.$ret.'</strike>';
			break;
		}
		return $ret;
	}

	/**
	 * @TODO: Das alles über die Linker realisieren!!
	 * $options = array('hide'=>'ausblenden,'edit'=>'bearbeiten,'remove'=>'löschen','history'='history','info'=>'info','move'=>'verschieben');
	 *
	 * @param 	tx_rnbase_model_base 	$item
	 * @param 	array 					$options
	 * @return 	string
	 */
	protected function getActions(tx_rnbase_model_base $item, array $options) {
		$ret = '';
		foreach($options as $sLinkId => $bTitle){
			switch($sLinkId) {
				case 'edit':
					$ret .= $this->getFormTool()->createEditLink($item->getTableName(), $item->getUid(), $bTitle);
					break;
				case 'hide':
					$ret .= $this->getFormTool()->createHideLink($item->getTableName(), $item->getUid(), $item->record['hidden']);
					break;
				case 'remove':
					//Es wird immer ein Bestätigungsdialog ausgegeben!!! Dieser steht
					//in der BE-Modul locallang.xml der jeweiligen Extension im Schlüssel
					//'confirmation_deletion'. (z.B. mkkvbb/mod1/locallang.xml) Soll kein
					//Bestätigungsdialog ausgegeben werden, dann einfach 'confirmation_deletion' leer lassen
					$ret .= $this->getFormTool()->createDeleteLink($item->getTableName(), $item->getUid(), $bTitle,array('confirm' => $GLOBALS['LANG']->getLL('msg_confirmation_deletion')));
					break;
				default:
					break;
			}
		}
		return $ret;
	}

	/**
	 * Returns the module
	 * @return tx_rnbase_mod_IModule
	 */
	private function getModule() {
		return $this->mod;
	}
	private function getFormTool() {
		return $this->getModule()->getFormTool();
	}
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/t3rest/mod1/decorator/class.tx_t3rest_mod1_decorator_Logs.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/t3rest/mod1/decorator/class.tx_t3rest_mod1_decorator_Logs.php']);
}
