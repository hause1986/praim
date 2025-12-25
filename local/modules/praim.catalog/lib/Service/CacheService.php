<?php

namespace Praim\Catalog\Service;

use Bitrix\Main\Data\Cache;

/**
 * @internal
 */
class CacheService
{

    static public $cacheDir = '/praim/catalog/';

    static public function clearCache()
    {
        $cache = Cache::createInstance();
        $cache->CleanDir( self::$cacheDir );

    }
}