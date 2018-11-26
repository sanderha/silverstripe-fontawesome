<?php

use SilverStripe\Core\Config\Config;
use SilverStripe\Forms\GroupedDropdownField;
use SilverStripe\View\HTML;
use SilverStripe\View\Requirements;

class FontAwesomeIconField extends GroupedDropdownField
{
    public function __construct($name, $title = null)
    {
        parent::__construct($name, $title);
        $this->addExtraClass('font-awesome-picker dropdown no-change-track select2 no-chzn');
        $this->setEmptyString('Select an icon...');
    }


    private static $extensions = [
        FontAwesomeIconFieldConfigProvider::class
    ];

    public function getSource()
    {
        return Config::inst()->get(__CLASS__, 'icons')['categorized'];
    }

    public function getAttributes()
    {
        $attributes = parent::getAttributes();
        if($this->getHasEmptyDefault()){
            $attributes['data-placeholder'] = $this->getEmptyString();
        }

        if(!$this->Required()){
            $attributes['data-allow-clear'] = 'true';
        }


        return $attributes;
    }

    public function Field($properties = array())
    {

        $version = self::config()->get('version');
        Requirements::css("//maxcdn.bootstrapcdn.com/font-awesome/{$version}/css/font-awesome.min.css");
        Requirements::css('//cdnjs.cloudflare.com/ajax/libs/select2/4.0.1/css/select2.min.css');
        Requirements::javascript('//cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/js/select2.full.min.js');
        Requirements::javascript('hailwood/silverstripe-fontawesome:client/javascript/iconpicker.js');

        //additional styling to fit the moderno admin theme
        if (class_exists('ModernoAdminExtension')) {
            Requirements::css('hailwood/silverstripe-fontawesome:client/css/iconpicker-moderno.css');
        } else {
            Requirements::css('hailwood/silverstripe-fontawesome:client/css/iconpicker-standard.css');
        }

        $options = '';

        if ($this->getHasEmptyDefault()) {
            $options .= '<option></option>';
        }

        foreach ($this->getSource() as $value => $title) {
            if (is_array($title)) {
                $options .= "<optgroup label=\"$value\">";
                foreach ($title as $value2 => $title2) {
                    $disabled = '';
                    if (array_key_exists($value, $this->disabledItems)
                        && is_array($this->disabledItems[$value])
                        && in_array($value2, $this->disabledItems[$value])
                    ) {
                        $disabled = 'disabled="disabled"';
                    }
                    $selected = $value2 == $this->value ? " selected=\"selected\"" : "";
                    $options .= "<option$selected value=\"$value2\" data-unicode=\"{$title2['unicode']}\" $disabled>{$title2['label']}</option>";
                }
                $options .= "</optgroup>";
            } else { // Fall back to the standard dropdown field
                $disabled = '';
                if (in_array($value, $this->disabledItems)) {
                    $disabled = 'disabled="disabled"';
                }
                $selected = $value == $this->value ? " selected=\"selected\"" : "";
                $options .= "<option$selected value=\"$value\" $disabled>$title</option>";
            }
        }

        return HTML::createTag('select', $this->getAttributes(), $options);
    }

}