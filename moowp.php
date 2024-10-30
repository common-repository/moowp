<?php
/**
* Plugin Name: mooWP
* Plugin URI: https://github.com/moosocialdev/moowp
* Description: mooWP is a wordpress plugin, it acts as a bridge between mooSocial platform and Wordpress to turn your WordPress Site Into a Social Network. It allows you to turn your one-time visitors into loyal, long-term users by providing them a place to sign up, connect with each other, post messages, and more
* Version: 1.0.0
* Author: mooSocial
* Author URI: https://moosocial.com/social-networking-plugin-for-wordpress
* License: GPL v2 or later
* License URI: http://www.gnu.org/licenses/gpl-2.0.txt
**/

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

define('MOOWP_PLUGIN_NAME', 'mooWP');
define('MOOWP_PLUGIN_DIR', plugin_dir_path( __FILE__ ));
define('MOOWP_PLUGIN_URL', plugin_dir_url( __FILE__ ));
define('MOOWP_VERSION', '1.0.0');

define('MOOWP_ERROR_NOT_LOGIN', 'not-login');
define('MOOWP_CURLOPT_TIMEOUT', 3);
define('MOOWP_API_NAMESPACE', 'moowp-api');
define('MOOWP_APP_NAMESPACE', 'moowp-app');
define('MOOWP_CORE_NAMESPACE', 'wordpress_integrations');
define('MOOWP_CORE_API_NAMESPACE', 'wordpress_integrations/api');

require_once( MOOWP_PLUGIN_DIR . 'includes/class-moowp-app.php' );
require_once( MOOWP_PLUGIN_DIR . 'includes/class-moowp-admin.php' );
require_once( MOOWP_PLUGIN_DIR . 'includes/class-moowp-request.php' );
require_once( MOOWP_PLUGIN_DIR . 'includes/class-moowp-rest-api.php' );
require_once( MOOWP_PLUGIN_DIR . 'includes/class-moowp-view.php' );
require_once( MOOWP_PLUGIN_DIR . 'includes/class-moowp-user.php' );
require_once( MOOWP_PLUGIN_DIR . 'includes/class-moowp-activate.php' );

$moowp_admin = new MooWP_Admin();
$moowp_admin->init();

$moowp_user = new MooWP_User();
$moowp_user->init();

$moowp_rest_api = new MooWP_REST_API();
$moowp_rest_api->init();

$moowp_request = new MooWP_Request();
$moowp_request->init();

$moowp_view = new MooWP_View();
$moowp_view->init();

register_activation_hook( __FILE__, array( 'MooWP_Activate', 'moowp_plugin_activate' ) );
$moowp_activate = new MooWP_Activate();
$moowp_activate->init();

?>