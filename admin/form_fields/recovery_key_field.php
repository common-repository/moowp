<?php if ( ! defined( 'ABSPATH' ) ) {exit;} ?>
<?php $val = get_option(self::$option_name.'_recovery_key'); ?>
<?php echo esc_html($val); ?>