<?php
if((isset($_GET['output'])) && ($_GET['output'] === 'updated'))
{
    $notice = array('success', __('Your settings have been successfully updated.', 'medias-toolbox'));
}
elseif((isset($_GET['output'])) && ($_GET['output'] === 'error'))
{
    if((isset($_GET['type'])) && ($_GET['type'] === 'status'))
    {
        $notice = array('wrong', __('The file renamer status is not valid !!', 'medias-toolbox'));
    }
}
?>
<div class="wrap">
    <section class="wpdx-wrapper">
        <div class="wpdx-container">
            <div class="wpdx-tabs">
                <?php echo $this->return_plugin_header(); ?>
                <main class="tabs-main">
                    <?php echo $this->return_tabs_menu('tab1'); ?>
                    <section class="tab-section">
                        <?php if(isset($notice)) { ?>
                        <div class="wpdx-notice <?php echo esc_attr($notice[0]); ?>">
                            <span class="close-btn" onclick="this.parentElement.style.display='none';">&times;</span>
                            <span><?php echo esc_attr($notice[1]); ?></span>
                        </div>
                        <?php } elseif((isset($opts['status']) && ($opts['status']) === 'off')) { ?>
                        <div class="wpdx-notice warning">
                            <span class="close-btn" onclick="this.parentElement.style.display='none';">&times;</span>
                            <span><?php echo _e('You have not set up your automatic rename options ! In order to do so, please use the below form.', 'medias-toolbox'); ?></span>
                        </div>
                        <?php } else { ?>
                        <div class="wpdx-notice info">
                            <span class="close-btn" onclick="this.parentElement.style.display='none';">&times;</span>
                            <span><?php echo _e('Your plugin is properly configured ! You can change at anytime your automatic rename options using the below form.', 'medias-toolbox'); ?></span>
                        </div>
                        <?php } ?>
                        <form method="POST">
                            <input type="hidden" name="mtb-update-renamer" value="true" />
                            <?php wp_nonce_field('mtb-referer-form', 'mtb-referer-renamer'); ?>
                            <div class="wpdx-form">
                                <div class="field">
                                    <?php $fieldID = uniqid(); ?>
                                    <span class="label"><?php echo _e('Automatic Files Renamer', 'medias-toolbox'); ?></span>
                                    <div class="onoffswitch">
                                        <input id="<?php echo esc_attr($fieldID); ?>" type="checkbox" name="_medias_toolbox_renamer[status]" class="onoffswitch-checkbox input-status" <?php if((isset($opts['status'])) && ($opts['status'] === 'on')) { echo esc_attr('checked="checked"');} ?>/>
                                        <label class="onoffswitch-label" for="<?php echo esc_attr($fieldID); ?>">
                                            <span class="onoffswitch-inner"></span>
                                            <span class="onoffswitch-switch"></span>
                                        </label>
                                    </div>
                                    <small><?php echo _e('Do you want to automatically rename your files on upload ?', 'medias-toolbox'); ?></small>
                                </div>
                                <div id="handler-renamer" class="subfield <?php if((isset($opts['status'])) && ($opts['status'] === 'on')) { echo 'show'; } ?>">
                                    <?php
                                    foreach(MEDIAS_TOOLBOX_FILETYPE as $fileExt) 
                                    {
                                        $fieldID = uniqid();
                                        $html = '<div class="field">';
                                        $html.= '<span class="label">'.sprintf(__('Rename %s File', 'medias-toolbox'), strtoupper($fileExt)).'</span>';
                                        $html.= '<div class="onoffswitch">';
                                        $html.= '<input id="'.esc_attr($fieldID).'" type="checkbox" name="_medias_toolbox_renamer['.esc_attr($fileExt).']" class="onoffswitch-checkbox" '.((isset($opts[$fileExt]) && $opts[$fileExt] === 'on') ? 'checked="checked"' : '' ).'/>';
                                        $html.= '<label class="onoffswitch-label" for="'.esc_attr($fieldID).'">';
                                        $html.= '<span class="onoffswitch-inner"></span>';
                                        $html.= '<span class="onoffswitch-switch"></span>';
                                        $html.= '</label>';
                                        $html.= '</div>';
                                        $html.= '<small>'.sprintf(__('Do you want to automatically rename your %s files on upload ?', 'medias-toolbox'), strtoupper($fileExt)).'</small>';
                                        $html .= '</div>';
                                        echo $html;
                                    }
                                    ?>
                                    <div class="field">
                                        <span class="label"><?php echo _e('Rename Patterns', 'medias-toolbox'); ?></span>
                                        <?php
                                        $level = array
                                        (
                                            '1' => array
                                            (
                                                'title' => __('Current Date/Time + Sanitized filename', 'medias-toolbox'),
                                                'pattern' => sprintf(__('%s-original-filename-sanitized.jpg'), date('YmdHi'))
                                            ),
                                            '2' => array
                                            (
                                                'title' => __('Current Timestamp + Sanitized filename', 'medias-toolbox'),
                                                'pattern' => sprintf(__('%s-original-filename-sanitized.jpg'), time())
                                            ),
                                            '3' => array
                                            (
                                                'title' => __('Current Date/Time + Unique Random String', 'medias-toolbox'),
                                                'pattern' => sprintf(__('%s-%s.jpg'), date('YmdHi'), md5('original-filename-sanitized'))
                                            ),
                                            '4' => array
                                            (
                                                'title' => __('Current Timestamp + Unique Random String', 'medias-toolbox'),
                                                'pattern' => sprintf(__('%s-%s.jpg'), time(), md5('original-filename-sanitized'))
                                            ),
                                            '5' => array
                                            (
                                                'title' => __('Current Date/Time + Encrypted filename', 'medias-toolbox'),
                                                'pattern' => sprintf(__('%s-%s.jpg'), date('YmdHi'), md5('original-filename-sanitized'))
                                            ),
                                            '6' => array
                                            (
                                                'title' => __('Current Timestamp + Encrypted filename', 'medias-toolbox'),
                                                'pattern' => sprintf(__('%s-%s.jpg'), time(), md5('original-filename-sanitized'))
                                            )
                                        );

                                        if(!isset($opts['pattern']))
                                        {
                                            $opts = array('pattern' => '1');
                                        }

                                        foreach($level as $minkey => $minval)
                                        {
                                            $fieldID = uniqid();
                                            $html = '<div class="wpdx-break"><!-- Line Break 05px --></div>';
                                            $html.= '<label class="radiobox">';
                                            $html.= '<span><b>'.$minval["title"].'</b><br>'.$minval["pattern"].'</span>';
                                            $html.= '<input type="radio" id="'.esc_attr($fieldID).'" name="_medias_toolbox_renamer[pattern]" value="'.$minkey.'" class="common" '.(((isset($opts['pattern'])) && ($opts['pattern'] == $minkey)) ? esc_attr('checked="checked"') : '').' />';
                                            $html.= '<span style="top:10px;" class="checkmark"></span>';
                                            $html.= '</label>';
                                            $html.= '<hr/>';
                                            echo $html;
                                        }
                                        ?>
                                    </div>
                                </div>
                                <div class="form-footer">
                                    <input type="submit" class="button button-primary button-theme" style="height:45px;" value="<?php _e('Update Settings', 'medias-toolbox'); ?>">
                                </div>
                            </div>
                        </form>
                    </section>
                </main>
            </div>
        </div>
    </section>
</div>