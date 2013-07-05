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
tx_rnbase::load('tx_rnbase_util_Logger');
tx_rnbase::load('tx_rnbase_util_DB');
tx_rnbase::load('tx_t3rest_exception_DataNotFound');



/**
 * Frontcontroller for REST-API calls
 * 
 * @author Rene Nitzsche
 */
class tx_t3rest_controller_Base {
	/**
	 * Entry point for REST calls
	 *
	 * @return string JSON string
	 */
	public function execute() {
		$start = microtime(true);
		$startMem = memory_get_usage(true);
		$this->init();
		$initTime = microtime(true) - $start;
		$initMem = memory_get_usage(true);

//		tx_rnbase_util_Debug::debug($_COOKIE, 'class.tx_t3rest_controller_Base.php LINE: '.__LINE__); // TODO: remove me
		if(!$this->isAllowed()) {
			return '';
		}

		$data = array('100');
		try {
			$providerData = $this->getProviderData();
			// Für den Cache sind die TS-Config des Providers und die Parameter der URL relevant
			 
			$cacheHandler = $this->getCacheHandler($providerData->getConfigurations(), 'caching.');
			$data = $cacheHandler ? $cacheHandler->getOutput($providerData) : '';
			if(! (is_object($data) || is_array($data) )) {
				$provider = $this->getProvider($providerData);
				if($provider) {
					$data = $provider->execute($providerData);
				}
				if($cacheHandler && (is_object($data) || is_array($data)))
					$cacheHandler->setOutput($data, $providerData);
			}
		}
		catch(tx_t3rest_exception_DataNotFound $dnfe) {
			$data = tx_rnbase::makeInstance('tx_t3rest_models_Error', $dnfe->getMessage(), $dnfe->getCode());
		}
		catch(Exception $e) {
			$data = tx_rnbase::makeInstance('tx_t3rest_models_Error', $e->getMessage(), $e->getCode());
			tx_rnbase_util_Logger::fatal('Error for rest call!', 't3rest', array('Exception'=> $e->getMessage));
		}
		
		$response = $this->createResponse();
		$response->setData($data);
		$endMem = memory_get_usage(true);
		$response->addInfo('mem_used', ($endMem - $startMem));
		$response->addInfo('mem_end', $endMem);
		$response->addInfo('mem_init', $initMem);
		$time = (microtime(true) - $start);
		$response->addInfo('time', $time);
		$response->addInfo('inittime', $initTime);

		$this->logRequest($response, $time);
		header('Cache-Control: no-cache, must-revalidate');
		header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
		header('Content-type: application/json');
		echo json_encode($response);
	}
	private function logRequest($response, $time) {
		// TODO: Log-Dir in Extension-Config!!
		$dir = '/srv/www/vhosts/www.chemnitzerfc.de/logs/t3rest/';
		$filename =  strftime('access_%Y%m%d.log');
		$file = $dir.$filename;
		$data = array();
		$data[] = $_SERVER['REMOTE_ADDR'];
		$data[] = $_COOKIE['version'];
		$data[] = $_COOKIE['app'];
		$data[] = $time;
		$data[] = $_SERVER['HTTP_HOST'];
		$data[] = '-';
		$data[] = date('[d/M/Y:H:i:s O]', $_SERVER['REQUEST_TIME']);
		$data[] = '"'.$_SERVER['REQUEST_METHOD'] .' '. $_SERVER['REQUEST_URI'].' '. $_SERVER['SERVER_PROTOCOL'].' '. $_SERVER['REDIRECT_STATUS'].'"';
		$data[] = '"'.$_SERVER['HTTP_USER_AGENT'].'"';
		file_put_contents($file, implode("\t", $data)."\n", FILE_APPEND | LOCK_EX);

		// Nochmal in die DB
		$data = array();
		$data['ip'] = $_SERVER['REMOTE_ADDR'];
		$data['version'] = $_COOKIE['version'];
		$data['app'] = $_COOKIE['app'];
		$system = $_COOKIE['system'];
		$data['os'] = $this->getOS($system, $_SERVER['HTTP_USER_AGENT']);
		$data['system'] = $system;
		$data['sysver'] = $_COOKIE['sysver'];
		$data['runtime'] = round($time, 4);
		$data['host'] = $_SERVER['HTTP_HOST'];
		$data['tstamp'] = date('Y-m-d H:i:s', $_SERVER['REQUEST_TIME']);
		$data['method'] = $_SERVER['REQUEST_METHOD'];
		$data['uri'] = $_SERVER['REQUEST_URI'];
		$data['status'] =  $_SERVER['REDIRECT_STATUS'];
		$data['useragent'] = $_SERVER['HTTP_USER_AGENT'];
		if($data['os'] == 'IOS')
			$this->correctIOS($data);

		tx_rnbase_util_DB::doInsert('tx_t3rest_accesslog', $data);
//		file_put_contents($file, var_export($_COOKIE,true) ."\n", FILE_APPEND | LOCK_EX);
	}
	/**
	 * Korrektur der sinnlosen Daten vom IPhone...
	 */
	private function correctIOS(&$data) {
		if($data['system'])
			return; // Vermutlich korrekte neue Version
		// Die OS Version setzen
		$data['system'] = 'IOS ' . $data['version'];
		// $data['sysver'] = ''; // Zur Hardware läßt sich nichts erkennen
		//Appversion setzen
		$data['version'] = '1.0';
	}
	private function getOS($system, $userAgent) {
		$os = '';
		if(strpos(strtolower($system), 'android') !== FALSE)
			$os = 'ANDROID';
		elseif(strpos(strtolower($userAgent), 'darwin') !== FALSE)
			$os = 'IOS';
		elseif(strpos(strtolower($system), 'windows') !== FALSE)
			$os = 'WINDOWS';
		return $os;
	}
	/**
	 * 
	 * @param tx_t3rest_models_Provider $provData
	 * @return tx_t3rest_provider_IProvider
	 */
	protected function getProvider($provData) {
		if(!$provData) return null;
		$classname = $provData->getClassname();
		return tx_rnbase::makeInstance($provData->getClassname());
	}
	/**
	 * @return tx_t3rest_models_Provider
	 */
	protected function getProviderData() {
		$action = $this->getParameters()->get('action');
		if(!$action) return null;

		$options['wrapperclass'] = 'tx_t3rest_models_Provider';
		$options['where'] = 'restkey = \'' . $GLOBALS['TYPO3_DB']->quoteStr($action,'tx_t3rest_providers') . '\'';
		$ret = tx_rnbase_util_DB::doSelect('tx_t3rest_providers.*', 'tx_t3rest_providers', $options);
		if(empty($ret)) return null;
		$providerData = $ret[0];
		$this->initProviderData($providerData);
		return $providerData;
	}
	/**
	 * Configuration initialisieren
	 * @param tx_t3rest_models_Provider $providerData
	 */
	protected function initProviderData($providerData) {
		$ts = $providerData->getConfig();
		// This handles ts setup from flexform
		$tsParser = t3lib_div::makeInstance('t3lib_TSparser');
//		$tsParser->setup = $this->_dataStore->getArrayCopy();
		// Man muss vorher selbst nach Includes suchen. Typisch TYPO3... :-/
		$ts = $tsParser->checkIncludeLines($ts);
		$tsParser->parse($ts);
		$configArr = $tsParser->setup;
		tx_rnbase::load('tx_rnbase_configurations');
		$config = new tx_rnbase_configurations();
		$config->init($configArr, false, 't3rest', 't3rest');
		$config->setParameters($this->getParameters());
		$providerData->setConfigurations($config);
	}
	/**
	 * @return tx_t3rest_models_Response
	 */
	protected function createResponse() {
		return tx_rnbase::makeInstance('tx_t3rest_models_Response');
	}
	protected function init() {
		tslib_eidtools::connectDB(); //Connect to database
		tslib_eidtools::initTCA();
		tx_rnbase_util_Misc::prepareTSFE();
		//		$feUserObj = tslib_eidtools::initFeUser(); // Initialize FE user object    
	}

