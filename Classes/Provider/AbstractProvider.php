<?php
/**
 * Copyright notice
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

tx_rnbase::load('Tx_T3rest_Model_ProviderHolder');
tx_rnbase::load('Tx_T3rest_Provider_InterfaceProvider');

/**
 * abstract provider
 *
 * @package TYPO3
 * @subpackage Tx_T3rest
 * @author Michael Wagner
 */
abstract class Tx_T3rest_Provider_AbstractProvider
	extends Tx_T3rest_Model_ProviderHolder
	implements Tx_T3rest_Provider_InterfaceProvider
{
	/**
	 * @var Tx_T3rest_Transformer_InterfaceTransformer
	 */
	private $transformer = NULL;

	/**
	 * a transformer instance
	 *
	 * @return Tx_T3rest_Transformer_InterfaceTransformer
	 */
	protected function getTransformer()
	{
		if ($this->transformer === NULL) {
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

}
