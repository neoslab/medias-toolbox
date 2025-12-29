<?php
/**
 * Fired during plugin deactivation
 *
 * @link https://neoslab.com
 * @since 1.0.0
 *
 * @package Medias_Toolbox
 * @subpackage Medias_Toolbox/includes
*/

/**
 * Class `Medias_Toolbox_Deactivator`
 * This class defines all code necessary to run during the plugin's deactivation
 * @since 1.0.0
 * @package Medias_Toolbox
 * @subpackage Medias_Toolbox/includes
 * @author NeosLab <support@neoslab.com>
*/
class Medias_Toolbox_Deactivator
{
	/**
	 * Deactivate plugin
	 * @since 1.0.0
	*/
	public static function deactivate()
	{
		global $wpdb;

		$table = $wpdb->prefix.'medias';
        $query = "DROP TABLE IF EXISTS `".$table."`";
        $fetch = $wpdb->query($query);

		$option = delete_option('_medias_toolbox_cleanup');
		$option = delete_option('_medias_toolbox_renamer');
	}
}

?>