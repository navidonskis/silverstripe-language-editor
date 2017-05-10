<?php

/**
 * @author    Donatas Navidonskis <donatas@navidonskis.com>
 * @since     2017
 * @class     LangEditor
 *
 */
class LangEditor extends LeftAndMain implements PermissionProvider {

    /**
     * @var string
     * @config
     */
    private static $menu_title = 'Lang Editor';

    /**
     * @var string
     * @config
     */
    private static $url_segment = 'lang-editor';

    /**
     * @var float
     * @config
     */
    private static $menu_priority = -0.6;

    /**
     * @var array
     * @config
     */
    private static $allowed_actions = [
        'FormEntities',
    ];

    /**
     * Default configuration of items per page.
     * Set "null" if unlimited.
     *
     * @var int
     * @config
     */
    private static $items_per_page = 30;

    /**
     * @return array
     */
    public function providePermissions() {
        $title = _t('LangEditor.MENU_TITLE', 'Lang Editor');

        return [
            "CMS_ACCESS_LangEditorAdmin" => [
                'name'     => _t('CMSMain.ACCESS', "Access to '{title}' section", ['title' => $title]),
                'category' => _t('Permission.CMS_ACCESS_CATEGORY', 'CMS Access'),
            ],
        ];
    }

    /**
     *
     * @param Member $member
     *
     * @return int|bool
     */
    public function canView($member = null) {
        return Permission::check('CMS_ACCESS_LangEditorAdmin', 'any', $member);
    }

    public function init() {
        static::config()->menu_title = _t('LangEditor.MENU_TITLE', 'Lang Editor');

        return parent::init();
    }

    public static function flushCache() {
        $cache = SS_Cache::factory(static::class);
        $cache->clean(Zend_Cache::CLEANING_MODE_ALL);
    }

    public function getModules() {
        $list = new ArrayList();
        $parameters = $this->getRequest()->getVars();

        unset($parameters['url']);
        unset($parameters['start']);
        unset($parameters['search']);

        LangModule::get()->each(function (LangModule $module) use ($parameters, &$list) {
            if (array_key_exists('moduleId', $parameters) && $parameters['moduleId'] == $module->ID) {
                $module->Current = true;
            }

            $parameters['moduleId'] = $module->ID;

            $module->Link = Controller::join_links(
                Controller::curr()->Link(),
                "?".http_build_query($parameters)
            );

            $list->push($module);
        });

        if (! $list->filter('Current', true)->first() && $list->count() > 0) {
            $list->first()->Current = true;
        }

        return $list;
    }

    public function getCurrentModule() {
        return $this->getModules()->filter('Current', true)->first();
    }

    public function getCurrentSearchTerm() {
        return $this->getRequest()->getVar('search');
    }

    public function FormEntities() {
        /** @var LangModule $module */
        if ($module = $this->getModules()->filter('Current', true)->first()) {
            $entities = $module->Entities();
            $searchTerm = $this->getRequest()->getVar('search');

            if (! empty($searchTerm)) {
                $entities = $entities->filter('SearchFields:LangFulltextBoolean', $searchTerm);
            }

            $parameters = $this->getRequest()->getVars();

            unset($parameters['url']);

            $parameters = count($parameters) > 0 ? '?'.http_build_query($parameters) : '';

            $form = new FormEntities(Controller::curr(), __FUNCTION__, $entities);
            $form->clearMessage();
            $form->setFormAction(Controller::join_links(
                $this->Link('FormEntities'),
                $parameters
            ));

            return $form;
        }

        return false;
    }
}