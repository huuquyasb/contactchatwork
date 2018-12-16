<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://softworldvietnam.com
 * @since             1.0.0
 * @package           Contact_Chatwork
 *
 * @wordpress-plugin
 * Plugin Name:       Contact Chatwork
 * Plugin URI:        https://softworldvietnam.com
 * Description:       This is a lightweight plugin to create  task in Chatwork from contact form and it's easy to customize. Add shortcode [sw-contact] on a page or use the widget to display your form.
 * Version:           1.0.0
 * Author:            SoftworldVietNam
 * Author URI:        https://softworldvietnam.com
 * License:           GPL-2.0+
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'PLUGIN_NAME_VERSION', '1.0.0' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-contact-chatwork-activator.php
 */
function activate_contact_chatwork() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-contact-chatwork-activator.php';
	Contact_Chatwork_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-contact-chatwork-deactivator.php
 */
function deactivate_contact_chatwork() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-contact-chatwork-deactivator.php';
	Contact_Chatwork_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_contact_chatwork' );
register_deactivation_hook( __FILE__, 'deactivate_contact_chatwork' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-contact-chatwork.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_contact_chatwork() {

	$plugin = new Contact_Chatwork();
	$plugin->run();

}
run_contact_chatwork();
