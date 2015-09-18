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
class Tx_T3rest_Model_Provider
	extends tx_rnbase_model_base
{

	private $configurations;

	/**
	 * @return String Tabellenname
	 */
	function getTableName() {
		return 'tx_t3rest_providers';
	}

	/**
	 * @param tx_rnbase_configurations $config
	 */
	public function setConfigurations($config) {
		$this->configurations = $config;
	}

	/**
	 * @return tx_rnbase_configurations
	 */
	public function getConfigurations() {
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
		$instance = tx_rnbase::makeInstance($this->getProviderClassName());
		if (!$instance instanceof Tx_T3rest_Provider_InterfaceProvider) {
			throw new Exception(
				sprintf(
					'Provider "%1$s" has to implement the interface "Tx_T3rest_Provider_InterfaceProvider".',
					get_class($instance)
				)
			);
		}
		return $instance;
	}
}
