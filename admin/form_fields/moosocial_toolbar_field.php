<?php if ( ! defined( 'ABSPATH' ) ) {exit;} ?>
<?php $wp_address_url = get_site_url(); ?>

<?php if($this->moosocial_is_connecting == 0): ?>
    <div class="error-message notice inline notice-warning notice-alt">
        <p>
            <?php echo esc_html(__('Please setup mooSocial Site', 'moowp')); ?>
        </p>
    </div>
    <a id="resetNewSite" href="<?php echo esc_attr(admin_url( "admin.php?page=moo-reconnect" )) ?>" style="text-decoration: none; color: #000000;"><?php echo esc_html(__( 'If you already have a moosocial website, click here to setup it.', 'moowp' )); ?></a>
<?php else: ?>

    <?php if($error_flag): ?>
        <div class="error-message notice inline notice-warning notice-alt">
            <p>
                <?php echo esc_html($message_error); ?>
                <?php if($is_update_community_security_key): ?>
                    <a href="<?php echo esc_url($this->moosocial_address_url.'/'.MOOWP_CORE_NAMESPACE.'/recovery_security_key?sec='.$this->moosocial_security_key.'&url='.$wp_address_url) ?>" class="button button-secondary" id="update-admin-security"><?php echo esc_html(__('Update Security Key in mooSocial site', 'moowp')) ?></a>
                <?php endif; ?>
                <?php if($is_confirm_update_community_security_key): ?>
                    <a href="javascript:void(0);" class="button button-secondary" id="confirm-admin-security"><?php echo esc_html(__('Confirm update Security Key in mooSocial site', 'moowp')) ?></a>
                    <script type="text/javascript">
                        jQuery(document).ready(function (){
                            jQuery('#confirm-admin-security').click(function (e){
                                e.preventDefault();
                                if(!jQuery(this).hasClass('disabled')){
                                    var url_recover = '<?php echo esc_js(esc_url($wp_address_url.'/wp-json/'.MOOWP_APP_NAMESPACE.'/security/admin_confirm_update_security_key')) ?>';
                                    jQuery.ajax({
                                        type: "POST",
                                        url: url_recover,
                                        dataType:"json",
                                        data: {
                                            security_key: '<?php echo esc_js($this->moosocial_security_key) ?>',
                                            recovery_key: '<?php echo esc_js($this->moosocial_recovery_key) ?>',
                                        },
                                        beforeSend: function( xhr ) {
                                            jQuery('#confirm-admin-security').addClass('disabled');
                                        },
                                        success: function(response){
                                            if(response.success == true){
                                                location.href = window.location.href;
                                            }else{
                                                jQuery('#confirm-admin-security').removeClass('disabled');
                                            }
                                        }
                                    });
                                }
                            });
                        });
                    </script>
                <?php endif; ?>
            </p>
        </div>
    <?php else: ?>
        <div class="internal-message notice inline notice-warning notice-alt">
            <p style="color: #00a32a">
                <b><?php echo esc_html($message_ok); ?></b>
                <?php if($is_access_community_panel): ?>
                    <button type="button" class="button button-secondary" id="root-admin-link"><?php echo esc_html(__('Access To Community Admin Panel', 'moowp')) ?></button>
                    <script type="text/javascript">
                        jQuery(document).ready(function (){
                            jQuery('#root-admin-link').click(function (e){
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
                                            jQuery('#root-admin-link').addClass('disabled');
                                        },
                                        success: function(response){
                                            if(response.success == true){
                                                window.location.href = "<?php echo esc_js($this->moosocial_address_url.'/'.MOOWP_CORE_NAMESPACE.'/go-to-admin-panel/'.$current_user->moo_user_key.'/') ?>" + response.data.admin_login_token;
                                            }else{
                                                jQuery('#root-admin-link').removeClass('disabled');
                                            }
                                        }
                                    });
                                }
                            });
                        });
                    </script>
                <?php endif; ?>
            </p>
        </div>
    <?php endif; ?>

    <p>
        <a id="resetNewSite" href="javascript:void(0);" style="text-decoration: none; color: red;"><?php echo esc_html(__( 'If you want to set up a new moosocial website, please click here.', 'moowp' )); ?></a>
    </p>
    <script type="text/javascript">
        jQuery(document).ready(function (){
            jQuery('#resetNewSite').click(function (e){
                e.preventDefault();

                if (window.confirm("<?php echo esc_js(__( 'Are you sure reset to install new moosocial site?', 'moowp' )) ?>")) {
                    if(!jQuery(this).hasClass('disabled')){
                        var url_recover = '<?php echo esc_js($wp_address_url.'/wp-json/'.MOOWP_APP_NAMESPACE.'/reset/new_setup') ?>';
                        jQuery.ajax({
                            type: "POST",
                            url: url_recover,
                            dataType:"json",
                            data: {
                                security_key: '<?php echo esc_js($this->moosocial_security_key) ?>',
                                user_id: <?php echo esc_js($current_user->ID) ?>
                            },
                            beforeSend: function( xhr ) {
                                jQuery('#confirm-admin-security').addClass('disabled');
                            },
                            success: function(response){
                                if(response.success == true){
                                    location.href = window.location.href;
                                }else{
                                    jQuery('#resetNewSite').removeClass('disabled');
                                }
                            }
                        });
                    }
                }
            });
        });
    </script>
<?php endif; ?>