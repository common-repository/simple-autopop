<?php
/*
Plugin Name: Simple AutoPOP
Plugin URI: https://wordpress.org/plugins/simple-autopop/
Description: Displays events from EventUpon.com, activate and place [simple-autopop] tag to a page you want to have the calendar.
Version: 1.0
Author: EventUpon
Author URI: www.eventupon.com
License: GPL2
*/
/*
Copyright 2018  Max Kovrigovich (email : max.kovrigovich@gmail.com)

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License, version 2, as
published by the Free Software Foundation.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/
require_once 'services/event-generator.php';
require_once 'services/event-provider.php';
require_once 'services/event-cacher.php';
require_once 'widget.php';

if (!class_exists('Simple_AutoPOP')) {
    class Simple_AutoPOP
    {
        const TAG = 'simple-autopop';
        const CALENDAR_BASE = 'www.eventupon.com';
        const PREFIX = 'simple_autopop';

        /**
         * Construct the plugin object
         */
        public function __construct()
        {
            // Initialize Settings
            require_once(sprintf("%s/settings.php", dirname(__FILE__)));
            $this->pageSettings = new Simple_AutoPOP_Settings();

            $plugin = plugin_basename(__FILE__);
            add_filter("plugin_action_links_$plugin", array($this, 'plugin_settings_link'));
            add_action('admin_menu', array($this, 'add_pages'));

            add_action( 'widgets_init', array($this, 'register_widget') );
        } // END public function __construct

        function register_widget() {
            register_widget( 'SimpleAutoPOP_Widget' );
        }

        public function check_rights()
        {
            if (!current_user_can('publish_posts')) {
                wp_die(__('You do not have sufficient permissions to access this page.'));
            }
        }

        public function add_pages()
        {
            add_menu_page('Settings', 'Simple AutoPOP', 'publish_posts', 'simple_autopop_menu', array($this->pageSettings, 'plugin_settings_page'), 'dashicons-calendar', 5);
        }

        /**
         * Activate the plugin
         */
        public static function activate()
        {
            add_option(Simple_AutoPOP::PREFIX . '_saved_search_id', '');
            add_option(Simple_AutoPOP::PREFIX . '_url', 'http://www.eventupon.com/services/rest.json');
            add_option(Simple_AutoPOP::PREFIX . '_activate_links', true);
            add_option(Simple_AutoPOP::PREFIX . '_cache', '{}');
            add_option(Simple_AutoPOP::PREFIX . '_cache_lifetime', 0);
            add_option(Simple_AutoPOP::PREFIX . '_event_number', 3);
            add_option(Simple_AutoPOP::PREFIX . '_title', 'Upcoming Events');
            // Do nothing
        } // END public static function activate

        /**
         * Uninstall the plugin
         */
        public static function uninstall()
        {
            delete_option(Simple_AutoPOP::PREFIX . '_saved_search_id');
            delete_option(Simple_AutoPOP::PREFIX . '_url');
            delete_option(Simple_AutoPOP::PREFIX . '_activate_links');
            delete_option(Simple_AutoPOP::PREFIX . '_cache');
            delete_option(Simple_AutoPOP::PREFIX . '_cache_lifetime');
            delete_option(Simple_AutoPOP::PREFIX . '_event_number');
            delete_option(Simple_AutoPOP::PREFIX . '_title');

        } // END public static function deactivate

        // Add the settings link to the plugins page
        function plugin_settings_link($links)
        {
            $settings_link = '<a href="options-general.php?page=' . Simple_AutoPOP::PREFIX . '">Settings</a>';
            array_unshift($links, $settings_link);
            return $links;
        }

        public static function get_event_provider()
        {
            $savedSearchId = get_option(Simple_AutoPOP::PREFIX . '_saved_search_id', '');
            $apiEndpoint = get_option(Simple_AutoPOP::PREFIX . '_url', '');
            $cacheLifeTime = get_option(Simple_AutoPOP::PREFIX . '_cache_lifetime', 0);

            $eventCacher = new Simple_AutoPOP_EventCacher((int)$cacheLifeTime);
            $eventProvider = new Simple_AutoPOP_EventProvider($apiEndpoint, $savedSearchId, $eventCacher);
            return $eventProvider;
        }

        public static function tag_filter()
        {
            $activateLinks = get_option(Simple_AutoPOP::PREFIX . '_activate_links', 0);
            $eventNumber = get_option(Simple_AutoPOP::PREFIX . '_event_number', 3);

            $eventProvider = self::get_event_provider();
            $eventProvider->setEventNumber($eventNumber);
            $eventGenerator = new Simple_AutoPOP_EventGenerator($activateLinks);

            $list = $eventProvider->getEvents();

            return $eventGenerator->generateFrom($list);
        }

    } // END class Simple_AutoPOP
} // END if(!class_exists('Simple_AutoPOP'))

if (class_exists('Simple_AutoPOP')) {
    // Installation and uninstallation hooks
    register_activation_hook(__FILE__, array('Simple_AutoPOP', 'activate'));
    register_uninstall_hook(__FILE__, array('Simple_AutoPOP', 'uninstall'));
    add_shortcode(Simple_AutoPOP::TAG, array('Simple_AutoPOP', 'tag_filter'));

    wp_register_style('simple-autopop-styles', plugins_url('style.css',__FILE__ ));
    wp_enqueue_style( 'simple-autopop-styles');
    // Hook for adding admin menus

    // instantiate the plugin class
    $instance = new Simple_AutoPOP();
}