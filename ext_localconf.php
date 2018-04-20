<?php
defined('TYPO3_MODE') || die('Access denied.');

require_once tx_rnbase_util_Extensions::extPath('rn_base', 'class.tx_rnbase.php');

//tx_rnbase::load('tx_t3rest_controller_Base');
$TYPO3_CONF_VARS['FE']['eID_include']['t3rest'] = 'EXT:t3rest/controller/class.tx_t3rest_controller_Base.php';

// Include services
require_once tx_rnbase_util_Extensions::extPath('t3rest', 'srv/ext_localconf.php');

// was called after db initialisation, direktly after eID
// and before ob_start compression handler
$TYPO3_CONF_VARS['SC_OPTIONS']['tslib/class.tslib_fe.php']['connectToDB']
    ['t3rest'] = 'EXT:t3rest/Classes/Hook/TsFe.php:&Tx_T3rest_Hook_TsFe->checkAndRunRestApi';

// preloading som classes
tx_rnbase::load('Tx_T3rest_Utility_Config');
tx_rnbase::load('Tx_T3rest_Utility_Factory');
