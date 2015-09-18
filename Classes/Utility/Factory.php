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
 * extension configs
 *
 * @package TYPO3
 * @subpackage Tx_T3rest
 * @author Michael Wagner
 */
final class Tx_T3rest_Utility_Factory
{
	/**
	 * a new respect rest router instance.
	 *
	 * @return Tx_T3rest_Router_Respect
	 */
	public static function getRespectRestRouter()
	{
		return tx_rnbase::makeInstance('Tx_T3rest_Router_Respect');
	}

	/**
	 * returns an provider repo instance
	 *
	 * @return Tx_T3rest_Repository_Provider
	 */
	public static function getProviderRepository()
	{
		return tx_rnbase::makeInstance('Tx_T3rest_Repository_Provider');
	}

}
