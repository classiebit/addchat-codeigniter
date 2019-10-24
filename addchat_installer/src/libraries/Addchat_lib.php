<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');


/**
 * Library Addchat_lib
 *
 * This class handles all the functionality
 *
 * @package     addChat
 * @author      classiebit
**/

class Addchat_lib 
{
    // globals
	private $AC_LIB;
	private $AC_SETTINGS;
    private $AC_CONFIG;
	
    function __construct()
    {
        // load addchat prerequisite
        $this->AC_LIB =& get_instance();
        $this->AC_LIB->load->helper(array('form', 'url', 'email'));
		$this->AC_LIB->load->library(array('addchat_db_lib', 'form_validation', 'session'));

        // get application default language + sync with AddChat lang
        // get default lang if in session
        if(!empty($this->AC_LIB->session->language))
            $this->AC_LIB->config->set_item('language', $this->AC_LIB->session->language);
            
        $language = $this->AC_LIB->config->item('language');
        $this->AC_LIB->lang->load('addchat', $language);

        // get addchat settings
		$this->AC_SETTINGS  				= $this->AC_LIB->session->userdata('ac_session');
        
        // get addchat config
        $this->AC_LIB->config->load('addchat', TRUE);
        $this->AC_CONFIG = $this->AC_LIB->config->item('addchat', 'addchat');
        
        // get the logged in user
		$this->AC_SETTINGS->logged_user_id  = isset($_SESSION[$this->AC_CONFIG->session_user_id]) ? (int) $_SESSION[$this->AC_CONFIG->session_user_id] : NULL;
        
        // get the admin user
		$this->AC_SETTINGS->admin_user_id 	= (int) $this->AC_SETTINGS->admin_user_id;
		$this->AC_SETTINGS->is_admin 		= $this->AC_SETTINGS->admin_user_id === $this->AC_SETTINGS->logged_user_id ? 1 : 0;
	}


	/*
    * Get-set lang
    */
    public function get_lang()
    {
        // get lang variables
        $lang_variables =    $this->AC_LIB->lang->language;

        // send to app
        $this->format_json(['lang' => $lang_variables]);
    }
	
	/*
    * Get configurations
    */
	public function get_config()
	{
		$data['config'] 						= 	array();
		$data['config']['site_name'] 			= 	$this->AC_SETTINGS->site_name;
		$data['config']['site_logo'] 			= 	$this->AC_SETTINGS->site_logo;
		$data['config']['chat_icon'] 			= 	$this->AC_SETTINGS->chat_icon;
		$data['config']['logged_user_id'] 		= 	$this->AC_SETTINGS->logged_user_id;
		$data['config']['img_upld_pth']			= 	$this->AC_SETTINGS->img_upload_path;
		$data['config']['assets_path']			=	$this->AC_SETTINGS->assets_path;
		$data['config']['is_admin']				= 	$this->AC_SETTINGS->is_admin;
		$data['config']['admin_user_id']		=	$this->AC_SETTINGS->admin_user_id;
		$data['config']['pagination_limit']		=	$this->AC_SETTINGS->pagination_limit;
		$data['config']['users_table']			=	$this->AC_SETTINGS->users_table;
		$data['config']['users_col_id']			=	$this->AC_SETTINGS->users_col_id;
		$data['config']['users_col_email']		=	$this->AC_SETTINGS->users_col_email;
		$data['config']['notification_type']	=	$this->AC_SETTINGS->notification_type;
		$data['config']['footer_text']		    =	$this->AC_SETTINGS->footer_text;
		$data['config']['footer_url']		    =	$this->AC_SETTINGS->footer_url;
		
		$this->format_json($data);
	}

	/*
	*	Get user's profile 
	*/
	public function get_profile($is_return = false)
    {
        // check is logged-in
        $this->check_auth();

		$data					= array();
		$data['status'] 		= true;
		$data['profile'] 		= $this->AC_LIB->addchat_db_lib->get_user($this->AC_SETTINGS->logged_user_id);

		if($is_return)
			return $data;

		$this->format_json($data);
	}
	
