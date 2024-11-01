<?php
if(!class_exists('Simple_AutoPOP_Settings'))
{
    class Simple_AutoPOP_Settings
    {
        /**
         * Construct the plugin object
         */
        public function __construct()
        {
            // register actions
            add_action('admin_init', array(&$this, 'admin_init'));
            add_action('admin_menu', array(&$this, 'add_menu'));

            $options = array(
            );

            foreach ($options as $option_name => $value) {
                if ( get_option($option_name) == '') {
                    update_option($option_name, $value);
                } else {
                    add_option($option_name, $value);
                }
            }
        } // END public function __construct


        /**
         * hook into WP's admin_init action hook
         */
        public function admin_init()
        {
            // register your plugin's settings
            register_setting(Simple_AutoPOP::PREFIX . '-group', Simple_AutoPOP::PREFIX . '_saved_search_id');
            // register_setting(Simple_AutoPOP::PREFIX . '-group', Simple_AutoPOP::PREFIX . '_activate_links');
            register_setting(Simple_AutoPOP::PREFIX . '-group', Simple_AutoPOP::PREFIX . '_url');
            register_setting(Simple_AutoPOP::PREFIX . '-group', Simple_AutoPOP::PREFIX . '_cache_lifetime');
            register_setting(Simple_AutoPOP::PREFIX . '-group', Simple_AutoPOP::PREFIX . '_event_number');
            register_setting(Simple_AutoPOP::PREFIX . '-group', Simple_AutoPOP::PREFIX . '_title');

// add General settings section
            add_settings_section(
                Simple_AutoPOP::PREFIX . '-section',
                'General Settings',
                array(&$this, 'settings_section'),
                Simple_AutoPOP::PREFIX . '_general'
            );

            add_settings_field(
                Simple_AutoPOP::PREFIX . '-title',
                'Title',
                array(&$this, 'settings_field_input_text'),
                Simple_AutoPOP::PREFIX . '_general',
                Simple_AutoPOP::PREFIX . '-section',
                array(
                    'field' => Simple_AutoPOP::PREFIX . '_title',
                    'help' => 'Title'
                )
            );

            add_settings_field(
                Simple_AutoPOP::PREFIX . '-calendar_url',
                'API Endpoint *',
                array(&$this, 'settings_field_input_text'),
                Simple_AutoPOP::PREFIX . '_general',
                Simple_AutoPOP::PREFIX . '-section',
                array(
                    'field' => 'simple_autopop_url',
                    'help' => 'API Endpoint',
                    'required' => true,
                    'size' => 50
                )
            );

            add_settings_field(
                Simple_AutoPOP::PREFIX . '-calendar_saved_search',
                'Search Token *',
                array(&$this, 'settings_field_input_text'),
                Simple_AutoPOP::PREFIX . '_general',
                Simple_AutoPOP::PREFIX . '-section',
                array(
                    'field' => 'simple_autopop_saved_search_id',
                    'help' => 'Please follow instructions in the Plugin Installation Instructions, section "Once Installed"',
                    'required' => true,
                )
            );

/*            add_settings_field(
                Simple_AutoPOP::PREFIX . '-activate_links',
                'Activate Links',
                array(&$this, 'settings_field_checkbox'),
                Simple_AutoPOP::PREFIX . '_general',
                Simple_AutoPOP::PREFIX . '-section',
                array(
                    'field' => Simple_AutoPOP::PREFIX . '_activate_links',
                    'help' => 'This must be set so that clicking on an event will link to the event details (WordPress requires all external links to be opt-in)'
                )
            );*/

            add_settings_field(
                Simple_AutoPOP::PREFIX . '-cache_lifetime',
                'Cache Lifetime, min',
                array(&$this, 'settings_field_input_text'),
                Simple_AutoPOP::PREFIX . '_general',
                Simple_AutoPOP::PREFIX . '-section',
                array(
                    'field' => Simple_AutoPOP::PREFIX . '_cache_lifetime',
                    'help' => '# of min API response is stored. Set 0 to disable caching.'
                )
            );
            add_settings_field(
                Simple_AutoPOP::PREFIX . '-event_number',
                'Display # events',
                array(&$this, 'settings_field_input_text'),
                Simple_AutoPOP::PREFIX . '_general',
                Simple_AutoPOP::PREFIX . '-section',
                array(
                    'field' => Simple_AutoPOP::PREFIX . '_event_number',
                    'help' => '# of events to display'
                )
            );

            // Possibly do additional admin_init tasks
        } // END public static function activate

        public function settings_section()
        {
            // Think of this as help text for the section.
            echo 'Displays events from EventUpon.com, activate and place <b>[simple-autopop]</b> tag to a page you want to have the calendar.';
        }

        /**
         * This function provides text inputs for settings fields
         */
        public function settings_field_input_text($args)
        {
            // Get the field name from the $args array
            $field = $args['field'];
            // Get the value of this setting
            $value = get_option($field);
            // echo a proper input type="text"
            echo sprintf('<input type="text" name="%s" id="%s" value="%s" ' . (isset($args['class']) ? 'class="' . $args['class'] . '"' : '') . ' ' . ((isset($args['required']) && $args['required']) ? ' required ' : '') . ' ' . ((isset($args['size']) && $args['size']) ? (' size="' . $args['size'] . '"') : '') . '/>', $field, $field, $value);
            if (isset($args['help'])) {
                echo '<p>' . $args['help'] . '</p>';
            }
        } // END public function settings_field_input_text($args)

        /**
         * This function provides text inputs for settings fields
         */
        public function settings_field_checkbox($args)
        {
            // Get the field name from the $args array
            $field = $args['field'];
            // Get the value of this setting
            $value = get_option($field);
            // echo a proper input type="text"
            echo sprintf('<input type="checkbox" name="%s" id="%s" value="1" ' . checked( 1, $value, false ) . (isset($args['class']) ? ' class="' . $args['class'] . '"' : '') . ' />', $field, $value);
            if (isset($args['help'])) {
                echo '<p>' . $args['help'] . '</p>';
            }
        } // END public function settings_field_input_text($args)

        /**
         * This function provides select for settings fields
         */
        public function settings_field_select($args)
        {
            // Get the field name from the $args array
            $field = $args['field'];
            $options = $args['options'];
            // Get the value of this setting
            $value = get_option($field);
            // echo a proper input type="text"
            echo sprintf('<select name="%s" id="%s" ' . (isset($args['class']) ? 'class="' . $args['class'] . '"' : '') . ' />', $field, $field);
            foreach ($options as $key => $val) {
                echo sprintf('<option value="%s" ' . ($value == $key ? 'selected="selected"' : '') . '>%s</option>', $key, $val);
            }
            echo '</select>';
        } // END public function settings_field_select($args)

        /**
         * add a menu
         */
        public function add_menu()
        {
            // Add a page to manage this plugin's settings
            add_options_page(
                'Simple AutoPOP Settings',
                'Simple AutoPOP',
                'manage_options',
                Simple_AutoPOP::PREFIX,
                array(&$this, 'plugin_settings_page')
            );

            if (isset($_GET['settings-updated']) && $_GET['settings-updated']) {
                update_option(Simple_AutoPOP::PREFIX . '_cache', '');
            } // END public function add_menu()
        }

        /**
         * Menu Callback
         */
        public function plugin_settings_page()
        {
            if(!current_user_can('manage_options'))
            {
                wp_die(__('You do not have sufficient permissions to access this page.'));
            }

            // Render the settings template
            include(sprintf("%s/templates/settings.tpl.php", dirname(__FILE__)));
        } // END public function plugin_settings_page()
    } // END class Simple_AutoPOP_Settings
} // END if(!class_exists('Simple_AutoPOP_Settings'))
