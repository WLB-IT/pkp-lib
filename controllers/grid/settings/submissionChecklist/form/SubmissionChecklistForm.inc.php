<?php

/**
 * @file controllers/grid/settings/submissionChecklist/form/SubmissionChecklistForm.inc.php
 *
 * Copyright (c) 2014-2021 Simon Fraser University
 * Copyright (c) 2003-2021 John Willinsky
 * Distributed under the GNU GPL v3. For full terms see the file docs/COPYING.
 *
 * @class SubmissionChecklistForm
 * @ingroup controllers_grid_settings_submissionChecklist_form
 *
 * @brief Form for adding/edditing a submissionChecklist
 * stores/retrieves from an associative array
 */

use PKP\form\Form;

class SubmissionChecklistForm extends Form
{
    /** @var int The id for the submissionChecklist being edited */
    public $submissionChecklistId;

    /**
     * Constructor.
     *
     * @param null|mixed $submissionChecklistId
     */
    public function __construct($submissionChecklistId = null)
    {
        $this->submissionChecklistId = $submissionChecklistId;
        parent::__construct('controllers/grid/settings/submissionChecklist/form/submissionChecklistForm.tpl');

        // Validation checks for this form
        $this->addCheck(new \PKP\form\validation\FormValidatorLocale($this, 'checklistItem', 'required', 'maganer.setup.submissionChecklistItemRequired'));
        $this->addCheck(new \PKP\form\validation\FormValidatorPost($this));
        $this->addCheck(new \PKP\form\validation\FormValidatorCSRF($this));
    }

    /**
     * Initialize form data from current settings.
     *
     * @see Form::initData
     *
     * @param array $args
     */
    public function initData($args)
    {
        $request = Application::get()->getRequest();
        $context = $request->getContext();

        $submissionChecklistAll = $context->getData('submissionChecklist');
        $checklistItem = [];
        // preparea  localizable array for this checklist Item
        foreach (AppLocale::getSupportedLocales() as $locale => $name) {
            $checklistItem[$locale] = null;
        }

        // if editing, set the content
        // use of 'content' as key is for backwards compatibility
        if (isset($this->submissionChecklistId)) {
            foreach (AppLocale::getSupportedLocales() as $locale => $name) {
                if (!isset($submissionChecklistAll[$locale][$this->submissionChecklistId]['content'])) {
                    $checklistItem[$locale] = '';
                } else {
                    $checklistItem[$locale] = $submissionChecklistAll[$locale][$this->submissionChecklistId]['content'];
                }
            }
        }
        // assign the data to the form
        $this->_data = [ 'checklistItem' => $checklistItem	];

        // grid related data
        $this->_data['gridId'] = $args['gridId'];
        $this->_data['rowId'] = $args['rowId'] ?? null;
    }

    /**
     * @copydoc Form::fetch()
     *
     * @param null|mixed $template
     */
    public function fetch($request, $template = null, $display = false)
    {
        AppLocale::requireComponents(LOCALE_COMPONENT_APP_MANAGER);
        return parent::fetch($request, $template, $display);
    }

    /**
     * Assign form data to user-submitted data.
     *
     * @see Form::readInputData()
     */
    public function readInputData()
    {
        $this->readUserVars(['submissionChecklistId', 'checklistItem']);
        $this->readUserVars(['gridId', 'rowId']);
    }

    /**
     * @copydoc Form::execute()
     */
    public function execute(...$functionArgs)
    {
        $request = Application::get()->getRequest();
        $router = $request->getRouter();
        $context = $router->getContext($request);
        $submissionChecklistAll = $context->getData('submissionChecklist');
        $locale = AppLocale::getPrimaryLocale();
        //FIXME: a bit of kludge to get unique submissionChecklist id's
        $this->submissionChecklistId = ($this->submissionChecklistId != null ? $this->submissionChecklistId : (max(array_keys($submissionChecklistAll[$locale])) + 1));

        $order = 0;
        foreach ($submissionChecklistAll[$locale] as $checklistItem) {
            if ($checklistItem['order'] > $order) {
                $order = $checklistItem['order'];
            }
        }
        $order++;

        $checklistItem = $this->getData('checklistItem');
        foreach (AppLocale::getSupportedLocales() as $locale => $name) {
            if (isset($checklistItem[$locale])) {
                $submissionChecklistAll[$locale][$this->submissionChecklistId]['content'] = $checklistItem[$locale];
                $submissionChecklistAll[$locale][$this->submissionChecklistId]['order'] = $order;
            }
        }

        $context->updateSetting('submissionChecklist', $submissionChecklistAll, 'object', true);
        parent::execute(...$functionArgs);
        return true;
    }
}
