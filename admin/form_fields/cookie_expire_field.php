<?php if ( ! defined( 'ABSPATH' ) ) {exit;} ?>
<?php
    $val = get_option(self::$option_name.'_cookie_expire');
    if($val <= 0){
        $val = 60*12*7;
    }
?>
<input type="text" class="regular-text" name="<?php echo esc_attr(self::$option_name.'_cookie_expire') ?>" id="<?php echo esc_attr(self::$option_name.'_cookie_expire') ?>" value="<?php echo absint($val) ?>">
<p class="description" id="home-description">
    <?php echo esc_html(__( 'Set the cookie expiration time in minutes. e.g: 10', 'moowp' )) ?>
</p>