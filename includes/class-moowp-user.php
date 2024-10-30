<?php
class MooWP_User extends MooWP_App {
    private $initiated = false;

    public function init() {
        if ( ! $this->initiated ) {
            add_action( 'init', array( $this, 'init_hooks' ) );
        }
    }

    /**
     * Initializes WordPress hooks
     */
    public function init_hooks() {
        $this->initiated = true;

        add_filter('auth_cookie_expiration', array($this, 'wpdev_login_session') );

        add_action('show_user_profile',  array($this, 'render_profile_fields'));
        add_action('edit_user_profile', array($this, 'render_profile_fields'));
        add_action('personal_options_update', array($this, 'save_profile_fields') );
        add_action('edit_user_profile_update', array($this, 'save_profile_fields') );
        add_action('delete_user', array($this, 'before_delete_user') );
        add_action('profile_update', array($this, 'check_user_email_updated'), 10, 3 );
        add_action('wp_login', array($this, 'after_login_user'), 10, 2);

        add_filter('user_register', array($this, 'after_register_new_user' ));

        add_filter('wp_logout', array($this, 'after_logout_user'));
        //add_filter('auth_cookie', array($this, 'auth_cookie_login'), 10, 5);
        add_filter('user_row_actions', array($this, 'user_row_actions'), 1, 2);
        add_action('delete_user', array($this, 'root_account_check'));

        add_action('check_admin_referer', array($this, 'logout_without_confirm'), 10, 2);
        add_action('admin_init', array($this, 'ajax_log_out_everywhere'));
    }

    public function wpdev_login_session( $expire ) { // Set login session limit in seconds
        //return YEAR_IN_SECONDS MONTH_IN_SECONDS HOUR_IN_SECONDS DAY_IN_SECONDS;
        $cookie_expire = absint(get_option(self::$option_name.'_cookie_expire'));

        if(empty($cookie_expire)){
            $cookie_expire = DAY_IN_SECONDS;
        }elseif($cookie_expire == 0){
            $cookie_expire = DAY_IN_SECONDS;
        }else{
            $cookie_expire = $cookie_expire*60;
        }

        return $cookie_expire;
    }

    public function render_profile_fields( $user ) {
        $moo_user_key = $this->_generate_moo_key($user->ID);

        $url = $this->moosocial_address_url.'/'.MOOWP_CORE_API_NAMESPACE. '/get_user';
        $post = [
            'security_key' => $this->moosocial_security_key,
            'id'   => $user->ID,
            //'user_key'   => $user->moo_user_key,
            'user_key'   => $moo_user_key,
            'email' => $user->user_email,
            'username' => $user->user_login,
            'firstname' => $user->user_firstname,
            'lastname' => $user->user_lastname
        ];

        $result_data = $this->curl_post($url, $post, 'json');

        $mooUser = null;
        if($result_data['success'] == true){
            $mooUser = $result_data['data'];
        }
        $current_user = wp_get_current_user();
        $isSuperAdminInMoo = (!empty($mooUser) && $mooUser['role_id'] === 1);
        $isAdminWP = in_array('administrator', $user->roles, true);

        include MOOWP_PLUGIN_DIR . 'admin/profile_fields/profile_field.php';
    }

    public function save_profile_fields( $user_id ) {

        if( ! isset( $_POST[ '_wpnonce' ] ) || ! wp_verify_nonce( sanitize_text_field(wp_unslash($_POST[ '_wpnonce' ])), 'update-user_' . $user_id ) ) {
            return;
        }

        if( ! current_user_can( 'edit_user', $user_id ) ) {
            return;
        }

        $this->_generate_moo_key($user_id);
    }

    public function check_user_email_updated( $user_id, $old_data ) {
        $new_data = get_userdata( $user_id );

        $old_user_email = $old_data->user_email;
        $new_user_email = $new_data->user_email;

        if ( $new_user_email !== $old_user_email ) {
            $moo_user_key = $this->_generate_moo_key($user_id);
            $url = $this->moosocial_address_url.'/'.MOOWP_CORE_API_NAMESPACE.'/email_updated';
            $post = [
                'security_key' => $this->moosocial_security_key,
                'id'   => $user_id,
                'user_key' => $moo_user_key,
                'new_email' => $new_user_email,
                'old_email' => $old_user_email,
                'username' => $new_data->user_login,
                'firstname'   => $new_data->user_firstname,
                'lastname'   => $new_data->user_lastname
            ];
            $this->curl_post($url, $post, 'json');
        }
    }

    public function after_register_new_user($user_id) {
        $user_info = get_userdata( $user_id );
        $moo_user_key = $this->_generate_moo_key($user_info->ID);
        $url = $this->moosocial_address_url.'/'.MOOWP_CORE_API_NAMESPACE.'/create_user';
        $post = [
            'id'   => $user_info->ID,
            'user_key' => $moo_user_key,
            'email' => $user_info->user_email,
            'username' => $user_info->user_login,
            'firstname'   => $user_info->user_firstname,
            'lastname'   => $user_info->user_lastname
        ];
        $this->curl_post($url, $post, 'json');
    }

