<?php
/**
 * Copyright notice.
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
 * tsfe hooks.
 *
 * @author Michael Wagner
 */
class Tx_T3rest_Hook_TsFe
{
    /**
     * in this hook we check for an mobile redirect.
     *
     * @param array &$params
     * @param \TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController $tsfe
     *
     * @return void
     */
    public function checkAndRunRestApi(&$params, $tsfe)
    {
        // the hook is not enabled, skip!
        if (!Tx_T3rest_Utility_Config::isRestHookEnabled()) {
            return;
        }

        $this->getController()->execute();
    }

    /**
     * returns an instance of a controller.
     *
     * @return Tx_T3rest_Controller_InterfaceController
     */
    public function getController()
    {
        return Tx_T3rest_Utility_Factory::getRestApiController();
    }
}
