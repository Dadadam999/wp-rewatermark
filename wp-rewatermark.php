<?php
/**
 * Plugin Name: wp-rewatermark
 * Plugin URI: https://github.com/Dadadam999/wp-rewatermark
 * Description: Плагин для изменения ватермарков изображений.
 * Version: 1.0.1
 * Author: Bogdanov Andrey
 * Author URI: mailto://swarzone2100@yandex.ru
 *
 * @package wp-rewatermark
 * @author Bogdanov Andrey (swarzone2100@yandex.ru)
*/
require_once __DIR__ .'/wp-rewatermark-autoload.php';
require_once __DIR__ . '/vendor/autoload.php';
require_once ABSPATH . 'wp-admin/includes/image.php';
require_once ABSPATH . 'wp-admin/includes/file.php';
require_once ABSPATH . 'wp-admin/includes/media.php';
use wpr\Main;
new Main();
