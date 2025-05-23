<?php

/*
 * Copyright notice
 *
 * (c) DMK E-BUSINESS GmbH <dev@dmk-ebusiness.de>
 * All rights reserved
 *
 * This file is part of the "t3rest" Extension for TYPO3 CMS.
 *
 * This script is part of the TYPO3 project. The TYPO3 project is
 * free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * GNU Lesser General Public License can be found at
 * www.gnu.org/licenses/lgpl.html
 *
 * This script is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * This copyright notice MUST APPEAR in all copies of the script!
 */

/**
 * repository to handle provider data.
 *
 * @author Michael Wagner
 *
 * @SuppressWarnings("PHPMD.CamelCaseClassName")
 */
class Tx_T3rest_Repository_Provider extends Sys25\RnBase\Domain\Repository\AbstractRepository
{
    /**
     * Liefert den Namen der Suchklasse.
     *
     * @return string
     */
    protected function getSearchClass()
    {
        return Sys25\RnBase\Search\SearchGeneric::class;
    }

    /**
     * Liefert die Model Klasse.
     *
     * @return string
     */
    protected function getWrapperClass()
    {
        return 'Tx_T3rest_Model_Provider';
    }

    /**
     * Search database.
     *
     * @return array[\Sys25\RnBase\Domain\Model\BaseModel]
     */
    public function search(array $fields, array $options)
    {
        if (empty($options['searchdef']) || !is_array($options['searchdef'])) {
            $options['searchdef'] = [];
        }

        $options['searchdef'] = Sys25\RnBase\Utility\Arrays::mergeRecursiveWithOverrule(
            // default sercher config
            $this->getSearchdef(),
            // searcher config overrides
            $options['searchdef']
        );

        return parent::search($fields, $options);
    }

    /**
     * the search config, to work without a searcher class.
     */
    protected function getSearchdef(): array
    {
        $table = $this->getEmptyModel()->getTableName();

        return [
            'usealias' => '1',
            'basetable' => $table,
            'basetablealias' => 'PROVIDER',
            'wrapperclass' => $this->getWrapperClass(),
            'alias' => [
                'PROVIDER' => [
                    'table' => $table,
                ],
            ],
        ];
    }
}
