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
 * a provider holder trait.
 * actually a abstract class, for php 5.3 support.
 *
 * @author Michael Wagner
 */
abstract class Tx_T3rest_Model_ProviderHolder
{
    /**
     * @var Tx_T3rest_Model_Provider
     */
    private $provider;

    /**
     * injects the provider model.
     *
     * @param Tx_T3rest_Model_Provider $model
     *
     * @return Tx_T3rest_Provider_AbstractProvider
     */
    public function setProvider(
        Tx_T3rest_Model_Provider $model
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
     *
     * @return mixed
     */
    protected function getConfig($confId)
    {
        return $this->getConfigurations()->get($confId);
    }
}
