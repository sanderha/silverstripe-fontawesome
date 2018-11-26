<?php

use SilverStripe\Core\Config\Config;
use SilverStripe\Core\Flushable;
use SilverStripe\ORM\DataExtension;
use Symfony\Component\Yaml\Yaml;

class FontAwesomeIconFieldConfigProvider extends DataExtension implements Flushable
{

    public static function flush()
    {
        self::removeCache();
    }

    public static function get_extra_config($class, $extensionClass, $args)
    {
        $statics = [];
        if ($iconData = self::readCacheFile()) {
            $statics['icons'] = $iconData;
            $statics['icons']['from_cache'] = true;

            return $statics;
        } else {
            $statics['icons'] = self::updateCacheFile();
            $statics['icons']['from_cache'] = false;

            return $statics;
        }
    }

    protected static function cacheFilePath()
    {
        //$version = FontAwesomeIconField::config()->get('version');
        $version = '4.6.3';

        return TEMP_FOLDER . "font-awesome-{$version}-icons.cache";
    }

    protected static function cacheFileExists()
    {
        return file_exists(self::cacheFilePath());
    }

    protected static function removeCache()
    {
        if (self::cacheFileExists()) {
            unlink(self::cacheFilePath());
        }
    }

    protected static function updateCacheFile()
    {
        //$version = FontAwesomeIconField::config()->get('version');
        $version = '4.6.3';
        $fontAwesomeYamlText = file_get_contents("https://raw.githubusercontent.com/FortAwesome/Font-Awesome/v{$version}/src/icons.yml");

        $yamlToSave = [
            'list'        => [],
            'categorized' => []
        ];
        foreach (Yaml::parse($fontAwesomeYamlText)['icons'] as $icon) {
            $yamlToSave['list'][$icon['id']] = [
                'label'   => $icon['name'],
                'unicode' => $icon['unicode']
            ];
            foreach ($icon['categories'] as $iconCategory) {
                if (!array_key_exists($iconCategory, $yamlToSave['categorized'])) {
                    $yamlToSave['categorized'][$iconCategory] = [];
                }

                $yamlToSave['categorized'][$iconCategory][$icon['id']] = &$yamlToSave['list'][$icon['id']];
            }
        }

        file_put_contents(self::cacheFilePath(), serialize($yamlToSave));

        return $yamlToSave;
    }

    protected static function readCacheFile()
    {
        if (!self::cacheFileExists()) {
            return false;
        }
        $cacheData = unserialize(file_get_contents(self::cacheFilePath()));
        if (!is_array($cacheData) || empty($cacheData)) {
            return false;
        }

        return $cacheData;
    }
}