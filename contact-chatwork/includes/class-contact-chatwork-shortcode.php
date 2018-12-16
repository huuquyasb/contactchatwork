<?php
// disable direct access
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
// the shortcode
function sw_shortcode($sw_atts) {
	// processing form
	$msg = '';
	$error = false;
	if (($_SERVER['REQUEST_METHOD'] == 'POST') && isset($_POST['sw_contact_submit'])) {
		// sanitize content
		$post_data = array(
			'fullname' => sanitize_text_field($_POST['sw_fullname']),
			'email' => sanitize_email($_POST['sw_email']),
			'phone' => sanitize_text_field($_POST['sw_phone']),
			'message' => sanitize_textarea_field($_POST['sw_message'])
		);
		// validate form
		if($post_data['fullname'] === '' || $post_data['email'] === '' || $post_data['phone'] === '' || $post_data['message'] === ''){
			$msg = 'Please enter full information!';
			$error = true;
		}
		// Connect to Chatwork
		if(!$error){
			$message = $post_data['fullname'].' - '. $post_data['phone'] .' - '.$post_data['email']. ' - '. $post_data['message'];
			$sw_api_token = esc_attr( get_option( 'sw_api_token'));
			$roomId = esc_attr( get_option( 'sw_room_id'));
			if($sw_api_token !='' && $roomId !=''){
				// Create task to chatwork
				$result = sw_send_chatwork($sw_api_token,$roomId,$message);
				$msg = $result['msg'];
				$error = $result['error'];
			}	
		}
	}
    $sw_contact_form =  '<form  method="post" class="sw-contact">
		<label for="sw_fullname">Full Name</label>
		<input type="text" id="sw_fullname" name="sw_fullname" maxlength="100" required="" placeholder="Full Name..">
		<label for="sw_email">Email</label>
		<input type="email" id="sw_email" name="sw_email" maxlength="100" required="" placeholder="Email..">
		<label for="email">Phone</label>
		<input type="text" id="sw_phone" name="sw_phone" maxlength="50" required="" placeholder="Phone..">
		<label for="sw_message">Message</label>
		<textarea class="form-control " name="sw_message" id="sw_message" placeholder="Message" rows="2" required=""></textarea>
		<p class="'.($error ? "msg-error" : "msg-success").'">'.$msg.'</p>
		<input type="submit" name="sw_contact_submit" value="Submit">
	</form>'; 
	return $sw_contact_form; 		
} 
add_shortcode('sw-contact', 'sw_shortcode');

function sw_send_chatwork($sw_api_token ='',$roomId ='',$message ='')
{
	$msg = '';
	$error = false;
	// Get List memeber
	$memberInfoUrl = 'https://api.chatwork.com/v2/rooms/'.$roomId.'/members';
	//Post new task
	$postTaskUrl = 'https://api.chatwork.com/v2/rooms/'.$roomId.'/tasks';

	$args = array(
		'headers' => array(
			'X-ChatWorkToken' => $sw_api_token
		)
	);
	$response = wp_remote_get($memberInfoUrl, $args);
	$body = wp_remote_retrieve_body( $response );

	$api_result = json_decode($body, true);
	if(!empty($api_result) && count($api_result)>0){
		if(!isset($api_result['errors'])){
			$index = rand(0,count($api_result)-1);
			$salesSelected = $api_result[$index];
			if(isset($salesSelected)){
				if (isset($salesSelected['account_id']) || array_key_exists('account_id', $salesSelected)) {
					//Create task
					$params = array(
						'body' =>'[To:'.$salesSelected['account_id'].']'. $message,
						'to_ids' => $salesSelected['account_id'],
						'limit'=>time()
					);
					$post_args = array(
						'body' => $params,
						'timeout' => '5',
						'redirection' => '5',
						'httpversion' => '1.0',
						'blocking' => true,
						'headers' => array(
							'X-ChatWorkToken' => $sw_api_token
						),
						'cookies' => array()
					);
					
					$response = wp_remote_post( $postTaskUrl, $post_args );
					$body = wp_remote_retrieve_body( $response );
					$api_result = json_decode($body, true);
					if(isset($api_result['task_ids'])){
						$msg = 'Your information was successfully sent. We will respond back to the e-mail provided as soon as we can.';
					}else{
						$error = true;
						$msg = 'Error! Please try again.';
					}
				}
			}
		}else{
			$msg = $api_result['errors'][0];
			$error = true;
		}
	}
	return array(
		'error'=>$error,
		'msg'=>$msg,
	);
}
