<?php
class MooWP_App {
    public $plugin_name = MOOWP_PLUGIN_NAME;
    public static $option_name = 'moowp_setting';
    public $moosocial_is_connecting = 0;
    public $moosocial_address_url = '';
    public $moosocial_re_address_url = '';
    public $moosocial_security_key = '';
    public $moosocial_notification_position = '';
    public $moosocial_chat_plugin_enable = 0;
    public $moosocial_error_flag = 0;
    public $moosocial_recovery_key = '';
    public $moosocial_pages_menu = array(
        /*'#moosociallogin#' => array(
            'title' => __('Moosocial Blogs', 'moowp'),
            'url' => 'http://core317.local.com/blogs',
        ),
        '#moosociallogout#' => array(
            'title' => __('mooSocial Groups', 'moowp'),
            'url' => 'http://core317.local.com/groups',
        ),
        '#moosocialloginout#' => array(
            'title' => __('mooSocial Topics', 'moowp').'|'.__('Log Out', 'moowp'),
            'url' => 'http://core317.local.com/topics',
        )*/
    );
    public $login_moo_as_user_normal = 'user_normal';
    public $login_moo_as_user_admin = 'user_admin';
    public $login_moo_as_root_admin = 'root_admin';

    function __construct() {
        $this->get_recovery_key();
        $this->get_moo_error_flag();
        $this->get_moo_is_mapping();
        $this->get_moo_address_url();
        $this->get_moo_re_address_url();
        $this->get_moo_security_key();
        $this->get_page_menu();
        $this->get_moo_notification_position();
        $this->get_moo_chat_plugin_enable();
    }

    private function get_recovery_key(){
        $this->moosocial_recovery_key = get_option(self::$option_name.'_recovery_key');
    }
    private function get_page_menu(){
        $moo_menu_data = get_option(self::$option_name.'_pages_menu');

        if(!empty($moo_menu_data)){
            if(is_serialized($moo_menu_data)){
                $data = maybe_unserialize($moo_menu_data);

                if($data == null){
                    $this->moosocial_pages_menu = array();
                }else{
                    $this->moosocial_pages_menu = $data;
                }
            }else{
                $this->moosocial_pages_menu = array();
            }
        }else{
            $this->moosocial_pages_menu = array();
        }
    }
    private function get_moo_error_flag(){
        $moosocial_error_flag = absint(get_option(self::$option_name.'_error_flag'));
        $this->moosocial_error_flag = $moosocial_error_flag;
    }
    private function get_moo_is_mapping(){
        $moosocial_is_connecting = absint(get_option(self::$option_name.'_is_connecting'));
        $this->moosocial_is_connecting = $moosocial_is_connecting;
    }
    private function get_moo_address_url(){
        $moosocial_address_url = get_option(self::$option_name.'_address_url');
        if(empty($moosocial_address_url)){
            $this->moosocial_address_url = '';
        }else{
            if(substr($moosocial_address_url, -1) == '/' ){
                $moosocial_address_url = substr($moosocial_address_url, 0, -1);
            }
            $this->moosocial_address_url = $moosocial_address_url;
        }
    }
    private function get_moo_re_address_url(){
        $moosocial_re_address_url = get_option(self::$option_name.'_re_address_url');
        if(empty($moosocial_re_address_url)){
            $this->moosocial_re_address_url = '';
        }else{
            if(substr($moosocial_re_address_url, -1) == '/' ){
                $moosocial_re_address_url = substr($moosocial_re_address_url, 0, -1);
            }
            $this->moosocial_re_address_url = $moosocial_re_address_url;
        }
    }
    private function get_moo_security_key(){
        $this->moosocial_security_key = get_option(self::$option_name.'_security_key');
    }
    private function get_moo_notification_position(){
        $moosocial_notification_position = get_option(self::$option_name.'_notification_position');
        $this->moosocial_notification_position = $moosocial_notification_position;
    }
    private function get_moo_chat_plugin_enable(){
        $moosocial_chat_plugin_enable = absint(get_option(self::$option_name.'_chat_plugin_enable'));
        $this->moosocial_chat_plugin_enable = $moosocial_chat_plugin_enable;
    }

    public static function getRandomSecurityKey($length = 32) {
        $stringSpace = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $pieces = [];
        $max = mb_strlen($stringSpace, '8bit') - 1;
        for ($i = 0; $i < $length; ++ $i) {
            $pieces[] = $stringSpace[random_int(0, $max)];
        }
        return implode('', $pieces);
    }

    public function get_table_prefix() {
        global $wpdb;
        return $wpdb->prefix;
    }

    public function _check_request_permission(){
        $user_ID = get_current_user_id();

        if ($user_ID == 0) {
            header('Cache-Control: no-cache, must-revalidate');
            header('Expires: ' . gmdate('D, d M Y H:i:s \G\M\T', time() + 3600));
            header('Content-type: application/json');

            echo wp_json_encode(array(
                'error_code' => MOOWP_ERROR_NOT_LOGIN,
                'success' => false,
                'messages' => ''
            ));

            exit();
        }
    }

    public function _generate_moo_key($user_id){
        $moo_user_key = get_user_meta( $user_id, 'moo_user_key', true );
        if(empty($moo_user_key)){
            $key = wp_generate_password( 10, false );
            $moo_user_key = md5($user_id) . 'moo' . $key;

            update_user_meta( $user_id, 'moo_user_key', sanitize_text_field( $moo_user_key ) );
        }
        return $moo_user_key;
    }

    public function curl_get($url = '', $value_type = 'json'){
        $response = wp_remote_get( $url );

        if( is_wp_error( $response ) ) {
            return array(
                'success' => false,
                'messages' => 'Request Error',
                'code' => '',
                'data' => array()
            );
        }

        $body = wp_remote_retrieve_body($response);

        if($value_type == 'json'){
            $body = json_decode( $body , true);
        }

        return $body;
    }

    public function curl_post($url = '', $post_data = array(), $value_type = 'json'){
        $args = array(
            'body'        => $post_data,
            'timeout'     => '5',
            'redirection' => '5',
            'httpversion' => '1.0',
            'blocking'    => true,
            'headers'     => array(),
            'cookies'     => array(),
            'sslverify' => false
        );

        $response = wp_remote_post( $url, $args );

        if( is_wp_error( $response ) ) {
            return array(
                'success' => false,
                'messages' => 'Request Error',
                'code' => '',
                'data' => array()
            );
        }

        $body = wp_remote_retrieve_body($response);

        if($value_type == 'json'){
            $body = json_decode( $body , true);
        }

        return $body;
    }

    public function update_connecting_error(){
        if($this->moosocial_error_flag == 0){
            $this->moosocial_error_flag = 1;
            update_option(self::$option_name . '_error_flag', 1);
        }
    }

    public function update_connecting_ok(){
        if($this->moosocial_error_flag == 1){
            $this->moosocial_error_flag = 0;
            update_option(self::$option_name . '_error_flag', 0);
        }
    }

    public function get_user_by_meta_key($meta_value){
        global $wpdb;

        $userID = $wpdb->get_row($wpdb->prepare("SELECT user_id FROM ".$wpdb->usermeta." WHERE (meta_key = 'moo_user_key' AND meta_value = %s)", $meta_value)); // phpcs:ignore WordPress.DB.DirectDatabaseQuery

        if(!empty($userID)){
            return get_user_by('id', (int)$userID->user_id);
        }
        return null;
    }
}
?>