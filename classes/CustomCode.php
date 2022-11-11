<?php
/**
 * FPCustomCode v1.0.0
 * https://github.com/flexpik/ps-custom-code
 * Copyright (c) 2022 FlexPik.com
 * Released under the MIT license
 * Date: 2022-11-06
 */
class CustomCode extends ObjectModel
{
    public static $definition = [
        'table' => 'fp_custom_code',
        'primary' => 'id_custom_code',
        'fields' => [
            'type' => ['type' => self::TYPE_INT, 'required' => true],
            'position' => ['type' => self::TYPE_INT, 'required' => true],
            'code_js' => ['type' => self::TYPE_HTML, 'required' => false],
            'code_css' => ['type' => self::TYPE_HTML, 'required' => false],
            'active' => ['type' => self::TYPE_BOOL, 'required' => true],
            'date_add' => ['type' => self::TYPE_DATE, 'copy_post' => false],
            'date_upd' => ['type' => self::TYPE_DATE, 'copy_post' => false],
        ],
    ];

    public $id_custom_code;
    public $type;
    public $position;
    public $code_js;
    public $code_css;
    public $active;
    public $date_add;
    public $date_upd;

    public const PAGE_TYPE_HOME = 1;
    public const PAGE_TYPE_CATEGORY = 2;
    public const PAGE_TYPE_PRODUCT = 3;
    public const PAGE_TYPE_CART = 4;
    public const PAGE_TYPE_CMS = 5;
    public const PAGE_TYPE_PAGE_NOT_FOUND = 6;
    public const PAGE_TYPE_CHECKOUT = 7;
    public const PAGE_TYPE_PS_BLOG = 8;
    public const PAGE_TYPE_SMART_BLOG = 9;
    public const POSITION_HEAD = 0;
    public const POSITION_BODY = 1;

    /**
     * @param int $type
     * @param int $position
     *
     * @return array
     *
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     */
    public static function getByTypeAndPosition(int $type, int $position): array
    {
        $results = [];
        $query = new DbQuery();
        $query->select(self::$definition['primary']);
        $query->from(self::$definition['table']);
        $query->where("`type`={$type} AND `position`={$position} AND `active` = 1");

        $rows = Db::getInstance()->executeS($query);

        foreach ($rows as $row) {
            $results[] = new CustomCode($row['id_custom_code']);
        }

        return $results;
    }
}
