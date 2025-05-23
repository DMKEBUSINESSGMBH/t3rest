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
 * Class Tx_T3rest_Provider_AbstractProvider.
 *
 * @author  Hannes Bochmann
 * @license http://www.gnu.org/licenses/lgpl.html
 *          GNU Lesser General Public License, version 3 or later
 *
 * @SuppressWarnings("PHPMD.CamelCaseClassName")
 */
abstract class Tx_T3rest_Provider_AbstractProvider extends Tx_T3rest_Model_ProviderHolder implements Tx_T3rest_Provider_InterfaceProvider
{
    private ?Tx_T3rest_Transformer_InterfaceTransformer $transformer = null;

    private string|bool|null $rawRequestBody = null;

    /**
     * @var Tx_T3rest_Routines_Auth_Ip
     */
    protected $ipAuthentication;

    protected ?Tx_T3rest_Routines_Auth_FeUser $auth = null;

    /**
     * a transformer instance.
     *
     * @return Tx_T3rest_Transformer_InterfaceTransformer
     */
    protected function getTransformer()
    {
        if (!$this->transformer instanceof Tx_T3rest_Transformer_InterfaceTransformer) {
            $this->transformer = Tx_T3rest_Utility_Factory::getTransformer(
                $this->getTransformerClass()
            );
            if ($this->transformer instanceof Tx_T3rest_Model_ProviderHolder) {
                $this->transformer->setProvider($this->getProvider());
            }
        }

        return $this->transformer;
    }

    /**
     * returns the transformer class for this provider.
     *
     * @return Tx_T3rest_Transformer_InterfaceTransformer
     */
    protected function getTransformerClass()
    {
        $class = $this->getConfig('transformer.class');

        return $class ?: 'Tx_T3rest_Transformer_Simple';
    }

    /**
     * a instance od the auth fe user routine.
     *
     * @return Tx_T3rest_Routines_Auth_FeUser
     */
    protected function getAuthFeUserRoutine()
    {
        if (!$this->auth instanceof Tx_T3rest_Routines_Auth_FeUser) {
            $this->auth = TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(
                'Tx_T3rest_Routines_Auth_FeUser',
                $this->getProvider()->getFeGroup()
            );
        }

        return $this->auth;
    }

    /**
     * a instance od the auth IP routine.
     *
     * @return Tx_T3rest_Routines_Auth_Ip
     */
    protected function getAuthIpRoutine()
    {
        if (null === $this->ipAuthentication) {
            $this->ipAuthentication = TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(
                'Tx_T3rest_Routines_Auth_Ip',
                (array) $this->getProvider()->getConfigurations()->get('allowedIps.')
            );
        }

        return $this->ipAuthentication;
    }

    /**
     * returns the raw body of the request.
     *
     * @return string
     */
    protected function getRawRequestBody()
    {
        if (null === $this->rawRequestBody) {
            $this->rawRequestBody = file_get_contents('php://input');
        }

        return $this->rawRequestBody;
    }
}
