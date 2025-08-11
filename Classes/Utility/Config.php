<?php

/*
 * Copyright notice
 *
 * (c) DMK E-BUSINESS GmbH <dev@dmk-ebusiness.de>
 * All rights reserved
 *
 * This file is part of the "t3rest" Extension for TYPO3 CMS.
 *
 * This script is part of the TYPO3 project. The TYPO3 project is
 * free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * GNU Lesser General Public License can be found at
 * www.gnu.org/licenses/lgpl.html
 *
 * This script is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * This copyright notice MUST APPEAR in all copies of the script!
 */

/**
 * Class Tx_T3rest_Utility_Config.
 *
 * @author  Hannes Bochmann
 * @license http://www.gnu.org/licenses/lgpl.html
 *          GNU Lesser General Public License, version 3 or later
 *
 * @SuppressWarnings("PHPMD.CamelCaseClassName")
 */
final class Tx_T3rest_Utility_Config
{
    /**
     * reads the extension config.
     */
    private static function getExtConf(string $key)
    {
        static $config = [];
        if (!isset($config[$key])) {
            $config[$key] = Sys25\RnBase\Configuration\Processor::getExtensionCfgValue(
                't3rest',
                $key
            );
        }

        return $config[$key];
    }

    /**
     * is the new rest api hook enabled?
     */
    public static function isRestHookEnabled(): bool
    {
        return (bool) self::getExtConf('restEnableHook');
    }

    /**
     * returns the rest api path segment with leading and trailing slash.
     * default is /api/.
     */
    public static function getRestApiUriPath(): string
    {
        $apiSegment = self::getExtConf('restApiUriPath') ?: 'api';
        $apiSegment = trim((string) $apiSegment, '/');

        return '/'.('' === $apiSegment || '0' === $apiSegment ? '' : $apiSegment.'/');
    }

    /**
     * Should the language from the site config be respected?
     */
    private static function getRestApiRespectLanguage(): bool
    {
        return (bool) self::getExtConf('restApiRespectLanguage') ?: false;
    }

    /**
     * For typo3 9 or later the language is not given by get parameter `L` anymore.
     * We has to add the language base url to the rest aoi uri!
     */
    public static function getRestApiUriPathForSiteLanguage(): string
    {
        $baseUri = self::getRestApiUriPath();

        if (self::getRestApiRespectLanguage()) {
            $language = Tx_T3rest_Utility_Factory::getCurrentSiteLanguage();
            if ($language instanceof TYPO3\CMS\Core\Site\Entity\SiteLanguage) {
                $baseUri = rtrim($language->getBase()->getPath(), '/').$baseUri;
            }
        }

        return $baseUri;
    }

    /**
     * returns the pid of the storage with the fe users.
     */
    public static function getAuthUserStoragePid(): int
    {
        return (int) self::getExtConf('restAuthUserStoragePid');
    }

    /**
     * returns the signed pid of the storage with the fe users.
     */
    public static function getSignedAuthUserStoragePid(): string
    {
        return sprintf(
            '%s@%s',
            self::getAuthUserStoragePid(),
            self::getHmacForAuthUserStoragePid()
        );
    }

    /**
     * @SuppressWarnings("PHPMD.MissingImport")
     */
    private static function getHmacForAuthUserStoragePid(): string
    {
        if ((new TYPO3\CMS\Core\Information\Typo3Version())->getMajorVersion() < 13) {
            return TYPO3\CMS\Core\Utility\GeneralUtility::hmac(
                (string) self::getAuthUserStoragePid(),
                TYPO3\CMS\Frontend\Authentication\FrontendUserAuthentication::class
            );
        }

        return TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(TYPO3\CMS\Core\Crypto\HashService::class)
            ->hmac(
                (string) self::getAuthUserStoragePid(),
                TYPO3\CMS\Frontend\Authentication\FrontendUserAuthentication::class
            );
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
     */
    public static function isBasicAuthHeaderEnabled(): bool
    {
        return (bool) self::getExtConf('isBasicAuthHeaderEnabled');
    }
}
