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

tx_rnbase::load('Tx_Rnbase_Domain_Repository_AbstractRepository');

/**
 * repository to handle provider data
 *
 * @package TYPO3
 * @subpackage Tx_T3rest
 * @author Michael Wagner
 */
class Tx_T3rest_Repository_Provider
	extends Tx_Rnbase_Domain_Repository_AbstractRepository
{
	/**
	 * Liefert den Namen der Suchklasse
	 *
	 * @return 	string
	 */
	protected function getSearchClass() {
		return 'tx_rnbase_util_SearchGeneric';
	}

	/**
	 * Liefert die Model Klasse.
	 *
	 * @return 	string
	 */
	protected function getWrapperClass() {
		return 'Tx_T3rest_Model_Provider';
	}

	/**
	 * Search database
	 *
	 * @param array $fields
	 * @param array $options
	 * @return array[tx_rnbase_model_base]
	 */
	public function search(array $fields, array $options) {
		if (empty($options['searchdef']) || !is_array($options['searchdef'])) {
			$options['searchdef'] = array();
		}
		tx_rnbase::load('tx_rnbase_util_Arrays');
		$options['searchdef'] = tx_rnbase_util_Arrays::mergeRecursiveWithOverrule(
			// default sercher config
			$this->getSearchdef(),
			// searcher config overrides
			$options['searchdef']
		);
		return parent::search($fields, $options);
	}

	/**
	 * the search config, to work without a searcher class.
	 *
	 * @return array
	 */
	protected function getSearchdef() {
		$table = $this->getEmptyModel()->getTableName();
		return array(
			'usealias' => '1',
			'basetable' => $table,
			'basetablealias' => 'PROVIDER',
			'wrapperclass' => $this->getWrapperClass(),
			'alias' => array(
				'PROVIDER' => array(
					'table' => $table
				),
			)
		);
	}

}
