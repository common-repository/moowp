<?php
class MooWP_Activate extends MooWP_App {
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
        register_activation_hook( __FILE__, array( $this, 'moowp_plugin_activate' ) );
        add_action('admin_init', array($this, 'load_plugin'));
    }

    public static function moowp_plugin_activate( $network_wide ) {
        // add an option to do something later, if there is one.
        add_option( 'MOOWP_Activated', 'moowp' );
        add_option( self::$option_name . '_security_key', '' );
        add_option( self::$option_name . '_error_flag', false );
        add_option( self::$option_name . '_is_connecting', false );
        add_option( self::$option_name . '_pages_menu', false );
        add_option( self::$option_name . '_recovery_key', '' );
        add_option( self::$option_name . '_re_address_url', '' );
        add_option( self::$option_name . '_address_url', '' );

        // Here the activation code ...
        $current_user = wp_get_current_user();
        $isAdminWP = in_array('administrator', $current_user->roles, true);

        if($isAdminWP) {
            update_option(self::$option_name . '_user_map_root', $current_user->ID);
            update_option(self::$option_name . '_security_key', self::getRandomSecurityKey());
        }
    }

    public function load_plugin() {
        //update_option(self::$option_name . '_user_map_root', 1);
        //update_option(self::$option_name . '_security_key', self::getRandomSecurityKey());
        //update_option(self::$option_name . '_address_url', '');
        //update_option(self::$option_name . '_is_connecting', 0);
        //update_option(self::$option_name . '_error_flag', false);
        //update_option(self::$option_name . '_pages_menu', '');
        //update_option(self::$option_name . '_recovery_key', '');
        if ( is_admin() && get_option( 'MOOWP_Activated' ) == 'moowp' ) {
            // delete the added option so that it is no longer triggered
            // and do what needs to be done...
            delete_option( 'MOOWP_Activated' );

            // Do something once, after activating the plugin
            // For example: add_action('init', 'my_init_function' );
        }else{
            $user_map_root = absint(get_option(self::$option_name.'_user_map_root'));
            if(empty($user_map_root)){
                $current_user = wp_get_current_user();
                if(in_array('administrator', $current_user->roles)){
                    update_option(self::$option_name . '_user_map_root', $current_user->ID);
                }
            }

        }

    }
}
?>