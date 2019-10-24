<?php

$domain = strtolower($_SERVER['SERVER_NAME']);
if (strpos($domain, 'classiebit.com') !== FALSE)
{
    // DEMO mode
    define('INSTALL_MODE', 0);
}
else
{
    // non-demo mode
    define('INSTALL_MODE', 1);
}

// Setting this to E_ALL showed that that cause of not redirecting were few blank lines added in some php files.
error_reporting(0); 

$db_config_path     = '../application/controllers';
$install_success    = 0;

// Only load the classes in case the user submitted the form
if($_POST && INSTALL_MODE === 1) {

	// Load the classes and create the new objects
	require_once('includes/Core.php');
	require_once('includes/Database.php');

	$core = new Core();
	$database = new Database();

	// Validate the post data
	if($core->validate_post($_POST) == true)
	{
		// First  create the database, then create tables, then write config file
        /**
         * 1. Verify connection to DB
         * 2. Write config and transfer to application
         * 3. Transfer controller, libraries & assets data
         * 4. Create AddChat db tables
          */

        if($database->connect_database($_POST) == false) 
        {
			$message = $core->show_message('error',"Could not connect to Database, please verify your settings.");
		} 
        else if ($core->write_config($_POST) == false) 
        {
			$message = $core->show_message('error',"AddChat config file could not be transferred, please make sure you have set correct write permissions to project directory.");
		} 
        else if($core->transfer_files($_POST) == false) 
        {
            $message = $core->show_message('error', "AddChat files could not be transferred, please make sure you have set correct write permissions to project directory.");
        }
		else if ($database->create_tables($_POST) == false) 
        {
			$message = $core->show_message('error',"The database tables could not be created, please verify your settings.");
		} 

		// If no errors, redirect to registration page
		if(!isset($message)) 
        {
	      	$install_success = 1;
		}
	}
	else 
    {
		$message = $core->show_message('error','Please fill in &nbsp;&nbsp;(required) fields');
	}
}

