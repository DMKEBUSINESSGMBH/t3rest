<?php
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2012-2017 Rene Nitzsche
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

tx_rnbase::load('tx_t3rest_decorator_Base');
tx_rnbase::load('tx_t3rest_util_FAL');
tx_rnbase::load('Tx_Rnbase_Database_Connection');

/**
 * Sammelt zusätzliche Daten
 *
 * @author Rene Nitzsche
 */
class tx_t3rest_decorator_News extends tx_t3rest_decorator_Base
{
    protected static $externals = array('dampictures', 'categories');

    protected function addDampictures($item, $configurations, $confId)
    {
        $pics = tx_t3rest_util_FAL::getFalPictures($item->getUid(), 'tt_news', 'tx_mktools_fal_images', $configurations, $confId);
        $item->setProperty('dampictures', $pics);
    }

    protected function addCategories($item)
    {
        $from = array('tt_news_cat As NEWSCAT JOIN tt_news_cat_mm AS NEWSCATMM ON NEWSCATMM.uid_foreign = NEWSCAT.UID',
                'tt_news_cat', 'NEWSCAT');
        $options['where'] = 'NEWSCATMM.uid_local = '. $item->getUid();
        $item->setProperty(
            'categories',
            Tx_Rnbase_Database_Connection::getInstance()->doSelect('uid,title,image', $from, $options)
        );
    }

    /**
     * @overwrite
     */
    protected function getExternals()
    {
        return self::$externals;
    }
    protected function getDecoratorId()
    {
        return 'news';
    }
    protected function handleItemBefore($item, $configurations, $confId)
    {
    }
    protected function handleItemAfter($item, $configurations, $confId)
    {
    }
}
