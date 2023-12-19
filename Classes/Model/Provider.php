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
 * Frontcontroller for REST-API calls.
 *
 * @author Rene Nitzsche
 */
class Tx_T3rest_Model_Provider extends \Sys25\RnBase\Domain\Model\BaseModel
{
    private $configurations;

    /**
     * Gets the name of the database table.
     *
     * @return string Tabellenname
     */
    public function getTableName()
    {
        return 'tx_t3rest_providers';
    }

    /**
     * the ts config for from the provider.
     *
     * @return \Sys25\RnBase\Configuration\Processor
     */
    public function getConfigurations()
    {
        if (null === $this->configurations) {
            $configArray = \Sys25\RnBase\Utility\TypoScript::parseTsConfig($this->getConfig());
            /* @var $configurations \Sys25\RnBase\Configuration\Processor */
            $this->configurations = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\Sys25\RnBase\Configuration\Processor::class);
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
            \Sys25\RnBase\Utility\Logger::warn(
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

        $instance = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance($this->getProviderClassName());
        if ($instance instanceof Tx_T3rest_Model_ProviderHolder) {
            $instance->setProvider($this);
        }

        return $instance;
    }
}
