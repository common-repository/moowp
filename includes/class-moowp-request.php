<?php
class MooWP_Request extends MooWP_App {
    private $initiated = false;

    public function init() {
        if ( ! $this->initiated ) {
            add_action( 'rest_api_init', array( $this, 'register_routes' ) );
        }
    }

    public function register_routes() {
        $this->initiated = true;

        //--url: /wp-json/moowp-app/notifications/all
        //--wp: wp-content/plugins/moowp/public/assets/js/moosocial.js | load_tab_content()
        register_rest_route( MOOWP_APP_NAMESPACE.'/notifications', '/all', array(
            array(
                'methods'             => WP_REST_Server::CREATABLE,
                'callback'            => array($this, 'get_notifications' ),
                'permission_callback' => array($this, 'get_permissions_check' ),
            )
        ) );

        //--url: /wp-json/moowp-app/conversations/all
        //--wp: wp-content/plugins/moowp/public/assets/js/moosocial.js | load_tab_content()
        register_rest_route( MOOWP_APP_NAMESPACE.'/conversations', '/all', array(
            array(
                'methods'             => WP_REST_Server::CREATABLE,
                'callback'            => array($this, 'get_conversations' ),
                'permission_callback' => array($this, 'get_permissions_check' ),
            )
        ) );

        //--url: /wp-json/moowp-app/notifications/refresh
        //--wp: wp-content/plugins/moowp/public/assets/js/moosocial.js | load_tab_content()
        register_rest_route( MOOWP_APP_NAMESPACE.'/notifications', '/refresh', array(
            array(
                'methods'             => WP_REST_Server::CREATABLE,
                'callback'            => array($this, 'get_notifications_refresh' ),
                'permission_callback' => array($this, 'get_permissions_check' ),
            )
        ) );

        //--url: /wp-json/moowp-app/notifications/mark_all_read
        //--wp: wp-content/plugins/moowp/public/assets/js/moosocial.js | init_notification_action()
        register_rest_route( MOOWP_APP_NAMESPACE.'/notifications', '/mark_all_read', array(
            array(
                'methods'             => WP_REST_Server::CREATABLE,
                'callback'            => array($this, 'do_notification_mark_all_read' ),
                'permission_callback' => array($this, 'get_permissions_check' ),
            )
        ) );

        //--url: /wp-json/moowp-app/notifications/clear_all_notifications
        //--wp: wp-content/plugins/moowp/public/assets/js/moosocial.js | init_notification_action()
        register_rest_route( MOOWP_APP_NAMESPACE.'/notifications', '/clear_all_notifications', array(
            array(
                'methods'             => WP_REST_Server::CREATABLE,
                'callback'            => array($this, 'do_notification_clear_all' ),
                'permission_callback' => array($this, 'get_permissions_check' ),
            )
        ) );

        //--url: /wp-json/moowp-app/notifications/mark_read
        //--wp: wp-content/plugins/moowp/public/assets/js/moosocial.js | init_notification_action()
        register_rest_route( MOOWP_APP_NAMESPACE.'/notifications', '/mark_read', array(
            'methods'             => WP_REST_Server::CREATABLE,
            'callback'            => array($this, 'do_notification_mark_read' ),
            'permission_callback' => array($this, 'get_permissions_check' ),
        ));

        //--url: /wp-json/moowp-app/notifications/remove
        //--wp: wp-content/plugins/moowp/public/assets/js/moosocial.js | init_notification_action()
        register_rest_route( MOOWP_APP_NAMESPACE.'/notifications', '/remove', array(
            'methods'             => WP_REST_Server::CREATABLE,
            'callback'            => array($this, 'do_notification_remove' ),
            'permission_callback' => array($this, 'get_permissions_check' ),
        ));

        //--url: /wp-json/moowp-app/user/sync_user/xxx
        //--wp: wp-content/plugins/moowp/admin/profile_fields/profile_field.php
        register_rest_route( MOOWP_APP_NAMESPACE.'/user', '/sync_user'. '/(?P<moo_user_key>[a-zA-Z0-9-]+)', array(
            array(
                'methods'             => WP_REST_Server::READABLE,
                'callback'            => array($this, 'do_sync_user_wp_to_moo' ),
                'permission_callback' => array($this, 'get_permissions_check' ),
            )
        ) );

        //--url: /wp-json/moowp-app/user/admin_login_verification
        //--wp: wp-content/plugins/moowp/admin/profile_fields/profile_field.php
        //--wp: wp-content/plugins/moowp/admin/form_fields/moosocial_toolbar_field.php
        register_rest_route( MOOWP_APP_NAMESPACE.'/user', '/admin_login_verification', array(
            array(
                'methods'             => WP_REST_Server::CREATABLE,
                'callback'            => array($this, 'do_admin_login_verification' ),
                'permission_callback' => array($this, 'get_permissions_check' ),
            )
        ) );

        //--url: /wp-json/moowp-app/user/clear_login/xxx
        //--moo: web/community/app/Plugin/WordpressIntegration/Lib/WordpressIntegrationListener.php
        register_rest_route( MOOWP_APP_NAMESPACE.'/user', '/clear_login'. '/(?P<moo_user_key>[a-zA-Z0-9-]+)', array(
            array(
                'methods'             => WP_REST_Server::READABLE,
                'callback'            => array($this, 'do_clear_cookie_login' ),
                'permission_callback' => array($this, 'get_permissions_check' ),
            )
        ) );

        //--url: /wp-json/moowp-app/security/admin_confirm_update_security_key
        //--wp: wp-content/plugins/moowp/admin/form_fields/moosocial_toolbar_field.php
        register_rest_route( MOOWP_APP_NAMESPACE.'/security', '/admin_confirm_update_security_key', array(
            array(
                'methods'             => WP_REST_Server::CREATABLE,
                'callback'            => array($this, 'do_confirm_update_security_key' ),
                'permission_callback' => array($this, 'get_permissions_check' ),
            )
        ) );

        register_rest_route( MOOWP_APP_NAMESPACE.'/reset', '/new_setup', array(
            array(
                'methods'             => WP_REST_Server::CREATABLE,
                'callback'            => array($this, 'do_reset_new_setup' ),
                'permission_callback' => array($this, 'get_permissions_check' ),
            )
        ) );
    }

