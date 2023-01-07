<?php
/**
 * FPCustomCode v1.0.0
 * https://github.com/flexpik/ps-custom-code
 * Copyright (c) 2022 FlexPik.com
 * Released under the MIT license
 * Date: 2022-11-06
 */

if (!defined('_PS_VERSION_')) {
    exit;
}

$autoloadPath = __DIR__ . '/vendor/autoload.php';
if (file_exists($autoloadPath)) {
    require_once $autoloadPath;
}

class FPCustomCode extends Module
{
    private string $html = '';
    public array $fieldsForm;
    public array $errors = [];
    private string $templateHead = 'module:fpcustomcode/views/templates/hook/header.tpl';
    private string $templateBody = 'module:fpcustomcode/views/templates/hook/body.tpl';

    public function __construct()
    {
        $this->name = 'fpcustomcode';
        $this->tab = 'front_office_features';
        $this->version = '1.0.0';
        $this->author = 'FlexPik';
        $this->need_instance = 1;
        $this->bootstrap = true;
        parent::__construct();
        $this->displayName = $this->trans('Custom JS and CSS', [], 'Modules.FPCustomCode.Admin');
        $this->description = $this->trans('This module allows you to add custom JavaScript or CSS
        to your site.', [], 'Modules.FPCustomCode.Admin');
        $this->ps_versions_compliancy = ['min' => '1.7', 'max' => _PS_VERSION_];
    }

    /**
     * @return bool
     *
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     */
    private function installTab(): bool
    {
        $customCodesTabId = (int) Tab::getIdFromClassName('AdminCustomCodes');
        $tab = new Tab($customCodesTabId ?? null);
        $tab->name = $this->stringToMultilangArray('Custom JS and CSS');
        $tab->class_name = 'AdminCustomCodes';
        $tab->id_parent = (int) Tab::getIdFromClassName('AdminParentModulesSf');
        $tab->module = $this->name;
        $tab->position = 0;
        $tab->active = 1;

        return $tab->save();
    }

    /**
     * @throws PrestaShopException
     * @throws PrestaShopDatabaseException
     */
    private function uninstallTab(): bool
    {
        $tabId = (int) Tab::getIdFromClassName('AdminCustomCode');
        $tab = new Tab($tabId);

        return $tab->delete();
    }

    /**
     * @return bool
     *
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     */
    public function install(): bool
    {
        include dirname(__FILE__) . '/sql/install.php';

        $result = true;
        if (!parent::install()
            || !Configuration::updateValue('FP_CUSTOM_CODE_GLOBAL_CSS', '')
            || !Configuration::updateValue('FP_CUSTOM_CODE_GLOBAL_JS', '')
            || !$this->registerHook('displayHeader')
            || !$this->registerHook('displayBeforeBodyClosingTag')
            || !$this->registerHook('displayBackOfficeHeader')
            || !$this->installTab()
        ) {
            $result = false;
        }

        return $result;
    }

    /**
     * @return bool
     *
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     */
    public function uninstall(): bool
    {
        Configuration::deleteByName('FP_CUSTOM_CODE_GLOBAL_CSS');
        Configuration::deleteByName('FP_CUSTOM_CODE_GLOBAL_JS');

        if (!parent::uninstall() || !$this->uninstallTab()) {
            return false;
        }

        include dirname(__FILE__) . '/sql/uninstall.php';

        return true;
    }

    /**
     * @return string
     *
     * @throws PrestaShopException
     */
    public function getContent(): string
    {
        $this->context->controller->addCSS($this->_path . 'views/css/admin.css');
        $this->initFieldsForm();

        if (Tools::getValue('save_custom_code')) {
            foreach ($this->fieldsForm as $form) {
                foreach ($form['form']['input'] as $field) {
                    if (isset($field['validation'])) {
                        $errors = [];
                        $value = Tools::getValue($field['name']);
                        if (isset($field['required']) && $field['required'] && $value) {
                            $errors[] = sprintf(
                                Tools::displayError('Field "%s" is required.'),
                                $field['label']
                            );
                        } elseif ($value) {
                            $field_validation = $field['validation'];
                            if (!Validate::$field_validation($value)) {
                                $errors[] = sprintf(
                                    Tools::displayError('Field "%s" is invalid.'),
                                    $field['label']
                                );
                            }
                        }
                        // Set default value
                        if ($value === false && isset($field['default_value'])) {
                            $value = $field['default_value'];
                        }

                        if (count($errors)) {
                            $this->errors = array_merge($this->errors, $errors);
                        } elseif ($value === false) {
                            switch ($field['validation']) {
                                case 'isUnsignedId':
                                case 'isUnsignedInt':
                                case 'isInt':
                                case 'isBool':
                                    $value = 0;
                                    break;
                                default:
                                    $value = '';
                                    break;
                            }
                            Configuration::updateValue('FP_CUSTOM_CODE_' . strtoupper($field['name']), $value);
                        } else {
                            Configuration::updateValue(
                                'FP_CUSTOM_CODE_' . strtoupper($field['name']),
                                htmlentities($value)
                            );
                        }
                    }
                }
            }

            if (count($this->errors)) {
                $this->html .= $this->displayError(implode('<br/>', $this->errors));
            } else {
                $this->html .= $this->displayConfirmation(
                    $this->getTranslator()->trans('Settings updated', [], 'Modules.FPCustomCode.Admin')
                );
            }
        }

        $helper = $this->initForm();

        return $this->html . $helper->generateForm($this->fieldsForm);
    }

    /**
     * @return void
     */
    protected function initFieldsForm(): void
    {
        $this->fieldsForm[0]['form'] = [
            'legend' => [
                'title' => $this->displayName,
                'icon' => 'icon-cogs',
            ],
            'input' => [
                [
                    'type' => 'textarea',
                    'label' => $this->trans('CSS code:', [],
                        'Modules.FPCustomCode.Admin'),
                    'name' => 'global_css',
                    'lang' => false,
                    'cols' => 80,
                    'rows' => 20,
                    'desc' => $this->trans('Code here will be injected before the closing </head> tag on every page in your site.',
                        [], 'Modules.FPCustomCode.Admin'),
                    'validation' => 'isAnything',
                ],
                [
                    'type' => 'textarea',
                    'label' => $this->trans('JavaScript code:', [],
                        'Modules.FPCustomCode.Admin'),
                    'name' => 'global_js',
                    'lang' => false,
                    'cols' => 80,
                    'rows' => 20,
                    'desc' => $this->trans('Code here will be injected before the closing </body> tag on every page in your site.',
                        [], 'Modules.FPCustomCode.Admin'),
                    'validation' => 'isAnything',
                ],
            ],
            'submit' => [
                'title' => $this->trans('Save', [], 'Modules.FPCustomCode.Admin'),
            ],
        ];
    }

    /**
     * @param string $source
     *
     * @return array
     */
    public function stringToMultilangArray(string $source): array
    {
        $return = [];
        foreach (Language::getLanguages() as $language) {
            $return[(int) $language['id_lang']] = $source;
        }

        return $return;
    }

    /**
     * @return HelperForm
     *
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     */
    protected function initForm(): HelperForm
    {
        $helper = new HelperForm();
        $helper->show_toolbar = false;
        $helper->table = $this->table;
        $helper->module = $this;
        $lang = new Language((int) Configuration::get('PS_LANG_DEFAULT'));
        $helper->default_form_language = $lang->id;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') ?? 0;

        $helper->identifier = $this->identifier;
        $helper->submit_action = 'save_custom_code';
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules',
            false) . '&configure=' . $this->name . '&tab_module=' . $this->tab . '&module_name=' . $this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->tpl_vars = [
            'fields_value' => $this->getConfigFieldsValues(),
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id,
        ];

        return $helper;
    }

