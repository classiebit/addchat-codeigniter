<?php

class Core {

	// Function to validate the post data
	function validate_post($data)
	{
		/* Validating the hostname, the database name and the username. The password is optional. */
		return !empty($data['hostname']) && !empty($data['username']) && !empty($data['database']);
	}

	// Function to show an error
	function show_message($type,$message) 
    {
		return $message;
	}

    // Function to write the config file
	function write_config($data = []) 
    {
        // Config path
		$template_path 	= 'src/config/addchat.php';
		$output_path 	= '../'.$data['config'].'/addchat.php';
		
		// Open the source file
		$config_file    = file_get_contents($template_path);
		// replace by user input
        $new  = str_replace("%LOGGED_USER_ID%",$data['session_user_id'],$config_file);

		// Write the new addchat.php file on the destination
		$handle = fopen($output_path,'w+');

		// Chmod the file, in case the user forgot
		@chmod($output_path,0777);
		
		// Verify file permissions
		if(is_writable($output_path)) 
        {
			// Write the file
			if(fwrite($handle,$new))
				return true;
			else
				return false;
		} 
        else 
        {
			return false;
		}
        
	}

	function transfer_files($data = [])
    {
        $transfer 					= array();

        // 1. Assets
		$transfer[0]['source'] 		= 'src/assets/';
		$transfer[0]['path']		= 'Assets Path - '.$data['assets'];
		$transfer[0]['destination'] = '../'.$data['assets'].'/';
		$transfer[0]['file_check'] 	= 'addchat/index.html';
		$transfer[0]['make_dir']	= TRUE;

        // 2. controller
		$transfer[1]['source'] 		= 'src/controllers/';
		$transfer[1]['path']		= 'Controllers Path - '.$data['controllers'];
		$transfer[1]['destination'] = '../'.$data['controllers'].'/';
		$transfer[1]['file_check'] 	= 'Addchat_api.php';
		$transfer[1]['make_dir']	= FALSE;

        // 3. libraries
		$transfer[2]['source'] 		= 'src/libraries/';
		$transfer[2]['path']		= 'Libraries Path - '.$data['libraries'];
		$transfer[2]['destination'] = '../'.$data['libraries'].'/';
		$transfer[2]['file_check'] 	= 'Addchat_lib.php';
		$transfer[2]['make_dir']	= FALSE;

        // 4. language
		$transfer[3]['source'] 		= 'src/language/';
		$transfer[3]['path']		= 'Language (english) Path - '.$data['language'];
		$transfer[3]['destination'] = '../'.$data['language'].'/';
		$transfer[3]['file_check'] 	= 'addchat_lang.php';
		$transfer[3]['make_dir']	= FALSE;
		
		
        // Bulk transfer
        foreach($transfer as $trans)
		{
			$flag 					= $this->xcopy($trans);

			// now check if files are transferred or not
			if(! file_exists($trans['destination'].$trans['file_check']))
			{
				$response   = array('response'=>false, 'error'=>"Invalid ".$trans['path']);

				return false;
			}
		}

		return TRUE;
    }

    /**
	 * Copy a file, or recursively copy a folder and its contents
	 * @author      Aidan Lister <aidan@php.net>
	 * @version     1.0.1
	 * @link        http://aidanlister.com/2004/04/recursively-copying-directories-in-php/
	 * @param       string   $source    Source path
	 * @param       string   $dest      Destination path
	 * @param       int      $permissions New folder creation permissions
	 * @return      bool     Returns true on success, false on failure
	 */
	function xcopy($trans = array())
	{
		// Simple copy for a file
	    if (is_file($trans['source'])) {
	        return copy($trans['source'], $trans['destination']);
	    }

	    // Make destination directory
	    if (!is_dir($trans['destination'])) 
	    {
	    	if($trans['make_dir'])
	    	{
	    		mkdir($trans['destination'], 0777, true);
	    	}
	    	else
	    	{
	        	return false;
	    	}
	    }

	    // Loop through the folder
	    $dir = dir($trans['source']);
	    while (false !== $entry = $dir->read()) {
	        // Skip pointers
	        if ($entry == '.' || $entry == '..') {
	            continue;
	        }

	        // Deep copy directories
	        $this->xcopy(array('source'=>"".$trans['source']."/".$entry."", 'destination'=>"".$trans['destination']."/".$entry."", 'make_dir'=>$trans['make_dir']));
	    }

	    // Clean up
	    $dir->close();
	    return true;
	}

}