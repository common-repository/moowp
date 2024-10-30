<?php if ( ! defined( 'ABSPATH' ) ) {exit;} ?>
<?php $val = absint(get_option(self::$option_name.'_radio_bool')); ?>
<fieldset>
    <label>
        <input type="radio" name="<?php echo esc_attr(self::$option_name.'_radio_bool') ?>" id="<?php echo esc_attr(self::$option_name.'_radio_bool') ?>" value="true" <?php checked( $val, 'true' ); ?>>
        <?php echo esc_html(__( 'True', 'moowp' )); ?>
    </label>
    <br>
    <label>
        <input type="radio" name="<?php echo esc_attr(self::$option_name.'_radio_bool') ?>" value="false" <?php checked( $val, 'false' ); ?>>
        <?php echo esc_html(__( 'False', 'moowp' )); ?>
    </label>
</fieldset>