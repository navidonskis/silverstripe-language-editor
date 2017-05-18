<?php

/**
 * @author    Donatas Navidonskis <donatas@navidonskis.com>
 * @since     2017
 * @class     LangCollectorTask
 *
 */
class LangCollectorTask extends BuildTask {

    protected $title = "Lang Collector Task";

    protected $description = "Parameters:
- module: One or more modules to limit collection (comma-separated)
- merge: Merge new strings with existing ones already defined (default: FALSE)
- example: /dev/tasks/LangCollectorTask \"module=mysite,themes/default&merge=true\"
";

    /**
     * Merge the same created entities if task is running again
     *
     * @var bool
     */
    protected $merge = false;

    /**
     * Given modules from the user
     *
     * @var array
     */
    protected $module = [];

    /**
     * Text Collector instance to parse an entities
     *
     * @var i18nTextCollector
     */
    protected $textCollector;

    /**
     * Initialize within permissions
     *
     * @return SS_HTTPResponse
     */
    public function init() {
        parent::init();

        $canAccess = (Director::isDev() || Director::is_cli() || Permission::check("ADMIN"));
        if (! $canAccess) {
            return Security::permissionFailure($this);
        }
    }

    public function run($request) {
        increase_time_limit_to();

        $this->writeMessage($this->description);

        try {
            $this->mergeWith($request->getVars());
        } catch (LangCollectorException $ex) {
            $this->writeMessage($ex->getMessage());
            exit;
        }

        $this->runCollector();
    }

    /**
     * Run text collector
     *
     * @return void
     */
    protected function runCollector() {
        $this->textCollector = new i18nTextCollector();

        $modules = @$this->textCollector->collect($this->module, $this->merge);

        foreach ($modules as $moduleName => $entities) {
            if (count($entities) <= 0) {
                continue;
            }

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
     * it with class properties
     *
     * @param array $options
     *
     * @return void
     * @throws EmptyModuleException
     * @throws InvalidLocaleException
     */
    protected function mergeWith($options = []) {
        if (! array_key_exists('module', $options) || empty($options['module'])) {
            throw new EmptyModuleException("Please set one or more (comma-separated) module names");
        }

        $this->module = explode(',', $options['module']);

        if (array_key_exists('merge', $options)) {
            $this->merge = (bool) $options['merge'];
        }
    }
}