<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Library Addchat_db_lib
 *
 * This class handles database interraction
 *
 * @package     addChat
 * @author      classiebit
**/

class Addchat_db_lib 
{
    private $AC_LIB;
    private $AC_SETTINGS;
    
    public function __construct()
    {
        $this->AC_LIB =& get_instance();
        $this->AC_LIB->load->database();
        $this->AC_LIB->load->library(array('session'));
        
        // Addchat tables
        $this->profiles_tb                  = 'ac_profiles';
        $this->ac_messages_tb               = 'ac_messages';
        $this->ac_users_messages_tb         = 'ac_users_messages';
        $this->ac_settings_tb               = 'ac_settings';
        
        // fetch settings
        if(empty($this->AC_LIB->session->userdata('ac_session')))
		{
            $settings 			  	  			= 	$this->get_settings();
            
			$tmp 					            = 	new stdClass();
			foreach ($settings as $setting)
			{
				$tmp->{$setting->s_name} = $setting->s_value;
			}

			$this->AC_LIB->session->set_userdata('ac_session',$tmp);
			$this->AC_SETTINGS  		= 	$this->AC_LIB->session->userdata('ac_session');
		}
		else
		{
            $this->AC_SETTINGS  		= 	$this->AC_LIB->session->userdata('ac_session');
        }

        
        // External tables
        // users table
        $this->users_tb                     = $this->AC_SETTINGS->users_table;
        $this->users_tb_id                  = $this->AC_SETTINGS->users_col_id;
        $this->users_tb_email               = $this->AC_SETTINGS->users_col_email;
    }

    /* ------- Users ------- */

    // get specific user by id
    public function get_user($user_id = 0,  $logged_in_user  = 0)
    {
        $select = array(
            "$this->users_tb.$this->users_tb_id",
            "$this->users_tb.$this->users_tb_email",

            "$this->profiles_tb.fullname",
            "$this->profiles_tb.avatar",
            "$this->profiles_tb.status online",
        );
        
        return  $this->AC_LIB->db
                ->select($select)
                ->join($this->profiles_tb, "$this->profiles_tb.user_id = $this->users_tb.$this->users_tb_id", "left")
                ->where("$this->users_tb.$this->users_tb_id", $user_id)
                ->get($this->users_tb)
                ->row();
    }
    
    // get_users list
    public function get_users($login_user_id = 0, $filters = array(), $is_admin = null)
    {
        $this->AC_LIB->db
        ->select(array(
            "$this->users_tb.$this->users_tb_id",
            "$this->users_tb.$this->users_tb_email",
            "$this->profiles_tb.avatar",
            "$this->profiles_tb.fullname username",
            "$this->profiles_tb.status online",
            "(SELECT IF(COUNT(ACM.id) > 0, COUNT(ACM.id), null) FROM $this->ac_messages_tb ACM WHERE ACM.m_to = '$login_user_id' AND ACM.m_from = '$this->users_tb.$this->users_tb_id' AND ACM.is_read = '0') unread",
        ));

        // exclude logged in user
        $this->AC_LIB->db
        ->join($this->profiles_tb, "$this->profiles_tb.user_id = $this->users_tb.$this->users_tb_id", "left")
        ->where(array("$this->users_tb.$this->users_tb_id !=" =>$login_user_id));
    
        // in case of search, search amongst all users
        if(!empty($filters['search']) )
        {
            // admin can seach all users
            // and if have  is_groups off then user can search all users
            $this->AC_LIB->db
            ->group_start()
            ->or_like("$this->profiles_tb.fullname", $filters['search'], 'both')
            ->or_like("$this->users_tb.$this->users_tb_email", $filters['search'], 'both')
            ->group_end();
        }
        
        return  $this->AC_LIB->db
                ->limit($filters['limit'])
                ->offset($filters['offset'])
                ->get($this->users_tb)
                ->result();
    }
    
