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
 * Class `Medias_ToolBox_Table`
 * @package Medias_Toolbox
 * @subpackage Medias_Toolbox/admin
 * @author NeosLab <support@neoslab.com>
*/
class Medias_ToolBox_Table extends WP_List_Table
{
    /**
     * Initialize the class and set its properties
     */
    public function __construct()
    {
        global $status, $page;

        parent::__construct(array
        (
            'singular' => 'thumb',
            'plural' => 'thumbs',
            'ajax' => false
        ));
    }

    /**
     * Return Upload Directory
    */
    private function get_upload_dir()
    {
        $uploads = wp_upload_dir();
        return $uploads['basedir'];
    }

    /**
     * Return Upload URL
    */
    private function get_upload_url()
    {
        $uploads = wp_upload_dir();
        return $uploads['baseurl'];
    }

    /**
     * Return Debug Log File
    */
    private function get_upload_debug()
    {
        return $this->get_upload_dir().'/imagecleanup/debug.json';
    }

    /**
     * Get all attachment images from database
    */
    private function get_attachment_images()
    {
        $query_images_args = array
        (
            'post_type' => 'attachment',
            'post_mime_type' =>'image',
            'post_status' => 'inherit',
            'fields' => 'ids',
            'orderby' => 'ID',
            'posts_per_page' => -1
        );

        $query_images = new WP_Query($query_images_args);

        if($query_images->have_posts())
        {
            return $query_images;
        }

        return false;
    }

    /**
     * Return Attachment Array
    */
    private function get_attachment_array($wp_images)
    {
        global $wpdb;

        /**
         * Get wp_sizes
        */
        $wp_sizes = get_intermediate_image_sizes();
        $wp_sizes[] = "full";

        /**
         * Initialize vars
        */
        $array_data = array();
        $image_data = array();

        /**
         * Loop all attachments
        */
        if((isset($wp_images->posts)) && (!empty($wp_images->posts)) && (is_array($wp_images->posts)))
        {
            foreach($wp_images->posts as $image)
            {
                $meta = wp_get_attachment_metadata($image, true);

                foreach($wp_sizes as $size)
                {
                    if(isset($meta['sizes']))
                    {
                        if((is_array($meta['sizes']) && array_key_exists($size, $meta['sizes'])) || $size === 'full')
                        {
                            $image_data['att_id'] = $image;
                            $image_data['bd'] = null;
                            $image_data['lk'] = null;
                            $image_data['fn'] = null;
                            $image_data['mw'] = null;
                            $image_data['mh'] = null;
                            $image_data['sz'] = null;

                            if($size === "full")
                            {
                                if(isset($meta['file']))
                                {
                                    $image_data['bd'] = $this->get_upload_dir().'/'.dirname($meta['file']);
                                    $image_data['lk'] = $this->get_upload_url().'/'.dirname($meta['file']);
                                    $image_data['fn'] = preg_replace('/^.+[\\\\\\/]/', '', $meta['file']);
                                    $image_data['mw'] = $meta['width'];
                                    $image_data['mh'] = $meta['height'];
                                    $image_data['sz'] = $meta['width'].'x'.$meta['height'];
                                }
                                else
                                {
                                    file_put_contents($this->get_upload_debug(), "[".number_format(microtime(true) - $start, 4)."] Incorrect meta ([full] key not found):\r\n".json_encode($meta)."\r\n", FILE_APPEND);
                                }
                            }
                            else
                            {
                                if(isset($meta['file']) && isset($meta['sizes'][$size]['file']))
                                {
                                    $image_data['bd'] = $this->get_upload_dir().'/'.dirname($meta['file']);
                                    $image_data['lk'] = $this->get_upload_url().'/'.dirname($meta['file']);
                                    $image_data['fn'] = $meta['sizes'][$size]['file'];
                                    $image_data['mw'] = $meta['sizes'][$size]['width'];
                                    $image_data['mh'] = $meta['sizes'][$size]['height'];
                                    $image_data['sz'] = $meta['sizes'][$size]['width'].'x'.$meta['sizes'][$size]['height'];
                                }
                                else
                                {
                                    file_put_contents($this->get_upload_debug(), "[".number_format(microtime(true) - $start, 4)."] Incorrect meta ([".$size."] key not found):\r\n".json_encode($meta)."\r\n", FILE_APPEND);
                                }
                            }

                            if($image_data['fn'] != null)
                            {
                                $image_data['xist'] = file_exists($image_data['bd'].'/'.$image_data['fn']);
                            }
                            else
                            {
                                $image_data['xist'] = false;
                            }

                            $image_size[0] = 0;
                            $image_size[1] = 0;

                            if($image_data['xist'])
                            {
                                if($image_size = @getimagesize($image_data['bd'].'/'.$image_data['fn']))
                                {
                                    /**
                                     * No Rule
                                    */
                                }
                                else
                                {
                                    file_put_contents($this->get_upload_debug(),  "[".number_format(microtime(true) - $start, 4)."] Could not read image dimensions: ".$image_data['bd'].'/'.$image_data['fn']."\r\n", FILE_APPEND);
                                }
                            }

                            $image_data['w'] = $image_size[0];
                            $image_data['h'] = $image_size[1];
                            $image_data['ms'] = $size;

                            if($image_data['w'] === $image_data['mw'] && $image_data['h'] === $image_data['mh'] && $image_data['fn'] != null)
                            {
                                $array_data[] = $image_data;
                            }
                        }
                    }
                }
            }

            return $array_data;
        }
        else
        {
            return false;
        }
    }