    /**
     * @return array
     */
    private function getConfigFieldsValues(): array
    {
        return [
            'global_css' => Configuration::get('FP_CUSTOM_CODE_GLOBAL_CSS'),
            'global_js' => Configuration::get('FP_CUSTOM_CODE_GLOBAL_JS'),
        ];
    }

    /**
     * @param string $pageName
     *
     * @return int
     */
    public function getPageType(string $pageName): int
    {
        switch ($pageName) {
            case 'index':
                $type = CustomCode::PAGE_TYPE_HOME;
                break;
            case 'category':
                $type = CustomCode::PAGE_TYPE_CATEGORY;
                break;
            case 'cms':
                $type = CustomCode::PAGE_TYPE_CMS;
                break;
            case 'product':
                $type = CustomCode::PAGE_TYPE_PRODUCT;
                break;
            case 'cart':
                $type = CustomCode::PAGE_TYPE_CART;
                break;
            case 'checkout':
                $type = CustomCode::PAGE_TYPE_CHECKOUT;
                break;
            case 'module-prestablog-blog':
                $type = CustomCode::PAGE_TYPE_PS_BLOG;
                break;
            case 'module-smartblog-all':
            case 'module-smartblog-search':
            case 'module-smartblog-tagpost':
            case 'module-smartblog-archive':
            case 'module-smartblog-category':
            case 'module-smartblog-details':
                $type = CustomCode::PAGE_TYPE_SMART_BLOG;
                break;
            case 'pagenotfound':
                $type = CustomCode::PAGE_TYPE_PAGE_NOT_FOUND;
                break;
            default:
                $type = false;
                break;
        }

        return $type;
    }

