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

tx_rnbase::load('tx_t3rest_models_Provider');
tx_rnbase::load('tx_t3rest_provider_IProvider');

/**
 * Search logs
 *
 * @author Rene Nitzsche
 */
class tx_t3rest_search_Logs extends tx_rnbase_util_SearchBase
{
    protected function getTableMappings()
    {
        $tableMapping['LOGS'] = 'tx_t3rest_accesslog';

        // Hook to append other tables
        tx_rnbase_util_Misc::callHook(
            't3rest',
            'search_logs_getTableMapping_hook',
            array('tableMapping' => &$tableMapping),
            $this
        );

        return $tableMapping;
    }

    protected function useAlias()
    {
        return true;
    }

    protected function getBaseTableAlias()
    {
        return 'LOGS';
    }

    protected function getBaseTable()
    {
        return 'tx_t3rest_accesslog';
    }

    public function getWrapperClass()
    {
        return 'tx_t3rest_models_Generic';
    }

    protected function getJoins($tableAliases)
    {
        $join = '';

        // Hook to append other tables
        tx_rnbase_util_Misc::callHook(
            't3rest',
            'search_logs_getJoins_hook',
            array('join' => &$join, 'tableAliases' => $tableAliases),
            $this
        );

        return $join;
    }
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/t3rest/search/class.tx_t3rest_search_Logs.php']) {
    include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/t3rest/search/class.tx_t3rest_search_Logs.php']);
}
