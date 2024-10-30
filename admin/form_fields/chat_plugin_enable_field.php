<?php if ( ! defined( 'ABSPATH' ) ) {exit;} ?>
<?php $val = absint(get_option(self::$option_name.'_chat_plugin_enable')); ?>
<select class="regular-text" name="<?php echo esc_attr(self::$option_name.'_chat_plugin_enable') ?>" id="<?php echo esc_attr(self::$option_name.'_chat_plugin_enable') ?>">
    <option value="1" <?php if($val == 1): ?>selected<?php endif; ?>><?php echo esc_html(__( 'Yes', 'moowp' )); ?></option>
    <option value="0" <?php if($val == 0): ?>selected<?php endif; ?>><?php echo esc_html(__( 'No', 'moowp' )); ?></option>
</select>