	/**
	 * Get buddy
	 */
	public function get_buddy()
	{
        // check is logged-in
        $this->check_auth();

		/* Validate form input */
        $this->AC_LIB->form_validation
		->set_rules('user', lang('user'), 'trim|is_natural_no_zero');
		
		if($this->AC_LIB->form_validation->run() === FALSE)
        {
        	$this->format_json(array('status' => false, 'response'=> validation_errors()));
		}
		   
		$data				= array();
		$buddy 				= (int) $this->AC_LIB->input->post('user');
		$chatbuddy 			= $this->AC_LIB->addchat_db_lib->get_user($buddy, $this->AC_SETTINGS->logged_user_id);

		$c_buddy = array(
			'name' 		 	=> ucwords($chatbuddy->fullname),
			'status' 	 	=> $chatbuddy->online,
			'avatar'		=> $chatbuddy->avatar,
			'id' 		 	=> $chatbuddy->id,
			'email'			=> $chatbuddy->email,
		);

		$data['buddy']		=	$c_buddy;
		$data['status']		=	true;

		$this->format_json($data);
	}

	/*
    * Get users list get_users
    */
    public function get_users($offset = 0)
    {   
        // check is logged-in
        $this->check_auth();

		// filters
		$filters                    = array();
		$filters['limit']           = $this->AC_SETTINGS->pagination_limit;
		$filters['offset']          = (int) $offset;
		$filters['search']          = (string) $this->AC_LIB->input->post('search');

		$users      = 	$this->AC_LIB->addchat_db_lib->get_users(
            $this->AC_SETTINGS->logged_user_id, 
            $filters, 
            $this->AC_SETTINGS->is_admin
        );
	
        if(empty($users))
        {
            $data       = array(
                            'users'  	=> array(),
                            'offset'    => 0,
							'more'      => 0,  // to stop load more process
							'status'    => true,
                        );
            $this->format_json($data);
        }
        
        $data                       = array();
        $data['users'] 				= $users;
		$data['offset']             = $filters['offset'] == 0 ? $filters['limit'] : $filters['limit']+$filters['offset'];
		$data['more']               = 1;  // to continue load more process
		$data['status'] 			= true;

		$this->format_json($data);
	}
	

	/*
	* Get messages get_messages
	*/
	public  function get_messages($buddy_id = null, $offset = 0)
	{
        // check is logged-in
        $this->check_auth();

		$buddy_id         			= (int) $buddy_id;
		
		// filters
		$filters                    = array();
		$filters['limit']           = $this->AC_SETTINGS->pagination_limit;
		$filters['offset']          = (int) $offset;

		$total_messages 			= $this->AC_LIB->addchat_db_lib->get_messages($this->AC_SETTINGS->logged_user_id, $buddy_id, $filters);
 
		// 1st case
		if($filters['offset'] == 0)
			$filters['offset']		= $total_messages > $filters['limit'] ? $total_messages - $filters['limit'] : 0;
		else
			$filters['offset']		= $filters['offset'] - $filters['limit'];

		// last case
		$more = 1;
		if($filters['offset'] < 0 || $filters['offset']==0)
		{
			$filters['limit']  		= $filters['limit'] - $filters['offset'];
			$filters['offset'] 		= 0;
			$more = 0;
		}
		
		$messages 					= $this->AC_LIB->addchat_db_lib->get_messages($this->AC_SETTINGS->logged_user_id, $buddy_id, $filters, true);

		if(empty($messages))
        {
			$data       = array(
				'messages'  => array(),
				'offset'    => 0,
				'more'      => 0,  // to stop load more process
				'status'    => true,
			);
            $this->format_json($data);
		}

		// remove notification
		$this->AC_LIB->addchat_db_lib->remove_notification(array('buddy_id'=>$this->AC_SETTINGS->logged_user_id, 'users_id'=>$buddy_id));

		$data 					= array();
		$data['messages'] 		= array();
		foreach ($messages as $key => $message) 
		{
			$data['messages'][$key]['message_id'] 			= $message->id;
			$data['messages'][$key]['sender'] 				= $message->m_from;
			$data['messages'][$key]['recipient'] 			= $message->m_to;
			$data['messages'][$key]['message'] 				= $message->message;
			$data['messages'][$key]['is_read'] 				= $message->is_read;
			$data['messages'][$key]['dt_updated'] 			= $message->dt_updated; 
			
		}
		
		$data['offset']				= $filters['offset'];			
		$data['more']               = $more;  // to continue load more process
		$data['status'] 			= true;
		
		$this->format_json($data);
	}
	
