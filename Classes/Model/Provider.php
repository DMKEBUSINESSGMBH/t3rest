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
 * Frontcontroller for REST-API calls.
 *
 * @author Rene Nitzsche
 *
 * @SuppressWarnings("PHPMD.CamelCaseClassName")
 */
class Tx_T3rest_Model_Provider extends Sys25\RnBase\Domain\Model\BaseModel
{
    private ?object $configurations = null;

    /**
     * Gets the name of the database table.
     *
     * @return string Tabellenname
     */
    public function getTableName(): string
    {
        return 'tx_t3rest_providers';
    }

    /**
     * the ts config for from the provider.
     *
     * @return Sys25\RnBase\Configuration\Processor
     *
     * @SuppressWarnings("PHPMD.MissingImport")
     */
    public function getConfigurations(): object
    {
        if (null === $this->configurations) {
            $configArray = (new Sys25\RnBase\Utility\TypoScript())->parseTsConfig($this->getProperty('config'), 't3rest');
            /* @var $configurations \Sys25\RnBase\Configuration\Processor */
            $this->configurations = TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(Sys25\RnBase\Configuration\Processor::class);
            $this->configurations->init($configArray, false, 't3rest', 't3rest');
        }

        return $this->configurations;
    }

    /**
     * returns an instance of the provider.
     */
    public function getProviderClassName(): string
    {
        return $this->getProperty('classname');
    }

    /**
     * returns an instance of the provider.
     */
    public function getProviderInstance(): ?Tx_T3rest_Provider_InterfaceProvider
    {
        if (!class_exists($this->getProviderClassName())) {
            Sys25\RnBase\Utility\Logger::warn(
                sprintf(
                    'Providerclass "%3$s" for Provider "%2$s (%1$s)" could not be loaded',
                    $this->getUid(),
                    $this->getProperty('name'),
                    $this->getProviderClassName()
                ),
                't3rest'
            );

            return null;
        }

        $instance = TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance($this->getProviderClassName());
        if ($instance instanceof Tx_T3rest_Model_ProviderHolder) {
            $instance->setProvider($this);
        }

        return $instance;
    }
}
