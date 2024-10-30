<?php
class MooWP_REST_API extends MooWP_App {

    public function init() {
        add_action( 'rest_api_init', array( $this, 'register_routes' ) );
    }

    /**
     * Register the routes for the objects of the controller.
     */
    public function register_routes() {
        //--url: /wp-json/moowp-api/key
        register_rest_route(MOOWP_API_NAMESPACE, '/key', array(
            array(
                'methods'             => WP_REST_Server::READABLE,
                'callback'            => array($this, 'get_key' ),
                'permission_callback' => array($this, 'get_key_permissions_check' ),
            )
        ) );

        //--url: /wp-json/moowp-api/check_url
        //--moo: app/Controller/InstallController.php | ajax_check_url()
        register_rest_route(MOOWP_API_NAMESPACE, '/check_url', array(
            array(
                'methods'             => WP_REST_Server::READABLE,
                'callback'            => array($this, 'check_url' ),
                'permission_callback' => array($this, 'get_key_permissions_check' ),
            )
        ));

        //--url: /wp-json/moowp-api/setup_info
        //--moo: app/Controller/InstallController.php | ajax_check_wp()
        register_rest_route(MOOWP_API_NAMESPACE, '/setup_info', array(
            array(
                'methods'             => WP_REST_Server::CREATABLE,
                'callback'            => array($this, 'get_setup_info' ),
                'permission_callback' => array($this, 'get_key_permissions_check' ),
            )
        ));

        //--url: /wp-json/moowp-api/recovery_security_key
        //--moo: app/Plugin/WordpressIntegration/Controller/WordpressIntegrationsController.php | create_recovery_security_key()
        register_rest_route(MOOWP_API_NAMESPACE, '/recovery_security_key', array(
            array(
                'methods'             => WP_REST_Server::CREATABLE,
                'callback'            => array($this, 'recovery_security_key' ),
                'permission_callback' => array($this, 'get_key_permissions_check' ),
            )
        ));

        //--url: /wp-json/moowp-api/nav_menus
        //--moo: app/Plugin/WordpressIntegration/Controller/WordpressIntegrationsController.php | wordpress_nav_menu()
        register_rest_route(MOOWP_API_NAMESPACE, '/nav_menus', array(
            array(
                'methods'             => WP_REST_Server::CREATABLE,
                'callback'            => array($this, 'get_nav_menus' ),
                'permission_callback' => array($this, 'get_key_permissions_check' ),
            )
        ));

        //--url: /wp-json/moowp-api/search_link
        //--moo: app/Plugin/WordpressIntegration/Controller/WordpressIntegrationsController.php | wordpress_search()
        register_rest_route(MOOWP_API_NAMESPACE, '/search_link', array(
            array(
                'methods'             => WP_REST_Server::CREATABLE,
                'callback'            => array($this, 'get_search_link' ),
                'permission_callback' => array($this, 'get_key_permissions_check' ),
            )
        ));

        //--url: /wp-json/moowp-api/update_moo_info
        //--moo: app/Plugin/WordpressIntegration/Controller/WordpressIntegrationsController.php | update_settings()
        register_rest_route(MOOWP_API_NAMESPACE, '/update_moo_info', array(
            array(
                'methods'             => WP_REST_Server::CREATABLE,
                'callback'            => array($this, 'update_moo_info' ),
                'permission_callback' => array($this, 'get_key_permissions_check' ),
            )
        ));

        //--url: /wp-json/moowp-api/check_login_moo
        //--moo: app/Plugin/WordpressIntegration/Controller/WordpressIntegrationAppController.php | curl_check_accept_login_moo()
        register_rest_route(MOOWP_API_NAMESPACE, '/check_login_moo', array(
            array(
                'methods'             => WP_REST_Server::CREATABLE,
                'callback'            => array($this, 'check_login_moo' ),
                'permission_callback' => array($this, 'get_key_permissions_check' ),
            )
        ));
    }

    /**
     * Get a collection of items
     *
     * @param WP_REST_Request $request Full data about the request.
     * @return WP_Error|WP_REST_Response
     */
    public function get_key( $request ) {
        $key = md5(time());
        $data = array(
            'key' => $key
        );

        //return new WP_Error( 'cant-update', __( 'message', 'moowp' ), array( 'status' => 500 ) );
        //return new WP_Error( 'cant-create', __( 'message', 'moowp' ), array( 'status' => 500 ) );
        //return new WP_Error( 'code', __( 'message', 'moowp' ) );
        //return new WP_Error( 'cant-delete', __( 'message', 'moowp' ), array( 'status' => 500 ) );
        return new WP_REST_Response( $data, 200 );
        //return rest_ensure_response($data);
    }

    /**
     * Check if a given request has access to get items
     *
     * @param WP_REST_Request $request Full data about the request.
     * @return WP_Error|bool
     */
    public function get_key_permissions_check( $request ) {
        //return true; <--use to make readable by all
        //return current_user_can( 'edit_something' );
        //return current_user_can( 'manage_options' );
        return true;
    }

    public function check_url($request){
        $result = array(
            'success' => true,
            'messages' => '',
            'data' => array()
        );

        return new WP_REST_Response( $result, 200 );
    }

