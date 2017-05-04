<?php

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2012 Rene Nitzsche (rene@system25.de)
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

tx_rnbase::load('tx_rnbase_action_ICacheHandler');
tx_rnbase::load('tx_rnbase_cache_Manager');




/**
 * A default CacheHandler.
 * This cache has the same rules as the default TYPO3 page cache. The only difference is seperate
 * expire time for the plugin. It can be set by Typoscript:
 * plugints._caching.expires = 60 # time in seconds
 */
class tx_t3rest_cache_CacheHandlerDefault
{
    private $cacheConfId;
    /** @var tx_rnbase_configurations $configurations */
    private $configurations;
    private $cacheName;

    public function __construct($configurations, $confId)
    {
        $this->configurations = $configurations;
        $this->cacheConfId = $confId;
        $this->cacheName = $this->getConfigValue('name', 't3rest');
    }
    protected function getConfigValue($confId, $altValue = '')
    {
        $ret = $this->getConfigurations()->get($this->getCacheConfId() . $confId);

        return isset($ret) ? $ret : $altValue;
    }
    protected function getCacheName()
    {
        return $this->cacheName;
    }
    /**
     * @return tx_rnbase_configurations
     */
    protected function getConfigurations()
    {
        return $this->configurations;
    }
    /**
     * @return string
     */
    protected function getCacheConfId()
    {
        return $this->cacheConfId;
    }
    /**
     * Generate a key used to store data to cache.
     * @param tx_t3rest_models_Provider $providerData
     * @return string
     */
    protected function generateKey($providerData)
    {
        // Der Key muss den Provider, die Provider-Config und die ausgewählten Parameter eindeutig identifizieren
        // Die Parameter müssen gesondert zusammengestellt werden. Der cHash ist leider nicht verwendbar.
        $parameters = $providerData->getConfigurations()->getParameters()->getAll();
        $parameters = is_array($parameters) ? implode('', $parameters) : '';
        $key = $providerData->getClassname().'_';
        $key .= md5($providerData->getConfig(). ($parameters));

        return 'ac_p'. $key;
    }

    protected function getTimeout()
    {
        $timeout = (int) $this->getConfigValue('expire');

        return $timeout ? $timeout : 60; // default timeout 1 minute
    }
    /**
     * Save output data to cache
     * @param string $output
     * @param tx_rnbase_configurations $configurations
     * @param string $confId
     */
    public function setOutput($output, $providerData)
    {
        $cache = tx_rnbase_cache_Manager::getCache($this->getCacheName());
        $cache->set($this->generateKey($providerData), $output, $this->getTimeout());
    }

    /**
     * Get output data from cache
     * @param tx_t3rest_models_Provider $providerData
     * @return string the output string
     */
    public function getOutput($providerData)
    {
        $cache = tx_rnbase_cache_Manager::getCache($this->getCacheName());
        $key = $this->generateKey($providerData);
        $out = $cache->get($key);

//t3lib_div::debug(array($configurations->getPluginId(), $configurations->cObj->data), $confId.' - class.tx_rnbase_action_CacheHandlerDefault.php Line: ' . __LINE__); // TODO: remove me
//t3lib_div::debug($out, $key.' - From CACHE class.tx_rnbase_action_CacheHandlerDefault.php Line: ' . __LINE__); // TODO: remove me

        return $out;
    }
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/rn_base/action/class.tx_rnbase_action_CacheHandlerDefault.php']) {
    include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/rn_base/action/class.tx_rnbase_action_CacheHandlerDefault.php']);
}
