<?php
/**
 * Backend Modul
 */

// DO NOT REMOVE OR CHANGE THESE 2 LINES:
define('TYPO3_MOD_PATH', '../typo3conf/ext/t3rest/mod/');
$BACK_PATH='../../../../typo3/';
$MCONF['name'] = 'user_txt3restM1';
$MCONF['script']='index.php';
	
$MCONF['access'] = 'user,group';

$MLANG['default']['tabs_images']['tab'] = 'moduleicon.gif';
$MLANG['default']['ll_ref'] = 'LLL:EXT:t3rest/mod/locallang.xml';

define('ICON_OK', -1);
define('ICON_INFO', 1);
define('ICON_WARN', 2);
define('ICON_FATAL', 3);

