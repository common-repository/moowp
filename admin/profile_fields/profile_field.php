<?php if ( ! defined( 'ABSPATH' ) ) {exit;} ?>
<?php $wp_address_url = get_site_url(); ?>
<h2><?php echo esc_html(__('mooWP', 'moowp')) ?></h2>
<table class="form-table">
    <tr>
        <th><label for="city"><?php echo esc_html(__('Moo User Key', 'moowp')) ?></label></th>
        <td>
            <input type="text" name="moo_user_key" id="moo_user_key" value="<?php echo esc_attr( $moo_user_key ) ?>" class="regular-text" disabled readonly />
        </td>
    </tr>
</table>

<div id="isSync" class="internal-message notice inline notice-warning notice-alt"<?php if(empty($mooUser)): ?> style="display: none;"<?php endif; ?>>
    <p style="color: #00a32a">
        <b><?php echo esc_html(__('User have been synced', 'moowp')) ?></b>
        <?php if($isAdminWP === 'xxx'): ?>
        <button type="button" class="button button-secondary" id="admin-link"><?php echo esc_html(__('Access To Community Admin Panel', 'moowp')) ?></button>
        <?php endif; ?>
    </p>
</div>
<?php if(empty($mooUser)): ?>
<div id="notSync" class="error-message notice inline notice-warning notice-alt">
    <p id="notSyncError">
        <?php echo esc_html(__('Users are not synchronized', 'moowp')) ?>,
        <button type="button" class="button button-secondary" id="sync-link"><?php echo esc_html(__('Sync now', 'moowp')) ?></button>
    </p>
</div>

<script type="text/javascript">
    jQuery(document).ready(function (){
        jQuery('#sync-link').click(function (e){
            e.preventDefault();
            if(!jQuery(this).hasClass('disabled')){
                jQuery(this).addClass('disabled');

                var url_sync_user = '<?php echo esc_js($wp_address_url.'/wp-json/'.MOOWP_APP_NAMESPACE.'/user/sync_user/'.$moo_user_key) ?>';

                jQuery.ajax({
                    type: "GET",
                    url: url_sync_user,
                    dataType:"json",
                    success: function(response){
                        if(response.success == true){
                            jQuery('#isSync').show();
                            jQuery('#notSync').hide();
                        }else{
                            jQuery('#isSync').hide();
                            jQuery('#notSync').show();

                            jQuery('#notSyncError').empty().html(response.messages);
                        }
                    }
                });
            }
        });
    });
</script>
<?php endif; ?>

<?php if($isAdminWP === 'xxx'): ?>
    <script type="text/javascript">
        jQuery(document).ready(function (){
            jQuery('#admin-link').click(function (e){
                e.preventDefault();
                if(!jQuery(this).hasClass('disabled')){
                    var url_sync_user = '<?php echo esc_js($wp_address_url.'/wp-json/'.MOOWP_APP_NAMESPACE.'/user/admin_login_verification') ?>';

                    jQuery.ajax({
                        type: "POST",
                        url: url_sync_user,
                        dataType:"json",
                        data: {
                            user_id: <?php echo esc_js($current_user->ID) ?>,
                            moo_login_as: '<?php echo esc_js($this->login_moo_as_user_admin) ?>',
                        },
                        beforeSend: function( xhr ) {
                            jQuery('#admin-link').addClass('disabled');
                        },
                        success: function(response){
                            if(response.success == true){
                                window.location.href = "<?php echo esc_js($this->moosocial_address_url.'/'.MOOWP_CORE_NAMESPACE.'/panel_admin/') ?>" + response.data.admin_login_token;
                            }else{
                                jQuery('#admin-link').removeClass('disabled');
                            }
                        }
                    });
                }
            });
        });
    </script>
<?php endif; ?>
