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

tx_rnbase::load('tx_rnbase_util_Logger');

/**
 * Utilty methods for DAM
 *
 * @author Rene Nitzsche
 */
class tx_t3rest_util_Objects
{
    /**
     * Wandelt ein record-Array in ein stdObject um.
     *
     * @param tx_rnbase_model_base $record
     * @param array $ignore
     * @return stdClass
     */
    public static function record2StdClass($item, $ignore = array())
    {
        if (empty($ignore)) {
            $ignore = self::getIgnoreFields();
        }
        $ignore = array_flip($ignore);
        $ret = new stdClass();
        if (!$item) {
            return $ret;
        }
        foreach ($item->getProperty() as $field => $value) {
            if (!$field || array_key_exists($field, $ignore)) {
                continue;
            }
            $ret->$field = $value;
        }
        foreach ($item as $fieldName => $value) {
            if (!$fieldName || array_key_exists($fieldName, $ignore)) {
                continue;
            }
            if ($fieldName == 'record') {
                continue;
            }
            $ret->$fieldName = $value;
        }

        return $ret;
    }
    /**
     * Liefert TYPO3 Felder, die man eigentlich nicht benötigt
     * @return array
     */
    public static function getIgnoreFields()
    {
        return array('hidden', 'deleted', 'pid','crdate','cruser_id','sorting');
    }
}
