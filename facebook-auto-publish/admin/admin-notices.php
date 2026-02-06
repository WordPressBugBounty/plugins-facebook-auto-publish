<?php
if( !defined('ABSPATH') ){ exit();}
function wp_fbap_admin_notice()
{
	add_thickbox();
	$sharelink_text_array_fb = array
						(
						"I use WP2Social Auto Publish wordpress plugin from @xyzscripts and you should too.",
						"WP2Social Auto Publish wordpress plugin from @xyzscripts is awesome",
						"Thanks @xyzscripts for developing such a wonderful Facebook auto publishing wordpress plugin",
						"I was looking for a Facebook publishing plugin and I found this. Thanks @xyzscripts",
						"Its very easy to use WP2Social Auto Publish wordpress plugin from @xyzscripts",
						"I installed WP2Social Auto Publish from @xyzscripts,it works flawlessly",
						"WP2Social Auto Publish wordpress plugin that i use works terrific",
						"I am using WP2Social Auto Publish wordpress plugin from @xyzscripts and I like it",
						"The WP2Social Auto Publish plugin from @xyzscripts is simple and works fine",
						"I've been using this Facebook plugin for a while now and it is really good",
						"WP2Social Auto Publish wordpress plugin is a fantastic plugin",
						"WP2Social Auto Publish wordpress plugin is easy to use and works great. Thank you!",
						"Good and flexible  WP2Social Auto Publish plugin especially for beginners",
						"The best Facebook Auto publish wordpress plugin I have used ! THANKS @xyzscripts",
						);
$sharelink_text_fb = array_rand($sharelink_text_array_fb, 1);
$sharelink_text_fb = $sharelink_text_array_fb[$sharelink_text_fb];
$xyz_fbap_link = admin_url('admin.php?page=facebook-auto-publish-settings&fbap_blink=en');
$xyz_fbap_link = wp_nonce_url($xyz_fbap_link,'fbap-blk');
$xyz_fbap_notice = admin_url('admin.php?page=facebook-auto-publish-settings&fbap_notice=hide');
$xyz_fbap_notice = wp_nonce_url($xyz_fbap_notice,'fbap-shw');
	echo '
	<script type="text/javascript">
			function xyz_fbap_shareon_tckbox(){
			tb_show("Share on","#TB_inline?width=500&amp;height=75&amp;inlineId=show_share_icons_fb&class=thickbox");
		}
	</script>
	<div id="fbap_notice_td" class="error" style="color: #666666;margin-left: 2px; padding: 5px;line-height:16px;">'?>
	<p><?php
	   $fbap_url="https://wordpress.org/plugins/facebook-auto-publish/";
	   $fbap_xyz_url="https://xyzscripts.com/";
	   $fbap_wp="WP2Social Auto Publish";
	   $fbap_xyz_com="xyzscripts.com";
	   $fbap_thanks_msg=sprintf( __('Thank you for using <a href="%s" target="_blank"> %s </a> plugin from <a href="%s" target="_blank"> %s </a>. Would you consider supporting us with the continued development of the plugin using any of the below methods?','facebook-auto-publish'),$fbap_url,$fbap_wp,$fbap_xyz_url,$fbap_xyz_com); 
	   echo $fbap_thanks_msg; ?></p>
	
	<p>
	<a href="https://wordpress.org/support/plugin/facebook-auto-publish/reviews" class="button xyz_fbap_rate_btn" target="_blank"><?php _e('Rate it 5★\'s on wordpress','facebook-auto-publish'); ?> </a>
	<?php if(get_option('xyz_credit_link')=="0") ?>
		<a href="<?php echo $xyz_fbap_link; ?>" class="button xyz_fbap_backlink_btn xyz_blink"> <?php _e('Enable Backlink','facebook-auto-publish'); ?> </a>
	
	<a class="button xyz_fbap_share_btn" onclick=xyz_fbap_shareon_tckbox();> <?php _e('Share on','facebook-auto-publish'); ?> </a>
		<a href="https://xyzscripts.com/donate/5" class="button xyz_fbap_donate_btn" target="_blank"> <?php _e('Donate','facebook-auto-publish'); ?> </a>
	
	<a href="<?php echo $xyz_fbap_notice; ?>" class="button xyz_fbap_show_btn"> <?php _e('Don\'t Show This Again','facebook-auto-publish'); ?> </a>
	</p>

	<div id="show_share_icons_fb" style="display: none;">
	<a class="button" style="background-color:#3b5998;color:white;margin-right:4px;margin-left:100px;margin-top: 25px;" href="http://www.facebook.com/sharer/sharer.php?u=https://xyzscripts.com/wordpress-plugins/Facebook-auto-publish/" target="_blank"> <?php _e('Facebook','facebook-auto-publish'); ?> </a>
	<a class="button" style="background-color:#00aced;color:white;margin-right:4px;margin-left:20px;margin-top: 25px;" href="http://Twitter.com/share?url=https://xyzscripts.com/wordpress-plugins/Facebook-auto-publish/&text='.$sharelink_text_fb.'" target="_blank"> <?php _e('Twitter','facebook-auto-publish'); ?> </a>
	<a class="button" style="background-color:#007bb6;color:white;margin-right:4px;margin-left:20px;margin-top: 25px;" href="http://www.linkedin.com/shareArticle?mini=true&url=https://xyzscripts.com/wordpress-plugins/Facebook-auto-publish/" target="_blank"> <?php _e('LinkedIn','facebook-auto-publish'); ?> </a>
	</div>
	<?php echo '</div>';
}
$fbap_installed_date = get_option('fbap_installed_date');
if ($fbap_installed_date=="") 
{
	$fbap_installed_date = time();
}

