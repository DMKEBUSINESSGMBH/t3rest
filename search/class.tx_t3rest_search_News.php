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

tx_rnbase::load('tx_t3rest_models_Provider');
tx_rnbase::load('tx_t3rest_provider_IProvider');

/**
 * REST provider for tt_news
 *
 * @author Rene Nitzsche
 */
class tx_t3rest_search_News extends tx_rnbase_util_SearchBase {

	protected function getTableMappings() {
		$tableMapping['NEWS'] = 'tt_news';
		$tableMapping['RELATEDNEWSMM'] = 'tt_news_related_mm';
		$tableMapping['RELATEDNEWS'] = 'tt_news';
		$tableMapping['NEWSCATMM'] = 'tt_news_cat_mm';

		// Hook to append other tables
		tx_rnbase_util_Misc::callHook('t3rest','search_news_getTableMapping_hook',
			array('tableMapping' => &$tableMapping), $this);
		return $tableMapping;
	}

	protected function useAlias() {
		return true;
	}

	protected function getBaseTableAlias() {
		return 'NEWS';
	}

	protected function getBaseTable() {
		return 'tt_news';
	}

	function getWrapperClass() {
		return 'tx_t3rest_models_Generic';
	}

	protected function getJoins($tableAliases) {
		$join = '';

		if (isset($tableAliases['NEWSCATMM'])) {
			$join .= ' JOIN tt_news_cat_mm AS NEWSCATMM ON NEWS.uid = NEWSCATMM.uid_local';
		}
		// TODO: Check visibility of related news.
		if (isset($tableAliases['RELATEDNEWSMM']) || (isset($tableAliases['RELATEDNEWS']))) {
			$join .= ' LEFT JOIN tt_news_related_mm AS RELATEDNEWSMM ON (RELATEDNEWSMM.uid_foreign = NEWS.uid AND RELATEDNEWSMM.tablenames="tt_news")';
		}
		if (isset($tableAliases['RELATEDNEWS'])) {
			$join .= ' JOIN tt_news AS RELATEDNEWS ON RELATEDNEWS.uid = RELATEDNEWSMM.uid_local';
		}

		// Hook to append other tables
		tx_rnbase_util_Misc::callHook('t3rest','search_news_getJoins_hook',
			array('join' => &$join, 'tableAliases' => $tableAliases), $this);
		return $join;
	}
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/t3rest/search/class.tx_t3rest_search_News.php']) {
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/t3rest/search/class.tx_t3rest_search_News.php']);
}
