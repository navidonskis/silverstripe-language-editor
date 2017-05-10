<?php

/**
 * @author    Donatas Navidonskis <donatas@navidonskis.com>
 * @since     2017
 * @class     LangModule
 *
 * @property string Name
 *
 * @method DataList Entities
 */
class LangModule extends DataObject {

    /**
     * @var array
     * @config
     */
    private static $db = [
        'Name' => 'Varchar',
    ];

    /**
     * @var array
     * @config
     */
    private static $has_many = [
        'Entities' => 'LangEntity',
    ];

    /**
     * Find module by name or create a new one if not existing.
     *
     * @param string $name
     *
     * @return LangModule
     */
    public static function findOrCreate($name) {
        $module = static::get()->filter('Name', strtolower($name));

        if ($module->exists()) {
            return $module->first();
        }

        $module = static::create(['Name' => $name]);
        $module->write();

        return $module;
    }

    protected function onBeforeWrite() {
        $this->Name = strtolower($this->Name);

        parent::onBeforeWrite();
    }

    public static function getByName($name) {
        if(($module = static::get()->filter('Name', $name)->first()) instanceof LangModule) {
            return $module;
        }

        return false;
    }

    public function mergeOrAddEntity($namespace, $value, $title = '', $merge = false) {
        // try to check is existing entity already
        $entity = LangEntity::get()->filter('Namespace', $namespace)->first();

        if ($entity instanceof LangEntity) {
            if ($merge) { // if we can overwrite value
                $entity->Value = $value;

                if (! empty($title)) {
                    $entity->Title = $title;
                }

                $entity->write();

                if (! ($this->Entities()->byID($entity->ID) instanceof LangEntity)) {
                    $entity->ModuleID = $this->ID;
                    $entity->write();

                    $this->Entities()->add($entity);

                    return $entity;
                }
            }
        } else { // entity not existing
            $entity = LangEntity::create([
                'Namespace' => $namespace,
                'Value'     => $value,
                'Title'     => $title,
                'ModuleID'  => $this->ID,
            ]);

            if ($entity->write()) {
                $this->Entities()->add($entity);

                return $entity;
            }
        }
    }
}