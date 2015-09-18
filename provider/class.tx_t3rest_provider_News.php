<?php
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2012-2014 Rene Nitzsche
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

require_once(t3lib_extMgm::extPath('rn_base') . 'class.tx_rnbase.php');

tx_rnbase::load('tx_t3rest_provider_AbstractBase');
tx_rnbase::load('tx_rnbase_filter_BaseFilter');
tx_rnbase::load('tx_t3rest_models_Provider');
tx_rnbase::load('tx_t3rest_provider_IProvider');


/**
 * This is a sample REST provider for tt_news
 * 
 * @author Rene Nitzsche
 */
class tx_t3rest_provider_News extends tx_t3rest_provider_AbstractBase {

	protected function handleRequest($configurations, $confId) {
		if($itemUid = $configurations->getParameters()->get('get')) {
			$confId = $confId.'get.';
			$item = $this->getItem($itemUid, $configurations, $confId, array(tx_cfcleague_util_ServiceRegistry::getMatchService(),'search'));
			$decorator = tx_rnbase::makeInstance('tx_t3rest_decorator_News');
			$data = $decorator->prepareItem($item, $configurations, $confId);
		}
		elseif($searchType = $configurations->getParameters()->get('search')) {
			$confId = $confId.'search.';
			$data = $this->getItems($searchType, $configurations, $confId);
		}
		return $data;
	}

	protected function getItems($searchType, $configurations, $confId) {
		$searcher = tx_rnbase_util_SearchBase::getInstance('tx_t3rest_search_News');
		$filter = tx_rnbase_filter_BaseFilter::createFilter($parameters, $configurations, null, $confId.'defined.'.$searchType.'.filter.');
		//$filter = tx_rnbase_filter_BaseFilter::createFilter($configurations->getParameters(), $configurations, null, $confId.'filter.');
		$fields = array();
		$options = array();
		//suche initialisieren
		$filter->init($fields, $options);
		$options['forcewrapper'] = 1;
//		if($_GET['test'] == 1)
//		$options['debug'] = 1;
		
		$prov = tx_rnbase::makeInstance('tx_rnbase_util_ListProvider');
		$searchCallback = array($searcher, 'search');
		$prov->initBySearch($searchCallback, $fields, $options);
		
		$this->configurations = $configurations;
		$this->confId = $confId;
		$this->decorator = tx_rnbase::makeInstance('tx_t3rest_decorator_News');
		$prov->iterateAll(array($this, 'loadItem'));
		
		return $this->items;

	}
	public function loadItem($item) {
		//
		$data = $this->decorator->prepareItem($item, $this->configurations, $this->confId);
		$this->items[] = $data;
	}
	protected function getBaseClass() {
		return 'tx_t3rest_models_Generic';
	}
	protected function getConfId() {
		return 'news.';
	}

// 	public function execute(tx_t3rest_models_Provider $provData) {

// 		$searcher = tx_rnbase_util_SearchBase::getInstance('tx_t3rest_search_News');

// 		$configurations = $provData->getConfigurations();
// 		$confId = 'news.';

// 		$filter = tx_rnbase_filter_BaseFilter::createFilter($configurations->getParameters(), $configurations, null, $confId.'filter.');
// 		$fields = array();
// 		$options = array();
// 		//suche initialisieren
// 		$filter->init($fields, $options);
// 		$options['forcewrapper'] = 1;

// 		// This part would be normally integrated into a filter class
// 		if($newsid = $configurations->getParameters()->getInt('newsid')) {
// 			$fields['NEWS.UID'][OP_EQ_INT] = $newsid;
// 		}
		
// 		// Soll ein PageBrowser verwendet werden?
// 		$filter->handlePageBrowser($configurations, 
// 			$confId.'pagebrowser', $viewData, $fields, $options, array(
// 			'searchcallback'=> array($searcher, 'search'), 'pbid' => 'news' )
// 		);

// 		$items = $searcher->search($fields, $options);
// 		$this->loadExternal($items, $configurations, $confId);

// 		return $items;
// 	}
// 	protected function loadExternal($items, $configurations, $confId) {
// 		$known = array('picture', 'dampicture', 'category');
// 		$externals = t3lib_div::trimExplode(',', $configurations->get($confId.'record.externals'));
// 		foreach($externals As $external) {
// 			if(!in_array($external, $known)) continue;
// 			foreach($items As $item) {
// 				switch ($external) {
// 					case 'category':
// 						$this->addCategories($item);
// 					;
// 					break;
// 					case 'dampicture':
// 						$this->addDamPictures($item, $configurations, $confId.'record.externals.dampicture.');
// 					;
// 					break;

// 					default:
// 						;
// 					break;
// 				}
// 			}
// 		}
// 	}
// 	protected function addDamPictures($item, $configurations, $confId) {
// 		$picCfg = $configurations->getKeyNames($confId);
// 		$fields = array('uid','title','file_hash');
// 		tx_rnbase::load('tx_rnbase_util_TSDAM');
// 		$ret = array();
// 		$files = tx_rnbase_util_TSDAM::fetchFiles('tt_news', $item->getUid(), 'tx_damnews_dam_images');
// 		foreach ($files['files'] As $uid => $filepath) {
// 			$data = new stdClass();
// 			$data->filepath = $filepath;
// 			$server = t3lib_div::getIndpEnv('TYPO3_SITE_URL');
// 			$data->absFilepath = $server.$filepath;
// 			$files['rows'][$uid]['file'] = $filepath;
// 			// Bild skalieren
// 			$cObj = $configurations->getCObj(1);
// 			$cObj->data = $files['rows'][$uid];
// 			foreach($picCfg As $picName) {
//         $data->$picName = $cObj->cObjGetSingle($configurations->get($confId.$picName),$configurations->get($confId.$picName.'.'));
//         if($data->$picName)
// 					$data->$picName = $server . $data->$picName;
// 			}
// 			foreach($fields As $fieldName) {
// 				$data->$fieldName = $files['rows'][$uid][$fieldName];
// 			}
// 			$ret[] = $data;
// 		}
// 		$item->dampictures = $ret;
// 	}
// 	protected function addCategories($item) {
// 		$from = array('tt_news_cat As NEWSCAT JOIN tt_news_cat_mm AS NEWSCATMM ON NEWSCATMM.uid_foreign = NEWSCAT.UID', 
// 			'tt_news_cat', 'NEWSCAT');
// 		$options['wrapperclass'] = 'tx_t3rest_models_Generic';
// 		$options['where'] = 'NEWSCATMM.uid_local = '. $item->getUid();
// 		$item->categories = tx_rnbase_util_DB::doSelect('uid,title,image', $from, $options);
// 	}
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/t3rest/provider/class.tx_t3rest_provider_News.php']) {
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/t3rest/provider/class.tx_t3rest_provider_News.php']);
}
