<?php
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2012 Rene Nitzsche
 *  Contact: rene@system25.de
 *  All rights reserved
 *
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 2.1 of the License, or (at your option) any later version.
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with this library; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 ***************************************************************/

require_once(t3lib_extMgm::extPath('rn_base') . 'class.tx_rnbase.php');

tx_rnbase::load('tx_rnbase_model_base');

/**
 * Frontcontroller for REST-API calls
 * 
 * @author Rene Nitzsche
 */
class tx_t3rest_models_Provider extends tx_rnbase_model_base {
	private $configurations;
	/**
	 * @return String Tabellenname
	 */
	function getTableName() {
		return 'tx_t3rest_providers';
	}
	/**
	 * @param tx_rnbase_configurations $config
	 */
	public function setConfigurations($config) {
		$this->configurations = $config;
	}
	/**
	 * @return tx_rnbase_configurations
	 */
	public function getConfigurations() {
		return $this->configurations;
	}
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/t3rest/controller/class.tx_t3rest_models_Provider.php']) {
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/t3rest/controller/class.tx_t3rest_models_Provider.php']);
}
