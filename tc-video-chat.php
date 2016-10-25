<?php
/*
* Plugin Name: TC Video Chat 
* Plugin URI: https://github.com/Ruddernation-Designs/wordpress-video-chat-advanced
* Author: Ruddernation Designs
* Author URI: https://profiles.wordpress.org/ruddernationdesigns
* Description: TinyChat full screen video chat for WordPress/BuddyPress, This also has smileys enabled, This advanced version allows you to add your own room name.
* Requires at least: WordPress 4.0, BuddyPress 2.0
* Tested up to: WordPress 4.6.1, BuddyPress 2.7.0
* Version: 1.0.1
* License: GPLv3
* License URI: http://www.gnu.org/licenses/gpl-3.0.html
* Date: 25th October 2016
*/
define('COMPARE_VERSION', '1.0.0');
register_activation_hook(__FILE__, 'tc_video_chat_install');
function tc_video_chat_install() {
	global $wpdb, $wp_version;
	$post_date = date("Y-m-d H:i:s");
	$post_date_gmt = gmdate("Y-m-d H:i:s");
	$sql = "SELECT * FROM ".$wpdb->posts." WHERE post_content LIKE '%[tc_video_chat_page]%' AND `post_type` NOT IN('revision') LIMIT 1";
	$page = $wpdb->get_row($sql, ARRAY_A);
	if($page == NULL) {
		$sql ="INSERT INTO ".$wpdb->posts."(
			post_author, post_date, post_date_gmt, post_content, post_content_filtered, post_title, post_excerpt,  post_status, comment_status, ping_status, post_password, post_name, to_ping, pinged, post_modified, post_modified_gmt, post_parent, menu_order, post_type)
VALUES ('1', '$post_date', '$post_date_gmt', '[tc_video_chat_page]', '', 'Video Chat', '', 'publish', 'closed', 'closed', '', 'video-chat', '', '', '$post_date', '$post_date_gmt', '0', '0', 'page')";
		$wpdb->query($sql);
		$post_id = $wpdb->insert_id;
		$wpdb->query("UPDATE $wpdb->posts SET guid = '" . get_permalink($post_id) . "' WHERE ID = '$post_id'");
	} else {
		$post_id = $page['ID'];
	}
	update_option('tc_video_chat_url', get_permalink($post_id));
}
add_filter('the_content', 'wp_show_tc_video_chat_page', 222);
function wp_show_tc_video_chat_page($content = '') {
	if(preg_match("/\[tc_video_chat_page\]/",$content)) {
		wp_show_tc_video_chat();
		return "";
	}
	return $content;
}
function wp_show_tc_video_chat() {
	if(!get_option('tc_video_chat_enabled', 0)) {
	}
?>
<form method="post" class="form">
<input type="text" name="room" title="Enter Room Name, If it does not exist then it will create the room for you." tabindex="1" placeholder="Just enter the name of the Tinychat room" autofocus required/>
<input type="submit" class="button2" value="Chat"/></form>
<?php 
$room = filter_input(INPUT_POST, 'room');
if(preg_match('/^[a-z0-9]/',
$room=strtolower($room),
$room=strip_tags($room))) {
$room=preg_replace('/[^a-zA-Z0-9]/','',$room);
$prohash = hash('sha256',filter_input(INPUT_SERVER, 'HTTP_CLIENT_IP'));
{
	if (strlen($room) < 3){
echo '<p>The Tinychat room needs to be more than 3 characters.</p>'; 
}else
if (strlen($room) > 36){
echo '<p>The Tinychat room needs to be less than 36 characters.</p>'; 
}else
echo '
<style>#chat{position:fixed;left:0px;right:0px;bottom:0px;height:100%;width:100%;z-index:9999}input[type="text"]{width:300px;display:block;}</style>
<div id="chat">
<script type="text/javascript">
var tinychat = ({
		room: "'.$room.'", 
		prohash: "'.$prohash.'",
		wmode:"transparent",
		chatSmileys:"true", 
		urlsuper: "http://www.ruddernation.info/'.$room.'", 
		desktop:"true",
		langdefault:"en"});
		</script>
<script src="https://cdn.ruddernation.com/js/eslag.js"></script>
<div id="client"></div></div>';
								}
									}
										}?>
