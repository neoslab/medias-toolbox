<?php
/**
 * Fired during plugin activation
 *
 * @link https://neoslab.com
 * @since 1.0.0
 *
 * @package Medias_Toolbox
 * @subpackage Medias_Toolbox/includes
*/

/**
 * Class `Medias_Toolbox_Activator`
 * This class defines all code necessary to run during the plugin's activation
 * @since 1.0.0
 * @package Medias_Toolbox
 * @subpackage Medias_Toolbox/includes
 * @author NeosLab <support@neoslab.com>
*/
class Medias_Toolbox_Activator
{
	/**
	 * Activate plugin
	 * @since 1.0.0
	*/
	public static function activate()
	{
		$option = add_option('_medias_toolbox_cleanup', false);
		$option = add_option('_medias_toolbox_renamer', false);
	}
}

?>