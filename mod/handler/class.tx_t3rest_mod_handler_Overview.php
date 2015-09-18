<?php
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2012 Rene Nitzsche (rene@system25.de)
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
 ***************************************************************/

require_once(t3lib_extMgm::extPath('rn_base') . 'class.tx_rnbase.php');
tx_rnbase::load('tx_rnbase_mod_IModHandler');
tx_rnbase::load('tx_rnbase_util_DB');
tx_rnbase::load('tx_rnbase_mod_Util');
tx_rnbase::load('tx_rnbase_util_Dates');


/**
 * Darstellung von Kennzahlen
 * @author digedag
 */
class tx_t3rest_mod_handler_Overview implements tx_rnbase_mod_IModHandler {

	private $data = array();
	private $warnings = array();

	public function getSubID() {
		return 'overview';
	}
	public function getSubLabel() {
		return '###LABEL_TAB_OVERVIEW###';
	}

	/**
	 * @return tx_t3rest_mod_handler_LogList
	 */
	public static function getInstance() {
		return tx_rnbase::makeInstance('tx_t3rest_mod_handler_LogList');
	}
	/**
	 * Maximal 120 Zeichen plus $url
	 * Ohne URL maximal 140 Zeichen
	 * @param tx_rnbase_mod_IModule $mod
	 */
	public function handleRequest(tx_rnbase_mod_IModule $mod) {
		$submitted = t3lib_div::_GP('change');
		if(!$submitted) return '';

		$this->data = t3lib_div::_GP('data');

		// Daten übernehmen
		$options['type'] = 'ses';
		$options['changed']['timespan'] = $this->data['timespan'];
		$this->data['timespan'] = unserialize(tx_rnbase_mod_Util::getModuleValue('timespan', $mod, $options));
		//		$mod->addMessage('###LABEL_MESSAGE_SENT###', 'Hinweis', 0);
	}


	public function showScreen($template, tx_rnbase_mod_IModule $mod, $options) {

		$formTool = $mod->getFormTool();
		$options = array();

		$markerArr = array();
		$subpartArr = array();
		$wrappedSubpartArr = array();

		if(!isset($this->data['timespan']))
			$this->data['timespan'] = unserialize(tx_rnbase_mod_Util::getModuleValue('timespan', $mod, $options));

		$logSrv = tx_t3rest_util_ServiceRegistry::getLogsService();

		$installs = $logSrv->getStatsApps('OS_ALL');
		foreach($installs As $os => $install) {
			$markerArr['###STATS_'.strtoupper($os).'_INSTALLS###'] = $install;
		}

		$period = isset($this->data['timespan']['period']) ?
					$this->data['timespan']['period'] : 0;

		$periods = array('day', 'week', 'month', 'year');
//		t3lib_div::debug($period, 'tx_t3rest_mod_handler_Overview Line: '.__LINE__); // TODO: remove me

		$markerArr['###SELECT_PERIOD###'] = $formTool->createSelectSingleByArray('data[timespan][period]', $period, array('0'=>'Tag','1'=>'Woche', '2'=>'Monat', '3'=>'Year'));

		$from = isset($this->data['timespan']['dfFrom']) ?
					$this->data['timespan']['dfFrom'] : $this->getDefaultFrom();
		$to = isset($this->data['timespan']['dfTo']) ?
					$this->data['timespan']['dfTo'] :
					tx_rnbase_util_Dates::getTodayDateString('d.m.Y');
		$markerArr['###DATE_FROM###'] = $from;
		$markerArr['###DATE_TO###'] = $to;
		// Jetzt die Logs holen
		$from = $this->str2Mysql($from);
		$to = $this->str2Mysql($to);

		$markerArr['###LOGS_REQUESTS###'] = json_encode(array_values( $this->findRequestData($from, $to, $periods[$period])));
		$markerArr['###LOGS_INSTALLS###'] = json_encode(array_values( $this->findInstallsData($from, $to, $periods[$period])));

		$out = tx_rnbase_util_Templates::substituteMarkerArrayCached($template, $markerArr, $subpartArr, $wrappedSubpartArr);

		return $out;
	}
	/**
	 * Sammelt die Requests im Zeitraum
	 * @param string $from MySQL-Datum
	 * @param string $to
	 */
	private function findRequestData($from, $to, $period) {
		$logSrv = tx_t3rest_util_ServiceRegistry::getLogsService();
		$reqLogs = array();
		// Gesamt-Daten
		$filter =array('system'=>'', 'from'=>$from, 'to'=>$to);
		$filter['period'] = $period;
		$filter['system'] = 'OS_ALL';
		$logs = $logSrv->getStatsAccessByFilter($filter);
		$this->addSeriesOS($reqLogs, $logs);

		return $reqLogs;
	}
	/**
	 * Sammelt die unterschiedlichen Devices im Zeitraum
	 * @param string $from MySQL-Datum
	 * @param string $to
	 */
	private function findInstallsData($from, $to, $period) {
		$logSrv = tx_t3rest_util_ServiceRegistry::getLogsService();
		$reqLogs = array();
		$filter =array('system'=>'', 'from'=>$from, 'to'=>$to);
		$filter['period'] = $period;
		// Gesamt-Daten
//		$logs = $logSrv->getStatsInstallsByFilter($filter);
//		$this->addSeries($reqLogs, $logs, 'installs');
//		$filter['system'] = 'ios';
//		$logs = $logSrv->getStatsInstallsByFilter($filter);
//		$this->addSeries($reqLogs, $logs, 'installsIOS');
//		$filter['system'] = 'android';
//		$logs = $logSrv->getStatsInstallsByFilter($filter);
//		$this->addSeries($reqLogs, $logs, 'installsAndroid');

		$filter['system'] = 'OS_ALL';
		$logs = $logSrv->getStatsInstallsByFilter($filter);
		$this->addSeriesOS($reqLogs, $logs);

		return $reqLogs;
	}
	private function addSeries(&$reqLogs, $logs, $name) {
		for($i=0, $cnt = count($logs); $i<$cnt; $i++) {
			$reqLogs[$i]['day'] = $logs[$i]['day'];
			$reqLogs[$i][$name] = intval($logs[$i]['value']);
		}
	}

