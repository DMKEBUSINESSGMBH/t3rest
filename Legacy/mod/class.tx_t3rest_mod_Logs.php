<?php

/**
 * Backend Modul für Auswertung der Logs
 * Sinnvolle Submodule wären die Listenansicht und dann vermutlich paar Statistiken.
 *
 * @author René Nitzsche
 */
class tx_t3rest_mod_Logs extends \Sys25\RnBase\Backend\Module\ExtendedModFunc
{
    /**
     * Method getFuncId.
     *
     * @return  string
     */
    public function getFuncId()
    {
        return 'logs';
    }

    /**
     * It is possible to overwrite this method and return an array of tab functions.
     *
     * @return array
     */
    protected function getSubMenuItems()
    {
        $menuItems = [];
        $menuItems[] = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('tx_t3rest_mod_handler_Overview');
        $menuItems[] = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('tx_t3rest_mod_handler_LogList');
        \Sys25\RnBase\Utility\Misc::callHook(
            't3rest',
            'modLogs_tabItems',
            ['tabItems' => &$menuItems],
            $this
        );

        return $menuItems;
    }

    /**
     * Liefert false, wenn es keine SubSelectors gibt. sonst ein Array mit den ausgewählten Werten.
     *
     * @param string $selectorStr
     *
     * @return array or false if not needed. Return empty array if no item found
     */
    protected function makeSubSelectors(&$selectorStr)
    {
        return false;
    }
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/t3rest/mod/class.tx_t3rest_mod_Logs.php']) {
    include_once $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/t3rest/mod/class.tx_t3rest_mod_Logs.php'];
}
