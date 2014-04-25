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
tx_rnbase::load('tx_t3rest_util_Objects');

/**
 * Sammelt zusätzliche Daten
 * 
 * @author Rene Nitzsche
 */
abstract class tx_t3rest_decorator_Base {

	public function prepareItem($item, $configurations, $confId) {
		$this->handleItemBefore($item, $configurations, $confId);
		$this->wrapRecord($item, $configurations, $confId.'record.');
		$this->loadExternal($item, $configurations, $confId);
		$this->handleItemAfter($item, $configurations, $confId);
		$data = tx_t3rest_util_Objects::record2StdClass($item, $this->getIgnoreFields($configurations, $confId));
		return $data;
	}
	protected function wrapRecord($item, $configurations, $confId) {
		$cObj = $configurations->getCObj();
		$tmpArr = $cObj->data;
		$conf = $configurations->get($confId);
		if($conf) {
			// Add dynamic columns
			$keys = $configurations->getUniqueKeysNames($conf);
			foreach($keys As $key) {
				if(t3lib_div::isFirstPartOfStr($key, 'dc') && !isset($record[$key]))
					$item->record[$key] = $conf[$key];
			}
		}
		$cObj->data = $record;
		foreach($item->record As $colname =>$value){
			if($conf[$colname]) {
				// Get value using cObjGetSingle
				$cObj->setCurrentVal($value);
				$item->record[$colname] = $cObj->cObjGetSingle($conf[$colname],$conf[$colname.'.']);
				$cObj->setCurrentVal(false);
			}
			else {
				$item->record[$colname] = $cObj->stdWrap($value, $conf[$colname.'.']);
			}
		}
	}
	/**
	 * Daten aus anderen Tabellen nachladen
	 *
	 * @param array $item
	 * @param tx_rnbase_configurations $configurations
	 * @param string $confId
	 */
	public function loadExternal($item, $configurations, $confId) {
		$known = $this->getExternals();
		// TODO: Das sollte abschaltbar sein...
		$paramExternals = $configurations->getParameters()->get('externals');
		$externals = array();
		if(is_array($paramExternals) && array_key_exists($this->getDecoratorId(), $paramExternals))
			$externals = t3lib_div::trimExplode(',', $paramExternals[$this->getDecoratorId()]);
		$externals = array_unique(array_merge($externals, t3lib_div::trimExplode(',', $configurations->get($confId.'record.externals'))));
		foreach($externals As $external) {
			if(!in_array($external, $known)) continue;
			$methodName = 'add'.ucwords($external);
			if(method_exists($this,$methodName)) {
				$this->$methodName($item, $configurations, $confId.'record.externals.'.$external.'.');
			}
			else {
				if(!in_array($methodName, self::$warned)) {
					tx_rnbase_util_Logger::warn('Method not found: ' . $methodName, 't3srest');
					self::$warned[] = $methodName;
				}
			}
		}
	}
	private static $warned = array();

	protected function handleItemBefore($item, $configurations, $confId) {
		
	}
	protected function handleItemAfter($items, $configurations, $confId) {
		
	}
	protected function getIgnoreFields($configurations, $confId) {
		$ignoreFields = $configurations->get($confId.'record.ignoreFields');
		$ignoreFields = $ignoreFields ? t3lib_div::trimExplode(',', $ignoreFields) : array();
		return array_merge($ignoreFields, tx_t3rest_util_Objects::getIgnoreFields());
	}
	/**
	 * Return the known externals. Each external must have an implementing method!
	 * @return array
	 */
	abstract protected function getExternals();
	/**
	 * Return the the id string of a concrete decorator
	 * @return string
	 */
	abstract protected function getDecoratorId();
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/t3rest/decorator/class.tx_t3rest_decorator_Base.php']) {
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/t3rest/decorator/class.tx_t3rest_decorator_Base.php']);
}