    /**
     * @return string
     *
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     */
    public function hookDisplayHeader(): string
    {
        $pageName = $this->context->controller->getPageName();

        if (!$this->isCached($this->templateHead, $this->getCacheId($pageName))) {
            $pageHeadJs = '';
            $pageHeadCss = '';
            $globalCss = Configuration::get('FP_CUSTOM_CODE_GLOBAL_CSS');
            $pageType = $this->getPageType($pageName);

            if ($pageType) {
                $customCodes = CustomCode::getByTypeAndPosition($pageType, CustomCode::POSITION_HEAD);
                foreach ($customCodes as $customCode) {
                    $pageHeadJs .= html_entity_decode($customCode->code_js);
                    $pageHeadCss .= html_entity_decode($customCode->code_css);
                }
            }

            $this->smarty->assign('custom_code', [
                'js' => $pageHeadJs,
                'css' => $pageHeadCss,
                'global_css' => html_entity_decode($globalCss),
            ]);
        }

        return $this->fetch($this->templateHead, $this->getCacheId($pageName));
    }

    /**
     * @return string
     *
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     */
    public function hookDisplayBeforeBodyClosingTag(): string
    {
        $pageName = $this->context->controller->getPageName();

        if (!$this->isCached($this->templateBody, $this->getCacheId($pageName))) {
            $pageBodyJs = '';
            $pageBodyCss = '';
            $globalJs = Configuration::get('FP_CUSTOM_CODE_GLOBAL_JS');
            $pageType = $this->getPageType($pageName);

            if ($pageType) {
                $customCodes = CustomCode::getByTypeAndPosition($pageType, CustomCode::POSITION_BODY);
                foreach ($customCodes as $customCode) {
                    $pageBodyJs .= html_entity_decode($customCode->code_js);
                    $pageBodyCss .= html_entity_decode($customCode->code_css);
                }
            }

            $this->smarty->assign('custom_code', [
                'js' => $pageBodyJs,
                'css' => $pageBodyCss,
                'global_js' => html_entity_decode($globalJs),
            ]);
        }

        return $this->fetch($this->templateBody, $this->getCacheId($pageName));
    }

    /**
     * Add the CSS & JavaScript files in BO.
     */
    public function hookDisplayBackOfficeHeader()
    {
        if (Tools::getValue('configure') === $this->name
            || 'AdminCustomCodes' == $this->context->controller->controller_name ) {
            $this->context->controller->addJS($this->_path.'views/lib/codemirror/codemirror.min.js');
            $this->context->controller->addJS($this->_path.'views/lib/codemirror/mode/javascript.js');
            $this->context->controller->addJS($this->_path.'views/lib/codemirror/mode/css.js');
            $this->context->controller->addJS($this->_path.'views/js/back.js');

            $this->context->controller->addCSS($this->_path.'views/lib/codemirror/codemirror.css');
            $this->context->controller->addCSS($this->_path.'views/lib/codemirror/codemirror.css');
            $this->context->controller->addCSS($this->_path.'views/css/back.css');
        }
    }
}