    /**
     * Return Image Array
    */
    public function return_images_database()
    {
        $wp_images = $this->get_attachment_images();
        $db_images = $this->get_attachment_array($wp_images);
        return $db_images;
    }

    /**
     * Convert File size unit
    */
    public function return_formatted_size($bytes)
    {
        if($bytes >= 1073741824)
        {
            $bytes = number_format($bytes / 1073741824, 2).' GB';
        }
        elseif($bytes >= 1048576)
        {
            $bytes = number_format($bytes / 1048576, 2).' MB';
        }
        elseif($bytes >= 1024)
        {
            $bytes = number_format($bytes / 1024, 2).' KB';
        }
        elseif($bytes > 1)
        {
            $bytes = $bytes.' bytes';
        }
        elseif($bytes === 1)
        {
            $bytes = $bytes.' byte';
        }
        else
        {
            $bytes = '0 bytes';
        }

        return $bytes;
    }

    /**
     * Return Default Column
     * @param array $item A singular item (one full row's worth of data)
     * @param array $column_name The name/slug of the column to be processed
     * @return string Text or HTML to be placed inside the column <td>
    */
    public function column_default($item, $column_name)
    {
        global $wpdb;

        $table = $wpdb->prefix.'medias';
        $media = $item['bd'].'/'.$item['fn'];

        $query = "SELECT `id`, `size`, `stat` FROM `".$table."` WHERE `file` = '".$media."'";
        $fetch = $wpdb->get_results($query, OBJECT);

        switch($column_name)
        {
            case 'thumb':
            $thumb_url = wp_get_attachment_image_src($item['att_id'], 'thumbnail', true);
            return '<img class="thumb" src="'.$thumb_url[0].'" alt="thumb" />';

            case 'filename':
            return $item['fn'];

            case 'size':
            return '<span style="color:red;">'.$this->return_formatted_size($fetch[0]->size).'</span>';

            case 'load':
            if($fetch[0]->stat == 0)
            {
                return '<span style="color:red;">'.$this->return_formatted_size(filesize($media)).'</span>';
            }
            else
            {
                return '<span style="color:green;">'.$this->return_formatted_size(filesize($media)).'</span>';
            }

            case 'dimensions':
            return $item['sz'];

            case 'type':
            return $item['ms'];

            case 'action':
            if($fetch[0]->stat == 0)
            {
                return '<a href="'.admin_url('admin.php?page=medias-toolbox-compress').'&file='.urlencode($media).'&action=compress&paged='.((isset($_GET['paged'])) ? $_GET['paged'] : '1').'" class="button button-primary">'.__('Compress', 'medias-toolbox').'</a>';
            }
            else
            {
                return '<a href="javascript:void(0);" class="button button-secondary">'.__('Compressed', 'medias-toolbox').'</a>';
            }

            default:
            return print_r($item, true);
        }
    }

    /**
     * Return Column Title
     * @see WP_List_Table::::single_row_columns()
     * @param array $item A singular item (one full row's worth of data)
     * @return string Text to be placed inside the column <td> (thumb title only)
    */
    public function column_title($item)
    {
        $actions = array
        (
            'compress' => sprintf('<a href="?page=%s&action=%s&thumb=%s">Delete</a>',$_REQUEST['page'],'compress',$item['ID']),
        );

        return sprintf('%1$s <span style="color:silver">(id:%2$s)</span>%3$s', $item['title'], $item['ID'], $this->row_actions($actions));
    }

    /**
     * Return Column CB
     * @see WP_List_Table::::single_row_columns()
     * @param array $item A singular item (one full row's worth of data)
     * @return string Text to be placed inside the column <td> (thumb title only)
    */
    public function column_cb($item)
    {
        global $wpdb;

        $table = $wpdb->prefix.'medias';
        $media = $item['bd'].'/'.$item['fn'];
        $query = "SELECT `id`, `size`, `stat` FROM `".$table."` WHERE `file` = '".$media."'";
        $fetch = $wpdb->get_results($query);

        if($fetch[0]->stat == 0)
        {
            return sprintf('<input type="checkbox" name="%1$s[]" value="%2$s" />', $this->_args['singular'], $media);
        }
    }

    /**
     * Get the Column
     * @see WP_List_Table::::single_row_columns()
     * @return array An associative array containing column information: 'slugs'=>'Visible Titles'
    */
    public function get_columns()
    {
        $columns = array
        (
            'cb'            => '<input type="checkbox" />',
            'thumb'         => 'Thumb',
            'filename'      => 'Filename',
            'size'          => 'Original Size',
            'load'          => 'Compressed Size',
            'dimensions'    => 'Dimensions',
            'type'          => 'Type',
            'action'        => 'Action'
        );

        return $columns;
    }