	/**
	 * Zielformat für Flot:
	 * 		{ label: "Android",  data: [[1427410800000,4],[1427497200000,3],[1427587200000,10]]},
	 * @param array $reqLogs
	 * @param array $logs
	 */
	private function addSeriesOS(&$reqLogs, $logs) {
		$dayIdx = -1;
		$lastDay = '';
		$daySum = 0;
//tx_rnbase_util_Debug::debug($logs,__FILE__.':'.__LINE__); // TODO: remove me
		$reqLogs['SUM'] = array('label'=>'Gesamt','data'=>array());
		for($i=0, $cnt = count($logs); $i<$cnt; $i++) {
			$os = $logs[$i]['os'];
			$day = $logs[$i]['day'];
			if(!array_key_exists($os, $reqLogs))
				$reqLogs[$os] = array('label'=>$os, 'data'=>array());
			// Daten für das OS eintragen
			$reqLogs[$os]['data'][] = array(
				tx_rnbase_util_Dates::date_mysql2tstamp($day) * 1000,
				intval($logs[$i]['value'])
			);
//tx_rnbase_util_Debug::debug(['day'=>$day, 'lastDay'=>$lastDay, 'Summe'=>$daySum, 'data'=>$reqLogs], 'Wechsel: ' . ($day != $lastDay ? 'Ja' : 'Nein') .' - ' . $i.' - ' . __FUNCTION__.':'.__LINE__); // TODO: remove me
			// Tageswechsel für Summe prüfen
			if(($day != $lastDay && $lastDay != '') || $i+1 == $cnt) {
				// Ein neuer Tag beginnt
				// Summe setzen
				$reqLogs['SUM']['data'][] = array(
						tx_rnbase_util_Dates::date_mysql2tstamp($lastDay) * 1000,
						$daySum
				);
				$daySum = 0;
			}
			else {
				// das ist noch der selbe Tag
			}
			$daySum += intval($logs[$i]['value']);
			$lastDay = $day;
		}
	}

	private function str2Mysql($date) {
		return tx_rnbase_util_Dates::date_tstamp2mysql(strtotime($date));
	}
	private function getDefaultFrom() {
		$datum = tx_rnbase_util_Dates::date_addIntDays(tx_rnbase_util_Dates::getTodayDateString('Ymd'),-7);
		return  substr($datum,6,2). '.'. substr($datum,4,2) .'.'.substr($datum,0,4);
	}
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/t3rest/mod/handler/class.tx_t3rest_mod_handler_Overview.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/t3rest/mod/handler/class.tx_t3rest_mod_handler_Overview.php']);
}
?>