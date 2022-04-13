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

/**
 * A default CacheHandler.
 * This cache has the same rules as the default TYPO3 page cache. The only difference is seperate
 * expire time for the plugin. It can be set by Typoscript:
 * plugints._caching.expires = 60 # time in seconds.
 */
class tx_t3rest_cache_CacheHandlerDefault
{
    private $cacheConfId;
    /** @var \Sys25\RnBase\Configuration\Processor */
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
        $ret = $this->getConfigurations()->get($this->getCacheConfId().$confId);

        return isset($ret) ? $ret : $altValue;
    }

    protected function getCacheName()
    {
        return $this->cacheName;
    }

    /**
     * @return \Sys25\RnBase\Configuration\Processor
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
     *
     * @param tx_t3rest_models_Provider $providerData
     *
     * @return string
     */
    protected function generateKey($providerData)
    {
        // Der Key muss den Provider, die Provider-Config und die ausgewählten Parameter eindeutig identifizieren
        // Die Parameter müssen gesondert zusammengestellt werden. Der cHash ist leider nicht verwendbar.
        $parameters = $providerData->getConfigurations()->getParameters()->getAll();
        $parameters = is_array($parameters) ? implode('', $parameters) : '';
        $key = $providerData->getClassname().'_';
        $key .= md5($providerData->getConfig().($parameters));

        return 'ac_p'.$key;
    }

    protected function getTimeout()
    {
        $timeout = (int) $this->getConfigValue('expire');

        return $timeout ? $timeout : 60; // default timeout 1 minute
    }

    /**
     * Save output data to cache.
     *
     * @param string $output
     * @param \Sys25\RnBase\Configuration\Processor $configurations
     * @param string $confId
     */
    public function setOutput($output, $providerData)
    {
        $cache = \Sys25\RnBase\Cache\CacheManager::getCache($this->getCacheName());
        $cache->set($this->generateKey($providerData), $output, $this->getTimeout());
    }

    /**
     * Get output data from cache.
     *
     * @param tx_t3rest_models_Provider $providerData
     *
     * @return string the output string
     */
    public function getOutput($providerData)
    {
        $cache = \Sys25\RnBase\Cache\CacheManager::getCache($this->getCacheName());
        $key = $this->generateKey($providerData);
        $out = $cache->get($key);

        return $out;
    }
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/rn_base/action/class.tx_rnbase_action_CacheHandlerDefault.php']) {
    include_once $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/rn_base/action/class.tx_rnbase_action_CacheHandlerDefault.php'];
}
