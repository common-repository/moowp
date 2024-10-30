<?php if ( ! defined( 'ABSPATH' ) ) {exit;} ?>
<?php
    if( isset($showInHeader) && $showInHeader === true ){
        $position = 'top-header';
    }else{
        $position = empty($this->moosocial_notification_position) ? 'left' : $this->moosocial_notification_position;
    }
?>

<div id="mooPopup" class="moo-popup moo-<?php echo esc_attr($position) ?>">
    <div class="moo-popup-main">
        <div class="moo-popup-body">
            <div class="moo-popup-content">
                <div class="moo-popup-tabs">
                    <div id="mooTabMessages" class="moo-popup-tab-item active"></div>
                    <div id="mooTabNotifications" class="moo-popup-tab-item"></div>
                    <!--<div id="mooTabSettings" class="moo-popup-tab-item">SETTING</div>-->
                </div>
                <div class="moo-popup-loadings">
                    <div class="moo-popup-lds-ripple"><div></div><div></div></div>
                </div>
                <div id="moo-popup-close" class="moo-popup-close">
                    <svg class="moo-popup-close-img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 48 48">
                        <path class="moo-popup-close-fill" fill="#00ffffff" d="m16.5 33.6 7.5-7.5 7.5 7.5 2.1-2.1-7.5-7.5 7.5-7.5-2.1-2.1-7.5 7.5-7.5-7.5-2.1 2.1 7.5 7.5-7.5 7.5ZM24 44q-4.1 0-7.75-1.575-3.65-1.575-6.375-4.3-2.725-2.725-4.3-6.375Q4 28.1 4 24q0-4.15 1.575-7.8 1.575-3.65 4.3-6.35 2.725-2.7 6.375-4.275Q19.9 4 24 4q4.15 0 7.8 1.575 3.65 1.575 6.35 4.275 2.7 2.7 4.275 6.35Q44 19.85 44 24q0 4.1-1.575 7.75-1.575 3.65-4.275 6.375t-6.35 4.3Q28.15 44 24 44Zm0-3q7.1 0 12.05-4.975Q41 31.05 41 24q0-7.1-4.95-12.05Q31.1 7 24 7q-7.05 0-12.025 4.95Q7 16.9 7 24q0 7.05 4.975 12.025Q16.95 41 24 41Zm0-17Z"/>
                    </svg>
                </div>
            </div>
        </div>
        <div class="moo-action-bar ">
            <div class="moo-action-item moo-action-notifications">
                <a id="moo-action-notifications" class="moo-action-btn moo-action-icon" data-tab="mooTabNotifications" data-ajax="1" data-active="0" href="javascript:void(0);">
                    <?php if( isset($showInHeader) && $showInHeader === true ): ?>
                        <span class="moo-group-icon material-symbols-outlined notranslate notranslate moo-icon">notifications</span>
                    <?php else: ?>
                        <svg class="moo-action-icon-img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 48 48">
                            <path class="moo-action-icon-fill" fill="#333333" d="M8 38v-3h4.2V19.7q0-4.2 2.475-7.475Q17.15 8.95 21.2 8.1V6.65q0-1.15.825-1.9T24 4q1.15 0 1.975.75.825.75.825 1.9V8.1q4.05.85 6.55 4.125t2.5 7.475V35H40v3Zm16-14.75ZM24 44q-1.6 0-2.8-1.175Q20 41.65 20 40h8q0 1.65-1.175 2.825Q25.65 44 24 44Zm-8.8-9h17.65V19.7q0-3.7-2.55-6.3-2.55-2.6-6.25-2.6t-6.275 2.6Q15.2 16 15.2 19.7Z"/>
                        </svg>
                    <?php endif; ?>
                    <span id="moo-notifications-count" class="moo-action-count"></span>
                </a>
            </div>
            <?php if($this->moosocial_chat_plugin_enable == 1): ?>
            <div class="moo-action-item moo-action-messages">
                <a id="moo-action-messages" class="moo-action-btnX moo-action-icon" href="<?php echo esc_attr($this->moosocial_address_url) ?>">
                    <?php if( isset($showInHeader) && $showInHeader === true ): ?>
                        <span class="moo-group-icon material-symbols-outlined notranslate notranslate moo-icon">chat_bubble_outline</span>
                    <?php else: ?>
                        <svg class="moo-action-icon-img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 48 48">
                            <path class="moo-action-icon-fill" fill="#333333" d="M4 44V7q0-1.15.9-2.075Q5.8 4 7 4h34q1.15 0 2.075.925Q44 5.85 44 7v26q0 1.15-.925 2.075Q42.15 36 41 36H12Zm3-7.25L10.75 33H41V7H7ZM7 7v29.75Z"/>
                        </svg>
                    <?php endif; ?>
                    <span id="moo-messages-count" class="moo-action-count"></span>
                </a>
            </div>
            <?php else: ?>
            <div class="moo-action-item moo-action-messages">
                <a id="moo-action-messages" class="moo-action-btn moo-action-icon" data-tab="mooTabMessages" data-ajax="1" data-active="1" href="javascript:void(0);">
                    <?php if( isset($showInHeader) && $showInHeader === true ): ?>
                        <span class="moo-group-icon material-symbols-outlined notranslate notranslate moo-icon">chat_bubble_outline</span>
                    <?php else: ?>
                        <svg class="moo-action-icon-img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 48 48">
                            <path class="moo-action-icon-fill" fill="#333333" d="M4 44V7q0-1.15.9-2.075Q5.8 4 7 4h34q1.15 0 2.075.925Q44 5.85 44 7v26q0 1.15-.925 2.075Q42.15 36 41 36H12Zm3-7.25L10.75 33H41V7H7ZM7 7v29.75Z"/>
                        </svg>
                    <?php endif; ?>
                    <span id="moo-messages-count" class="moo-action-count"></span>
                </a>
            </div>
            <?php endif; ?>
            <!--<div class="moo-action-item moo-action-settings mooHasNotify">
                <a id="moo-action-settings" class="moo-action-btn moo-action-icon" data-tab="mooTabSettings" data-ajax="0" data-active="0" href="javascript:void(0);">
                    <svg class="moo-action-icon-img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 48 48">
                        <path class="moo-action-icon-fill" fill="#333333" d="m19.4 44-1-6.3q-.95-.35-2-.95t-1.85-1.25l-5.9 2.7L4 30l5.4-3.95q-.1-.45-.125-1.025Q9.25 24.45 9.25 24q0-.45.025-1.025T9.4 21.95L4 18l4.65-8.2 5.9 2.7q.8-.65 1.85-1.25t2-.9l1-6.35h9.2l1 6.3q.95.35 2.025.925Q32.7 11.8 33.45 12.5l5.9-2.7L44 18l-5.4 3.85q.1.5.125 1.075.025.575.025 1.075t-.025 1.05q-.025.55-.125 1.05L44 30l-4.65 8.2-5.9-2.7q-.8.65-1.825 1.275-1.025.625-2.025.925l-1 6.3ZM24 30.5q2.7 0 4.6-1.9 1.9-1.9 1.9-4.6 0-2.7-1.9-4.6-1.9-1.9-4.6-1.9-2.7 0-4.6 1.9-1.9 1.9-1.9 4.6 0 2.7 1.9 4.6 1.9 1.9 4.6 1.9Zm0-3q-1.45 0-2.475-1.025Q20.5 25.45 20.5 24q0-1.45 1.025-2.475Q22.55 20.5 24 20.5q1.45 0 2.475 1.025Q27.5 22.55 27.5 24q0 1.45-1.025 2.475Q25.45 27.5 24 27.5Zm0-3.5Zm-2.2 17h4.4l.7-5.6q1.65-.4 3.125-1.25T32.7 32.1l5.3 2.3 2-3.6-4.7-3.45q.2-.85.325-1.675.125-.825.125-1.675 0-.85-.1-1.675-.1-.825-.35-1.675L40 17.2l-2-3.6-5.3 2.3q-1.15-1.3-2.6-2.175-1.45-.875-3.2-1.125L26.2 7h-4.4l-.7 5.6q-1.7.35-3.175 1.2-1.475.85-2.625 2.1L10 13.6l-2 3.6 4.7 3.45q-.2.85-.325 1.675-.125.825-.125 1.675 0 .85.125 1.675.125.825.325 1.675L8 30.8l2 3.6 5.3-2.3q1.2 1.2 2.675 2.05Q19.45 35 21.1 35.4Z"/>
                    </svg>
                    <span class="moo-action-count">9999</span>
                </a>
            </div>-->
        </div>
    </div>
</div>
<div id="mooPopupOverview" class="moo-popup-overview moo-<?php echo esc_attr($position) ?>"></div>
