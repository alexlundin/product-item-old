<?php
/**
 * Plugin Name: Товары и Преимущества by Alex Lundin
 * Description: Плагин позволяет создавать товары и размещать и через шорткод как карточки с описанием и фото
 * Author: Alex Lundin
 * Author URI: https://vk.com/aslundin
 * Version:     1.4.7
 * License:     GPL2
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 *
 */



require 'plugin-update-checker/plugin-update-checker.php';
$myUpdateChecker = Puc_v4_Factory::buildUpdateChecker(
	'https://github.com/alexlundin/product-item-old/',
	__FILE__,
	'product-item'
);

// //Optional: If you're using a private repository, specify the access token like this:
// $myUpdateChecker->setAuthentication('your-token-here');

//Optional: Set the branch that contains the stable release.
// $myUpdateChecker- > > setBranch('master');

require_once 'Product.php';
require_once 'Edge.php';

