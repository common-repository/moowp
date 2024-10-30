(function ( $ ) {
	'use strict';
	$(function () {
		var mooOption = WP_MOOSOCIAL_OPTION;
		var storeTimeInterval = null;
		//console.log(mooOption);

		var MooPopup = {
			initTabs: function (){
				$('.moo-action-btn').click(function (e){
					e.preventDefault();

					let eleParent = $(this).parent();

					if(!eleParent.hasClass('active')){
						let tab_id = $(this).attr('data-tab');

						$('.moo-action-item.active').removeClass('active');
						$('.moo-popup-tab-item.active').removeClass('active');
						$('.moo-action-btn[data-active="1"]').attr('data-active', 0);

						eleParent.addClass('active');
						$('#'+tab_id).addClass('active');
						$(this).attr('data-active', 1);

						if( $(this).attr('data-ajax') == 1 ){
							MooPopup.load_tab_content();
						}
						MooPopup.open();
					}else {
						MooPopup.close();
					}
				});
			},
			open: function (){
				$('#mooPopup').addClass('moo-popup-open');
				$('body').addClass('moo-popup-active');
			},
			close: function (){
				$('#mooPopup').removeClass('moo-popup-open');
				$('body').removeClass('moo-popup-active');

				$('.moo-action-item.active').removeClass('active');
				$('.moo-popup-tab-item.active').removeClass('active');
				$('.moo-action-btn[data-active="1"]').attr('data-active', 0);
			},
			toogle: function (){
				if($('#mooPopup').hasClass('moo-popup-open')){
					MooPopup.close();
				}else {
					MooPopup.open();
				}
			},
			load_tab_content: function (){
				let tabId = $('.moo-action-btn[data-active="1"]').attr('data-tab');

				let url_param = '';
				if(tabId == 'mooTabNotifications'){
					url_param = '/wp-json/'+mooOption.MOOWP_APP_NAMESPACE+'/notifications/all';
				}else if(tabId == 'mooTabMessages'){
					url_param = '/wp-json/'+mooOption.MOOWP_APP_NAMESPACE+'/conversations/all';
				}

				if(url_param ==''){
					return;
				}

				$.ajax({
					url: mooOption.wp_address_url + url_param,
					beforeSend: function( xhr ) {
						MooPopup.loading_start();
					},
					type: 'POST',
					dataType: 'json',
					data: {
						user_id: mooOption.wp_current_user.id,
					}
				}).done(function(result) {
					if(result.success == true){
						$('#'+tabId).html(result.data);
						MooPopup.init_notification_action();
					}

					MooPopup.loading_end();
				});
			},
			loading_start: function (){
				$('#mooPopup').addClass('moo-is-loading');
			},
			loading_end: function (){
				$('#mooPopup').removeClass('moo-is-loading');
			},
			check_notification_count: function (){
				let url_param = '/wp-json/'+mooOption.MOOWP_APP_NAMESPACE+'/notifications/refresh';

				$.ajax({
					url: mooOption.wp_address_url + url_param,
					/*beforeSend: function( xhr ) {
                        MooPopup.loading_start();
                    },*/
					type: 'POST',
					dataType: 'json',
					data: {
						user_id: mooOption.wp_current_user.id,
                    }
				}).done(function(result) {
					if(result.success == true){
						// update notification count for topbar menu
						if (parseInt(result.data.notification_count) > 0){
							$('#moo-action-notifications').parent().addClass('mooHasNotify');
							$('#moo-notifications-count').html(result.data.notification_count);
						}else{
							$('#moo-action-notifications').parent().removeClass('mooHasNotify');
							$('#moo-notifications-count').empty();

							$(document).find(".notification_item").each(function () {
								if($(this).find('.unread').length > 0){
									$(this).find('.unread').removeClass('unread');
									$(this).find('.mark_read').hide();
									$(this).find('.mark_unread').show();
								}
							});
						}

						// update conversation count
						if (parseInt(result.data.conversation_count) > 0){
							$('#moo-action-messages').parent().addClass('mooHasNotify');
							$('#moo-messages-count').html(result.data.conversation_count);
						}else{
							$('#moo-action-messages').parent().removeClass('mooHasNotify');
							$('#moo-messages-count').empty();
						}
						// MooPopup.loading_end();
					}else {
						if(result.code == 100){
							clearInterval(storeTimeInterval);
							storeTimeInterval = null;
						}
					}
				});
			},
			init_refresh_notification: function(){
				if(storeTimeInterval == null){
					storeTimeInterval = setInterval(function(){
						MooPopup.check_notification_count();
					}, 10000);
				}
			},
			init_notification_action: function (){
				$('#mooPopup').find('#markAllNotificationAsRead').unbind('click').click(function (e){
					e.preventDefault();

					let url_param = '/wp-json/'+mooOption.MOOWP_APP_NAMESPACE+'/notifications/mark_all_read';

					$.ajax({
						url: mooOption.wp_address_url + url_param,
						beforeSend: function( xhr ) {
							MooPopup.loading_start();
						},
						type: 'POST',
						dataType: 'json',
						data: {
							user_id: mooOption.wp_current_user.id,
                        }
					}).done(function(result) {
						if(result.success == true){
							$('#mooTabNotifications').find('.notification_item').each(function (index){
								if($(this).find('.notification_item_status').hasClass('unread')){
									$(this).find('.notification_item_status').removeClass('unread');
									$(this).find('.mark_read').hide();
									$(this).find('.mark_unread').show();
								}
							});

							$('#moo-action-notifications').parent().removeClass('mooHasNotify');
							$('#moo-notifications-count').empty();
							MooPopup.loading_end();
						}
					});
				});
				$('#mooPopup').find('#clearAllNotifications').unbind('click').click(function (e){
					e.preventDefault();

					let url_param = '/wp-json/'+mooOption.MOOWP_APP_NAMESPACE+'/notifications/clear_all_notifications';

					$.ajax({
						url: mooOption.wp_address_url + url_param,
						beforeSend: function( xhr ) {
							MooPopup.loading_start();
						},
						type: 'POST',
						dataType: 'json',
						data: {
							user_id: mooOption.wp_current_user.id,
                        }
					}).done(function(result) {
						if(result.success == true){
							$('#mooTabNotifications').find('.notification_item').each(function (index){
								$(this).remove();
							});

							$('#moo-action-notifications').parent().removeClass('mooHasNotify');
							$('#moo-notifications-count').empty();
							MooPopup.loading_end();
						}
					});
				});
				$('#mooPopup').find('.markMsgStatus').each(function (index){
					$(this).click(function (e){
						e.preventDefault();

						let url_param = '/wp-json/'+mooOption.MOOWP_APP_NAMESPACE+'/notifications/mark_read';
						let status = $(this).attr('data-status');
						let $notification_id = $(this).attr('data-id');

						$.ajax({
							url: mooOption.wp_address_url + url_param,
							beforeSend: function( xhr ) {
								MooPopup.loading_start();
							},
							type: 'POST',
							dataType: 'json',
							data: {
								user_id: mooOption.wp_current_user.id,
								status: status,
								notification_id: $notification_id,
							}
						}).done(function(result) {
							if(result.success == true){
								let status = result.data.status;
								$('#mooPopup').find(".notification_item[rel='"+result.data.notification_id+"']").each(function (index){
									if(status == 1){
										$(this).find('.markMsgStatus.mark_read').hide();
										$(this).find('.markMsgStatus.mark_unread').show();
										$(this).find('.notification_item_status').removeClass('unread');
									}else if (status == 0){
										$(this).find('.markMsgStatus.mark_unread').hide();
										$(this).find('.markMsgStatus.mark_read').show();
										$(this).find('.notification_item_status').addClass('unread');
									}

									if (parseInt(result.data.notification_count) > 0){
										$('#moo-action-notifications').parent().addClass('mooHasNotify');
										$('#moo-notifications-count').html(result.data.notification_count);
									}else{
										$('#moo-action-notifications').parent().removeClass('mooHasNotify');
										$('#moo-notifications-count').empty();
									}

									MooPopup.loading_end();
								});
							}
						});
					});
				});
				$('#mooPopup').find('.removeNotification').each(function (index){
					$(this).click(function (e){
						e.preventDefault();

						let url_param = '/wp-json/'+mooOption.MOOWP_APP_NAMESPACE+'/notifications/remove';
						let $notification_id = $(this).attr('data-id');

						$.ajax({
							url: mooOption.wp_address_url + url_param,
							beforeSend: function( xhr ) {
								MooPopup.loading_start();
							},
							type: 'POST',
							dataType: 'json',
							data: {
								user_id: mooOption.wp_current_user.id,
								notification_id: $notification_id,
							}
						}).done(function(result) {
							if(result.success == true){

								$('#mooPopup').find(".notification_item[rel='"+result.data.notification_id+"']").each(function (index){
									$(this).remove();

									if (parseInt(result.data.notification_count) > 0){
										$('#moo-action-notifications').parent().addClass('mooHasNotify');
										$('#moo-notifications-count').html(result.data.notification_count);
									}else{
										$('#moo-action-notifications').parent().removeClass('mooHasNotify');
										$('#moo-notifications-count').empty();
									}

									MooPopup.loading_end();
								});
							}
						});
					});
				});
			},
			init: function (){
				$('#mooPopupOverview').click(function (e){
					e.preventDefault();
					MooPopup.close();
				});
				$('#moo-popup-close').click(function (e){
					e.preventDefault();
					MooPopup.close();
				});

				MooPopup.initTabs();
				MooPopup.check_notification_count();
				MooPopup.init_refresh_notification();
			}
		};
		MooPopup.init();

	});

}(jQuery));