    // Update users update_user
    public function update_user($user_id = 0, $data = array())
    {
        $result =  $this->AC_LIB->db
                    ->select()
                    ->where('user_id', $user_id)
                    ->get("$this->profiles_tb")
                    ->row();
        
        // insert data in profile table if user have not exist 
        if(empty($result))
        {
            $this->AC_LIB->db->insert("$this->profiles_tb", $data);
        }
        else
        {
            // if user exist then update user data  
            $this->AC_LIB->db
            ->where("user_id", $user_id)
            ->update("$this->profiles_tb", $data);
        }        

        return true;
    }

    
    /* ------- Messages ------- */

    // Delete chat delete_chat
    public function delete_chat($user_id = 0, $sub_user_id = 0)
    {
        $this->AC_LIB->db
        ->where(array("$this->ac_messages_tb.m_from"=>$user_id, "$this->ac_messages_tb.m_to"=>$sub_user_id))
        ->update($this->ac_messages_tb, array("m_from_delete"=>1));

        $this->AC_LIB->db
        ->where(array("$this->ac_messages_tb.m_to"=>$user_id, "$this->ac_messages_tb.m_from"=>$sub_user_id))
        ->update($this->ac_messages_tb, array("m_to_delete"=>1));

        return TRUE;
    }

    // delete message
    public function message_delete($message_id = null, $login_user_id = null)
	{
        $message  =    $this->AC_LIB->db
                        ->select('*')
                        ->where("id", $message_id)
                        ->get($this->ac_messages_tb)
                        ->row();
        
        if(empty($message))
            return false;

        $this->AC_LIB->db
        ->where(array("id" => $message_id));
            
        if($message->m_from == $login_user_id)
            $this->AC_LIB->db->update($this->ac_messages_tb, array("m_from_delete" => '1'));

        if($message->m_to == $login_user_id)
            $this->AC_LIB->db->update($this->ac_messages_tb, array("m_to_delete" => '1'));    

        return true;
	}

    // send message
    public function send_message($data = array()) 
    {
        $this->AC_LIB->db->insert($this->ac_messages_tb, $data);
        return $this->AC_LIB->db->insert_id();
    }
    
    public function get_messages($user_id = 0, $chat_user = 0, $filters = array(), $count = false)
    {
        $this->AC_LIB->db
        ->select(array(
            "$this->ac_messages_tb.id ",
            "$this->ac_messages_tb.m_from ",
            "$this->ac_messages_tb.m_to ",
            "$this->ac_messages_tb.message ",
            "$this->ac_messages_tb.is_read ",
            "$this->ac_messages_tb.dt_updated ",
        ));
        
        // group query for removing deleted messages
        $this->AC_LIB->db
        ->where("( (`$this->ac_messages_tb`.`m_from` = '$user_id' AND `$this->ac_messages_tb`.`m_to` = '$chat_user')", null, FALSE)
        ->or_where("(`$this->ac_messages_tb`.`m_from` = '$chat_user' AND `$this->ac_messages_tb`.`m_to` = '$user_id') )", null, FALSE)
        ->where("( (IF(`$this->ac_messages_tb`.`m_from` = '$user_id', `$this->ac_messages_tb`.`m_from_delete`, `$this->ac_messages_tb`.`m_to_delete`) = 0) AND (IF(`$this->ac_messages_tb`.`m_to` = '$user_id', `$this->ac_messages_tb`.`m_to_delete`, `$this->ac_messages_tb`.`m_from_delete`) = 0) )", null, FALSE);

        if(!$count)
            return $this->AC_LIB->db->count_all_results($this->ac_messages_tb);
        
        $messages   = $this->AC_LIB->db
                    ->order_by("$this->ac_messages_tb.id")
                    ->limit($filters['limit'])
                    ->offset($filters['offset'])
                    ->get($this->ac_messages_tb);

        $this->AC_LIB->db
        ->where("$this->ac_messages_tb.m_to", $user_id)
        ->where("$this->ac_messages_tb.m_from", $chat_user)
        ->update($this->ac_messages_tb, array("$this->ac_messages_tb.is_read"=>'1'));

        return $messages->result();
    }
    
    
   /* ------- Notifications ------- */
    
