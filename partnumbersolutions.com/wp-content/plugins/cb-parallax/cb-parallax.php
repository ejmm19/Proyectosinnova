<?php

/**
 * The plugin bootstrap file.
 *
 * This is a plugin for custom backgrounds on single posts, products and pages, with a parallax effect..
 * The parallax effect requires an image with a width of at least 1920px and a height of at least 1200px.
 * Have Fun!
 *
 * in memoriam of Bender ( 1999 to 2013 )
 * Built with Tom McFarlin's WordPress Plugin Boilerplate in mind -
 * which is now maintained by Devin Vinson.
 * https://github.com/DevinVinson/WordPress-Plugin-Boilerplate
 *
 * @link              https://wordpress.org/plugins/cb-parallax/
 * @since             0.1.0
 * @package           cb_parallax
 * @wordpress-plugin
 * Plugin Name:       cbParallax
 * Plugin URI:        https://wordpress.org/plugins/cb-parallax/
 * Description:       Let's you add <a href="http://codex.wordpress.org/Custom_Backgrounds" target="_blank">custom background</a> - with or without vertical or horizontal parallax effect - for single posts, pages and products. It requires your theme to support the WordPress <code>custom-background</code> feature. It also requires you to set your theme's layout to "boxed" and / or to add a transparency to the container that holds the content in order to make the background image visible / shine trough.
 * Author:            Demis Patti <demispatti@gmail.com>
 * Author URI:        http://demispatti.ch
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Version:           0.8.2
 * Stable tag:        0.8.2
 * Text Domain:       cb-parallax
 * Domain Path:       /languages
 */

/**
 * If this file is called directly, abort.
 */
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-cb-parallax-activator.php
 *
 * @since    0.1.0
 * @return   void
 */
function activate_cb_parallax() {

	require_once plugin_dir_path( __FILE__ ) . 'includes/class-cb-parallax-activator.php';

	$activator = new cb_parallax_activator();
	$activator->activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-cb-parallax-deactivator.php
 *
 * @since    0.1.0
 * @return   void
 */
function deactivate_cb_parallax() {

	require_once plugin_dir_path( __FILE__ ) . 'includes/class-cb-parallax-deactivator.php';

	$deactivator = new cb_parallax_deactivator();
	$deactivator->deactivate();
}

/**
 * Register the activation and deactivation functionality of the plugin.
 *
 * @since    0.1.0
 */
register_activation_hook( __FILE__, 'activate_cb_parallax' );
register_deactivation_hook( __FILE__, 'deactivate_cb_parallax' );

/**
 * Include the core plugin class.
 *
 * @since    0.1.0
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-cb-parallax.php';

/**
 * Runs the plugin.
 *
 * @since    0.1.0
 * @return   void
 */
function run_cb_parallax() {

	$plugin = new cb_parallax();

	$plugin->run();
}

run_cb_parallax();
