<?php
/**
 * FPCustomCode v1.0.0
 * https://github.com/flexpik/ps-custom-code
 * Copyright (c) 2022 FlexPik.com
 * Released under the MIT license
 * Date: 2022-11-06
 */
$sql = [];

$sql[] = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'fp_custom_code` (
    `id_custom_code` int(11) NOT NULL AUTO_INCREMENT,
	`active` tinyint(1) NOT NULL,
	`type` tinyint(1) NOT NULL,
	`position` tinyint(1) NOT NULL,
	`code_js` longtext DEFAULT NULL,
	`code_css` longtext DEFAULT NULL,
	`date_add` datetime NOT NULL,
    `date_upd` datetime NOT NULL,
    PRIMARY KEY  (`id_custom_code`, `type`, `position`)
) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8;';

foreach ($sql as $query) {
    if (!Db::getInstance()->execute($query)) {
        return false;
    }
}