    public function after_login_user($user_login, WP_User $user){
        $url = $this->moosocial_address_url.'/'.MOOWP_CORE_API_NAMESPACE.'/clear_destroy_sessions';
        $moo_user_key = $this->_generate_moo_key($user->ID);

        $expiration = time() + DAY_IN_SECONDS;
        if ( COOKIEPATH != SITECOOKIEPATH ) {
            setcookie(COOKIE_WPMOO_KEY, $moo_user_key, $expiration, SITECOOKIEPATH, COOKIE_DOMAIN, is_ssl());
        }else{
            setcookie(COOKIE_WPMOO_KEY, $moo_user_key, $expiration, COOKIEPATH, COOKIE_DOMAIN, is_ssl());
        }

        $post_data = array(
            'user_id' => $user->ID,
            'user_key' => $moo_user_key,
            'security_key' => $this->moosocial_security_key
        );
        $this->curl_post($url, $post_data,'json');
    }

    public function after_logout_user($user_id){
        $expiration = time() - MONTH_IN_SECONDS;
        $moo_user_key = $this->_generate_moo_key($user_id);
        $secure = ( 'https' === wp_parse_url( wp_login_url(), PHP_URL_SCHEME ) );
        if ( COOKIEPATH != SITECOOKIEPATH ) {
            setcookie(COOKIE_WPMOO_KEY, $moo_user_key, $expiration, SITECOOKIEPATH, COOKIE_DOMAIN, $secure);
        }else{
            setcookie(COOKIE_WPMOO_KEY, $moo_user_key, $expiration, COOKIEPATH, COOKIE_DOMAIN, $secure);
        }
    }

    public function before_delete_user( $user_id ) {
        $user_info = get_userdata( $user_id );
        $moo_user_key = $this->_generate_moo_key($user_info->ID);
        $url = $this->moosocial_address_url.'/'.MOOWP_CORE_API_NAMESPACE.'/delete_user';
        $post = [
            'security_key' => $this->moosocial_security_key,
            'id'   => $user_info->ID,
            'user_key' => $moo_user_key,
            'email' => $user_info->user_email,
            'username' => $user_info->user_login,
            'firstname'   => $user_info->user_firstname,
            'lastname'   => $user_info->user_lastname
        ];
        $this->curl_post($url, $post, 'json');
    }

    public function auth_cookie_login($cookie, $user_id, $expiration, $scheme, $token){
        if($scheme == 'logged_in'){
            $moo_user_key = $this->_generate_moo_key($user_id);
            $cookie .= '|'.$moo_user_key;
        }
        return $cookie;
    }

    public function user_row_actions($actions, $user_object){
        $user_map_root_id = absint(get_option(self::$option_name.'_user_map_root'));

        if ( $user_map_root_id == $user_object->ID ){
            unset($actions['delete']);
        }
        return $actions;
    }

    public function root_account_check( $user_id ) {
        $user_map_root_id = absint(get_option(self::$option_name.'_user_map_root'));

        if ( $user_map_root_id == $user_id ){
            wp_die("User has a root account and can't be deleted");
        }
    }

    public function logout_without_confirm($action, $result)
    {
        /**
         * Allow logout without confirmation
         */
        // phpcs:disable WordPress.Security.NonceVerification.Recommended
        if ($action == "log-out" && !isset($_GET['_wpnonce'])) {
            $redirect_to = isset($_REQUEST['redirect_to']) ? sanitize_url($_REQUEST['redirect_to']) : home_url();
            $location = str_replace('&amp;', '&', wp_logout_url($redirect_to));
            header("Location: $location");
            die;
        }
        // phpcs:enable
    }

    /*
     * Click Button Log Out Everywhere
     * */
    public function ajax_log_out_everywhere(){
        // phpcs:disable WordPress.Security.NonceVerification.Missing
        $action = (isset($_POST['action'])) ? sanitize_text_field($_POST['action']) : '';
        $user_id = (isset($_POST['user_id'])) ? absint($_POST['user_id']) : '';
        // phpcs:enable
        if(!empty($action) && !empty($user_id) && $action == 'destroy-sessions' ){
            $url = $this->moosocial_address_url.'/'.MOOWP_CORE_API_NAMESPACE.'/add_destroy_sessions';
            $moo_user_key = $this->_generate_moo_key($user_id);

            $post_data = array(
                'user_id' => $user_id,
                'user_key' => $moo_user_key,
                'security_key' => $this->moosocial_security_key
            );
            $this->curl_post($url, $post_data,'json');
        }
    }
}
?>