    public function get_setup_info($request){
        $params = $request->get_params();

        $request_security_key = $params['security_key'];

        $result = array(
            'success' => false,
            'messages' => '',
            'data' => array(),
        );

        if($request_security_key == $this->moosocial_security_key){
            $user_map_root_id = absint(get_option(self::$option_name.'_user_map_root'));

            if(!empty($user_map_root_id)){
                $user_obj = get_user_by('id', $user_map_root_id);

                $result['success'] = true;
                $result['data'] = array(
                    'user_name' => $user_obj->user_login,
                    'user_email' => $user_obj->user_email
                );
            }else{
                $result['messages'] = __('Setting of "mooWP Plugin" in Wordpress Site is not correct, please try again', 'moosocial');
            }
        }else{
            $result['messages'] = __('The wordpress security key is not correct, please try again', 'moosocial');
        }

        return new WP_REST_Response( $result, 200 );
    }

    public function recovery_security_key($request){
        $params = $request->get_params();
        $request_security_key = $params['security_key'];
        $request_recovery_key = $params['recovery_key'];

        $result = array(
            'success' => false,
            'messages' => '',
            'data' => array(),
        );

        if(!empty($request_security_key) && !empty($request_recovery_key) && $request_security_key == $this->moosocial_security_key && $this->moosocial_error_flag == 1){
            $result['success'] = true;
            update_option(self::$option_name.'_recovery_key', $request_recovery_key);
        }

        return new WP_REST_Response( $result, 200 );
    }

    public function get_nav_menus($request){
        $params = $request->get_params();

        $request_security_key = $params['security_key'];

        $result = array(
            'success' => false,
            'messages' => '',
            'data' => array(),
        );

        if($request_security_key == $this->moosocial_security_key){
            $menus = wp_get_nav_menus();

            $data = array();
            foreach ($menus As $menu){
                $menu_obj = wp_get_nav_menu_items($menu->term_id);
                $menu_items = array();
                foreach ($menu_obj As $item){
                    $menu_items[] = array(
                        'title' => $item->title,
                        'url' => $item->url
                    );
                }
                $data[] = array(
                    'name' => $menu->name,
                    'slug' => $menu->slug,
                    'items' => $menu_items
                );
            }

            $result['success'] = true;
            $result['data'] = $data;
        }else{
            $result['messages'] = __('The wordpress security key is not correct, please try again', 'moosocial');
        }

        return new WP_REST_Response( $result, 200 );
    }

    public function get_search_link($request){
        $params = $request->get_params();
        $request_security_key = $params['security_key'];
        $search_keyword = $params['keyword'];

        $result = array(
            'success' => false,
            'messages' => '',
            'data' => array(),
        );

        if($request_security_key == $this->moosocial_security_key){
            $the_query = new WP_Query( array(
                's' => $search_keyword,
                'order'=> 'DESC',
                'posts_per_page'=> 12
            ) );

            if ( $the_query->have_posts() ) {
                $data = array();
                while ( $the_query->have_posts() ) : $the_query->the_post();
                    $data[] = array(
                        'title' => get_the_title(),
                        'url' => get_the_permalink(),
                        'type' => get_post_type()
                    );
                endwhile;
                $result['data'] = $data;
            } else {
                // no posts found
            }

            $result['success'] = true;
            /* Restore original Post Data */
            wp_reset_postdata();
        }else{
            $result['messages'] = __('The wordpress security key is not correct, please try again', 'moosocial');
        }

        return new WP_REST_Response( $result, 200 );
    }

    public function update_moo_info($request){
        $params = $request->get_params();

        $request_security_key = $params['security_key'];
        $request_moo_url = $params['moo_url'];

        $result = array(
            'success' => false,
            'messages' => '',
            'data' => array(),
        );

        if($request_security_key == $this->moosocial_security_key){
            update_option(self::$option_name.'_address_url', $request_moo_url);
            update_option(self::$option_name.'_is_connecting', 1);
            update_option(self::$option_name.'_error_flag', 0);

            $result['success'] = true;
        }else{
            $result['messages'] = __('The wordpress security key is not correct, please try again', 'moosocial');
        }

        return new WP_REST_Response( $result, 200 );
    }

    public function check_login_moo($request){
        $params = $request->get_params();

        $cookie_check = array(
            'security_key' => $params['security_key'],
            'user_key' => $params['user_key'],
            'login_as_user_role' => $params['login_as_user_role'] //user_normal | user_admin | super_admin
        );

        $result = array(
            'success' => false,
            'messages' => '',
            'code' => 0,
            'data' => array(),
        );

        if($this->moosocial_security_key == $params['security_key']){
            $user_info = $this->get_user_by_meta_key($cookie_check['user_key']);

            if(!empty($user_info)){

                $result['success'] = true;
                $result['data'] = array(
                    'wp_id'   => $user_info->ID,
                    'wp_user_key'   => $user_info->moo_user_key,
                    'wp_user_roles'   => wp_json_encode($user_info->roles),
                    'wp_email' => $user_info->user_email,
                    'wp_username' => $user_info->user_login,
                    'wp_firstname'   => $user_info->user_firstname,
                    'wp_lastname'   => $user_info->user_lastname,
                    'wp_display_name'   => $user_info->display_name,
                );

            }else{
                $result['messages'] = __('User not found!', 'moosocial');
            }
        }else{
            $result['messages'] = __('The MooSocial security key is not correct, please try again', 'moosocial');
        }

        /* Restore original Post Data */
        wp_reset_postdata();

        return new WP_REST_Response( $result, 200 );
    }
}