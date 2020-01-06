<?php
/**
 * Copyright notice.
 *
 * (c) 2015 DMK E-Business GmbH <dev@dmk-ebusiness.de>
 * All rights reserved
 *
 * This script is part of the TYPO3 project. The TYPO3 project is
 * free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * The GNU General Public License can be found at
 * http://www.gnu.org/copyleft/gpl.html.
 *
 * This script is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * This copyright notice MUST APPEAR in all copies of the script!
 */

/**
 * extension configs.
 *
 * @author Michael Wagner
 */
final class Tx_T3rest_Utility_Config
{
    /**
     * reads the extension config.
     *
     * @param string $key
     *
     * @return mixed
     */
    private static function getExtConf($key)
    {
        static $config = [];
        if (!isset($config[$key])) {
            tx_rnbase::load('tx_rnbase_configurations');
            $config[$key] = tx_rnbase_configurations::getExtensionCfgValue(
                't3rest',
                $key
            );
        }

        return $config[$key];
    }

    /**
     * is the new rest api hook enabled?
     *
     * @return bool
     */
    public static function isRestHookEnabled()
    {
        return (bool) self::getExtConf('restEnableHook');
    }

    /**
     * returns the rest api path segment with leading and trailing slash.
     * default is /api/.
     *
     * @return string
     */
    public static function getRestApiUriPath()
    {
        $apiSegment = self::getExtConf('restApiUriPath') ?: 'api';
        $apiSegment = trim($apiSegment, '/');
        $apiSegment = '/'.(empty($apiSegment) ? '' : $apiSegment.'/');

        return $apiSegment;
    }

    /**
     * returns the pid of the storage with the fe users.
     *
     * @return int
     */
    public static function getAuthUserStoragePid()
    {
        return (int) self::getExtConf('restAuthUserStoragePid');
    }

    /**
     * returns the controller class.
     *
     * @return string
     */
    public static function getRestApiController()
    {
        return self::getExtConf('restApiController') ?: 'Tx_T3rest_Controller_Json';
    }

    /**
     * returns the router class.
     *
     * @return string
     */
    public static function getRestApiRouter()
    {
        return self::getExtConf('restApiRouter') ?: 'Tx_T3rest_Router_Respect';
    }

    /**
     * returns if Basic Auth header should be send.
     *
     * @return bool
     */
    public static function isBasicAuthHeaderEnabled()
    {
        return (bool) self::getExtConf('isBasicAuthHeaderEnabled');
    }
}
