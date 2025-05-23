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
 * Class Tx_T3rest_Model_ProviderHolder.
 *
 * @author  Hannes Bochmann
 * @license http://www.gnu.org/licenses/lgpl.html
 *          GNU Lesser General Public License, version 3 or later
 *
 * @SuppressWarnings("PHPMD.CamelCaseClassName")
 */
abstract class Tx_T3rest_Model_ProviderHolder
{
    private ?Tx_T3rest_Model_Provider $provider = null;

    /**
     * injects the provider model.
     *
     * @return Tx_T3rest_Provider_AbstractProvider
     */
    public function setProvider(
        Tx_T3rest_Model_Provider $model,
    ) {
        $this->provider = $model;

        return $this;
    }

    /**
     * returns the provider model with the config.
     *
     * @return Tx_T3rest_Model_Provider
     */
    public function getProvider()
    {
        return $this->provider;
    }

    /**
     * the config from the provider.
     *
     * @return Sys25\RnBase\Configuration\Processor
     */
    protected function getConfigurations()
    {
        return $this->getProvider()->getConfigurations();
    }

    /**
     * a configuration for the path from the providerconfiguration.
     *
     * @param string $confId
     */
    protected function getConfig($confId)
    {
        return $this->getConfigurations()->get($confId);
    }
}
