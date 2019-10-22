<?php defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Addchat_api Controller
 *
 * This class connect addChat to AddChat_lib
 *
 * @package     addchat
 * @author      classiebit
*/

header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header('content-type: application/json; charset=utf-8');

class Addchat_api extends CI_Controller {

    /**
     * Constructor
     */
    function __construct()
    {
        parent::__construct();

        // Load AddChat required libraries
        $this->load->helper(array('form', 'url'));
        $this->load->library(array('form_validation'));

        // AddChat Lib
        $this->load->library('addchat_lib');
    }

    public function get_lang()
    {
        $this->addchat_lib->get_lang();
    }

    public function get_config()
    {
        $this->addchat_lib->get_config();
    }

    public function get_profile()
    {
        $this->addchat_lib->get_profile();
    }

    public function get_buddy()
    {
        $this->addchat_lib->get_buddy();
    }

    public function get_users($offset = 0)
    {
        $this->addchat_lib->get_users($offset);
    }

    public function get_messages($buddy_id = null,$offset = 0)
    {
        $this->addchat_lib->get_messages($buddy_id,$offset);
    }

    public function send_message()
    {
        $this->addchat_lib->send_message();
    }

    public function delete_chat($user_id = null)
    {
        $this->addchat_lib->delete_chat($user_id);
    }

    public function profile_update()
    {
        $this->addchat_lib->profile_update();
    }

    public function get_updates()
    {
        $this->addchat_lib->get_updates();
    }

    
    public function get_latest_message($buddy_id = null)
    {
        $this->addchat_lib->get_latest_message($buddy_id);
    }

    public function message_delete($message_id = null)
    {   
        $this->addchat_lib->message_delete($message_id);
    }
    

    /* ------- Admin methods start------- */

    public function check_admin()
    {
        $this->addchat_lib->check_admin();
    }

    public function save_settings()
    {
        $this->addchat_lib->save_settings();
    }

    public function a_chat_between($offset = 0)
    {
        $this->addchat_lib->a_chat_between($offset);
    }

    public function a_get_conversations($m_from = null, $m_to = null, $offset = 0)
    {
        $this->addchat_lib->a_get_conversations($m_from, $m_to, $offset);
    }

    
   
    /* ------- Admin methods end------- */
   
}

/* Addchat_api controller ends */