<?php if ( ! defined( 'ABSPATH' ) ) {exit;} ?>
<?php $val = get_option(self::$option_name.'_notification_position'); ?>
<select class="regular-text" name="<?php echo esc_attr(self::$option_name.'_notification_position') ?>" id="<?php echo esc_attr(self::$option_name.'_notification_position') ?>">
    <option value="bottom" <?php if($val == 'bottom'): ?>selected<?php endif; ?>><?php echo esc_html(__( 'Bottom', 'moowp' )); ?></option>
    <option value="left" <?php if($val == 'left'): ?>selected<?php endif; ?>><?php echo esc_html(__( 'Left', 'moowp' )); ?></option>
    <option value="right" <?php if($val == 'right'): ?>selected<?php endif; ?>><?php echo esc_html(__( 'Right', 'moowp' )); ?></option>
</select>