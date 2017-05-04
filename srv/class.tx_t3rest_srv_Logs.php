<?php
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

tx_rnbase::load('tx_rnbase_sv1_Base');

/**
 * Service for accessing logs
 *
 * @author Rene Nitzsche
 */
class tx_t3rest_srv_Logs extends tx_rnbase_sv1_Base {
    public function getSearchClass() {
        return 'tx_t3rest_search_Logs';
    }
    /**
     * Liefert die Anzhal von Installationen
     */
    public function getStatsApps($filter='') {
        //SELECT count(distinct app) FROM `tx_t3rest_accesslog` WHERE app != ''
        $options = array();
        $options['enablefieldsoff'] = 1;
        $options['where'] = "app != ''";
        $options['where'] .= $this->addSystemWhere($filter);
        $what = 'count(distinct app) As cnt';
        if($filter == 'OS_ALL') {
            $what .= ', os';
            $options['where'] .= " AND os != ''";
            $options['groupby'] = 'os';
        }

        $rows = tx_rnbase_util_DB::doSelect($what, 'tx_t3rest_accesslog', $options);
        if($filter == 'OS_ALL') {
            // Daten zusammenpacken
            $ret = array();
            $sum = 0;
            foreach($rows As $row) {
                $ret[$row['os']] = $row['cnt'];
                $sum += $row['cnt'];
            }
            $ret['ALL'] = $sum;
        }
        else
            $ret = $rows[0]['cnt'];
        return  $ret;
    }
    public function getStatsAccessByFilter($filter) {
        $sys = $filter['system'];
        $from = $filter['from'];
        $to = $filter['to'];
        $period = $filter['period'];

        $dateGroup = $period == 'day' ? 'DATE(tstamp)' :
            ($period=='week'? 'YEARWEEK(tstamp, 1)' :
                    ($period=='month' ? 'MONTH(tstamp)' : 'YEAR(tstamp)'));
        $options['enablefieldsoff'] = 1;
        $options['where'] = "app != ''";
        $options['where'] .= $this->addTimeWhere($from, $to);
        $options['where'] .= $this->addSystemWhere($sys);

        $options['groupby'] = $dateGroup;
        if($sys == 'OS_ALL') {
            $dateGroup = 'os, ' .$dateGroup;
            $options['groupby'] .= ', os';
        }
        $options['orderby'] = 'tstamp asc';
        $rows = tx_rnbase_util_DB::doSelect($dateGroup.' AS day, count(uid) As value', 'tx_t3rest_accesslog', $options);
        return $rows;
    }

    /**
     * Zugriffe pro Tag
     *
     */
    public function getStatsAccessByDay($sys='', $from='', $to='') {
/*
SELECT DATE(tstamp), count(uid)
FROM `tx_t3rest_accesslog`
WHERE
tstamp > '2012-10-01' AND tstamp < '2012-10-10'
GROUP BY DATE(tstamp)
ORDER BY tstamp desc
*/
        return $this->getStatsAccessByFilter(array('system'=>$sys, 'from'=>$from, 'to'=>$to, 'period'=>'day'));
    }
    private function addTimeWhere($from, $to) {
        $ret = '';
        if($from)
            $ret .= " AND DATE(tstamp) >= '$from'";
        if($to)
            $ret .= " AND DATE(tstamp) <= '$to'";
        return $ret;
    }

    private function addSystemWhere($system) {
        if(!$system || $system=='OS_ALL') {
            return '';
        }
        return ' AND os = \'' . strtoupper($system) . '\'';
        if($system == 'ios') {
            return ' AND useragent like \'%Darwin%\'';
        }
        return ' AND system like \'%' . $system . '%\'';
    }
    public function getStatsInstallsByFilter($filter) {
        $sys = $filter['system'];
        $from = $filter['from'];
        $to = $filter['to'];
        $period = $filter['period'];

        $dateGroup = $period == 'day' ? 'DATE(tstamp)' :
        ($period=='week'? 'YEARWEEK(tstamp, 1)' :
                ($period=='month' ? 'MONTH(tstamp)' : 'YEAR(tstamp)'));

        $options['enablefieldsoff'] = 1;
        $options['where'] = "app != ''";
        $options['where'] .= $this->addTimeWhere($from, $to);
        $options['where'] .= $this->addSystemWhere($sys);

        $options['groupby'] = $dateGroup;
        if($sys == 'OS_ALL') {
            $dateGroup = 'os, ' .$dateGroup;
            $options['groupby'] .= ', os';
        }

        $options['orderby'] = 'tstamp asc';
        $rows = tx_rnbase_util_DB::doSelect($dateGroup.' AS day, count(distinct app) As value', 'tx_t3rest_accesslog', $options);
        return $rows;
    }
    /**
     * Unterschiedliche Nutzer pro Tag
     *
     */
    public function getStatsInstallsByDay($sys='', $from='', $to='') {
/*
SELECT DATE(tstamp), count(distinct app)
FROM `tx_t3rest_accesslog`
WHERE

tstamp > '2012-10-01' AND tstamp < '2012-10-10'
GROUP BY DATE(tstamp)
ORDER BY tstamp desc
*/
        return $this->getStatsInstallsByFilter(array('system'=>$sys, 'from'=>$from, 'to'=>$to, 'period'=>'day'));
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


if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/t3rest/srv/class.tx_t3rest_srv_Logs.php']) {
    include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/t3rest/srv/class.tx_t3rest_srv_Logs.php']);
}
