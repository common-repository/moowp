<?php if ( ! defined( 'ABSPATH' ) ) {exit;} ?>
<div class="wrap">
    <h2><?php echo esc_html(__( 'mooWP Plugin', 'moowp' )); ?></h2>
    <form action="options.php" method="post">
        <?php
        settings_errors();
        settings_fields( MOOWP_PLUGIN_NAME );
        do_settings_sections( MOOWP_PLUGIN_NAME );
        if($show_button_submit){
            submit_button();
        }
        ?>
    </form>
</div>