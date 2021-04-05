<?php
use Sys25\RnBase\Typo3Wrapper\Service\AbstractService;
use Sys25\RnBase\Search\SearchBase;

/***************************************************************
*  Copyright notice
*
*  (c) 2012 Rene Nitzsche (rene@system25.de)
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
***************************************************************/

/**
 * Service for accessing logs.
 *
 * @author Rene Nitzsche
 */
class tx_t3rest_srv_Logs extends AbstractService
{
    /**
     * Search database for networks.
     *
     * @param array $fields
     * @param array $options
     *
     * @return array of tx_t3socials_models_Network
     */
    public function search($fields, $options)
    {
        $searcher = SearchBase::getInstance('tx_t3rest_search_Logs');

        return $searcher->search($fields, $options);
    }

    /**
     * Liefert die Anzhal von Installationen.
     */
    public function getStatsApps($filter = '')
    {
        //SELECT count(distinct app) FROM `tx_t3rest_accesslog` WHERE app != ''
        $options = [];
        $options['enablefieldsoff'] = 1;
        $options['where'] = "app != ''";
        $options['where'] .= $this->addSystemWhere($filter);
        $what = 'count(distinct app) As cnt';
        if ('OS_ALL' == $filter) {
            $what .= ', os';
            $options['where'] .= " AND os != ''";
            $options['groupby'] = 'os';
        }

        $rows = tx_rnbase_util_DB::doSelect($what, 'tx_t3rest_accesslog', $options);
        if ('OS_ALL' == $filter) {
            // Daten zusammenpacken
            $ret = [];
            $sum = 0;
            foreach ($rows as $row) {
                $ret[$row['os']] = $row['cnt'];
                $sum += $row['cnt'];
            }
            $ret['ALL'] = $sum;
        } else {
            $ret = $rows[0]['cnt'];
        }

        return $ret;
    }

    public function getStatsAccessByFilter($filter)
    {
        $sys = $filter['system'];
        $from = $filter['from'];
        $to = $filter['to'];
        $period = $filter['period'];

        $dateGroup = 'day' == $period ? 'DATE(tstamp)' :
            ('week' == $period ? 'YEARWEEK(tstamp, 1)' :
                    ('month' == $period ? 'MONTH(tstamp)' : 'YEAR(tstamp)'));
        $options['enablefieldsoff'] = 1;
        $options['where'] = "app != ''";
        $options['where'] .= $this->addTimeWhere($from, $to);
        $options['where'] .= $this->addSystemWhere($sys);

        $options['groupby'] = $dateGroup;
        if ('OS_ALL' == $sys) {
            $dateGroup = 'os, '.$dateGroup;
            $options['groupby'] .= ', os';
        }
        $options['orderby'] = 'tstamp asc';
        $rows = tx_rnbase_util_DB::doSelect($dateGroup.' AS day, count(uid) As value', 'tx_t3rest_accesslog', $options);

        return $rows;
    }

    /**
     * Zugriffe pro Tag.
     */
    public function getStatsAccessByDay($sys = '', $from = '', $to = '')
    {
        /*
SELECT DATE(tstamp), count(uid)
FROM `tx_t3rest_accesslog`
WHERE
tstamp > '2012-10-01' AND tstamp < '2012-10-10'
GROUP BY DATE(tstamp)
ORDER BY tstamp desc
*/
        return $this->getStatsAccessByFilter(['system' => $sys, 'from' => $from, 'to' => $to, 'period' => 'day']);
    }

    private function addTimeWhere($from, $to)
    {
        $ret = '';
        if ($from) {
            $ret .= " AND DATE(tstamp) >= '$from'";
        }
        if ($to) {
            $ret .= " AND DATE(tstamp) <= '$to'";
        }

        return $ret;
    }

    private function addSystemWhere($system)
    {
        if (!$system || 'OS_ALL' == $system) {
            return '';
        }

        return ' AND os = \''.strtoupper($system).'\'';
        if ('ios' == $system) {
            return ' AND useragent like \'%Darwin%\'';
        }

        return ' AND system like \'%'.$system.'%\'';
    }

    public function getStatsInstallsByFilter($filter)
    {
        $sys = $filter['system'];
        $from = $filter['from'];
        $to = $filter['to'];
        $period = $filter['period'];

        $dateGroup = 'day' == $period ? 'DATE(tstamp)' :
        ('week' == $period ? 'YEARWEEK(tstamp, 1)' :
                ('month' == $period ? 'MONTH(tstamp)' : 'YEAR(tstamp)'));

        $options['enablefieldsoff'] = 1;
        $options['where'] = "app != ''";
        $options['where'] .= $this->addTimeWhere($from, $to);
        $options['where'] .= $this->addSystemWhere($sys);

        $options['groupby'] = $dateGroup;
        if ('OS_ALL' == $sys) {
            $dateGroup = 'os, '.$dateGroup;
            $options['groupby'] .= ', os';
        }

        $options['orderby'] = 'tstamp asc';
        $rows = tx_rnbase_util_DB::doSelect($dateGroup.' AS day, count(distinct app) As value', 'tx_t3rest_accesslog', $options);

        return $rows;
    }

    /**
     * Unterschiedliche Nutzer pro Tag.
     */
    public function getStatsInstallsByDay($sys = '', $from = '', $to = '')
    {
        /*
SELECT DATE(tstamp), count(distinct app)
FROM `tx_t3rest_accesslog`
WHERE

tstamp > '2012-10-01' AND tstamp < '2012-10-10'
GROUP BY DATE(tstamp)
ORDER BY tstamp desc
*/
        return $this->getStatsInstallsByFilter(['system' => $sys, 'from' => $from, 'to' => $to, 'period' => 'day']);
        $options['enablefieldsoff'] = 1;
        $options['where'] = "app != ''";
        $options['where'] .= $this->addTimeWhere($from, $to);
        $options['where'] .= $this->addSystemWhere($sys);

        $options['groupby'] = 'DATE(tstamp)';
        $options['orderby'] = 'tstamp asc';
        $rows = tx_rnbase_util_DB::doSelect('DATE(tstamp) AS day, count(distinct app) As value', 'tx_t3rest_accesslog', $options);

        return $rows;
    }
}