    /**
     * Get the Sortable Columns
     * @return array An associative array containing all the columns that should be sortable: 'slugs'=>array('data_values',bool)
    */
    public function get_sortable_columns()
    {
        $sortable_columns = array
        (
            'filename'  => array('fn', false)
        );

        return $sortable_columns;
    }

    /**
     * Get the Bulk Action
     * @return array An associative array containing all the bulk actions: 'slugs'=>'Visible Titles'
    */
    public function get_bulk_actions()
    {
        $actions = array
        (
            'compress' => 'Compress Images'
        );

        return $actions;
    }

    /**
     * Return the Process Bulk Action
     * @see $this->prepare_items()
    */
    public function process_bulk_action()
    {
        if('compress' === $this->current_action())
        {
            /* No Action */
        }
    }

    /**
     * Prepare the Items
     * @global WPDB $wpdb
     * @uses $this->_column_headers
     * @uses $this->items
     * @uses $this->get_columns()
     * @uses $this->get_sortable_columns()
     * @uses $this->get_pagenum()
     * @uses $this->set_pagination_args()
    */
    public function prepare_items()
    {
        /**
         * This is used only if making any database queries
        */
        global $wpdb;

        /**
         * First, lets decide how many records per page to show
        */
        $per_page = 25;

        /**
         * REQUIRED : Now we need to define our column headers. This includes a complete
         * array of columns to be displayed (slugs & titles), a list of columns
         * to keep hidden, and a list of columns that are sortable. Each of these
         * can be defined in another method (as we've done here) before being
         * used to build the value for our _column_headers property.
        */
        $columns = $this->get_columns();
        $hidden = array();
        $sortable = $this->get_sortable_columns();

        /**
         * REQUIRED : Finally, we build an array to be used by the class for column
         * headers. The $this->_column_headers property takes an array which contains
         * 3 other arrays. One for all columns, one for hidden columns, and one
         * for sortable columns.
        */
        $this->_column_headers = array($columns, $hidden, $sortable);

        /**
         * OPTIONAL : You can handle your bulk actions however you see fit. In this
         * case, we'll handle them within our package just to keep things clean.
        */
        $this->process_bulk_action();

        /**
         * Instead of querying a database, we're going to fetch the example data
         * property we created for use in this plugin. This makes this example
         * package slightly different than one you might build on your own. In
         * this example, we'll be using array manipulation to sort and paginate
         * our data. In a real-world implementation, you will probably want to
         * use sort and pagination data to build a custom query instead, as you'll
         * be able to use your precisely-queried data immediately.
        */
        $data = $this->return_images_database();

        if($data === false)
        {
            $data = array();
        }

        /**
         * This checks for sorting input and sorts the data in our array accordingly.
         * In a real-world situation involving a database, you would probably want
         * to handle sorting by passing the 'orderby' and 'order' values directly
         * to a custom query. The returned data will be pre-sorted, and this array
         * sorting technique would be unnecessary.
        */
        function usort_reorder($a,$b)
        {
            $orderby = (!empty($_REQUEST['orderby'])) ? $_REQUEST['orderby'] : 'fn';
            $order = (!empty($_REQUEST['order'])) ? $_REQUEST['order'] : 'asc';
            $result = strcmp($a[$orderby], $b[$orderby]);
            return ($order === 'asc') ? $result : -$result;
        }

        usort($data, 'usort_reorder');

        /**
         * REQUIRED : for pagination. Let's figure out what page the user is currently
         * looking at. We'll need this later, so you should always include it in
         * your own package classes.
        */
        $current_page = $this->get_pagenum();

        /**
         * REQUIRED : for pagination. Let's check how many items are in our data array.
         * In real-world use, this would be the total number of items in your database,
         * without filtering. We'll need this later, so you should always include it
         * in your own package classes.
        */
        $total_items = count($data);

        /**
         * The WP_List_Table class does not handle pagination for us, so we need
         * to ensure that the data is trimmed to only the current page. We can use
         * array_slice() to
        */
        $data = array_slice($data,(($current_page-1)*$per_page),$per_page);

        /**
         * REQUIRED : Now we can add our *sorted* data to the items property, where
         * it can be used by the rest of the class.
        */
        $this->items = $data;

        $table = $wpdb->prefix.'medias';
        foreach($this->items as $item)
        {
            $media = $item['bd'].'/'.$item['fn'];
            $query = "SELECT `id` FROM `".$table."` WHERE `file` = '".$media."'";
            $fetch = $wpdb->get_results($query);

            if(count($fetch) === 0)
            {
                $wpdb->insert($table, array
                (
                    'file' => $media,
                    'size' => filesize($media),
                    'stat' => 0
                ));
            }
        }

        /**
         * REQUIRED : We also have to register our pagination options & calculations.
        */
        $this->set_pagination_args(array
        (
            'total_items' => $total_items,                  // WE have to calculate the total number of items
            'per_page' => $per_page,                        // WE have to determine how many items to show on a page
            'total_pages' => ceil($total_items/$per_page)   // WE have to calculate the total number of pages
        ));
    }
}

?>
