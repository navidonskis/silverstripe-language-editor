<?php

/**
 * @author    Donatas Navidonskis <donatas@navidonskis.com>
 * @since     2017
 * @class     LangCollectorTask
 *
 */
class LangCollectorTask extends BuildTask {

    protected $title = "Lang Collector Task";

    protected $description = "
		Parameters:
		- locale: Sets default locale
		- module: One or more modules to limit collection (comma-separated)
		- merge: Merge new strings with existing ones already defined (default: FALSE)
	";

    protected $locale;

    protected $merge = false;

    protected $module = [];

    protected $textCollector;

    public function init() {
        parent::init();

        $canAccess = (Director::isDev() || Director::is_cli() || Permission::check("ADMIN"));
        if (! $canAccess) {
            return Security::permissionFailure($this);
        }
    }

    public function run($request) {
        increase_time_limit_to();
        $definedLocale = Fluent::current_locale();
        $this->locale = Config::inst()->get('i18n', 'default_locale');

        try {
            $this->mergeWith($request->getVars());
        } catch (LangCollectorException $ex) {
            $this->writeMessage($ex->getMessage());
            exit;
        }

        if (count($this->module) > 0) {
            // set working locale
            Fluent::set_persist_locale($this->locale);

            $this->runCollector();

            // roll-back locale
            Fluent::set_persist_locale($definedLocale);
        }
    }

    /**
     * Run text collector
     *
     * @return void
     */
    protected function runCollector() {
        $this->textCollector = new i18nTextCollector($this->locale);

        $modules = $this->textCollector->collect($this->module, $this->merge);

        foreach ($modules as $moduleName => $entities) {
            if (count($entities) > 0) {
                // find or create a new module
                $module = LangModule::findOrCreate($moduleName);
                $this->writeMessage("Collecting module {$module->Name}");

                foreach ($entities as $namespace => $options) {
                    $value = $options[0];
                    $title = isset($options[1]) ? $options[1] : '';

                    $entity = $module->mergeOrAddEntity($namespace, $value, $title, $this->merge);

                    if ($entity instanceof LangEntity) {
                        $this->writeMessage("{$namespace} - $value");
                    }
                }
            }
        }
    }

    /**
     * Write output message
     *
     * @param string $message
     *
     * @return void
     */
    protected function writeMessage($message) {
        Debug::message($message, false);
    }

    /**
     * Collect parameters from given options array and merge
     * it with class properties (locale, module).
     *
     * @param array $options
     *
     * @return void
     * @throws EmptyModuleException
     * @throws InvalidLocaleException
     */
    protected function mergeWith($options = []) {
        if (array_key_exists('locale', $options) && ! empty($options['locale'])) {
            if (! i18n::validate_locale($options['locale'])) {
                throw new InvalidLocaleException("Given locale \"{$options['locale']}\" is invalid.");
            }

            $this->locale = $options['locale'];
        }

        if (array_key_exists('module', $options)) {
            if (empty($options['module'])) {
                throw new EmptyModuleException("Please set one or more (comma-separated) module names");
            }

            $this->module = explode(',', $options['module']);
        }

        if (array_key_exists('merge', $options)) {
            $this->merge = (bool) $options['merge'];
        }
    }
}