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
 * This is a sample REST provider for tt_news.
 *
 * @author Rene Nitzsche
 */
abstract class tx_t3rest_provider_AbstractBase implements tx_t3rest_provider_IProvider
{
    public function execute(tx_t3rest_models_Provider $provData)
    {
        $configurations = $provData->getConfigurations();
        $confId = $this->getConfId();
        $data = $this->handleRequest($configurations, $confId);
        if (false === $data) {
            $data = ['unsupported' => 1];
        }

        return $data;
    }

    /**
     * Lädt einen einzelnen Datensatz. Erwartet wird entweder die UID oder ein
     * Identifier. Letzterer muss dann in der Config als Filter konfiguriert sein.
     *
     * @param mixed $itemUid int oder string-Identifier
     * @param \Sys25\RnBase\Configuration\Processor $configurations
     * @param string $confId wird bei defined angepaßt
     *
     * @return \Sys25\RnBase\Domain\Model\BaseModel
     */
    public function getItem($itemUid, $configurations, &$confId, $searchCallback)
    {
        if (intval($itemUid)) {
            $item = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance($this->getBaseClass(), intval($itemUid));
        } else {
            // Prüfen, ob der Dienst konfiguriert ist
            $defined = $configurations->getKeyNames($confId.'defined.');
            if (in_array($itemUid, $defined)) {
                $confId = $confId.'defined.'.$itemUid.'.';
                // Item per Config laden
                $filter = tx_rnbase_filter_BaseFilter::createFilter($configurations->getParameters(), $configurations, null, $confId.'filter.');
                $fields = [];
                $options = [];
                //suche initialisieren
                $filter->init($fields, $options);
                $options['forcewrapper'] = 1;
                $options['limit'] = 1;
                $items = call_user_func($searchCallback, $fields, $options);
                $item = !empty($items) ? $items[0] : null;
            }
        }

        if (!$item || !$item->isValid()) {
            throw \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('tx_t3rest_exception_DataNotFound', 'Item not valid', 100);
        }

        return $item;
    }

    abstract protected function handleRequest($configurations, $confId);

    abstract protected function getConfId();

    abstract protected function getBaseClass();
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/t3rest/provider/class.tx_t3rest_provider_AbstractBase.php']) {
    include_once $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/t3rest/provider/class.tx_t3rest_provider_AbstractBase.php'];
}
