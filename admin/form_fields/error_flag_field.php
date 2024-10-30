<?php if ( ! defined( 'ABSPATH' ) ) {exit;} ?>
<?php $val = absint(get_option(self::$option_name.'_error_flag')); ?>
<?php
    if($val == 1){
        echo esc_html(__( 'Yes', 'moowp' ));
    }else{
        echo esc_html(__( 'No', 'moowp' ));
    }
?>

<!--<select class="regular-text" name="<?php /*echo esc_attr(self::$option_name.'_error_flag') */?>" id="<?php /*echo esc_attr(self::$option_name.'_error_flag') */?>">
    <option value="1" <?php /*if($val == 1): */?>selected<?php /*endif; */?>><?php /*echo esc_html(__( 'Yes', 'moosocial' )); */?></option>
    <option value="0" <?php /*if($val == 0): */?>selected<?php /*endif; */?>><?php /*esc_html(__( 'No', 'moosocial' )); */?></option>
</select>-->