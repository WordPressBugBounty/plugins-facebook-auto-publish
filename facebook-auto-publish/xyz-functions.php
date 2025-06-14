<?php
if( !defined('ABSPATH') ){ exit();}

if(!function_exists('xyz_trim_deep'))
{

	function xyz_trim_deep($value) {
		if ( is_array($value) ) {
			$value = array_map('xyz_trim_deep', $value);
		} elseif ( is_object($value) ) {
			$vars = get_object_vars( $value );
			foreach ($vars as $key=>$data) {
				$value->{$key} = xyz_trim_deep( $data );
			}
		} else {
			$value = trim($value);
		}

		return $value;
	}

}

if(!function_exists('esc_textarea'))
{
	function esc_textarea($text)
	{
		$safe_text = htmlspecialchars( $text, ENT_QUOTES );
		return $safe_text;
	}
}

if(!function_exists('xyz_fbap_plugin_get_version'))
{
	function xyz_fbap_plugin_get_version()
	{
		if ( ! function_exists( 'get_plugins' ) )
			require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
		$plugin_folder = get_plugins( '/' . plugin_basename( dirname( XYZ_FBAP_PLUGIN_FILE ) ) );
		// 		print_r($plugin_folder);
		return $plugin_folder['facebook-auto-publish.php']['Version'];
	}
}

if(!function_exists('xyz_fbap_run_upgrade_routines'))
{
function xyz_fbap_run_upgrade_routines() {
	global $wpdb;
	if (is_multisite()) {
		$blog_ids = $wpdb->get_col("SELECT blog_id FROM $wpdb->blogs");
		foreach ($blog_ids as $blog_id) {
			switch_to_blog($blog_id);
			fbap_install_free();
			restore_current_blog();
		}
	} else {
		fbap_install_free();
	}
}
}

if(!function_exists('xyz_fbap_links')){
	function xyz_fbap_links($links, $file) {
		$base = plugin_basename(XYZ_FBAP_PLUGIN_FILE);
		if ($file == $base) {

			$links[] = '<a href="https://help.xyzscripts.com/docs/facebook-auto-publish/faq/"  title="FAQ">FAQ</a>';
			$links[] = '<a href="https://help.xyzscripts.com/docs/facebook-auto-publish/"  title="Read Me">README</a>';
			$links[] = '<a href="https://xyzscripts.com/support/" class="xyz_fbap_support" title="Support"></a>';
			$links[] = '<a href="https://twitter.com/xyzscripts" class="xyz_fbap_twitt" title="Follow us on twitter"></a>';
			$links[] = '<a href="https://www.facebook.com/xyzscripts" class="xyz_fbap_fbook" title="Facebook"></a>';
			$links[] = '<a href="https://www.linkedin.com/company/xyzscripts" class="xyz_fbap_linkdin" title="Follow us on linkedIn"></a>';
			$links[] = '<a href="https://www.instagram.com/xyz_scripts/" class="xyz_fbap_insta" title="Follow us on Instagram+"></a>';
		}
		return $links;
	}
}


if(!function_exists('xyz_fbap_string_limit')){
	function xyz_fbap_string_limit($string, $limit) 
	{
	$space=" ";$appendstr=" ...";
		if (function_exists('mb_strlen') && function_exists('mb_substr') && function_exists('mb_strripos')) {
		if(mb_strlen($string) <= $limit) return $string;
		if(mb_strlen($appendstr) >= $limit) return '';
		$string = mb_substr($string, 0, $limit-mb_strlen($appendstr));
		$rpos = mb_strripos($string, $space);
		if ($rpos===false)
			return $string.$appendstr;
			else
				return mb_substr($string, 0, $rpos).$appendstr;
	} else {
		if(strlen($string) <= $limit) return $string;
		if(strlen($appendstr) >= $limit) return '';
		$string = substr($string, 0, $limit-strlen($appendstr));
		$rpos = strripos($string, $space);
		if ($rpos===false)
			return $string.$appendstr;
		else
			return substr($string, 0, $rpos).$appendstr;
	}
}

}

if(!function_exists('xyz_fbap_getimage')){
function xyz_fbap_getimage($post_ID,$description_org)
{
	$attachmenturl="";
	$post_thumbnail_id = get_post_thumbnail_id( $post_ID );
	if(!empty($post_thumbnail_id))
		$attachmenturl=wp_get_attachment_url($post_thumbnail_id);

	else 
	{
	    $matches=array();
	    $img_content = apply_filters('the_content', $description_org);
	    preg_match_all( '/< *img[^>]*src *= *["\']?([^"\']*)/is', $img_content, $matches );
		if(isset($matches[1][0]))
			$attachmenturl = $matches[1][0];
        else
            $attachmenturl=xyz_fbap_get_post_gallery_images_with_info($description_org,1);

	}
	return $attachmenturl;
		}

}

if(!function_exists('xyz_fbap_get_post_gallery_images_with_info'))
{
    function xyz_fbap_get_post_gallery_images_with_info($post_content,$single=1) 
    {
        $ids=$images_id=array();
        preg_match('/\[gallery.*ids=.(.*).\]/', $post_content, $ids);
        if (isset($ids[1]))
            $images_id = explode(",", $ids[1]);
            $image_gallery_with_info = array();
            foreach ($images_id as $image_id) {
                $attachment = get_post($image_id);
                $img_src=$attachment->guid;
                if($single==1)
                    return $img_src;
                    else
                        $image_gallery_with_info[]=$img_src;
	}
            return $image_gallery_with_info;
}

}

/* Local time formating */
if(!function_exists('xyz_fbap_local_date_time')){
	function xyz_fbap_local_date_time($format,$timestamp){
		return date($format, $timestamp + ( get_option( 'gmt_offset' ) * HOUR_IN_SECONDS ));
	}
}