    // add notification 
    public function set_notification($notification = array())
    {
        $result =  $this->AC_LIB->db
                    ->select()
                    ->where($notification)
                    ->get($this->ac_users_messages_tb)
                    ->row();
        
        // insert
        if(empty($result))
        {            
            $this->AC_LIB->db->insert($this->ac_users_messages_tb, $notification);
        }
        else // update 
        {
            $this->AC_LIB->db
            ->where($notification)
            ->set('messages_count', 'messages_count+1', FALSE)
            ->update($this->ac_users_messages_tb);
        }

        return true;
        
    }
     
    // Remove notification
    public function remove_notification($notification = array())
    {
        return $this->AC_LIB->db
                ->where($notification)
                ->delete($this->ac_users_messages_tb); 
        
    }
    
    //  get notification
    public function get_updates($login_user_id = null)
    {
        $this->AC_LIB->db
        ->select(array(
            "$this->ac_users_messages_tb.users_id",
            "$this->ac_users_messages_tb.buddy_id",
            "$this->ac_users_messages_tb.messages_count",
        ))
        ->where("buddy_id", $login_user_id);
        
        return $this->AC_LIB->db
                ->get($this->ac_users_messages_tb)
                ->result_array();
    }
    
    //  get latest message
    public function get_latest_message($login_user_id = null, $buddy_id = null)
    {
        $result =  $this->AC_LIB->db
                ->select(array(
                    "$this->ac_messages_tb.id ",
                    "$this->ac_messages_tb.m_from ",
                    "$this->ac_messages_tb.m_to ",
                    "$this->ac_messages_tb.message ",
                    "$this->ac_messages_tb.is_read ",
                    "$this->ac_messages_tb.dt_updated ",
                ))
                ->where(array("$this->ac_messages_tb.m_from" => $buddy_id, "$this->ac_messages_tb.m_to" => $login_user_id, "$this->ac_messages_tb.is_read" => '0'))
            
                //group query for removing unsend messages
                ->where(["$this->ac_messages_tb.m_from_delete" => "0", "$this->ac_messages_tb.m_to_delete" => "0"])
                ->order_by("$this->ac_messages_tb.id")
                ->get($this->ac_messages_tb);

        // delete notification
        $this->AC_LIB->db
        ->where("$this->ac_messages_tb.m_to", $login_user_id)
        ->where("$this->ac_messages_tb.m_from", $buddy_id)
        ->update($this->ac_messages_tb, array("$this->ac_messages_tb.is_read"=>'1'));

        return $result->result();
    }


    /* -------- Admin Panel --------- */
    
    /**
    *   save settings
    */
    public function save_settings($data = array())
    {
        if (!empty($data))
        {
            $saved = FALSE;

            foreach ($data as $key => $value)
            {
                $sql = "
                    UPDATE {$this->ac_settings_tb}
                    SET s_value = '" . $value . "',
                        dt_updated = '" . date('Y-m-d H:i:s') . "'
                    WHERE s_name = '" . $key . "'
                ";

                $this->AC_LIB->db->query($sql);

                if ($this->AC_LIB->db->affected_rows() > 0)
                {
                    $saved = TRUE;
                }
            }

            if ($saved)
            {
                return TRUE;
            }
        }

        return FALSE;
    }

    /*
    *  get settings
    */
    public function get_settings()
    {
        return  $this->AC_LIB->db
                    ->select(array(
                        "$this->ac_settings_tb.s_name",
                        "$this->ac_settings_tb.s_value",
                    ))
                    ->get($this->ac_settings_tb)
                    ->result();
    }