	/*
	* Send message send_message
	*/
	public function send_message()
	{
        // check is logged-in
        $this->check_auth();

		/* Validate form input */
        $this->AC_LIB->form_validation
        ->set_rules('user', lang('user'), 'required|trim|is_natural_no_zero')
        ->set_rules('message', lang('message'), 'trim|required|max_length[2000]');
		
        if($this->AC_LIB->form_validation->run() === FALSE)
        {
       		$data = array('status' => false, 'response'=> validation_errors());
			$this->format_json($data);
        }

		$buddy 				= (int) $this->AC_LIB->input->post('user');
		$message 			= nl2br($this->AC_LIB->input->post('message'));

        // return null if buddy or message is empty
        if(!$message || !$buddy)
			$this->format_json(['status' => false,'response' => 'N/A']);

        $msg    = array(
            "m_from"		=> $this->AC_SETTINGS->logged_user_id,
            "m_to" 			=> $buddy,
            "message" 		=> $message,
            "dt_updated" 	=> date('Y-m-d H:i:s'),
        );
            
        $msg_id = $this->AC_LIB->addchat_db_lib->send_message($msg);

        // 2. set_notification
        $this->AC_LIB->addchat_db_lib->set_notification(array('users_id' => $this->AC_SETTINGS->logged_user_id, 'buddy_id' => $buddy));
    
        $chat = array(
            'message_id' 		=> $msg_id,
            'sender' 			=> $msg['m_from'], 
            'recipient' 		=> $msg['m_to'],
            'message' 			=> $msg['message'],
            'dt_updated' 		=> $msg['dt_updated'],
            'is_read' 			=> 0,
        );

        $data = array(
            'status' 	=> true,
            'message' 	=> $chat 	  
        );
		
		$this->format_json($data);
	}

	/*
	* Delete chat history delete_chat
	*/
	public function delete_chat($user_id = null)
	{
        // check is logged-in
        $this->check_auth();

		$user_id = (int) $user_id;
        if(empty($user_id))
        {
        	$data  =  array('status' => false, 'response'=> lang('delete').' '.lang('fail'));
			$this->format_json($data);
        }

		$data					= array();
		$data['status'] 		= $this->AC_LIB->addchat_db_lib->delete_chat($this->AC_SETTINGS->logged_user_id, $user_id);

		$this->format_json($data);
	}

	/*
	* Update profile profile_update
	*/
    public function profile_update()
    {
        // check is logged-in
        $this->check_auth();
		
		$this->AC_LIB->form_validation
		->set_rules('status', lang('status'), 'required|trim')
		->set_rules('fullname', lang('fullname'), 'required|trim')
		->set_rules('user_id', lang('user'), 'required|trim|is_natural_no_zero');
		
        if($this->AC_LIB->form_validation->run() === FALSE)
        {
       		$data = array('status' => false, 'response'=> validation_errors());
			$this->format_json($data);
		}

		// upload profile image
		$filename               = null;
		if(! empty($_FILES['image']['name'])) // if image 
        {
			$file               = array('folder'=>$this->AC_SETTINGS->img_upload_path, 'input_file'=>'image');
	        $filename           = $this->upload_file($file);
            // through image upload error
            if(!empty($filename['error']))
            {
				$data	=	array('status' => false, 'response'=> lang('image_upload').' (png | jpg | jpeg)' );
				$this->format_json($data);
	        }
        }

		$data								= array();
		$data['status']		= $this->AC_LIB->input->post('status');
		$data['fullname']	= $this->AC_LIB->input->post('fullname');
		$data['user_id']	= $this->AC_LIB->input->post('user_id');
		$data['dt_updated'] =  date("Y-m-d H:i:s");

		if(!empty($filename))
			$data['avatar'] = $filename;
		
		// update user status
		$status           =  $this->AC_LIB->addchat_db_lib->update_user($this->AC_SETTINGS->logged_user_id, $data);
		if($status)
			$this->format_json($this->get_profile(true));

	}

	
    /*
    * Get realtime updates of messages get_updates
    */
    public function get_updates()
	{
        // check is logged-in
        $this->check_auth();

		$notification 	= $this->AC_LIB->addchat_db_lib->get_updates($this->AC_SETTINGS->logged_user_id);

		// stop sending notification if in case of same notification
		$is_same = false;
		if(!empty($_POST['notification']))
			if($_POST['notification'] == json_encode($notification))
				$is_same = true;

		// if no messages then do nothing
	    if(empty($notification) || $is_same)
	   		$this->format_json(array('status' => false, 'response'=> 'N/A'));
		
		$this->format_json(array('status' => true, 'notification' => $notification));
	}

