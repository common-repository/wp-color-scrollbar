<?php

class wp_color_scrollbar {

    public $plugin_dir = null;
    public $soocolor_options = null;
    public $autohide = null;
    public $active = true;
    public $upload_dir = null;

    function __construct() {
        $this->plugin_dir = WPCOLOR_PLUGIN_URL;
        $this->upload_dir = wp_upload_dir();
        $this->soocolor_options = get_option('soocolor_options');
        //Default Option
        if (!$this->soocolor_options) {
            $this->soocolor_options = array(
                'active' => true,
                'cursor_color' => '#666',
                'cursor_width' => '10px',
                'border_radius' => '0px',
                'cursor_border' => '0px solid #000',
                'scroll_speed' => '60',
                'auto_hide_mode' => 'true',
                'showfooter' => 'true'
            );
        }

        $this->autohide = array(
            'auto_hide_yes' => array(
                'value' => 'true',
                'label' => 'Activate auto hide'
            ),
            'auto_hide_no' => array(
                'value' => 'false',
                'label' => 'Deactivate auto hide'
            ),
        );

        add_action('init', array(&$this, 'add_jquery'));
        add_action('admin_menu', array(&$this, 'add_menu'));
        add_action('admin_enqueue_scripts', array(&$this, 'add_color_picker'));
        add_action('admin_init', array(&$this, 'register_settings'));
  
        if ($this->soocolor_options['active'] == true)
            wp_enqueue_script('soo-color-scrollbar', $this->plugin_dir . 'js/jquery.nicescroll.min.js', array('jquery'));
        if (is_admin() && $_GET['page'] == 'soocolor-settings')
            wp_enqueue_style('soo-colorscrollbar-css', $this->plugin_dir . 'css/color-scrollbar.css');
            
        add_action('wp_head', array(&$this, 'show_scrollbar_color'));
    }

    function add_jquery() {
        wp_enqueue_script('jquery');
    }

    function add_menu() {
        add_options_page('WP Color Scrollbar', 'WP Color Scrollbar', 'manage_options', 'soocolor-settings', array(&$this, 'options'));
    }

    function add_color_picker() {
        wp_enqueue_style('wp-color-picker');
        wp_enqueue_script('my-script-handle', $this->plugin_dir . 'js/color-pickr.js', array('wp-color-picker'), false, true);
    }

    function register_settings() {
        register_setting('soocolor_p_options', 'soocolor_options', array(&$this, 'validate_options'));
    }

    function validate_options($input) {
        global $auto_hide_mode;

        $settings = get_option('soocolor_options', $this->soocolor_options);

        // We strip all tags from the text field, to avoid vulnerablilties like XSS

        $input['active'] = wp_filter_post_kses($input['active']);
        $input['cursor_color'] = wp_filter_post_kses($input['cursor_color']);
        $input['cursor_width'] = wp_filter_post_kses($input['cursor_width']);
        $input['border_radius'] = wp_filter_post_kses($input['border_radius']);
        $input['cursor_border'] = wp_filter_post_kses($input['cursor_border']);
        $input['scroll_speed'] = wp_filter_post_kses($input['scroll_speed']);
        $input['showfooter'] = wp_filter_post_kses($input['showfooter']);


        // We select the previous value of the field, to restore it in case an invalid entry has been given
        $prev = $settings['layout_only'];
        // We verify if the given value exists in the layouts array
        if (!array_key_exists($input['layout_only'], $auto_hide_mode))
            $input['layout_only'] = $prev;



        return $input;
    }

