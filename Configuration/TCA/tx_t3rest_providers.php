<?php

declare(strict_types=1);

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

return [
    'ctrl' => [
        'title' => 'LLL:EXT:t3rest/Resources/Private/Language/locallang_db.xlf:tx_t3rest_providers',
        'label' => 'name',
        'tstamp' => 'tstamp',
        'crdate' => 'crdate',
        'delete' => 'deleted',
        'enablecolumns' => [
            'disabled' => 'hidden',
        ],
        'iconfile' => 'EXT:t3rest/Resources/Public/Icons/icon_table.gif',
    ],
    'feInterface' => [
        'fe_admin_fieldList' => 'hidden, name',
    ],
    'columns' => [
        'hidden' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.hidden',
            'config' => [
                'type' => 'check',
                'default' => '0',
            ],
        ],

        'name' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:t3rest/Resources/Private/Language/locallang_db.xlf:tx_t3rest_providers_name',
            'config' => [
                'type' => 'input',
                'size' => '30',
                'eval' => 'trim',
                'required' => true,
            ],
        ],
        'classname' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:t3rest/Resources/Private/Language/locallang_db.xlf:tx_t3rest_providers_classname',
            'config' => [
                'type' => 'input',
                'size' => '30',
                'eval' => 'trim',
                'required' => true,
            ],
        ],
        'fe_group' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.fe_group',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectMultipleSideBySide',
                'size' => 7,
                'maxitems' => 20,
                'items' => [
                    [
                        'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.hide_at_login',
                        'value' => -1,
                    ],
                    [
                        'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.any_login',
                        'value' => -2,
                    ],
                    [
                        'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.usergroups',
                        'value' => '--div--',
                    ],
                ],
                'exclusiveKeys' => '-1,-2',
                'foreign_table' => 'fe_groups',
                'foreign_table_where' => 'ORDER BY fe_groups.title',
            ],
        ],
        'config' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:t3rest/Resources/Private/Language/locallang_db.xlf:tx_t3rest_providers_config',
            'config' => [
                'type' => 'text',
                'cols' => '30',
                'rows' => '5',
                'eval' => 'trim',
            ],
        ],
    ],
    'types' => [
        '0' => ['showitem' => 'hidden;;1;;1-1-1,name,classname,fe_group,config'],
    ],
    'palettes' => [
        '1' => ['showitem' => ''],
    ],
];
