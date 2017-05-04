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

tx_rnbase::load('tx_rnbase_model_base');

/**
 * Frontcontroller for REST-API calls
 *
 * @author Rene Nitzsche
 */
class Tx_T3rest_Model_Provider extends tx_rnbase_model_base
{
    private $configurations = null;

    /**
     * Gets the name of the database table
     *
     * @return String Tabellenname
     */
    public function getTableName()
    {
        return 'tx_t3rest_providers';
    }

    /**
     * set the provider config
     *
     * @param tx_rnbase_configurations $config
     * @deprecated only used for the old api
     * @return void
     */
    public function setConfigurations($config)
    {
        $this->configurations = $config;
    }

    /**
     * the ts config for from the provider
     *
     * @return tx_rnbase_configurations
     */
    public function getConfigurations()
    {
        if ($this->configurations === null) {
            tx_rnbase::load('tx_rnbase_util_TS');
            $configArray = tx_rnbase_util_TS::parseTsConfig($this->getConfig());
            /* @var $configurations tx_rnbase_configurations */
            $this->configurations = tx_rnbase::makeInstance('tx_rnbase_configurations');
            $this->configurations->init($configArray, false, 't3rest', 't3rest');
        }

        return $this->configurations;
    }

    /**
     * returns an instance of the provider.
     *
     * @return Tx_T3rest_Provider_InterfaceProvider
     */
    public function getProviderClassName()
    {
        return $this->getProperty('classname');
    }

    /**
     * returns an instance of the provider.
     *
     * @return Tx_T3rest_Provider_InterfaceProvider
     */
    public function getProviderInstance()
    {
        if (!class_exists($this->getProviderClassName())) {
            tx_rnbase::load('tx_rnbase_util_Logger');
            tx_rnbase_util_Logger::warn(
                sprintf(
                    'Providerclass "%3$s" for Provider "%2$s (%1$s)" could not be loaded',
                    $this->getUid(),
                    $this->getName(),
                    $this->getProviderClassName()
                ),
                't3rest'
            );

            return null;
        }

        $instance = tx_rnbase::makeInstance($this->getProviderClassName());
        if ($instance instanceof Tx_T3rest_Model_ProviderHolder) {
            $instance->setProvider($this);
        }

        return $instance;
    }
}
