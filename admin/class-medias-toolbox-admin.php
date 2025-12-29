<?php
/**
 * The admin-specific functionality of the plugin
 *
 * @link https://neoslab.com
 * @since 1.0.0
 * @package Medias_Toolbox
 * @subpackage Medias_Toolbox/admin
*/

/**
 * Class `Medias_Toolbox_Admin`
 * @package Medias_Toolbox
 * @subpackage Medias_Toolbox/admin
 * @author NeosLab <support@neoslab.com>
*/
class Medias_Toolbox_Admin
{
	/**
	 * The ID of this plugin
	 * @since 1.0.0
	 * @access private
	 * @var string $pluginName the ID of this plugin
	*/
	private $pluginName;

	/**
	 * The version of this plugin
	 * @since 1.0.0
	 * @access private
	 * @var string $version the current version of this plugin
	*/
	private $version;

	/**
	 * Initialize the class and set its properties
	 * @since 1.0.0
	 * @param string $pluginName the name of this plugin
	 * @param string $version the version of this plugin
	*/
	public function __construct($pluginName, $version)
	{
		$this->pluginName = $pluginName;
		$this->version = $version;
	}

	/**
	 * Register the stylesheets for the admin area
	 * @since 1.0.0
	*/
	public function enqueue_styles()
	{
		wp_register_style($this->pluginName.'-fontawesome', plugin_dir_url(__FILE__).'assets/fonts/fontawesome/css/all.min.css', array(), $this->version, 'all');
		wp_register_style($this->pluginName.'-dashboard', plugin_dir_url(__FILE__).'assets/styles/medias-toolbox-admin.min.css', array(), $this->version, 'all');
		wp_enqueue_style($this->pluginName.'-fontawesome');
		wp_enqueue_style($this->pluginName.'-dashboard');
	}

	/**
	 * Register the JavaScript for the admin area
	 * @since 1.0.0
	*/
	public function enqueue_scripts()
	{
		wp_register_script($this->pluginName.'-script', plugin_dir_url(__FILE__).'assets/javascripts/medias-toolbox-admin.min.js', array('jquery'), $this->version, false);
		wp_enqueue_script($this->pluginName.'-script');
	}

	/**
	 * Return the header
	*/
	public function return_plugin_header()
	{
		$html = '<div class="wpdx-header"><span class="header-icon"><i class="fas fa-sliders-h"></i></span> <span class="header-text">'.__('Medias Toolbox', 'medias-toolbox').'</span></div>';
		return $html;
	}

	/**
	 * Return the tabs menu
	*/
	public function return_tabs_menu($tab)
	{
		$link = admin_url('admin.php');
		$list = array
		(
			array('tab1', 'medias-toolbox-renamer', 'fa-edit', __('Renamer', 'medias-toolbox')),
			array('tab2', 'medias-toolbox-compress', 'fa-compress', __('Compressor', 'medias-toolbox'))
		);

		$menu = null;
		foreach($list as $item => $value)
		{
			$html = array('div' => array('class' => array()), 'a' => array('href' => array()), 'i' => array('class' => array()), 'p' => array(), 'span' => array());
			$menu ='<div class="tab-label '.$value[0].' '.(($tab === $value[0]) ? 'active' : '').'"><a href="'.$link.'?page='.$value[1].'"><p><i class="fas '.$value[2].'"></i><span>'.$value[3].'</span></p></a></div>';
			echo wp_kses($menu, $html);
		}
	}

	/**
	 * Check file extension
	*/
	private function return_file_type($string)
	{
		$i = strrpos($string,".");
		if(!$i)
		{
			return "";
		}

		$case = strlen($string) - $i;
		$exts = substr($string, $i+1, $case);
		return strtolower($exts);
	}

	/**
	 * Return String Format Date + Name
	*/
	private function return_string_date_name($string)
	{
		return date('YmdHi').'-'.sanitize_file_name($string);
	}

	/**
	 * Return String Format Time + Name
	*/
	private function return_string_time_name($string)
	{
		return time().'-'.sanitize_file_name($string);
	}

