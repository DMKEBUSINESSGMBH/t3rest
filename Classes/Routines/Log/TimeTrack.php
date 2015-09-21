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
 * this json routine is for data transforming.
 * after the router did its job, this routine was called
 * and its the return value will be transformed to json.
 *
 * @package TYPO3
 * @subpackage Tx_T3rest
 * @author Michael Wagner
 */
class Tx_T3rest_Routines_Log_TimeTrack
	implements Tx_T3rest_Routines_InterfaceRouter
{

	protected $times = array();

	/**
	 * add a time tracking
	 *
	 * @param string $key
	 * @param int $mem
	 * @return Tx_T3rest_Routines_Log_TimeTrack
	 */
	public function add($key, $microtime = NULL)
	{
		$this->times[$key] = $microtime !== NULL ? $microtime : microtime(true);

		return $this;
	}

	/**
	 *
	 * @param Tx_T3rest_Router_InterfaceRouter $router
	 */
	public function prepareRouter(
		Tx_T3rest_Router_InterfaceRouter $router
	) {

		$this->add('start', $GLOBALS['TYPO3_MISC']['microtime_start'])->add('init');

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
	 * @return string
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

		$times = Tx_T3rest_Utility_Factory::getSupplier();

		$last = $GLOBALS['TYPO3_MISC']['microtime_start'];
		foreach ($this->times as $key => $value) {
			$times->add(
				$key,
				array(
					'stamp' => $value,
					'time' => $value === $last ? 0 : $value - $last,
				)
			);
			$last = $value;
		}

		$data->add('times', $times);

		return $data;
	}
}
