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

tx_rnbase::load('tx_rnbase_mod_IModHandler');
tx_rnbase::load('tx_rnbase_util_DB');

class tx_t3rest_mod_handler_LogList implements tx_rnbase_mod_IModHandler
{
    private $data = [];
    private $warnings = [];

    public function getSubID()
    {
        return 'loglist';
    }

    public function getSubLabel()
    {
        return '###LABEL_TAB_LOGLIST###';
    }

    /**
     * @return tx_t3rest_mod_handler_LogList
     */
    public static function getInstance()
    {
        return tx_rnbase::makeInstance('tx_t3rest_mod_handler_LogList');
    }

    /**
     * Maximal 120 Zeichen plus $url
     * Ohne URL maximal 140 Zeichen.
     *
     * @param tx_rnbase_mod_IModule $mod
     */
    public function handleRequest(tx_rnbase_mod_IModule $mod)
    {
        $submitted = tx_rnbase_parameters::getPostOrGetParameter('sendmsg');
        if (!$submitted) {
            return '';
        }

        $this->data = tx_rnbase_parameters::getPostOrGetParameter('data');

        $mod->addMessage('###LABEL_MESSAGE_SENT###', 'Hinweis', 0);
    }

    public function showScreen($template, tx_rnbase_mod_IModule $mod, $options)
    {
        $formTool = $mod->getFormTool();
        $options = [];

        $markerArr = [];
        $subpartArr = [];
        $wrappedSubpartArr = [];

        $searcher = tx_rnbase::makeInstance('tx_t3rest_mod_lister_Logs', $mod, $options);
        $markerArr['###SEARCHFORM###'] = $searcher->getSearchForm();
        $list = $searcher->getResultList();

        $markerArr['###LIST###'] = $list['table'];
        $markerArr['###PAGER###'] = $list['pager'];
        $markerArr['###TOTALSIZE###'] = $list['totalsize'];

        $out = tx_rnbase_util_Templates::substituteMarkerArrayCached($template, $markerArr, $subpartArr, $wrappedSubpartArr);

        return $out;
    }
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/t3rest/mod/handler/class.tx_t3rest_mod_handler_LogList.php']) {
    include_once $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/t3rest/mod/handler/class.tx_t3rest_mod_handler_LogList.php'];
}
