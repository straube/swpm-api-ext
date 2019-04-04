<?php

/*
Plugin Name: Simple Membership API (Extended)
Plugin URI:  https://github.com/straube/swpm-api-ext
Description: This plugin is an add-on for Simple Membership. It extends Simple Membership API add-on with additional API endpoints.
Version:     0.1.0
Author:      Gustavo Straube (straube.co)
Author URI:  http://straube.co
License:     GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Text Domain: swpm-api-ext
*/


/*
 * Direct access to this file is not permitted
 */
if (!defined('ABSPATH')) {
    exit;
}

/**
 * The plugin file name.
 *
 * This is used mainly to set hooks and other features that requires the base
 * plugin file name to work properly.
 *
 * @var string
 */
define('SWPM_API_EXT_PLUGIN', __FILE__);

/**
 * Loads the add-on only after all plugins are loaded and both Simple Membership
 * and Simple Membership API are active.
 *
 * @return void
 */
function swpm_api_ext_load()
{
    if (!(class_exists('SimpleWpMembership') && class_exists('SwpmAPI'))) {
        return;
    }

    /*
     * Loading classes.
     */
    require 'classes/SwpmApiExt.php';

    /*
     * Bootstrap...
     */
    $swpmApiExt = SwpmApiExt::instance();
}

add_action('plugins_loaded', 'swpm_api_ext_load');