add_filter( 'plugin_row_meta','xyz_fbap_links',10,2);


if (!function_exists("xyz_fbap_is_session_started")) {
function xyz_fbap_is_session_started()
{
       if ( version_compare(phpversion(), '5.4.0', '>=') ) {
            return session_status() === PHP_SESSION_ACTIVE ? TRUE : FALSE;
        } else {
            return session_id() === '' ? FALSE : TRUE;
        }
    
    return FALSE;
}
}

/*if (!function_exists("xyz_wp_fbap_attachment_metas")) {
	function xyz_wp_fbap_attachment_metas($attachment,$url)
	{
		$name='';$description_li='';$content_img='';$utf="UTF-8";
		$aprv_me_data=wp_remote_get($url,array('sslverify'=> (get_option('xyz_fbap_peer_verification')=='1') ? true : false));
		if( is_array($aprv_me_data) ) {
			$aprv_me_data = $aprv_me_data['body']; // use the content
		}
		else {
			$aprv_me_data='';
		}
		
		$og_datas = new DOMDocument();
		@$og_datas->loadHTML('<?xml encoding="UTF-8">'.$aprv_me_data);
		$xpath = new DOMXPath($og_datas);
/* 		if(isset($attachment['name']))
		{
			$ogmetaContentAttributeNodes_tit = $xpath->query("/html/head/meta[@property='og:title']/@content");

			foreach($ogmetaContentAttributeNodes_tit as $ogmetaContentAttributeNode_tit) {
				$name=$ogmetaContentAttributeNode_tit->nodeValue;

			}
			if(get_option('xyz_fbap_utf_decode_enable')==1)
				$name=utf8_decode($name);
// 			if(strcmp(get_option('blog_charset'),$utf)==0)
// 				$content_title=utf8_decode($content_title);
			if($name!='')
				$attachment['name']=$name;
		} */ /*
		if(isset($attachment['actions']))
		{
			if(isset($attachment['actions']['name']))
			{
				$ogmetaContentAttributeNodes_tit = $xpath->query("/html/head/meta[@property='og:title']/@content");

				foreach($ogmetaContentAttributeNodes_tit as $ogmetaContentAttributeNode_tit) {
					$name=$ogmetaContentAttributeNode_tit->nodeValue;

				}
				if(get_option('xyz_fbap_utf_decode_enable')==1)
					$name=utf8_decode($name);
// 				if(strcmp(get_option('blog_charset'),$utf)==0)
// 					$content_title=utf8_decode($content_title);
				if($name!='')
					$attachment['actions']['name']=$name;
			}
			if(isset($attachment['actions']['link']))
			{
				$attachment['actions']['link']=$url;
			}
		}
/* 		if(isset($attachment['description']))
		{
			$ogmetaContentAttributeNodes_desc = $xpath->query("/html/head/meta[@property='og:description']/@content");
			foreach($ogmetaContentAttributeNodes_desc as $ogmetaContentAttributeNode_desc) {
				$description_li=$ogmetaContentAttributeNode_desc->nodeValue;
			}
			if(get_option('xyz_fbap_utf_decode_enable')==1)
				$description_li=utf8_decode($description_li);
// 			if(strcmp(get_option('blog_charset'),$utf)==0)
// 				$content_desc=utf8_decode($content_desc);
			if($description_li!='')
				$attachment['description']=$description_li;
		} */
		/*if(isset($attachment['picture']))
		{
			$ogmetaContentAttributeNodes_img = $xpath->query("/html/head/meta[@property='og:image']/@content");
			foreach($ogmetaContentAttributeNodes_img as $ogmetaContentAttributeNode_img) {
				$content_img=$ogmetaContentAttributeNode_img->nodeValue;
			}
			if($content_img!='')
				$attachment['picture']=$content_img;
		}*/
/*
		if(isset($attachment['link']))
			$attachment['link']=$url;

		return $attachment;
	}
}*/



if(!function_exists('xyz_fbap_post_to_smap_api'))
{		function xyz_fbap_post_to_smap_api($post_details,$url,$xyzscripts_hash_val='') {
			if (function_exists('curl_init'))
			{
				$post_parameters['post_params'] = serialize($post_details);
				$post_parameters['request_hash'] = md5($post_parameters['post_params'].$xyzscripts_hash_val);
				$ch = curl_init();
				curl_setopt($ch, CURLOPT_URL, $url);
				curl_setopt($ch, CURLOPT_POST, true);
				curl_setopt($ch, CURLOPT_POSTFIELDS, $post_parameters);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
				curl_setopt ($ch, CURLOPT_SSL_VERIFYPEER,(get_option('xyz_fbap_peer_verification')=='1') ? true : false);
				$content = curl_exec($ch);
				curl_close($ch);
				if (empty($content))
				{
					if ($url==XYZ_SMAP_SOLUTION_PUBLISH_URL.'api/facebook.php')
					$response=array('status'=>0,'fb_api_count'=>0,'msg'=>'Error:unable to connect');
					$content=json_encode($response);
				}
				return $content;
			}
		}
}
if (!function_exists("xyz_fbap_clear_open_graph_cache")) {
	function xyz_fbap_clear_open_graph_cache($url,$access_tocken,$appid,$appsecret) {
$fbap_sslverify= (get_option('xyz_fbap_peer_verification')=='1') ? true : false;
		try {
			$params = array(
					'id' => $url,
					'scrape' => 'true',
					'access_token' => $access_tocken
			);
			$xyz_fb_cache_params_enc=json_encode($params);
			$response=xyz_fbap_scrape_url($xyz_fb_cache_params_enc,$fbap_sslverify);
			return $response;
		} catch (Exception $e){
			return 'Graph returned an error: ' . $e->getMessage();
		}
	}
}
?>