    /**
     *   get chat users who chat with each other means between users
     */
    public function a_chat_between($filters = array(), $logged_in_user = null)
    {   
        $query = "SELECT `id` FROM $this->ac_messages_tb WHERE `m_from` = $logged_in_user GROUP BY `m_from`";

        if( $this->AC_LIB->db->simple_query($query) )
        {
            // safe mode is off
            $select = array(
                "$this->ac_messages_tb.id",
                "$this->ac_messages_tb.m_to",
                "$this->ac_messages_tb.m_from",
                "$this->ac_messages_tb.dt_updated",
                "$this->ac_messages_tb.message",
                "(SELECT PR.fullname  FROM $this->profiles_tb  PR  WHERE PR.id  = $this->ac_messages_tb.m_from) m_from_username",
                "(SELECT PR2.fullname FROM $this->profiles_tb  PR2 WHERE PR2.id = $this->ac_messages_tb.m_to) m_to_username",
                "(SELECT UR.$this->users_tb_email  FROM $this->users_tb UR WHERE UR.$this->users_tb_id = $this->ac_messages_tb.m_from) m_from_email",
                "(SELECT UR2.$this->users_tb_email FROM $this->users_tb UR2 WHERE UR2.$this->users_tb_id = $this->ac_messages_tb.m_to) m_to_email",
            );
        }           
        else
        {
            // safe mode is on
            $select = array(
                "ANY_VALUE($this->ac_messages_tb.id) id",
                "ANY_VALUE($this->ac_messages_tb.m_to) m_to",
                "$this->ac_messages_tb.m_from",
                "ANY_VALUE($this->ac_messages_tb.dt_updated) dt_updated",
                "ANY_VALUE($this->ac_messages_tb.message) message",
                "ANY_VALUE((SELECT PR.fullname  FROM $this->profiles_tb  PR  WHERE PR.id  = $this->ac_messages_tb.m_from)) m_from_username",
                "ANY_VALUE((SELECT PR2.fullname FROM $this->profiles_tb  PR2 WHERE PR2.id = $this->ac_messages_tb.m_to)) m_to_username",
                "ANY_VALUE((SELECT UR.$this->users_tb_email  FROM $this->users_tb UR WHERE UR.$this->users_tb_id = $this->ac_messages_tb.m_from)) m_from_email",
                "ANY_VALUE((SELECT UR2.$this->users_tb_email FROM $this->users_tb UR2 WHERE UR2.$this->users_tb_id = $this->ac_messages_tb.m_to)) m_to_email",
            );
        }

        return  $this->AC_LIB->db
                ->select($select)
                ->where(['m_to !=' => '0', 'm_from !=' => '0'])
                ->group_by(array("$this->ac_messages_tb.m_from", "m_to"))
                ->order_by("id", 'DESC')
                ->limit($filters['limit'])
                ->offset($filters['offset'])  
                ->get($this->ac_messages_tb)
                ->result();
                 
    }

    /**
     *   get conversations between two users
     * 
     */
    public function a_get_conversations($m_from = null, $m_to = null, $filters = array(), $count = false)
    {
        $this->AC_LIB->db
        ->select(array(
            "$this->ac_messages_tb.id ",
            "$this->ac_messages_tb.m_from ",
            "$this->ac_messages_tb.m_to ",
            "$this->ac_messages_tb.message ",
            "$this->ac_messages_tb.is_read ",
            "$this->ac_messages_tb.dt_updated ",
            "$this->ac_messages_tb.m_to_delete ",
            "$this->ac_messages_tb.m_from_delete ",
            "(SELECT PR.avatar  FROM $this->profiles_tb PR WHERE PR.id    = $this->ac_messages_tb.m_from) m_from_image",
            "(SELECT PR2.avatar FROM $this->profiles_tb PR2 WHERE PR2.id  = $this->ac_messages_tb.m_to)   m_to_image",
        ));
        // //group query for removing deleted messages
        $this->AC_LIB->db
        ->where("( (`$this->ac_messages_tb`.`m_from` = '$m_from' AND `$this->ac_messages_tb`.`m_to` = '$m_to')", null, FALSE)
        ->or_where("(`$this->ac_messages_tb`.`m_from` = '$m_to' AND `$this->ac_messages_tb`.`m_to` = '$m_from') )", null, FALSE);
        

        
        if($count)
        return $this->AC_LIB->db->count_all_results($this->ac_messages_tb);

        return  $this->AC_LIB->db
                        ->order_by("$this->ac_messages_tb.id")
                        ->limit($filters['limit'])
                        ->offset($filters['offset'])
                        ->get($this->ac_messages_tb)
                        ->result();

    }
    
    

}

/* End Addchat_db_lib class */