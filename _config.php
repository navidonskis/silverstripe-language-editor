<?php

define('LANG_EDITOR_BASE', basename(dirname(__FILE__)));

i18n::register_translator(
    new Zend_Translate([
        'adapter'        => 'LangTranslateAdapter',
        'locale'         => Fluent::current_locale(),
        'disableNotices' => true,
    ]),
    'legacy',
    6
);

Config::inst()->update('LeftAndMain', 'extra_requirements_javascript', [LANG_EDITOR_BASE.'/assets/javascript/app.js']);
Config::inst()->update('LeftAndMain', 'extra_requirements_css', [LANG_EDITOR_BASE.'/assets/styles/app.css']);