if($fbap_installed_date < ( time() - (20*24*60*60) ))
{
	if (get_option('xyz_fbap_dnt_shw_notice') != "hide")
	{
		add_action('admin_notices', 'wp_fbap_admin_notice');
	}
}
/// --- SMAP Solutions Notice Section ---
function xyz_fbap_smapsolutions_admin_notice() {
    if (!current_user_can('administrator')) {
        return;
    }
    // --- SMAP Solutions Expiry Notices ---
    $expiry_data = get_option('xyz_fbap_smapsolutions_pack_expiry', []);
    if (empty($expiry_data)) 
        return;
    // Remove invalid or empty timestamps
    $expiry_data = array_filter($expiry_data, function($ts) {
        return !empty($ts) && is_numeric($ts);
    });
    if (empty($expiry_data)) 
        return;
    $now = current_time('timestamp');
    $messages = [];
    $displayed_services = [];
    // --- Only facebook Service ---
    $service = 'smapsolution_facebook_expiry';
    $expiry  = $expiry_data[$service] ?? null;
    if (empty($expiry)) 
        return;
    $service_name = xyz_fbap_format_smapsolutions_service_name($service);
    $dismissed_stage = get_user_meta(get_current_user_id(), "xyz_fbap_notice_dismissed_$service", true);
    $diff = $expiry - $now;
    // --- Individual Package Notice Conditions ---
    if ($diff <= 30 * DAY_IN_SECONDS && $diff > 7 * DAY_IN_SECONDS && $dismissed_stage !== '30days') {
        $messages[] = sprintf(
            __("SMAP Solutions %s package expires in 30 days.", "facebook-auto-publish"),
            $service_name
        );
        $displayed_services[] = $service;
    } elseif ($diff <= 7 * DAY_IN_SECONDS && $diff > 0 && $dismissed_stage !== '1week') {
        $messages[] = sprintf(
            __("SMAP Solutions %s package expires in 1 week!", "facebook-auto-publish"),
            $service_name
        );
        $displayed_services[] = $service;
    } elseif ($diff <= 0 && $dismissed_stage !== 'expired') {
        $messages[] = sprintf(
            __("SMAP Solutions %s package has expired!", "facebook-auto-publish"),
            $service_name
        );
        $displayed_services[] = $service;
    }
    // --- Display the Notice ---
    if (!empty($messages)) {
        $dismiss_url = wp_nonce_url(
            add_query_arg([
                'xyz_fbap_dismiss' => 1,
                'services' => implode(',', $displayed_services)
            ]),
            'xyz_fbap_dismiss_notice'
        );
        echo '<div class="notice notice-warning is-dismissible">';
        echo '<p style="color:#2271b1;padding:0;margin:2px 0;"><strong>' 
             . esc_html__("SMAP Solutions Notice:", "facebook-auto-publish") 
             . '</strong></p>';
        foreach ($messages as $msg) {
            echo '<span style="color:indianred;"><strong>' . esc_html($msg) . '</strong></span><br/>';
        }
        echo '<p style="text-align:right;padding:0;margin:2px 0;font-weight:bold;">
                <a href="' . esc_url($dismiss_url) . '">' 
                . esc_html__("Don't show this again", "facebook-auto-publish") 
              . '</a></p>';
        echo '</div>';
    }
}
// --- Format Service Name ---
function xyz_fbap_format_smapsolutions_service_name($service) {
    // Remove prefix
    $name = str_replace('smapsolution_', '', $service);
    // Remove _expiry suffix
    $name = str_replace('_expiry', '', $name);
    return ucfirst($name);
}
add_action('admin_notices', 'xyz_fbap_smapsolutions_admin_notice');
// --- Handle Dismissal Only for facebook Service ---
add_action('admin_init', function() {
    if (isset($_GET['xyz_fbap_dismiss']) && check_admin_referer('xyz_fbap_dismiss_notice')) {
        $expiry_data = get_option('xyz_fbap_smapsolutions_pack_expiry', []);
        $now = current_time('timestamp');
        // --- Only facebook Service ---
        $service = 'smapsolution_facebook_expiry';
        if (!isset($expiry_data[$service])) {
            return;
        }
        $expiry = $expiry_data[$service];
        $diff = $expiry - $now;
        $dismissed_stage = get_user_meta(get_current_user_id(), "xyz_fbap_notice_dismissed_$service", true);
        // --- 30 Days (30 > diff > 7) ---
        if ($diff <= 30 * DAY_IN_SECONDS && $diff > 7 * DAY_IN_SECONDS && $dismissed_stage !== '30days') {
            update_user_meta(get_current_user_id(), "xyz_fbap_notice_dismissed_$service", '30days');
        } 
        // --- 1 Week (7 > diff > 0) ---
        elseif ($diff <= 7 * DAY_IN_SECONDS && $diff > 0 && $dismissed_stage !== '1week') {
            update_user_meta(get_current_user_id(), "xyz_fbap_notice_dismissed_$service", '1week');
        } 
        // --- Expired (diff <= 0) ---
        elseif ($diff <= 0 && $dismissed_stage !== 'expired') {
            update_user_meta(get_current_user_id(), "xyz_fbap_notice_dismissed_$service", 'expired');
        }
        wp_safe_redirect(remove_query_arg(['xyz_fbap_dismiss', 'services']));
        exit;
    }
});
