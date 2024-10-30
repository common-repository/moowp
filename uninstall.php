<?php
/**
* Fired when the plugin is uninstalled.
*
* @package Moosocial
* @author Your Name <email@example.com>
* @license GPL-2.0+
* @link http://example.com
* @copyright 2014 Your Name or Company Name
*/

// If uninstall not called from WordPress, then exit
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

// @TODO: Define uninstall functionality here
// delete plugin options
delete_option('moowp_setting_general');
delete_option('moowp_setting_address_url');
delete_option('moowp_setting_pages_menu');
delete_option('moowp_setting_security_key');
delete_option('moowp_setting_cookie_expire');
delete_option('moowp_setting_notification_position');
delete_option('moowp_setting_moosocial_toolbar');
delete_option('moowp_setting_chat_plugin_enable');
delete_option('moowp_setting_user_map_root');
delete_option('moowp_setting_error_flag');
delete_option('moowp_setting_is_connecting');