    function options() {
        if (!isset($_REQUEST['updated']))
            $_REQUEST['updated'] = false;
        ?>
        <div class="wrap">
            <h2>WP Color Scrollbar</h2>
            <?php if (false !== $_REQUEST['updated']) : ?>
                <div class="updated fade"><p><strong><?php _e('Options saved'); ?></strong></p></div>
        <?php endif; // If the form has just been submitted, this shows the notification      ?>

            <form method="post" action="options.php">
                <?php $settings = $this->soocolor_options;
                ?>

                <?php
                settings_fields('soocolor_p_options');
                ?>
                <table class="form-table">

                    <tr valign="top">
                        <th scope="row"><label for="cursor_color">Active Scrollbar</label></th>
                        <td>
                            <div class="onoffswitch">
                                <input type="checkbox" name="soocolor_options[active]" class="onoffswitch-checkbox" style="display: none;" id="myonoffswitch" <?php echo ($settings['active'] ? 'checked' : ''); ?>>
                                <label class="onoffswitch-label" for="myonoffswitch">
                                    <div class="onoffswitch-inner"></div>
                                    <div class="onoffswitch-switch"></div>
                                </label>
                            </div>
                        </td>
                    </tr>

                    <tr valign="top">
                        <th scope="row"><label for="cursor_color">ScrollBar color</label></th>
                        <td>
                            <input id="cursor_color" type="text" name="soocolor_options[cursor_color]" value="<?php echo stripslashes($settings['cursor_color']); ?>" class="color-field" /><p class="description">Select your scrollbar color.</p>
                        </td>
                    </tr>

                    <tr valign="top">
                        <th scope="row"><label for="cursor_width">ScrollBar width</label></th>
                        <td>
                            <input id="cursor_width" type="text" name="soocolor_options[cursor_width]" value="<?php echo stripslashes($settings['cursor_width']); ?>" /><p class="description">Your Scrollbar With.</p>
                        </td>
                    </tr>

                    <tr valign="top">
                        <th scope="row"><label for="border_radius">ScrollBar border radius</label></th>
                        <td>
                            <input id="border_radius" type="text" name="soocolor_options[border_radius]" value="<?php echo stripslashes($settings['border_radius']); ?>" /><p class="description">Rounded corners of your scrollbar.</p>
                        </td>
                    </tr>

                    <tr valign="top">
                        <th scope="row"><label for="cursor_border">ScrollBar border style</label></th>
                        <td>
                            <input id="cursor_border" type="text" name="soocolor_options[cursor_border]" value="<?php echo stripslashes($settings['cursor_border']); ?>" /><p class="description">Border of the scrollbar. Ex: 2px solid #666 or 1px solid red.</p>
                        </td>
                    </tr>

                    <tr valign="top">
                        <th scope="row"><label for="scroll_speed">ScrollBar scroll speed</label></th>
                        <td>
                            <input id="scroll_speed" type="text" name="soocolor_options[scroll_speed]" value="<?php echo stripslashes($settings['scroll_speed']); ?>" /><p class="description">Your scrollbar speed. If you increase value, the scrolling speed will be slower. If you decrease value, scrolling speed will be faster.</p>
                        </td>
                    </tr>

                    <tr valign="top">
                        <th scope="row"><label for="background_color">Auto Hide</label></th>
                        <td>
        <?php foreach ($this->autohide as $activate) : ?>
                                <input type="radio" id="<?php echo $activate['value']; ?>" name="soocolor_options[auto_hide_mode]" value="<?php esc_attr_e($activate['value']); ?>" <?php checked($settings['auto_hide_mode'], $activate['value']); ?> />
                                <label for="<?php echo $activate['value']; ?>"><?php echo $activate['label']; ?></label><br />
        <?php endforeach; ?>
                            <p class="description">If we dont scroll, the scrollbar will be hiden in few seconds</p>
                        </td>
                    </tr>		

                    <tr valign="top">
                        <th scope="row"><label for="background_color">Show Footer</label></th>
                        <td>
                            <input type="radio" id="showfooter" name="soocolor_options[showfooter]" value="true" <?php echo ($settings['showfooter'] == 'true' ? 'checked' : ''); ?>/>
                            <label for="showfooter">Show</label><br />
                            <input type="radio" id="hidefooter" name="soocolor_options[showfooter]" value="false" <?php echo ($settings['showfooter'] == 'false' ? 'checked' : ''); ?>/>
                            <label for="hidefooter">Hide</label><br />
                            <p class="description">Help me with link to my site in footer!</p>
                        </td>
                    </tr>	

                </table>

                <p class="submit"><input type="submit" class="button-primary" value="Save Options" /></p>

            </form>

        </div>
        <?php
    }

    function show_scrollbar_color() {
        $soocolor_settings = $this->soocolor_options;
        if ($soocolor_settings['active'] == true) {
            ?>
            <script type="text/javascript">
                jQuery(document).ready(function() {
                    jQuery("html").niceScroll({
                        cursorcolor: "<?php echo $soocolor_settings['cursor_color']; ?>",
                        cursorwidth: "<?php echo $soocolor_settings['cursor_width']; ?>",
                        cursorborderradius: "<?php echo $soocolor_settings['border_radius']; ?>",
                        cursorborder: "<?php echo $soocolor_settings['cursor_border']; ?>",
                        scrollspeed: "<?php echo $soocolor_settings['scroll_speed']; ?>",
                        autohidemode: <?php echo $soocolor_settings['auto_hide_mode']; ?>,
                        touchbehavior: false,
                        bouncescroll: true,
                        horizrailenabled: false
                    });
                });
            </script>
            <?php
        }
    }
}
