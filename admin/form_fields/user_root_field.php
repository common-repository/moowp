<?php if ( ! defined( 'ABSPATH' ) ) {exit;} ?>
<?php $val = get_option(self::$option_name.'_user_root'); ?>
<input type="text" class="regular-text" name="<?php echo esc_attr(self::$option_name.'_user_root') ?>" id="<?php echo esc_attr($this->option_name.'_user_root') ?>" value="<?php echo absint($val) ?>">