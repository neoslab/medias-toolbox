<?php
/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link https://neoslab.com
 * @since 1.0.0
 * @package Medias_Toolbox
 *
 * @wordpress-plugin
 * Plugin Name: Medias Toolbox
 * Plugin URI: https://wordpress.org/plugins/medias-toolbox/
 * Description: Medias Toolbox allow you to sanitize and rename automatically media files during upload.
 * Version: 1.8.2
 * Author: NeosLab
 * Author URI: https://neoslab.com
 * License: GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain: medias-toolbox
 * Domain Path: /languages
*/

/**
 * If this file is called directly, then abort
*/
if(!defined('WPINC'))
{
	die;
}

/**
 * Currently plugin version
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions
*/
define('MEDIAS_TOOLBOX_VERSION', '1.8.2');

/**
 * Define Allowed Filetype
*/
$fileType = array
(
	"3g2", "3gp", "7z", "avi", "bzip2",
	"doc", "docx", "gif", "jpeg", "jpg",
	"m4a", "m4v", "mov", "mp3", "mp4",
	"mpg", "odt", "ogg", "ogv", "pdf",
	"png", "pps", "ppsx", "ppt", "pptx",
	"rar", "tar", "wav", "webp", "wmv",
	"xls", "xlsx", "zip"
);

define('MEDIAS_TOOLBOX_FILETYPE', $fileType);

/**
 * The code that runs during plugin activation
 * This action is documented in includes/class-medias-toolbox-activator.php
*/
function activate_medias_toolbox()
{
	require_once plugin_dir_path(__FILE__).'includes/class-medias-toolbox-activator.php';
	Medias_Toolbox_Activator::activate();
}

/**
 * The code that runs during plugin deactivation
 * This action is documented in includes/class-medias-toolbox-deactivator.php
*/
function deactivate_medias_toolbox()
{
	require_once plugin_dir_path(__FILE__).'includes/class-medias-toolbox-deactivator.php';
	Medias_Toolbox_Deactivator::deactivate();
}

/**
 * Activation/deactivation hook
*/
register_activation_hook(__FILE__, 'activate_medias_toolbox');
register_deactivation_hook(__FILE__, 'deactivate_medias_toolbox');

/**
 * The core plugin class that is used to define internationalization and admin-specific hooks
*/
require plugin_dir_path(__FILE__).'includes/class-medias-toolbox-core.php';

/**
 * Begins execution of the plugin
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle
 * @since 1.0.0
*/
function run_medias_toolbox()
{
	$plugin = new Medias_Toolbox();
	$plugin->run();
}

/**
 * Run plugin
*/
run_medias_toolbox();

?>