	/**
	 * Return String Format Date + Rand ID
	*/
	private function return_string_date_randid()
	{
		$l = 50;
		$c = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';

		$s = "";
		for($p = 0; $p < $l; $p++)
		{
		    $s.= $c[mt_rand(0, strlen($c)-1)];
		}

		return date('YmdHi').'-'.($s);
	}

	/**
	 * Return String Format Time + Rand ID
	*/
	private function return_string_time_randid()
	{
		$l = 50;
		$c = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';

		$s = "";
		for($p = 0; $p < $l; $p++)
		{
		    $s.= $c[mt_rand(0, strlen($c)-1)];
		}

		return time().'-'.($s);
	}

	/**
	 * Return String Format Date + Md5(Rand)
	*/
	private function return_string_date_md5rand()
	{
		return date('YmdHi').'-'.md5(uniqid(rand(), true));
	}

	/**
	 * Return String Format Time + Md5(Rand)
	*/
	private function return_string_time_md5rand()
	{
		return time().'-'.md5(uniqid(rand(), true));
	}

	/**
	 * Execute the rename function during upload
	*/
	public function return_rename_upload($fileName)
	{
		$opts = get_option('_medias_toolbox_renamer');
		if((isset($opts['status'])) && ($opts['status'] === 'on'))
		{
			$exts = $this->return_file_type($fileName);
			$name = basename($fileName, $exts);
			$name = rtrim($name, '.');

			if(((isset($exts)) && (in_array($exts, MEDIAS_TOOLBOX_FILETYPE))) && ((isset($opts[$exts])) && ($opts[$exts] === 'on')))
			{
				if(isset($opts['pattern']))
				{
					if($opts['pattern'] === '1')
					{
						$fileName = $this->return_string_date_name($name).'.'.$exts;
					}
					elseif($opts['pattern'] === '2')
					{
						$fileName = $this->return_string_time_name($name).'.'.$exts;
					}
					elseif($opts['pattern'] === '3')
					{
						$fileName = $this->return_string_date_randid().'.'.$exts;
					}
					elseif($opts['pattern'] === '4')
					{
						$fileName = $this->return_string_time_randid().'.'.$exts;
					}
					elseif($opts['pattern'] === '5')
					{
						$fileName = $this->return_string_date_md5rand().'.'.$exts;
					}
					elseif($opts['pattern'] === '6')
					{
						$fileName = $this->return_string_time_md5rand().'.'.$exts;
					}
					else
					{
						$fileName = $this->return_string_date_name($name).'.'.$exts;
					}
				}
				else
				{
					$fileName = $this->return_string_date_name($name).'.'.$exts;
				}
			}
		}

		return $fileName;
	}

