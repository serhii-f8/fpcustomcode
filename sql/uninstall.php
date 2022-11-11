<?php
/**
 * FPCustomCode v1.0.0
 * https://github.com/flexpik/ps-custom-code
 * Copyright (c) 2022 FlexPik.com
 * Released under the MIT license
 * Date: 2022-11-06
 */
Db::getInstance()->Execute('DROP TABLE IF EXISTS ' . _DB_PREFIX_ . 'fp_custom_code');