	/*
    * Get latest message of active buddy
    */
    public function get_latest_message($buddy_id = null)
	{
		// check is logged-in
        $this->check_auth();

		$buddy_id = (int) $buddy_id;
		$messages 	= array();
		if($buddy_id)
		{
			$messages 	= $this->AC_LIB->addchat_db_lib->get_latest_message($this->AC_SETTINGS->logged_user_id, $buddy_id);

			// if any new message then remove the specific notification
			// remove notification
			$this->AC_LIB->addchat_db_lib->remove_notification(array('buddy_id'=>$this->AC_SETTINGS->logged_user_id, 'users_id'=>$buddy_id));

		}

		// if no messages then do nothing
	    if(empty($messages))
	   		$this->format_json(array('status' => false, 'response'=> 'N/A'));

		$this->format_json(array('status' => true, 'messages' => $messages));
	}

	
	/**
	 *  message delete
	 */
	public function message_delete($message_id = null)
	{
        // check is logged-in
        $this->check_auth();
		
		$message_id = (int) $message_id;
		if(empty($message_id))
			$this->format_json(array('status' => false));

		$status  	= $this->AC_LIB->addchat_db_lib->message_delete($message_id, $this->AC_SETTINGS->logged_user_id);

		if($status)
			$this->format_json(array('status' => true, 'message' => lang('message').' '.lang('deleted')));
		
		$this->format_json(array('status' => false, 'message'=> lang('delete').' '.lang('fail')));
	}

	/* ========== ADMIN PANEL APIs start ==========*/

    /**
     * Check admin auth
    */
    public function check_admin($is_return = false)
    {
        // check if logged-in user is admin
		if($this->AC_SETTINGS->is_admin !== $this->AC_SETTINGS->logged_user_id)
			$this->format_json(array('status' => false));

		if(!$is_return)
			$this->format_json(array('status' => true));

		return true;
	}

