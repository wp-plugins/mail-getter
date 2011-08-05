<?php 
/*
Plugin Name: Mail Getter
Plugin URI: http://cibergeek.com/mail-getter
Description: This plugin helps you retrieve the mails from posts comments.
Version: 0.9
Author: Tomas Cot
Author URI: http://cibergeek.com/
Author Email: tomascot@gmail.com
License: GPLv2
*/

//This plugin is very basic, I promise I'll update it sometime, if you have any feedback I would appreciate it.

// ------------> PLUGIN CODE START <---------------


//plugin menu creator and config
function mail_getter_admin_menu(){
	if (function_exists('add_management_page')){
		//with the current capability (4) authors, editors and admins can use the plugin
		$mg_page= add_management_page('Mail Getter', 'Mail Getter', 4, 'mail_getter_plugin_admin', 'mail_getter_admin');
		//this is for loading the javascript just in the plugin page
		add_action('admin_print_scripts-' . $mg_page, 'mail_getter_javascript_hook');
	}
} //end mail_getter_admin_menu


function mail_getter_admin(){
	//creating the template
	$mail_getter_template='<h2 style="text-align:center">Mail Getter Plugin</h2>
							<div style="margin:30px auto; width:200px;">
							<input style="display:block; box-shadow:1px 1px 3px silver; margin:4px auto;" type="text" name="mg_post_id" id="mg_post_id" value="Write the Post ID here"/>
								<select id="mg_option">
								<option>All</option>
								<option>Approved</option>
								<option>Pending</option>
								</select>
							<input id="mg_submit" style="box-shadow:1px 1px 2px silver;margin:4px;" type="submit" value="Send" onClick="mg_ajax_magic(\''.admin_url().'admin-ajax.php\')">
							</div>
							<div id="maillist" style="width:80%; margin:20px auto;border-radius:4px; border:1px solid silver; box-shadow:1px 1px 2px silver; padding:3px;">Mail list</div>'; 
	echo $mail_getter_template;
}



function mail_getter_database_stuff(){
	global $wpdb;
	
	
	//checks  is the POST variable exists and if is an integer
	if(isset($_POST['mg_post_id']) && intval($_POST['mg_post_id'])){
		//default query
		$mg_sql_query='SELECT DISTINCT comment_author_email FROM
						wp_comments c, wp_posts p WHERE
						c.comment_post_id=p.ID AND p.ID =' . $_POST['mg_post_id'];			
		
		//if you just want approved comments
		if($_POST['mg_option']==='Approved'){
			
			$mg_sql_query=$mg_sql_query .' AND c.comment_approved=1';
		} elseif ($_POST['mg_option']==='Pending'){
			
			$mg_sql_query=$mg_sql_query .' AND c.comment_approved=0';
		}	
		
		$mg_mails=$wpdb->get_results($mg_sql_query, OBJECT_K);
								
		foreach($mg_mails as $mail) {
			//echoing the mails to retrieve them with AJAX
			if(is_email($mail->comment_author_email)){
				echo $mail->comment_author_email .'; ';
			}
		}
	}
	else {
	//if the post id is not and integer this error is triggered
	echo 'Post ID must be an integer';
	}
	//this is here because when the ajax response is over WP add a 0 at the end, adding die avoids that
	die;
} // end mail_getter_database stuff

function mail_getter_javascript_hook() {
	//the script needs jquery, this makes jquery load before the script
	wp_enqueue_script('mail_getter_js', plugins_url('/js/mail_getter_js.js', __FILE__), array('jquery'));
}//end mail_getter_javascript_hook



//HOOKS
add_action('admin_menu', 'mail_getter_admin_menu');
add_action('wp_ajax_mail_getter_database_stuff','mail_getter_database_stuff');


?>