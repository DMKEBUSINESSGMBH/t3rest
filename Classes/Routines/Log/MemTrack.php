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

tx_rnbase::load('Tx_T3rest_Routines_InterfaceRouter');

/**
 * memory tracking routine
 *
 * @package TYPO3
 * @subpackage Tx_T3rest
 * @author Michael Wagner
 */
class Tx_T3rest_Routines_Log_MemTrack
	implements Tx_T3rest_Routines_InterfaceRouter
{

	protected $mem = array();

	/**
	 * add a memory tracking
	 *
	 * @param string $key
	 * @param int $mem
	 * @return Tx_T3rest_Routines_Log_MemTrack
	 */
	public function add($key, $mem = NULL)
	{
		$this->mem[$key] = $mem !== NULL ? $mem : memory_get_usage(TRUE);

		return $this;
	}

	/**
	 * add the before and after callbacks
	 *
	 * @param Tx_T3rest_Router_InterfaceRouter $router
	 * @return void
	 */
	public function prepareRouter(
		Tx_T3rest_Router_InterfaceRouter $router
	) {

		$this->add('start', 0)->add('init');

		// register post routine for Respect/Rest
		if ($router instanceof Tx_T3rest_Router_Respect) {
			$router->always(
				'By',
				array($this, 'byRespect')
			);
			$router->always(
				'Through',
				function() {
					return array($this, 'throughRespect');
				}
			);
		}
	}

	/**
	 * was called after provider returns his value.
	 * this method can be extended by child classes
	 *
	 * @param mixed $data
	 * @return void
	 */
	public function byRespect($data)
	{
		$this->add('by');
	}

	/**
	 * was called after provider returns his value.
	 * this method can be extended by child classes
	 *
	 * @param mixed $data
	 * @return string
	 */
	public function throughRespect($data)
	{
		$this->add('through');

		if (!$data instanceof Tx_T3rest_Model_Supplier) {
			return $data;
		}

		$mem = Tx_T3rest_Utility_Factory::getSupplier();

		$last = 0;
		foreach ($this->mem as $key => $value) {
			$mem->add(
				$key,
				array(
					'start' => $last,
					'end' => $value,
					'used' => $value - $last,
				)
			);
			$last = $value;
		}

		$data->add('mem', $mem);

		return $data;
	}
}