<?php
/**
 * Plugin Name: Товары и Преимущества
 * Description: Плагин позволяет создавать товары и размещать и через шорткод как карточки с описанием и фото
 * Version:     1.4
 * License:     GPL2
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 *
 */

require 'plugin-update-checker/plugin-update-checker.php';
$MyUpdateChecker = Puc_v4_Factory::buildUpdateChecker(
    'https://alexlundin.com/wp-update-server?action=get_metadata&slug=product-item', //Metadata URL.
    __FILE__, //Full path to the main plugin file.
    'product-item' //Plugin slug. Usually it's the same as the name of the directory.
);

require_once 'Product.php';
require_once 'Edge.php';