	/**
	*	Save settings
	*/
	public function save_settings()
	{
        //check admin authentication
		$this->check_admin(true);

        // do not respond empty request
		if($_SERVER['REQUEST_METHOD'] == 'OPTIONS') 
        {
			header('Access-Control-Allow-Origin: *');
			header('Access-Control-Allow-Headers: X-Requested-With');
			header("HTTP/1.1 200 OK");
			die();
		}

        if($this->demo_mode())
        {
       		$data = array('status' => false, 'response'=> 'DEMO MODE');
			$this->format_json($data);
		}
		
		/* Validate form input */
		$this->AC_LIB->form_validation
		->set_rules('site_name', lang('site_name'), 'required|trim')
		->set_rules('footer_text', lang('footer_text'), 'trim')
		->set_rules('footer_url', lang('footer_text').' '.lang('URL'), 'trim')
		->set_rules('admin_user_id', lang('admin').' '.lang('user').' '.lang('id'), 'required|trim|is_natural_no_zero')
		->set_rules('pagination_limit', lang('pagination_limit'), 'required|trim|is_natural_no_zero')
		->set_rules('img_upload_path', lang('img_upload_path'), 'required|trim')
		->set_rules('assets_path', lang('assets_path'), 'required|trim')
		->set_rules('users_table', lang('users_table').' '.lang('name'), 'required|trim')
		->set_rules('users_id', lang('users_id'), 'required|trim')
		->set_rules('users_email', lang('users_email'), 'required|trim')
		->set_rules('notification_type', lang('notification_type'), 'numeric');
		
		if($this->AC_LIB->form_validation->run() === FALSE)
        {
       		$data = array('status' => false, 'response'=> validation_errors());
			$this->format_json($data);
		}
		
		$data 					= array();

		// upload  site logo
		$filename               = null;
		if(! empty($_FILES['image']['name'])) // if image 
        {
			$file               = array('folder'=>$this->AC_SETTINGS->img_upload_path, 'input_file'=>'image');
	        $filename           = $this->upload_file($file);
            // through image upload error
            if(!empty($filename['error']))
            {
				$data	=	array('status' => false, 'response'=> lang('image_upload').' (png | jpg | jpeg)' );
				$this->format_json($data);
	        }
		}

		// upload  chat icon
		$chat_icon               = null;
		if(! empty($_FILES['chat_icon']['name'])) // if image 
		{
			$chat_icon_file               = array('folder'=>$this->AC_SETTINGS->img_upload_path, 'input_file'=>'chat_icon');
			$chat_icon           		  = $this->upload_file($chat_icon_file);
			
			// through image upload error
			if(!empty($chat_icon['error']))
			{
				$data	=	array('status' => false, 'response'=> lang('image_upload').' (png | jpg | jpeg)' );
				$this->format_json($data);
			}
		}
		
		// site logo
		if(!empty($filename))
			$data['site_logo'] = $filename;

		// chat icon
		if(!empty($chat_icon))
			$data['chat_icon'] = $chat_icon;

		$data['site_name']	        = $this->AC_LIB->input->post('site_name');
		$data['footer_text']	    = $this->AC_LIB->input->post('footer_text');
		$data['footer_url']	        = $this->AC_LIB->input->post('footer_url');
		$data['admin_user_id']		= $this->AC_LIB->input->post('admin_user_id');
		$data['pagination_limit']	= $this->AC_LIB->input->post('pagination_limit');
		$data['img_upld_pth']		= $this->AC_LIB->input->post('img_upload_path');
		$data['assets_path']		= $this->AC_LIB->input->post('assets_path');
		$data['users_table']		= $this->AC_LIB->input->post('users_table');
		$data['users_col_id']		= $this->AC_LIB->input->post('users_id');
		$data['users_col_email']	= $this->AC_LIB->input->post('users_email');
		$data['notification_type'] 	= $this->AC_LIB->input->post('notification_type'); 
		
		$status    					= $this->AC_LIB->addchat_db_lib->save_settings($data);

				
		$this->format_json(array('status' => $status));
	}

	/**
	 *  get chat users who chat with each other means between users
	 * 
	 */
	public function a_chat_between($offset = 0)
	{
		//check admin authentication
		$this->check_admin(true);

		// filters
		$filters                    = array();
		$filters['limit']           = $this->AC_SETTINGS->pagination_limit;
		$filters['offset']    		= (int) $offset;

		$chat_betweens 	= $this->AC_LIB->addchat_db_lib->a_chat_between($filters, $this->AC_SETTINGS->logged_user_id);
		if(empty($chat_betweens))
		{
			$data       = array(
				'chat_betweens'  	=> array(),
				'offset'    		=> 0,
				'more'      		=> 0,  // to stop load more process
				'status'    		=> true,
			);
			$this->format_json($data);
		}

		$data = array(
			'status' 				=> true,
			'offset'    			=> $filters['offset'] == 0 ? $filters['limit'] : $filters['limit']+$filters['offset'],
			'more'      			=> 1,  // to stop load more process
			'chat_betweens' 		=> $chat_betweens,
		);

		$this->format_json($data);
	}

