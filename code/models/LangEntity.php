<?php

/**
 * @author    Donatas Navidonskis <donatas@navidonskis.com>
 * @since     2017
 * @class     LangEntity
 *
 * @property int    ModuleID
 * @property string Namespace
 * @property string Value
 * @property string Title
 *
 * @method LangModule Module
 */
class LangEntity extends DataObject {

    /**
     * @var array
     * @config
     */
    private static $db = [
        'Namespace' => 'Varchar(318)',
        'Value'     => 'Varchar(318)',
        'Title'     => 'Varchar(512)',
    ];

    /**
     * @var array
     * @config
     */
    private static $translate = ['Value'];

    /**
     * @var array
     * @config
     */
    private static $extensions = ['FluentExtension'];

    /**
     * @var array
     * @config
     */
    private static $has_one = [
        'Module' => 'LangModule',
    ];

    /**
     * @var array
     * @config
     */
    private static $create_table_options = [
        'MySQLDatabase' => 'ENGINE=MyISAM',
    ];

    /**
     * @var array
     * @config
     */
    private static $indexes = [
        'SearchFields' => [
            'type'  => 'fulltext',
            'name'  => 'SearchFields',
            'value' => '"Namespace", "Value", "Title"',
        ],
    ];

    /**
     * @param string $namespace
     *
     * @return LangEntity
     */
    public static function getByNamespace($namespace) {
        return static::get()->filter('Namespace', $namespace)->first();
    }
}