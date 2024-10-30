<?php
class MooWP_View extends MooWP_App {
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
        $current_user_id = get_current_user_id();
        if($current_user_id > 0){
            add_action( 'wp_enqueue_scripts', array($this, 'load_resources'));

            $show_in_header = apply_filters('theme_notification_show_in_header', false);

            if($show_in_header){
                add_action( 'moowp_notification_html', array( $this, 'html_notification' ), 10, 1 );
            }else{
                add_action('wp_body_open', array($this, 'html_notification'));
            }
        }
    }

    public function load_resources() {
        global $hook_suffix;

        wp_register_style( 'moowp-style', MOOWP_PLUGIN_URL . 'public/assets/css/moosocial.css', array(), MOOWP_VERSION );
        wp_enqueue_style( 'moowp-style');

        wp_register_script( 'moosocial-script', MOOWP_PLUGIN_URL . 'public/assets/js/moosocial.js', array('jquery'), MOOWP_VERSION, false );
        wp_enqueue_script( 'moosocial-script' );

        $wp_address_url = get_site_url();

        $current_user = wp_get_current_user();

        //add option config to js
        $inline_js = array(
            'MOOWP_APP_NAMESPACE' => MOOWP_APP_NAMESPACE,
            'moosocial_address_url'=> $this->moosocial_address_url,
            'moosocial_notification_position'=> $this->moosocial_notification_position,
            'wp_address_url'=> $wp_address_url,
            'wp_current_user' => array(
                'id' => $current_user->ID,
                'moo_user_key' => $current_user->moo_user_key
            ),
        );

        wp_localize_script( 'moosocial-script', 'WP_MOOSOCIAL_OPTION', $inline_js );
    }

    public function html_notification($showInHeader){
        include MOOWP_PLUGIN_DIR . 'views/widget-html-notification.php';
    }
}
?>