	/**
	 *   get conversation of between to  users
	 */
	public function a_get_conversations($m_from = null, $m_to = null, $offset = 0)
	{
		//check admin authentication
		$this->check_admin(true);

		$m_from			= (int) $m_from;
		$m_to			= (int)	$m_to;

		// filters
		$filters                    = array();
		$filters['limit']           = $this->AC_SETTINGS->pagination_limit;
		$filters['offset']          = (int) $offset;

		$total_messages 			= $this->AC_LIB->addchat_db_lib->a_get_conversations($m_from, $m_to, $filters, true);

		// 1st case
		if($filters['offset'] == 0)
			$filters['offset']		= $total_messages > $filters['limit'] ? $total_messages - $filters['limit'] :	0;

			
		else
			$filters['offset']		= $filters['offset'] - $filters['limit'];

		// last case
		$more = 1;
		if($filters['offset'] < 0 || $filters['offset']==0)
		{
			$filters['limit']  		= $filters['limit'] - $filters['offset'];
			$filters['offset'] 		= 0;
			$more = 0;
		}

		$conversations 	= $this->AC_LIB->addchat_db_lib->a_get_conversations($m_from, $m_to, $filters);

		if(empty($conversations))
        {
			$data       = array(
				'conversations'  => array(),
				'offset'    	 => 0,
				'more'      	 => 0,  // to stop load more process
				'status'         => true,
			);
            $this->format_json($data);
		}
	
		$data       = array(
			'conversations'  	=> $conversations,
			'status'    		=> true,
			'more'				=> $more,	// to continue load more process
			'offset'			=> $filters['offset'],
		);
		$this->format_json($data);
	}

    /* ========== ADMIN PANEL APIs end==========*/

	

	




    /* ========== PRIVATE HELPER FUNCTIONS ==========*/
	/**
    * Upload File
    */
    private function upload_file($data = array())
    {
        $this->AC_LIB->load->library(array('upload', 'image_lib'));
        
        $config                         = array();
        $config['allowed_types']        = 'jpg|JPG|jpeg|JPEG|png|PNG';
        $config['size']                 = '8388608';
        $config['file_ext_tolower']     = TRUE;
        $config['overwrite']            = TRUE;
        $config['remove_spaces']        = TRUE;
        $config['upload_path']          = './'.$data['folder'].'/';
        
        if (!is_dir($config['upload_path']))
            mkdir($config['upload_path'], 0777, TRUE);
        
        $filename                       = time().rand(1,988);
        $extension                      = strtolower(pathinfo($_FILES[$data['input_file']]['name'], PATHINFO_EXTENSION));
        
        // original file for resizing
        $config['file_name']            = $filename.'_large'.'.'.$extension;

        // file name for further use
        $filename                       = $filename.'.'.$extension;
        
        $this->AC_LIB->upload->initialize($config);

        if (! $this->AC_LIB->upload->do_upload($data['input_file'])) 
        {            
            // remove all uploaded files in case of error
            $this->reset_file($config['upload_path'], $filename);
            return array('error' => $this->AC_LIB->upload->display_errors());
        }

        // cropped thumbnail
        $thumb                          = array();
        $thumb['image_library']         = 'gd2';
        $thumb['source_image']          = $config['upload_path'].$config['file_name'];
        $thumb['new_image']             = $config['upload_path'].$filename;
        $thumb['maintain_ratio']        = TRUE;
        $thumb['width']                 = 800;
        $thumb['height']                = 600;
        $thumn['quality']               = 50;
        $thumb['file_permissions']      = 0644;
        
        $this->AC_LIB->image_lib->initialize($thumb);  
        
        if (! $this->AC_LIB->image_lib->resize()) 
        {
            $this->reset_file($config['upload_path'], $filename);
            return array('error' => $this->AC_LIB->image_lib->display_errors());
        }

        $this->AC_LIB->image_lib->clear();        

        // remove the original image
        unlink($config['upload_path'].$config['file_name']);
        
        return $filename;
        
    } 

    /**
     * Reset File
    */
    private function reset_file($path = '', $data = '')
    {
        if(file_exists($path.$data))
            @unlink($path.$data);
        
        return 1;
	}
	
    /**
     * Validate email
    */
	private function isValidEmail($email)
    {
		return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
	}

    /**
     * Check if user logged in
    */
    private function check_auth()
    {
        if(!$this->AC_SETTINGS->logged_user_id) 
    		$this->format_json(array('status' => false, 'response'=> lang('access_denied')));

        return true;
    }

    /**
     * Echo json
    */
    private function format_json($data = array())
	{
		header('Content-Type: application/json');
		echo json_encode($data);
		exit;
	}

    /**
     *  Detect demo mode
    */
    private function demo_mode()
    {
        $domain = strtolower($_SERVER['SERVER_NAME']);
        if (strpos($domain, 'classiebit.com') !== FALSE || strpos($domain, 'addchat-codeigniter.test') !== FALSE)
            return true;
        
        return FALSE;
    }


}
/*End Addchat_lib Class*/