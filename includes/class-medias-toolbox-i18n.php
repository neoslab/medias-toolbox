<?php
/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link https://neoslab.com
 * @since 1.0.0
 *
 * @package Medias_Toolbox
 * @subpackage Medias_Toolbox/includes
*/

/**
 * Class `Medias_Toolbox_i18n`
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 * @since 1.0.0
 * @package Medias_Toolbox
 * @subpackage Medias_Toolbox/includes
 * @author NeosLab <support@neoslab.com>
*/
class Medias_Toolbox_i18n
{
	/**
	 * Load the plugin text domain for translation
	 * @since 1.0.0
	*/
	public function load_plugin_textdomain()
	{
		load_plugin_textdomain('medias-toolbox', false, dirname(dirname(plugin_basename(__FILE__))).'/languages/');
	}
}

?>