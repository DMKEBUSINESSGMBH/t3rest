<?php
/**
 *  Copyright notice.
 *
 *  (c) 2012 René Nitzsche <rene@system25.de>
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 2 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 */

// @todo this file can be removed when support for TYPO3 6.2 is dropped
if (!\Sys25\RnBase\Utility\TYPO3::isTYPO70OrHigher()) {
    $SOBE = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('tx_t3rest_mod_Module');
    $SOBE->__invoke();
}
