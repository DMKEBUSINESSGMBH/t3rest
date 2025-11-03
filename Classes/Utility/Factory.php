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
 * Class Tx_T3rest_Utility_Factory.
 *
 * @author  Hannes Bochmann
 * @license http://www.gnu.org/licenses/lgpl.html
 *          GNU Lesser General Public License, version 3 or later
 *
 * @SuppressWarnings("PHPMD.CamelCaseClassName")
 */
final class Tx_T3rest_Utility_Factory
{
    /**
     * returns the rest api controller.
     */
    public static function getRestApiController(): Tx_T3rest_Controller_InterfaceController
    {
        $class = Tx_T3rest_Utility_Config::getRestApiController();
        $instance = TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance($class);
        if (!$instance instanceof Tx_T3rest_Controller_InterfaceController) {
            throw new Exception(sprintf('Controller "%1$s" has to implement the interface "Tx_T3rest_Controller_InterfaceController".', $instance::class));
        }

        return $instance;
    }

    /**
     * a new respect rest router instance.
     */
    public static function getRespectRestRouter(): Tx_T3rest_Router_InterfaceRouter
    {
        $class = Tx_T3rest_Utility_Config::getRestApiRouter();
        $instance = TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance($class);
        if (!$instance instanceof Tx_T3rest_Router_InterfaceRouter) {
            throw new Exception(sprintf('Router "%1$s" has to implement the interface "Tx_T3rest_Router_InterfaceRouter".', $instance::class));
        }

        return $instance;
    }

    /**
     * returns an provider repo instance.
     */
    public static function getProviderRepository(): Tx_T3rest_Repository_Provider
    {
        return TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('Tx_T3rest_Repository_Provider');
    }

    /**
     * the transformer class.
     */
    public static function getTransformer($class = null): Tx_T3rest_Transformer_InterfaceTransformer
    {
        $class = empty($class) ? 'Tx_T3rest_Transformer_Simple' : $class;
        $instance = TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance($class);
        if (!$instance instanceof Tx_T3rest_Transformer_InterfaceTransformer) {
            throw new Exception(sprintf('Transformer "%1$s" has to implement the interface "Tx_T3rest_Transformer_InterfaceTransformer".', $instance::class));
        }

        return $instance;
    }

    /**
     * the suplier model.
     * was used to transfer the model date to the rest api provider.
     */
    public static function getSupplier(array $ignoreKeys = []): Tx_T3rest_Model_Supplier
    {
        return TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(
            'Tx_T3rest_Model_Supplier',
            $ignoreKeys
        );
    }

    /**
     * If the current request has a site language, this means that the SiteResolver has detected a
     * page with a site configuration and a selected language, so let's choose that one.
     *
     * @SuppressWarnings("PHPMD.Superglobals")
     */
    public static function getCurrentSiteLanguage(): ?TYPO3\CMS\Core\Site\Entity\SiteLanguage
    {
        if ($GLOBALS['TYPO3_REQUEST'] instanceof Psr\Http\Message\ServerRequestInterface) {
            return $GLOBALS['TYPO3_REQUEST']->getAttribute('language');
        }

        return null;
    }
}
