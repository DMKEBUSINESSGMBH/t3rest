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
 * simple item to supplier transformer.
 *
 * @author Michael Wagner
 */
class Tx_T3rest_Transformer_Simple extends Tx_T3rest_Model_ProviderHolder implements Tx_T3rest_Transformer_InterfaceTransformer
{
    /**
     *  transforms the item.
     *
     * @param Sys25\RnBase\Domain\Model\DataInterface $item
     * @param string $confId
     *
     * @return Tx_T3rest_Model_Supplier
     */
    public function transform(
        Sys25\RnBase\Domain\Model\DataInterface $item,
        $confId = 'item.'
    ) {
        $this->prepareItem($item, $confId);
        $this->wrapRecord($item, $confId.'record.');
        $this->prepareLinks($item, $confId.'links.');

        return $this->buildSupplier($item, $confId);
    }

    /**
     * prepares the item to transform.
     *
     * @param Sys25\RnBase\Domain\Model\DataInterface $item
     * @param string $confId
     *
     * @return void
     */
    protected function prepareItem(
        Sys25\RnBase\Domain\Model\DataInterface $item,
        $confId = 'item.'
    ) {
    }

    /**
     * wraps the record using stdwrap.
     *
     * @param Sys25\RnBase\Domain\Model\DataInterface $item
     * @param string $confId
     *
     * @return void
     */
    protected function wrapRecord(
        Sys25\RnBase\Domain\Model\DataInterface $item,
        $confId = 'item.record.'
    ) {
        $cObj = $this->getConfigurations()->getCObj();
        $config = $this->getConfig($confId);

        // Add dynamic columns
        if (is_array($config) && !empty($config)) {
            $keys = $this->getConfigurations()->getUniqueKeysNames($config);
            foreach ($keys as $key) {
                if ('d' === $key[0]
                    && 'c' === $key[0]
                    && !$item->hasProperty($key)
                ) {
                    $item->setProperty($key, $config[$key]);
                }
            }
        }

        // @TODO: thats a big performance hole! why?
        $cObjTempData = $cObj->data;
        $cObj->data = $item->getProperty();
        foreach ($cObj->data as $colname => $value) {
            if ($config[$colname] ?? null) {
                // Get value using cObjGetSingle
                $cObj->setCurrentVal($value);
                $item->setProperty(
                    $colname,
                    $cObj->cObjGetSingle($config[$colname], $config[$colname.'.'])
                );
                $cObj->setCurrentVal(false);
            } elseif (!empty($config[$colname.'.'] ?? null)) {
                $item->setProperty(
                    $colname,
                    $cObj->stdWrap($value, $config[$colname.'.'])
                );
            }
        }

        $cObj->data = $cObjTempData;
    }

    /**
     * creates the links.
     *
     * @param Sys25\RnBase\Domain\Model\DataInterface $item
     * @param string $confId
     *
     * @return void
     */
    protected function prepareLinks(
        Sys25\RnBase\Domain\Model\DataInterface $item,
        $confId = 'item.links.'
    ) {
        // prepare the tsfe for link creation (config,sys_page and tmpl are required)
        Sys25\RnBase\Utility\Misc::prepareTSFE();

        $linkIds = $this->getConfigurations()->getKeyNames($confId);
        foreach ($linkIds as $link) {
            $linkId = $confId.$link.'.';
            $params = [];
            $paramMap = (array) $this->getConfig($linkId.'_cfg.params.');
            foreach ($paramMap as $paramName => $colName) {
                if (is_scalar($colName) && $item->hasProperty($colName)) {
                    $params[$paramName] = $item->getProperty($colName);
                }
            }
            $linkObj = $this->getConfigurations()->createLink(false);
            $linkObj->initByTS($this->getConfigurations(), $linkId, $params);
            // Immer absolute URLs setzen!
            $linkObj->isAbsUrl() ?: $linkObj->setAbsUrl(true);
            $item->setProperty(
                'link_'.$link,
                $linkObj->makeUrl(false)
            );
        }
    }

    /**
     * creates an link object.
     *
     * @param Sys25\RnBase\Domain\Model\DataInterface $item
     * @param string $confId
     * @param array $parameters
     *
     * @return Sys25\RnBase\Utility\Link
     */
    protected function initLink(
        Sys25\RnBase\Domain\Model\DataInterface $item,
        $confId = 'item.links.show.',
        array $parameters = []
    ) {
        $linkObj = $this->getConfigurations()->createLink();
        $linkObj->initByTS($this->getConfigurations(), $confId, $parameters);

        if (!$linkObj->isAbsUrl()) {
            $linkObj->setAbsUrl(true);
        }

        return $linkObj;
    }

    /**
     * creates the supplier.
     *
     * @param Sys25\RnBase\Domain\Model\DataInterface $item
     * @param string $confId
     *
     * @return Tx_T3rest_Model_Supplier
     */
    protected function buildSupplier(
        Sys25\RnBase\Domain\Model\DataInterface $item,
        $confId = 'item.'
    ) {
        return Tx_T3rest_Utility_Factory::getSupplier(
            $this->getIgnoreFields($confId.'record.')
        )
            ->add('_object', get_class($item))
            ->add($item);
    }

    /**
     * get ignorefields from ts.
     *
     * @param string $confId
     *
     * @return Ambigous <multitype:, string, multitype:unknown >
     */
    protected function getIgnoreFields($confId = 'item.record.')
    {
        return Sys25\RnBase\Utility\Strings::trimExplode(
            ',',
            $this->getConfig($confId.'ignoreFields'),
            true
        );
    }
}
