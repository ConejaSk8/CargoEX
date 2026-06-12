<?php
/*
Plugin Name: CargoEX Costa Rica Logistic
Plugin URI: http://www.desing-cr.com/support/plugins
Description: CargoEX Costa Rica Especializado en envíos nacionales e internacionales, con cobertura en todo el territorio nacional y servicio puerta a puerta.
Version: 1.1
Author: ConejaSK8
Author URI: http://URI_del_Autor_del_Plugin
License: GPL
*/

if (!defined('ABSPATH')) {
    exit;
}


define('COURIER_PLUGIN_PATH', plugin_dir_path(__FILE__));
define('COURIER_PLUGIN_URL', plugin_dir_url(__FILE__));

require_once plugin_dir_path(__FILE__) . 'includes/class-db.php';
require_once plugin_dir_path(__FILE__) . 'includes/class-installer.php';
require_once plugin_dir_path(__FILE__) . 'includes/class-admin.php';

register_activation_hook(
    __FILE__,
    ['Courier_Installer', 'install']
);

Courier_Admin::init();

    add_action('admin_enqueue_scripts', function () {

        if (isset($_GET['page']) && strpos($_GET['page'], 'cargoex') !== false) {

            wp_enqueue_style(
                'cargoex-admin',
                plugin_dir_url(__FILE__) . 'css/cargoex-admin.css',
                [],
                '1.0.0'
            );
        }

    });

    

?>