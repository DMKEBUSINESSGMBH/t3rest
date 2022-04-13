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

/**
 * @author Rene Nitzsche
 */
class tx_t3rest_decorator_Simple extends tx_t3rest_decorator_Base
{
    protected static $externals = [];
    private static $instance = null;

    /**
     * @overwrite
     */
    protected function getExternals()
    {
        return self::$externals;
    }

    protected function getDecoratorId()
    {
        return 'simple';
    }

    /**
     * @return tx_t3rest_decorator_Simple
     */
    public static function getInstance()
    {
        if (is_object(self::$instance)) {
            self::$instance == \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('tx_t3rest_decorator_Simple');
        }

        return self::$instance;
    }
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/t3rest/decorator/class.tx_t3rest_decorator_Simple.php']) {
    include_once $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/t3rest/decorator/class.tx_t3rest_decorator_Simple.php'];
}