    public function do_reset_new_setup($request){
        $params = $request->get_params();
        $security_key = $params['security_key'];
        $user_id = $params['user_id'];

        $result = array(
            'success' => false,
            'messages' => '',
            'data' => array(),
        );

        if($this->moosocial_security_key == $security_key){
            update_option(self::$option_name . '_user_map_root', $user_id);
            update_option(self::$option_name . '_security_key', self::getRandomSecurityKey());
            update_option(self::$option_name . '_is_connecting', 0);
            update_option(self::$option_name . '_error_flag', 0);
            update_option(self::$option_name . '_pages_menu', '');
            update_option(self::$option_name . '_recovery_key', '');
            update_option(self::$option_name . '_address_url', '');
            update_option(self::$option_name . '_re_address_url', '');
            $result['success'] = true;
        }

        return new WP_REST_Response( $result, 200 );
    }

    public function get_permissions_check( $request ) {
        $params = $request->get_params();

        return true;
        /*if ((int)$params['userID']!==(int)$GLOBALS['user_id']) {
            return new \WP_Error( 'rest_forbidden', __('Sorry, you are not allowed to do that.', 'moowp'), ['status' => 401] );
        }else{
            return true;
        }*/
    }

    public function get_notifications($request){
        $params = $request->get_params();
        $user_id = $params['user_id'];

        $result = array(
            'success' => true,
            'messages' => '',
            'data' => array(),
        );

        $user_check = get_userdata($user_id);

        if(!empty($user_check)){
            $moo_user_key = $this->_generate_moo_key($user_id);

            $user_info = $this->get_user_by_meta_key($moo_user_key);

            if(!empty($user_info)){

                $url = $this->moosocial_address_url.'/'.MOOWP_CORE_API_NAMESPACE.'/html_notifications';

                $post = array(
                    'security_key' => $this->moosocial_security_key,
                    'id'   => $user_info->ID,
                    'user_key'   => $user_info->moo_user_key,
                    'email' => $user_info->user_email,
                    'username' => $user_info->user_login,
                    'firstname'   => $user_info->user_firstname,
                    'lastname'   => $user_info->user_lastname
                );

                $result_data = $this->curl_post($url, $post, 'html');

                $result['success'] = true;
                $result['messages'] = 'true';
                $result['data'] = $result_data;
            }else{
                $result['success'] = false;
                $result['messages'] = 'false';
            }
        }else{
            $result['success'] = false;
            $result['messages'] = 'false';
        }

        return new WP_REST_Response( $result, 200 );
    }

