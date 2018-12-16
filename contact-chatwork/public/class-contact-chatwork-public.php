<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://softworldvietnam.com
 * @since      1.0.0
 *
 * @package    Contact_Chatwork
 * @subpackage Contact_Chatwork/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Contact_Chatwork
 * @subpackage Contact_Chatwork/public
 * @author     huuquyasb <huuquy.ctltqb@gmail.com>
 */
class Contact_Chatwork_Public {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $contact_chatwork    The ID of this plugin.
	 */
	private $contact_chatwork;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $contact_chatwork       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $contact_chatwork, $version ) {

		$this->contact_chatwork = $contact_chatwork;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Contact_Chatwork_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Contact_Chatwork_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->contact_chatwork, plugin_dir_url( __FILE__ ) . 'css/contact-chatwork-public.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Contact_Chatwork_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Contact_Chatwork_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->contact_chatwork, plugin_dir_url( __FILE__ ) . 'js/contact-chatwork-public.js', array( 'jquery' ), $this->version, false );

	}
	/**
	 * Register the shortcode.
	 *
	 * @since    1.0.0
	 */
	public function sw_shortcode_function( $atts ) {
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
					$result = $this->sw_send_chatwork($sw_api_token,$roomId,$message);
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
	/**
	 * Call api sent task .
	 * Api Url: http://download.chatwork.com/ChatWork_API_Documentation.pdf
	 * @since    1.0.0
	 */
	private function sw_send_chatwork($sw_api_token ='',$roomId ='',$message ='')
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
							// Created task
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

}