	/**
	 * @return tx_rnbase_IParameters
	 */
	protected function getParameters() {
		if(!is_object($this->parameters)) {
			$this->parameters = tx_rnbase::makeInstance('tx_rnbase_parameters');
			$this->parameters->init('t3rest');
		}
		return $this->parameters;
	}


	protected function isAllowed() {
		if(isset($_GET['test']))
			return true;
		if(!isset($_COOKIE['version']) || trim($_COOKIE['version']) == '')
			return false;
		if(!isset($_COOKIE['app']) || trim($_COOKIE['app']) == '')
			return false;
		return true;
	}


	/**
	 * Find a configured cache handler.
	 * 
	 * @param tx_rnbase_configurations $configurations
	 * @param string $confId
	 * @return tx_t3rest_cache_CacheHandlerDefault
	 */
	protected function getCacheHandler($configurations, $confId) {
		$clazz = $configurations->get($confId.'enable');
		if(!$clazz) return false;
		$handler = tx_rnbase::makeInstance('tx_t3rest_cache_CacheHandlerDefault', $configurations, $confId);
		return $handler;
	}
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/t3rest/controller/class.tx_t3rest_controller_Base.php']) {
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/t3rest/controller/class.tx_t3rest_controller_Base.php']);
}


tx_rnbase::makeInstance('tx_t3rest_controller_Base')->execute();