    public function get_conversations($request){
        $params = $request->get_params();
        $user_id = $params['user_id'];

        $result = array(
            'success' => true,
            'messages' => '',
            'data' => array(),
        );

        $user_check = get_userdata($user_id);

        if(!empty($user_check)){
            $moo_user_key = $this->_generate_moo_key($user_id);

            $user_info = $this->get_user_by_meta_key($moo_user_key);

            if(!empty($user_info)){

                $url = $this->moosocial_address_url.'/'.MOOWP_CORE_API_NAMESPACE.'/html_conversations';

                $post = [
                    'security_key' => $this->moosocial_security_key,
                    'id'   => $user_info->ID,
                    'user_key'   => $user_info->moo_user_key,
                    'email' => $user_info->user_email,
                    'username' => $user_info->user_login,
                    'firstname'   => $user_info->user_firstname,
                    'lastname'   => $user_info->user_lastname
                ];

                $result_data = $this->curl_post($url, $post, 'html');

                $result['success'] = true;
                $result['messages'] = 'true';
                $result['data'] = $result_data;
            }else{
                $result['success'] = false;
                $result['messages'] = 'false';
            }
        }else{
            $result['success'] = false;
            $result['messages'] = 'false';
        }

        return new WP_REST_Response( $result, 200 );
    }

    public function get_notifications_refresh($request){
        $params = $request->get_params();
        $user_id = $params['user_id'];

        $result = array(
            'success' => true,
            'messages' => '',
            'code' => 0,
            'data' => array(),
        );

        $user_check = get_userdata($user_id);

        if(!empty($user_check)){
            $moo_user_key = $this->_generate_moo_key($user_id);

            $user_info = $this->get_user_by_meta_key($moo_user_key);

            if(!empty($user_info)){

                $url = $this->moosocial_address_url.'/'.MOOWP_CORE_API_NAMESPACE.'/notifications_refresh';

                $post = [
                    'security_key' => $this->moosocial_security_key,
                    'id'   => $user_info->ID,
                    'user_key'   => $user_info->moo_user_key,
                    'email' => $user_info->user_email,
                    'username' => $user_info->user_login,
                    'firstname'   => $user_info->user_firstname,
                    'lastname'   => $user_info->user_lastname
                ];

                $result_data = $this->curl_post($url, $post, 'json');

                if($result_data['success'] == true){
                    $result['success'] = true;
                    $result['messages'] = 'true';
                    $result['data'] = $result_data['data'];
                }else{
                    $result['success'] = false;
                    $result['messages'] = $result_data['messages'];
                    $result['code'] = $result_data['code'];
                }
            }else{
                $result['success'] = false;
                $result['messages'] = 'false';
            }
        }else{
            $result['success'] = false;
            $result['messages'] = 'false';
        }

        return new WP_REST_Response( $result, 200 );
    }

    public function do_notification_mark_all_read($request){
        $params = $request->get_params();
        $user_id = $params['user_id'];

        $result = array(
            'success' => true,
            'messages' => '',
            'data' => array(),
        );

        $user_check = get_userdata($user_id);

        if(!empty($user_check)){
            $moo_user_key = $this->_generate_moo_key($user_id);

            $user_info = $this->get_user_by_meta_key($moo_user_key);

            if(!empty($user_info)){

                $url = $this->moosocial_address_url.'/'.MOOWP_CORE_API_NAMESPACE.'/mark_all_read';

                $post = [
                    'security_key' => $this->moosocial_security_key,
                    'id'   => $user_info->ID,
                    'user_key'   => $user_info->moo_user_key,
                    'email' => $user_info->user_email,
                    'username' => $user_info->user_login,
                    'firstname'   => $user_info->user_firstname,
                    'lastname'   => $user_info->user_lastname
                ];

                $result_data = $this->curl_post($url, $post, 'json');

                if($result_data['success'] == true){
                    $result['success'] = true;
                    $result['messages'] = 'true';
                }else{
                    $result['success'] = false;
                    $result['messages'] = 'false';
                }
            }else{
                $result['success'] = false;
                $result['messages'] = 'false';
            }
        }else{
            $result['success'] = false;
            $result['messages'] = 'false';
        }

        return new WP_REST_Response( $result, 200 );
    }

    public function do_notification_clear_all($request){
        $params = $request->get_params();
        $user_id = $params['user_id'];

        $result = array(
            'success' => true,
            'messages' => '',
            'data' => array(),
        );

        $user_check = get_userdata($user_id);

        if(!empty($user_check)){
            $moo_user_key = $this->_generate_moo_key($user_id);

            $user_info = $this->get_user_by_meta_key($moo_user_key);

            if(!empty($user_info)){

                $url = $this->moosocial_address_url.'/'.MOOWP_CORE_API_NAMESPACE.'/clear_all_notifications';

                $post = [
                    'security_key' => $this->moosocial_security_key,
                    'id'   => $user_info->ID,
                    'user_key'   => $user_info->moo_user_key,
                    'email' => $user_info->user_email,
                    'username' => $user_info->user_login,
                    'firstname'   => $user_info->user_firstname,
                    'lastname'   => $user_info->user_lastname
                ];

                $result_data = $this->curl_post($url, $post, 'json');

                if($result_data['success'] == true){
                    $result['success'] = true;
                    $result['messages'] = 'true';
                }else{
                    $result['success'] = false;
                    $result['messages'] = 'false';
                }
            }else{
                $result['success'] = false;
                $result['messages'] = 'false';
            }
        }else{
            $result['success'] = false;
            $result['messages'] = 'false';
        }

        return new WP_REST_Response( $result, 200 );
    }

