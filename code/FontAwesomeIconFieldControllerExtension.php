<?php

use SilverStripe\Core\Config\Config;
use SilverStripe\ORM\DataExtension;
use SilverStripe\View\Requirements;

class FontAwesomeIconFieldControllerExtension extends DataExtension
{


    public function contentcontrollerInit()
    {

        if (Config::inst()->get(FontAwesomeIconField::class, 'autoload_css')) {
            $version = Config::inst()->get('FontAwesomeIconField', 'version');
            Requirements::css("//maxcdn.bootstrapcdn.com/font-awesome/{$version}/css/font-awesome.min.css");
        }
    }
}