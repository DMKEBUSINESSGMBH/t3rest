<?php
if (!defined ('TYPO3_MODE')) {
  die ('Access denied.');
}
if (TYPO3_MODE == 'BE') {
	// Einbindung einer PageTSConfig
	t3lib_extMgm::addPageTSConfig('<INCLUDE_TYPOSCRIPT: source="FILE:EXT:'.$_EXTKEY.'/mod/pageTSconfig.txt">');

	t3lib_extMgm::addModule('user', 'txt3restM1', '', t3lib_extMgm::extPath($_EXTKEY) . 'mod/');
	// Auswertung der Logs
	t3lib_extMgm::insertModuleFunction('user_txt3restM1','tx_t3rest_mod_Logs',
		t3lib_extMgm::extPath($_EXTKEY).'mod/class.tx_t3rest_mod_Logs.php',
		'LLL:EXT:t3rest/mod/locallang.xml:label_t3rest_mod_logs'
	);
	// TODO: Schaltung von Werbung

}
