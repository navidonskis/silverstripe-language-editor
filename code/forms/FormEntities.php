<?php

/**
 * @author    Donatas Navidonskis <donatas@pixelneat.com>
 * @since     2017
 * @class     FormEntities
 *
 */
class FormEntities extends Form {

    public function __construct(Controller $controller, $name, DataList $entities) {
        $fields = new FieldList();

        /** @var LangEntity $entity */
        foreach ($entities as $entity) {
            $fields->push(
                $entityField = TextField::create("Entities[{$entity->ID}]", $entity->Namespace, $entity->Value)
            );

            if (! empty($entity->Title)) {
                $entityField->setRightTitle($entity->Title);
                $entityField->setAttribute('title', $entity->Namespace);
            }
        }

        $actions = new FieldList([
            $button = new FormAction('doSave', _t('FormEntities.SAVE', 'Save')),
        ]);

        $button->addExtraClass('action ss-ui-action-constructive ss-ui-button ui-button ui-widget ui-state-default ui-corner-all ui-button-text-icon-primary');

        parent::__construct($controller, $name, $fields, $actions, null);
    }

    public function doSave(array $data = [], Form $form) {
        if (array_key_exists('Entities', $data) && $data['Entities'] > 0) {
            foreach ($data['Entities'] as $id => $value) {
                /** @var LangEntity $entity */
                $entity = LangEntity::get()->byID($id);
                $entity->Value = $value;
                $entity->write();
            }
        }

        if (! Director::is_ajax()) {
            return $form->getController()->redirectBack();
        }
    }

}