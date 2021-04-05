<?php
/**
 *  Copyright notice.
 *
 *  (c) 2012 René Nitzsche <rene@system25.de>
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
 */

/**
 * @author René Nitzsche
 */
class tx_t3rest_mod_lister_Logs extends tx_rnbase_mod_base_Lister
{
    /**
     * Liefert die Funktions-Id.
     */
    public function getSearcherId()
    {
        return 'logs';
    }

    /**
     * Liefert den Service.
     *
     * @return tx_t3rest_srv_Logs
     */
    protected function getService()
    {
        return tx_t3rest_util_ServiceRegistry::getLogsService();
    }

    /**
     * Liefert die Spalten für den Decorator.
     *
     * @return  array
     */
    protected function getColumns($defaultDecorator)
    {
        return [
            'uid' => [
                'title' => '###LABEL_CLUBID###',
                'width' => 90,
            ],
            'tstamp' => [
                'title' => '###LABEL_TSTAMP###',
                'decorator' => $defaultDecorator,
            ],
            'runtime' => [
                'title' => '###LABEL_RUNTIME###',
                'decorator' => $defaultDecorator,
            ],
            'version' => [
                'title' => '###LABEL_VERSION###',
                'decorator' => $defaultDecorator,
            ],
            'system' => [
                'title' => '###LABEL_SYSTEM###',
                'decorator' => $defaultDecorator,
            ],
            'sysver' => [
                'title' => '###LABEL_SYSVER###',
                'decorator' => $defaultDecorator,
            ],
            'app' => [
                'title' => '###LABEL_APP###',
                'decorator' => $defaultDecorator,
            ],
            'uri' => [
                'title' => '###LABEL_URI###',
                'decorator' => $defaultDecorator,
            ],
            'useragent' => [
                'title' => '###LABEL_USERAGENT###',
                'decorator' => $defaultDecorator,
            ],
            'linker' => [$this],
        ];
    }

    public function makeLink($item, $formTool, $currentPid, $options)
    {
        return $ret;
    }

    protected function createDefaultDecorator()
    {
        return tx_rnbase::makeInstance('tx_t3rest_mod_decorator_Logs', $this->getModule());
    }

    /**
     * (non-PHPdoc).
     *
     * @see tx_rnbase_mod_base_Searcher::getSearchColumns()
     */
    protected function getSearchColumns()
    {
        return [
            'LOGS.UID', 'LOGS.VERSION', 'LOGS.SYSTEM', 'LOGS.APP',
        ];
    }

    protected function prepareFieldsAndOptions(array &$fields, array &$options)
    {
        parent::prepareFieldsAndOptions($fields, $options);
        // Es gibt hier keine TCA
        $options['limit'] = 50000;
        $options['enablefieldsoff'] = 1;
        $options['orderby']['LOGS.UID'] = 'desc';
    }
}
