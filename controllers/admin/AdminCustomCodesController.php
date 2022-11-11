<?php
/**
 * FPCustomCode v1.0.0
 * https://github.com/flexpik/ps-custom-code
 * Copyright (c) 2022 FlexPik.com
 * Released under the MIT license
 * Date: 2022-11-06
 */
class AdminCustomCodesController extends ModuleAdminController
{
    public function __construct()
    {
        $this->table = 'fp_custom_code';
        $this->className = 'CustomCode';
        $this->identifier = 'id_custom_code';
        $this->lang = false;
        $this->allow_export = true;
        $this->addRowAction('edit');
        $this->addRowAction('delete');
        $this->bootstrap = true;
        $this->context = Context::getContext();
        $this->context->controller = $this;

        parent::__construct();

        $this->meta_title = $this->trans('Custom CSS and JS codes', [], 'Modules.FPCustomCode.Admin');

        $this->bulk_actions = [
            'delete' => [
                'text' => $this->l('Delete selected'),
                'confirm' => $this->l('Delete selected items?'),
                'icon' => 'icon-trash',
            ],
        ];

        $this->fields_list = [
            'id_custom_code' => [
                'title' => $this->trans('ID', [], 'Modules.FPCustomCode.Admin'),
                'align' => 'center',
                'filter_key' => 'a!id_custom_code',
            ],
            'type' => [
                'title' => $this->trans('Page', [], 'Modules.FPCustomCode.Admin'),
                'align' => 'center',
                'type' => 'text',
                'callback' => 'getPageTypeName',
                'filter' => false,
                'search' => false,
            ],
            'position' => [
                'title' => $this->trans('Position', [], 'Modules.FPCustomCode.Admin'),
                'align' => 'center',
                'type' => 'text',
                'callback' => 'getPositionName',
                'filter' => false,
                'search' => false,
            ],
            'active' => [
                'title' => $this->trans('Active', [], 'Modules.FPCustomCode.Admin'),
                'width' => '70',
                'align' => 'center',
                'active' => 'status',
                'type' => 'bool',
                'orderby' => true,
                'filter' => true,
                'search' => true,
            ],
            'date_add' => [
                'title' => $this->trans('Created', [], 'Modules.FPCustomCode.Admin'),
                'havingFilter' => true,
                'type' => 'datetime',
            ],
            'date_upd' => [
                'title' => $this->trans('Last Updated', [], 'Modules.FPCustomCode.Admin'),
                'havingFilter' => true,
                'type' => 'datetime',
            ],
        ];

        $this->fields_form = [
            'input' => [
                [
                    'type' => 'select',
                    'label' => $this->trans('Page', [], 'Modules.FPCustomCode.Admin'),
                    'name' => 'type',
                    'default_value' => CustomCode::PAGE_TYPE_PRODUCT,
                    'required' => true,
                    'options' => [
                        'query' => [
                            [
                                'id_type' => CustomCode::PAGE_TYPE_HOME,
                                'name' => $this->getPageTypeName(CustomCode::PAGE_TYPE_HOME),
                            ],
                            [
                                'id_type' => CustomCode::PAGE_TYPE_CATEGORY,
                                'name' => $this->getPageTypeName(CustomCode::PAGE_TYPE_CATEGORY),
                            ],
                            [
                                'id_type' => CustomCode::PAGE_TYPE_PRODUCT,
                                'name' => $this->getPageTypeName(CustomCode::PAGE_TYPE_PRODUCT),
                            ],
                            [
                                'id_type' => CustomCode::PAGE_TYPE_CART,
                                'name' => $this->getPageTypeName(CustomCode::PAGE_TYPE_CART),
                            ],
                            [
                                'id_type' => CustomCode::PAGE_TYPE_CMS,
                                'name' => $this->getPageTypeName(CustomCode::PAGE_TYPE_CMS),
                            ],
                            [
                                'id_type' => CustomCode::PAGE_TYPE_PAGE_NOT_FOUND,
                                'name' => $this->getPageTypeName(CustomCode::PAGE_TYPE_PAGE_NOT_FOUND),
                            ],
                            [
                                'id_type' => CustomCode::PAGE_TYPE_CHECKOUT,
                                'name' => $this->getPageTypeName(CustomCode::PAGE_TYPE_CHECKOUT),
                            ],
                            [
                                'id_type' => CustomCode::PAGE_TYPE_PS_BLOG,
                                'name' => $this->getPageTypeName(CustomCode::PAGE_TYPE_PS_BLOG),
                            ],
                            [
                                'id_type' => CustomCode::PAGE_TYPE_SMART_BLOG,
                                'name' => $this->getPageTypeName(CustomCode::PAGE_TYPE_SMART_BLOG),
                            ],
                        ],
                        'id' => 'id_type',
                        'name' => 'name',
                    ],
                ],
                [
                    'type' => 'select',
                    'label' => $this->trans('Position', [], 'Modules.FPCustomCode.Admin'),
                    'name' => 'position',
                    'default_value' => CustomCode::POSITION_BODY,
                    'required' => true,
                    'options' => [
                        'query' => [
                            [
                                'id_position' => CustomCode::POSITION_BODY,
                                'name' => $this->getPositionName(CustomCode::POSITION_BODY),
                            ],
                            [
                                'id_position' => CustomCode::POSITION_HEAD,
                                'name' => $this->getPositionName(CustomCode::POSITION_HEAD),
                            ],
                        ],
                        'id' => 'id_position',
                        'name' => 'name',
                    ],
                ],
                [
                    'type' => 'textarea',
                    'label' => $this->trans('Code JS', [], 'Modules.FPCustomCode.Admin'),
                    'name' => 'code_js',
                    'cols' => 80,
                    'rows' => 20,
                    'validation' => 'isAnything',
                ],
                [
                    'type' => 'textarea',
                    'label' => $this->trans('Code CSS', [], 'Modules.FPCustomCode.Admin'),
                    'name' => 'code_css',
                    'cols' => 80,
                    'rows' => 20,
                    'validation' => 'isAnything',
                ],
                [
                    'type' => 'switch',
                    'label' => $this->trans('Active', [], 'Modules.FPCustomCode.Admin'),
                    'name' => 'active',
                    'values' => [
                        [
                            'id' => '1',
                            'value' => 1,
                            'label' => $this->trans('Enabled', [], 'Modules.FPCustomCode.Admin'),
                        ],
                        [
                            'id' => '0',
                            'value' => 0,
                            'label' => $this->trans('Disabled', [], 'Modules.FPCustomCode.Admin'),
                        ],
                    ],
                ],
            ],
            'submit' => ['title' => $this->trans('Save', [], 'Modules.FPCustomCode.Admin')],
        ];
    }

