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

/**
 *
 * @package TYPO3
 * @subpackage Tx_T3rest
 * @author Michael Wagner
 */
class Tx_T3rest_Model_Supplier
	extends stdClass
{

	private $ignoreKeys = array('hidden', 'deleted', 'pid', 'crdate', 'cruser_id', 'sorting');

	/**
	 *
	 * @param array $ignoreKeys
	 */
	public function __construct(
		array $ignoreKeys = array()
	) {
		$this->ignoreKeys = array_flip(array_merge($ignoreKeys, $this->ignoreKeys));
	}

	/**
	 *
	 * @param mixed $key
	 * @param mixed $value
	 * @return Tx_T3rest_Model_Supplier
	 */
	public function add($key, $value = NULL)
	{
		if ($value === NULL) {
			$value = $key;
			$node = &$this;
		} else {
			if (isset($this->ignoreKeys[$key])) {
				return $this;
			}

			if (!isset($this->{$key})) {
				$this->{$key} = NULL;
			}
			$node = &$this->{$key};
			if (!is_scalar($value) && !is_object($node)) {
				$node = new self();
			}
		}

		// there is an array, iterate it and set the values
		if (is_array($value)) {
			foreach ($value as $subKey => $subValue) {
				$node->add($subKey, $subValue);
			}
		}
		// there is a object, parse all object vars
		elseif (is_object($value)) {
			$vars = get_object_vars($value);
			// there is a model too, parse the record data
			if ($value instanceof tx_rnbase_model_data) {
				$node->add($value->getProperty());
				unset($vars['record']);
			}
			$node->add($vars);
		}
		else {
			$node = $value;
		}

		return $this;
	}

}