    public function do_notification_mark_read($request){
        $params = $request->get_params();
        $user_id = $params['user_id'];
        $status = $params['status'];
        $notification_id = $params['notification_id'];

        $result = array(
            'success' => true,
            'messages' => '',
            'data' => $params,
        );

        $user_check = get_userdata($user_id);

        if(!empty($user_check)){
            $moo_user_key = $this->_generate_moo_key($user_id);

            $user_info = $this->get_user_by_meta_key($moo_user_key);

            if(!empty($user_info)){

                $url = $this->moosocial_address_url.'/'.MOOWP_CORE_API_NAMESPACE.'/mark_read';

                $post = [
                    'security_key' => $this->moosocial_security_key,
                    'id'   => $user_info->ID,
                    'user_key'   => $user_info->moo_user_key,
                    'email' => $user_info->user_email,
                    'username' => $user_info->user_login,
                    'firstname'   => $user_info->user_firstname,
                    'lastname'   => $user_info->user_lastname,
                    'status' => $status,
                    'notification_id' => $notification_id,
                ];

                $result_data = $this->curl_post($url, $post, 'json');

                if($result_data['success'] == true){
                    $result['success'] = true;
                    $result['messages'] = 'true';
                    $result['data'] = $result_data['data'];
                }else{
                    $result['success'] = false;
                    $result['messages'] = 'false';
                }
            }else{
                $result['success'] = false;
                $result['messages'] = 'false';
            }
        }else{
            $result['success'] = false;
            $result['messages'] = 'false';
        }

        return new WP_REST_Response( $result, 200 );
    }

    public function do_notification_remove($request){
        $params = $request->get_params();
        $user_id = $params['user_id'];
        $notification_id = $params['notification_id'];

        $result = array(
            'success' => true,
            'messages' => '',
            'data' => $params,
        );

        $user_check = get_userdata($user_id);

        if(!empty($user_check)){
            $moo_user_key = $this->_generate_moo_key($user_id);

            $user_info = $this->get_user_by_meta_key($moo_user_key);

            if(!empty($user_info)){

                $url = $this->moosocial_address_url.'/'.MOOWP_CORE_API_NAMESPACE.'/notification_remove';

                $post = [
                    'security_key' => $this->moosocial_security_key,
                    'id'   => $user_info->ID,
                    'user_key'   => $user_info->moo_user_key,
                    'email' => $user_info->user_email,
                    'username' => $user_info->user_login,
                    'firstname'   => $user_info->user_firstname,
                    'lastname'   => $user_info->user_lastname,
                    'notification_id' => $notification_id,
                ];

                $result_data = $this->curl_post($url, $post, 'json');

                if($result_data['success'] == true){
                    $result['success'] = true;
                    $result['messages'] = 'true';
                    $result['data'] = $result_data['data'];
                }else{
                    $result['success'] = false;
                    $result['messages'] = 'false';
                }
            }else{
                $result['success'] = false;
                $result['messages'] = 'false';
            }
        }else{
            $result['success'] = false;
            $result['messages'] = 'false';
        }

        return new WP_REST_Response( $result, 200 );
    }

    public function do_sync_user_wp_to_moo($request){
        $params = $request->get_params();
        $moo_user_key = $params['moo_user_key'];

        $result = array(
            'success' => false,
            'messages' => '',
            'data' => array(),
        );

        $user_info = $this->get_user_by_meta_key($moo_user_key);

        if(!empty($user_info)){

            $url = $this->moosocial_address_url.'/'.MOOWP_CORE_API_NAMESPACE.'/create_user';

            $post = [
                'security_key' => $this->moosocial_security_key,
                'roles' => wp_json_encode($user_info->roles),
                'id'   => $user_info->ID,
                'user_key'   => $user_info->moo_user_key,
                'email' => $user_info->user_email,
                'username' => $user_info->user_login,
                'firstname'   => $user_info->user_firstname,
                'lastname'   => $user_info->user_lastname
            ];
            $result_data = $this->curl_post($url, $post, 'json');

            if($result_data['success'] == true){
                $result['success'] = true;
                $result['messages'] = 'true';
                $result['data'] = $result_data['data'];
            }else{
                $result['messages'] = $result_data['messages'];
            }
        }else{
            $result['messages'] = 'User not found!';
        }

        wp_reset_postdata();

        return new WP_REST_Response( $result, 200 );
    }