	/**
	 * Create Record Database
	*/
	public function create_record_database()
	{
		global $wpdb;

		$table = $wpdb->prefix.'medias';
		if($wpdb->get_var("SHOW TABLES LIKE '$table'") !== $table)
		{
			$charset = $wpdb->get_charset_collate();
			$query = "CREATE TABLE $table(
			id bigint(20) NOT NULL AUTO_INCREMENT,
			file varchar(255) DEFAULT '' NOT NULL,
			size varchar(10) DEFAULT '' NOT NULL,
			stat int(1) DEFAULT '0' NOT NULL,
			PRIMARY KEY (id)
			) $charset;";
			$fetch = $wpdb->query($query);
		}
	}

	/**
	 * Compress Image
	*/
	public function compress_image($source_url, $destination_url, $quality)
	{
		$info = getimagesize($source_url);
		if($info['mime'] === 'image/jpeg')
		{
			$image = imagecreatefromjpeg($source_url);
		}
		elseif($info['mime'] === 'image/gif')
		{
			$image = imagecreatefromgif($source_url);
		}
		elseif($info['mime'] === 'image/png')
		{
			$image = imagecreatefrompng($source_url);
		}

		imagejpeg($image, $destination_url, $quality);
		return $destination_url;
	}

	/**
	 * Compress Media
	*/
	public function return_proceed_medias()
	{
		global $wpdb;

		if((isset($_GET['page'])) && ($_GET['page'] === 'medias-toolbox-compress'))
		{
			if((isset($_GET['paged'])) && (is_numeric($_GET['paged'])))
			{
				$paged = $_GET['paged'];
			}
			else
			{
				$paged = '1';
			}

			if((isset($_GET['file'])) && ((isset($_GET['action'])) && ($_GET['action'] === 'compress')))
			{
				$file = urldecode($_GET['file']);
				if(file_exists($file))
				{
					$path = substr($file, 0, strrpos($file, "/"));
					$this->compress_image($file, $path.'/'.basename($file), 70);

					$table = $wpdb->prefix.'medias';
					$query = "UPDATE `".$table."` SET `stat` = '1' WHERE `file` = '".$file."'";
					$fetch = $wpdb->query($query);

					header('location:'.admin_url('admin.php?page=medias-toolbox-compress').'&output=compressed&paged='.$paged);
					die();
				}
			}

            if(isset($_GET['thumb']))
            {
                foreach($_GET['thumb'] as $file)
                {
                    $file = urldecode($file);
                    if(file_exists($file))
                    {
                        $path = substr($file, 0, strrpos($file, "/"));
                        $this->compress_image($file, $path.'/'.basename($file), 70);

                        $table = $wpdb->prefix.'medias';
						$query = "UPDATE `".$table."` SET `stat` = '1' WHERE `file` = '".$file."'";
						$fetch = $wpdb->query($query);
                    }
                }


                header('location:'.admin_url('admin.php?page=medias-toolbox-compress').'&output=compressed&paged='.$paged);
                die();
            }
		}
	}

	/**
	 * Update `Options` on form submit
	*/
	public function return_update_options()
	{
		if((isset($_POST['mtb-update-renamer'])) && ($_POST['mtb-update-renamer'] === 'true')
		&& check_admin_referer('mtb-referer-form', 'mtb-referer-renamer'))
		{
			$opts = array('status' => 'off', 'pattern' => '1');
			if(isset($_POST['_medias_toolbox_renamer']['status']))
			{
				$opts['status'] = sanitize_text_field($_POST['_medias_toolbox_renamer']['status']);
				if($opts['status'] !== 'on')
				{
					header('location:'.admin_url('admin.php?page=medias-toolbox-renamer').'&output=error&type=status');
					die();
				}
			}
			else
			{
				$opts['status'] = 'off';
			}

			foreach(MEDIAS_TOOLBOX_FILETYPE as $fileExt)
			{
				if(isset($_POST['_medias_toolbox_renamer'][$fileExt]))
				{
					$opts[$fileExt] = 'on';
				}
			}

			$allowed = array('1', '2', '3', '4', '5', '6');
			if((isset($_POST['_medias_toolbox_renamer']['pattern']))
			&& (in_array($_POST['_medias_toolbox_renamer']['pattern'], $allowed)))
			{
				$opts['pattern'] = sanitize_text_field($_POST['_medias_toolbox_renamer']['pattern']);
			}

			$data = update_option('_medias_toolbox_renamer', $opts);
			header('location:'.admin_url('admin.php?page=medias-toolbox-renamer').'&output=updated');
			die();
		}
	}

	/**
	 * Return the `Compress` page
	*/
	public function return_compress_page()
	{
		require_once plugin_dir_path(__FILE__).'partials/medias-toolbox-admin-compress.php';
	}

	/**
	 * Return the `Renamer` page
	*/
	public function return_renamer_page()
	{
		$opts = get_option('_medias_toolbox_renamer');
		require_once plugin_dir_path(__FILE__).'partials/medias-toolbox-admin-renamer.php';
	}

	/**
	 * Return Backend Menu
	*/
	public function return_admin_menu()
	{
		add_menu_page('Medias ToolBox', 'Medias ToolBox', 'manage_options', 'medias-toolbox-admin', array($this, 'return_compress_page'), 'dashicons-format-image');
		add_submenu_page('medias-toolbox-admin', 'Renamer', 'Renamer', 'manage_options', 'medias-toolbox-renamer', array($this, 'return_renamer_page'));
		add_submenu_page('medias-toolbox-admin', 'Clean-Up', 'Compressor', 'manage_options', 'medias-toolbox-compress', array($this, 'return_compress_page'));
		remove_submenu_page('medias-toolbox-admin', 'medias-toolbox-admin');
	}
}

?>