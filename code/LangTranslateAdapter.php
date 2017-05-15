<?php

/**
 * @author    Donatas Navidonskis <donatas@navidonskis.com>
 * @since     2017
 * @class     LangTranslateAdapter
 *
 */
class LangTranslateAdapter extends i18nSSLegacyAdapter {

    /**
     *
     * @param array|string $messageId
     * @param string       $locale
     *
     * @return string
     */
    public function translate($messageId, $locale = null) {
        if (class_exists('LangEntity') && class_exists('LangModule')) {
            $query = DB::query("SHOW TABLES LIKE 'LangEntity'");

            if (count($query->column()) > 0) {
                if ($result = $this->getFromCache($messageId, $locale)) {
                    return $result;
                }

                if (! is_array($messageId)) {
                    try {
                        $entity = LangEntity::getByNamespace($messageId);
                    } catch (SS_DatabaseException $ex) {
                        return parent::translate($messageId, $locale);
                    }

                    if ($entity) {
                        $this->storeToCache($messageId, $entity->Value, $locale);

                        return $entity->Value;
                    }
                }
            }
        }

        return parent::translate($messageId, $locale);
    }

    /**
     * @return string
     */
    public function toString() {
        return static::class;
    }

    /**
     * @param string $namespace
     * @param string $locale
     *
     * @return string|false
     */
    public function getFromCache($namespace, $locale = null) {
        $namespace = static::filterCacheKey($namespace);

        if (! is_null($locale)) {
            $locale = strtoupper(explode('_', $locale)[0]);
        }

        $cacheKey = sprintf('_%s_%s', $namespace, $locale);
        $cache = SS_Cache::factory(LangEditor::class);
        $result = $cache->load($cacheKey);

        if ($result) {
            return $result;
        }

        return false;
    }

    /**
     * @param string $namespace
     * @param string $value
     * @param string $locale
     *
     * @return void
     */
    public function storeToCache($namespace, $value, $locale = null) {
        $namespace = static::filterCacheKey($namespace);

        if (! is_null($locale)) {
            $locale = strtoupper(explode('_', $locale)[0]);
        }

        $cacheKey = sprintf('_%s_%s', $namespace, $locale);
        $cache = SS_Cache::factory(LangEditor::class);
        $cache->save($value, $cacheKey);
    }

    /**
     * @param string $value
     *
     * @return string
     */
    public static function filterCacheKey($value) {
        return strtolower(trim(preg_replace('~[^a-zA-Z0-9_]+~u', '_', LangFulltextBooleanFilter::convertForeignToLatin($value))));
    }

}