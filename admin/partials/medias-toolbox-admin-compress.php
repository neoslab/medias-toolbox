<?php
if(((isset($_GET['page'])) && ($_GET['page'] === 'medias-toolbox-compress')) && ((isset($_GET['output'])) && ($_GET['output'] === 'compressed')))
{
    $notice = array('success', __('The selected media have been successfully compressed !!', 'medias-toolbox'));
}
?>
<div class="wrap">
    <section class="wpdx-wrapper">
        <div class="wpdx-container">
            <div class="wpdx-tabs">
                <?php echo $this->return_plugin_header(); ?>
                <main class="tabs-main">
                    <?php echo $this->return_tabs_menu('tab2'); ?>
                    <section class="tab-section">
                        <?php if(isset($notice)) { ?>
                        <div class="wpdx-notice <?php echo esc_attr($notice[0]); ?>">
                            <span class="close-btn" onclick="this.parentElement.style.display='none';">&times;</span>
                            <span><?php echo esc_attr($notice[1]); ?></span>
                        </div>
                        <?php } else { ?>
                        <div class="wpdx-notice info">
                            <span class="close-btn" onclick="this.parentElement.style.display='none';">&times;</span>
                            <span><?php echo _e('Below the list of all the images that currently present in your database.', 'medias-toolbox'); ?></span>
                        </div>
                        <?php } ?>
                        <div class="wpdx-datatables">
                            <?php
                            $records = $this->create_record_database();
                            if(!class_exists('WP_List_Table'))
                            {
                                require_once(ABSPATH.'wp-admin/includes/class-wp-list-table.php');
                            }

                            require_once(dirname(plugin_dir_path(__FILE__)).'/class-medias-toolbox-table.php');
                            $listTable = new Medias_ToolBox_Table();
                            $listTable->prepare_items();
                            ?>
                            <form id="images-filter" method="GET">
                                <input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>" />
                                <?php $listTable->display() ?>
                            </form>
                        </div>
                    </section>
                </main>
            </div>
        </div>
    </section>
</div>