?>
<!doctype html>
<html lang="en">
<head>
    <title>AddChat CodeIgniter Lite | Installer</title>
    <!-- &nbsp;&nbsp;(required) meta tags -->
    <meta charset="utf-8">
    
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.3.1/css/bootstrap.min.css">
    
    <style type="text/css">
    	body {
		  padding-top: 40px;
		  padding-bottom: 40px;
		  background-color: #eee;
		}
        .container {
            padding-top: 15px;
            padding-bottom: 15px;
        }
        .card {
            background: #fff;
            border-radius: 16px;
            box-shadow: 6px 0px 26px 0 rgba(0,0,0,.1);
            width: 100%;
            display: table;
            min-height: 84vh;
            overflow: hidden;
        }
        .card-heading {
            background-image: url(http://anofie.com/themes/default/img/anofie-installer.jpg);
            background-repeat: no-repeat;
            background-size: cover;
            background-position-x: left;
            display: table-cell;
            width: 50%;
            position: relative;
        }
        .card-body {
            padding-bottom: 60px;
        }
        .instructions {
            color: #fff;
            background: rgba(0, 0, 0, 0.5);
            height: 150px;
        }
        .instructions h2 {
            padding-top: 7%;
        }
        footer {
            position: absolute;
            bottom: 0;
            left: 5%;
            color: #fff;
            font-weight: 500;
            font-size: 18px;
        }
        a {
            text-decoration: none !important;
            color: #ffffff !important;
        }
        pre.d-code {
            background: #212121;
            color: #2979ff;
            font-size: 12px;
        }
        pre.d-code strong {
            color: #ffeb3b;
            font-size: 13px;
            font-weight: 400;
        }
    </style>
</head>
<body>

<div class="container">
    <div class="row">
        <div class="col">
            <div class="card">
                <?php if($install_success) { ?> 

                <div class="alert alert-success" role="alert">
                    <h4 class="alert-heading">Well done!</h4>
                    <p>Congratulations! You successfully installed AddChat into your website.</p>
                    <hr>
                    <p class="mb-0">Now, follow the simple steps provided below to finish setup.</p>
                </div>

                <div class="alert alert-light mb-0" role="alert">
                    <p><strong>Go to your project and open the common layout file, mostly the common layout file is the file which contains the HTML & BODY tags.</strong></p>
                </div>

                <ul class="list-group list-group-flush">
                    <li class="list-group-item">
                        1. Copy AddChat CSS code and paste it right before closing <strong>&lt;/head&gt;</strong> tag

    <br><br>
    <pre class="d-code rounded-lg">
    <code data-lang="php">
        <strong>
        &lt;!-- 1. AddChat css --&gt;
        &lt;link href="&lt;?php echo base_url('<?php echo str_replace('/', '', $_POST['assets']) ?>/addchat/css/addchat.min.css') ?&gt;" rel="stylesheet"&gt;
        </strong>      
    &lt;/head&gt;
    </code>
    </pre>

                    </li>
                    <li class="list-group-item">
                        2. Copy AddChat Widget code and paste it right after opening <strong>&lt;body&gt;</strong> tag

    <br><br>
    <pre class="d-code rounded-lg">
    <code data-lang="php">
    &lt;body&gt;
        <strong>
        &lt;!-- 2. AddChat widget --&gt;
        &lt;div id="addchat_app" 
            data-baseurl="&lt;?php echo base_url() ?&gt;"
            data-csrfname="&lt;?php echo $this->security->get_csrf_token_name() ?&gt;"
            data-csrftoken="&lt;?php echo $this->security->get_csrf_hash() ?&gt;"
        >&lt;/div&gt;
        </strong>
    </code>
    </pre>
                        
                    </li>
                    <li class="list-group-item">
                        3. Copy AddChat JS code and paste it right before closing <strong>&lt;/body&gt;</strong> tag

    <br><br>
    <pre class="d-code rounded-lg">
    <code data-lang="php">
        <strong>
        &lt;!-- 3. AddChat JS --&gt;
        &lt;!-- Modern browsers --&gt;
        &lt;script  type="module" src="&lt;?php echo base_url('<?php echo str_replace('/', '', $_POST['assets']) ?>/addchat/js/addchat.min.js') ?&gt;"&gt;&lt;/script&gt;
        &lt;!-- Fallback support for Older browsers --&gt;
        &lt;script nomodule src="&lt;?php echo base_url('<?php echo str_replace('/', '', $_POST['assets']) ?>/addchat/js/addchat-legacy.min.js') ?&gt;"&gt;&lt;/script&gt;
        </strong>

    &lt;/body&gt;
    </code>
    </pre>
                    </li>
                </ul>
                
                <div class="alert alert-light mt-3 mb-0" role="alert">
                    <p class="mb-0"><strong>The final code will look something like below. Finally, visit your website to see some action :)</strong></p>
                </div>

                <ul class="list-group list-group-flush">
                    <li class="list-group-item border-0">

    <pre class="d-code rounded-lg">
    <code data-lang="php">

    &lt;head&gt;

        &lt;!-- Your site other content --&gt;

        <strong>
        &lt;!-- 1. AddChat css --&gt;
        &lt;link href="&lt;?php echo base_url('<?php echo str_replace('/', '', $_POST['assets']) ?>/addchat/css/addchat.min.css') ?&gt;" rel="stylesheet"&gt;
        </strong>
                                
    &lt;/head&gt;

    &lt;body&gt;

        <strong>
        &lt;!-- 2. AddChat widget --&gt;
        &lt;div id="addchat_app" 
            data-baseurl="&lt;?php echo base_url() ?&gt;"
            data-csrfname="&lt;?php echo $this->security->get_csrf_token_name() ?&gt;"
            data-csrftoken="&lt;?php echo $this->security->get_csrf_hash() ?&gt;"
        >&lt;/div&gt;
        </strong>

        &lt;!-- Your site other content --&gt;

        <strong>
        &lt;!-- 3. AddChat JS --&gt;
        &lt;!-- Modern browsers --&gt;
        &lt;script  type="module" src="&lt;?php echo base_url('<?php echo str_replace('/', '', $_POST['assets']) ?>/addchat/js/addchat.min.js') ?&gt;"&gt;&lt;/script&gt;
        &lt;!-- Fallback support for Older browsers --&gt;
        &lt;script nomodule src="&lt;?php echo base_url('<?php echo str_replace('/', '', $_POST['assets']) ?>/addchat/js/addchat-legacy.min.js') ?&gt;"&gt;&lt;/script&gt;
        </strong>

    &lt;/body&gt;

    </code>
    </pre>

                    </li>
                </ul>

                
                
                


                <?php } else { ?>
                <div class="card-heading">
                    <div class="instructions">
                        <h2 class="text-center"><small style="font-size: 12px !important;font-weight: 600 !important;">V1.0</small> AddChat CodeIgniter Lite | Installer</h2>
                        <p style="text-align: center;font-size: 14px;"><strong><em>IMPORTANT! </em> Please read the installation guide in <a href="http://addchat-docs.classiebit.com" style="color: #00BCD4 !important;" target="_blank">AddChat docs</a></strong></p>
                        
                    </div>
                    <footer>
                        <div>
                        <p> <a href="https://classiebit.com/">product by Classiebit</a></p>
                        </div>
                    </footer>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="offset-md-2 col-md-8">
                            <?php if(is_writable($db_config_path)) { ?>

                            <?php if(isset($message)) {echo '<div class="alert alert-danger alert-dismissible fade show"  role="alert"><strong>'.$message.'</strong><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';} ?>

                            <?php if(INSTALL_MODE === 0) {echo '<div class="alert alert-warning fade show"  role="alert"><strong>IN DEMO MODE</strong></div>';} ?>

                            <form class="form-signin" id="install_form" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">

                                <!-- Database configs -->
                                <h5>Database</h5>
                                <hr>

                                <div class="form-group">
                                    <label for="hostname">Hostname<small >&nbsp;&nbsp;(required)</small></label>
                                    <input type="text" id="hostname" placeholder="e.g localhost" class="form-control form-control-lg" name="hostname" aria-describedby="hostnameHelp" autocomplete="false" &nbsp;&nbsp;(required)="" />
                                    <small id="hostnameHelp" class="form-text text-muted">Enter Mysql hostname.</small>
                                </div>
                                
                                <div class="form-group">
                                    <label for="username">Username<small >&nbsp;&nbsp;(required)</small></label>
                                    <input type="text" id="username" placeholder="e.g root" class="form-control form-control-lg" name="username" aria-describedby="usernameHelp" autocomplete="false" &nbsp;&nbsp;(required)=""/>
                                    <small id="usernameHelp" class="form-text text-muted">Enter Mysql username.</small>
                                </div>
                                
                                <div class="form-group">
                                    <label for="password">Password</label>
                                    <input type="password" id="password" placeholder="e.g *******" class="form-control form-control-lg" name="password" aria-describedby="passwordHelp" autocomplete="false"/>
                                    <small id="passwordHelp" class="form-text text-muted">Enter Mysql password</small>
                                </div>
                                
                                <div class="form-group">
                                    <label for="database">Database<small >&nbsp;&nbsp;(required)</small></label>
                                    <input type="text" id="database" placeholder="e.g example_db" class="form-control form-control-lg" name="database" aria-describedby="databaseHelp" autocomplete="false"/>
                                    <small id="databaseHelp" class="form-text text-muted">Enter new database name.</small>
                                </div>

                                <br><br>

                                <!-- Assets configs -->
                                <h5>Assets</h5>
                                <hr>

                                <div class="form-group">
                                    <label for="assets">Assets Folder Path<small >&nbsp;&nbsp;(required)</small></label>
                                    <input type="text" id="assets" placeholder="e.g assets" class="form-control form-control-lg" name="assets" aria-describedby="assetsHelp" autocomplete="false" value="assets"/>
                                    <small id="assetsHelp" class="form-text text-muted">Enter path to assets folder.</small>
                                </div>

                                <br><br>

                                <!-- Config configs -->
                                <h5>Config</h5>
                                <hr>

                                <div class="form-group">
                                    <label for="config">Config Folder Path<small>&nbsp;&nbsp;(required)</small></label>
                                    <input type="text" id="config" placeholder="e.g application/config" class="form-control form-control-lg" name="config" aria-describedby="configHelp" autocomplete="false" value="application/config"/>
                                    <small id="configHelp" class="form-text text-muted">Enter path to config folder.</small>
                                </div>

                                <div class="form-group">
                                    <label for="session_user_id">Logged-in user-id Session Key<small>&nbsp;&nbsp;(required)</small></label>
                                    <input type="text" id="session_user_id" placeholder="e.g user_id" class="form-control form-control-lg" name="session_user_id" aria-describedby="session_user_idHelp" autocomplete="false" value="user_id"/>
                                    <small id="session_user_idHelp" class="form-text text-muted">Enter the $_SESSION variable key name in which your application stores the logged-in user id e.g $_SESSION['user_id'] then the key is 'user_id'</small>
                                </div>

                                <br><br>

                                <!-- Application configs -->
                                <h5>Application</h5>
                                <hr>

                                <div class="form-group">
                                    <label for="controllers">Controllers Folder Path<small >&nbsp;&nbsp;(required)</small></label>
                                    <input type="text" id="controllers" placeholder="e.g application/controllers" class="form-control form-control-lg" name="controllers" aria-describedby="controllersHelp" autocomplete="false" value="application/controllers"/>
                                    <small id="controllersHelp" class="form-text text-muted">Enter path to controllers folder.</small>
                                </div>

                                <div class="form-group">
                                    <label for="libraries">Libraries Folder Path<small >&nbsp;&nbsp;(required)</small></label>
                                    <input type="text" id="libraries" placeholder="e.g application/libraries" class="form-control form-control-lg" name="libraries" aria-describedby="librariesHelp" autocomplete="false" value="application/libraries"/>
                                    <small id="librariesHelp" class="form-text text-muted">Enter path to libraries folder.</small>
                                </div>

                                <div class="form-group">
                                    <label for="language">Language Folder Path<small >&nbsp;&nbsp;(required)</small></label>
                                    <input type="text" id="language" placeholder="e.g application/language/english" class="form-control form-control-lg" name="language" aria-describedby="languageHelp" autocomplete="false" value="application/language/english"/>
                                    <small id="languageHelp" class="form-text text-muted">Enter path to <strong>English</strong> Language folder.</small>
                                </div>
                                
                                <br>
                                <button type="submit" id="submit" class="btn btn-primary btn-lg btn-block">Install</button>

                            </form>
                            <?php } else { ?>
                            <br><br><br><br>
                            <div class="alert alert-danger" role="alert"><strong>Please make sure that your project folders have correct write permissions. </strong></div>
                            <?php } ?>
                        </div>
                    </div>
                </div>
                <?php } ?>
            </div>
        </div>
    </div>
</div>

    
</body>
</html>