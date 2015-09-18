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


/**
 * Utilty methods for DAM
 * 
 * @author Rene Nitzsche
 */
class tx_t3rest_util_DAM {

	public static function getDamPictures($refUid, $refTable, $refField, $configurations, $confId, $fields=array()) {
		$picCfg = $configurations->getKeyNames($confId);
		if(empty($fields))
			$fields = array('uid','title','file_hash', 'tstamp');
		tx_rnbase::load('tx_rnbase_util_TSDAM');
		$ret = array();
		$files = tx_rnbase_util_TSDAM::fetchFiles($refTable, $refUid, $refField);
		foreach ($files['rows'] As $uid => $record) {
			$ret[] = self::convertDAM2StdClass($record, $configurations, $confId, $picCfg, $fields);
		}
		return $ret;
	}
	public static function convertDAM2StdClass($record, $configurations, $confId, $picCfg, $fields=array()) {
		if(empty($fields))
			$fields = array('uid','title','file_hash', 'tstamp');
		$data = new stdClass();
		$filepath = $record['file_path'].$record['file_name'];
		$data->filepath = $filepath;
		$server = t3lib_div::getIndpEnv('TYPO3_SITE_URL');
		$data->absFilepath = $server.$filepath;
		$record['file'] = $filepath;
		// Bild skalieren
		$cObj = $configurations->getCObj(1);
		$cObj->data = $record;
		foreach($picCfg As $picName) {
			$data->$picName = $cObj->cObjGetSingle($configurations->get($confId.$picName),$configurations->get($confId.$picName.'.'));
			if($data->$picName)
				$data->$picName = $server . $data->$picName;
		}
		foreach($fields As $fieldName) {
			$data->$fieldName = $record[$fieldName];
		}
		return $data;
	}
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/t3rest/search/class.tx_t3rest_util_DAM.php']) {
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/t3rest/search/class.tx_t3rest_util_DAM.php']);
}