    /**
     * @param int $param
     *
     * @return string
     */
    public function getPageTypeName(int $param): string
    {
        $types = [
            CustomCode::PAGE_TYPE_HOME => $this->trans('Home', [], 'Modules.FPCustomCode.Admin'),
            CustomCode::PAGE_TYPE_PRODUCT => $this->trans('Product', [], 'Modules.FPCustomCode.Admin'),
            CustomCode::PAGE_TYPE_CATEGORY => $this->trans('Product Category', [], 'Modules.FPCustomCode.Admin'),
            CustomCode::PAGE_TYPE_CMS => $this->trans('CMS', [], 'Modules.FPCustomCode.Admin'),
            CustomCode::PAGE_TYPE_CART => $this->trans('Cart', [], 'Modules.FPCustomCode.Admin'),
            CustomCode::PAGE_TYPE_CHECKOUT => $this->trans('Checkout', [], 'Modules.FPCustomCode.Admin'),
            CustomCode::PAGE_TYPE_PS_BLOG => $this->trans('PrestaBlog', [], 'Modules.FPCustomCode.Admin'),
            CustomCode::PAGE_TYPE_SMART_BLOG => $this->trans('SmartBlog', [], 'Modules.FPCustomCode.Admin'),
            CustomCode::PAGE_TYPE_PAGE_NOT_FOUND => $this->trans('Page not found', [], 'Modules.FPCustomCode.Admin'),
        ];

        return $types[$param];
    }

    /**
     * @param int $param
     *
     * @return string
     */
    public function getPositionName(int $param): string
    {
        $types = [
            CustomCode::POSITION_HEAD => $this->trans('Head', [], 'Modules.FPCustomCode.Admin'),
            CustomCode::POSITION_BODY => $this->trans('Body', [], 'Modules.FPCustomCode.Admin'),
        ];

        return $types[$param];
    }

    /**
     * @return void
     */
    public function initPageHeaderToolbar()
    {
        if (empty($this->display)) {
            $this->page_header_toolbar_title = $this->trans('Custom codes', [], 'Modules.FPCustomCode.Admin');
            $this->toolbar_title = $this->trans('Custom codes', [], 'Modules.FPCustomCode.Admin');

            $this->page_header_toolbar_btn['new_custom_code'] = [
                'href' => self::$currentIndex . '&addfp_custom_code&token=' . $this->token,
                'desc' => $this->trans('Add to page'),
                'icon' => 'process-icon-new',
            ];
        } elseif ($this->display == 'edit') {
            $this->page_header_toolbar_title = $this->trans('Edit custom code', [], 'Modules.FPCustomCode.Admin');
            $this->page_header_toolbar_btn['delete'] = [
                'href' => self::$currentIndex .
                    '&token=' . $this->token .
                    '&id_custom_code=' .
                    (int) Tools::getValue('id_custom_code') .
                    '&deletefp_custom_code',

                'desc' => $this->trans('Delete custom code', [], 'Modules.FPCustomCode.Admin'),
            ];
        } elseif ($this->display == 'add') {
            $this->page_header_toolbar_title = $this->trans('Add custom code', [], 'Modules.FPCustomCode.Admin');
        }

        $this->page_header_toolbar_btn['settings'] = [
            'href' => $this->context->link->getAdminLink('AdminModules') .
                '&configure=' . $this->module->name,
            'icon' => 'process-icon-configure',
            'desc' => $this->trans('Add global code', [], 'Modules.FPCustomCode.Admin'),
        ];

        parent::initPageHeaderToolbar();
    }
}