    public function do_admin_login_verification($request){
        $params = $request->get_params();
        $user_id = $params['user_id'];
        $moo_login_as = $params['moo_login_as'];

        $result = array(
            'success' => false,
            'messages' => '',
            'data' => array(),
        );

        $user_check = get_userdata($user_id);
        if(!empty($user_check)){
            $moo_user_key = $this->_generate_moo_key($user_id);

            $user_info = $this->get_user_by_meta_key($moo_user_key);

            if(!empty($user_info)){

                $isAdminWP = in_array('administrator', $user_info->roles, true);

                if($isAdminWP){
                    $url = $this->moosocial_address_url.'/'.MOOWP_CORE_API_NAMESPACE.'/check-admin-login-panel';

                    $post = [
                        'security_key' => $this->moosocial_security_key,
                        'id'   => $user_info->ID,
                        'user_key'   => $user_info->moo_user_key,
                        'moo_login_as' => $moo_login_as,
                        'email' => $user_info->user_email,
                        'username' => $user_info->user_login,
                        'firstname'   => $user_info->user_firstname,
                        'lastname'   => $user_info->user_lastname,
                        'wp_display_name'   => $user_info->display_name,
                    ];
                    $result_data = $this->curl_post($url, $post, 'json');

                    if($result_data['success'] == true){
                        $result['success'] = true;
                        $result['messages'] = $result_data['messages'];
                        $result['data'] = $result_data['data'];
                        /*$admin_login_token = $moo_login_as.'|'.$result_data['data']['admin_login_token'];
                        $expiration = 2*60;
                        $secure = ( 'https' === parse_url( wp_login_url(), PHP_URL_SCHEME ) );
                        if ( COOKIEPATH != SITECOOKIEPATH ) {
                            setcookie(LOGGED_ADMIN_COOKIE, $admin_login_token, $expiration, SITECOOKIEPATH, COOKIE_DOMAIN, $secure);
                        }else{
                            setcookie(LOGGED_ADMIN_COOKIE, $admin_login_token, $expiration, COOKIEPATH, COOKIE_DOMAIN, $secure);
                        }*/
                    }else{
                        $result['success'] = false;
                        $result['messages'] = $result_data['messages'];
                    }
                }
            }
        }

        wp_reset_postdata();

        return new WP_REST_Response( $result, 200 );
    }

    public function do_clear_cookie_login($request){
        $params = $request->get_params();
        $moo_user_key = $params['moo_user_key'];

        $result = array(
            'success' => false,
            'messages' => '',
            'data' => array(),
        );

        $user_info = $this->get_user_by_meta_key($moo_user_key);

        if(!empty($user_info)){
            $result['success'] = true;
            $result['messages'] = 'true';
            wp_destroy_current_session();
            wp_clear_auth_cookie();
            wp_set_current_user( 0 );

            // phpcs:disable WordPress.Security.NonceVerification.Recommended
            $redirect_url = (isset($_GET['moowp_redirect'])) ? sanitize_url($_GET['moowp_redirect']) : '';
            // phpcs:enable

            if(!empty($redirect_url)){
                wp_redirect(urldecode($redirect_url));
            }else{
                wp_redirect(home_url());
            }

            exit();
        }else{
            $result['messages'] = 'User not found!';
        }

        wp_reset_postdata();

        return new WP_REST_Response( $result, 200 );
    }

    public function do_confirm_update_security_key($request){
        $params = $request->get_params();
        $recovery_key = $params['recovery_key'];

        $result = array(
            'success' => false,
            'messages' => '',
            'data' => array(),
        );

        if(!empty($recovery_key) && $recovery_key == $this->moosocial_recovery_key){
            $url = $this->moosocial_address_url.'/'.MOOWP_CORE_API_NAMESPACE.'/confirm_update_security_key';
            $post = [
                'security_key' => $this->moosocial_security_key,
                'recovery_key'   => $this->moosocial_recovery_key,
            ];
            $result_data = $this->curl_post($url, $post, 'json');

            if(isset($result_data['success']) && $result_data['success'] == true){
                update_option(self::$option_name . '_recovery_key', '');
                $result['success'] = true;
            }
        }

        wp_reset_postdata();

        return new WP_REST_Response( $result, 200 );
    }
}
?>