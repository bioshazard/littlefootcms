<?php

//check for crucial php settings
if(version_compare(phpversion(), '5.4.0', '<')
&& get_magic_quotes_gpc()) // magic quotes (only affects <5.4)
        $warnings[] = '[<a target="_blank" href="https://www.google.com/search?q=how+to+disable+magic+quotes">Fix it</a>] Magic quotes is ENABLED';

if(version_compare(phpversion(), '5.4.0', '<')
&& ini_get('short_open_tag') == false) // php short tags (only affects <5.4)
        $warnings[] = '[<a target="_blank" href="https://www.google.com/search?q=how+to+enable+php+short+tags">Fix it</a>] Short tags is DISABLED ';

if(is_file('config.php'))
{
	include 'config.php';
	$dbconn = new Database($db);

	if($dbconn->error != '') $errors = $dbconn->error;
	else
	{
		if(!$dbconn->fetch("select * from lf_settings limit 1"))
			$msg .= 'I found a config file, but can\'t seem to connect to your database. Please verify the contents of lf/config.php or try and reconfigure the credentials.';
		else
			$msg .= 'The config file exists, but the database seems to be missing crucial data in at least lf_settings.';
	}
}

$host = isset($_POST['host']) ? $_POST['host'] : 'localhost';
$user = isset($_POST['user']) ? $_POST['user'] : '';
$dbname = isset($_POST['dbname']) ? $_POST['dbname'] : '';
	

?>
<html class="lf">
	<head>
		<link href="http://fonts.googleapis.com/css?family=Open+Sans" rel="stylesheet" type="text/css">
		<style type="text/css">
			<?php readfile(ROOT.'system/lib/lf.css');?>
		</style>
	</head>
	<body>
		<form action="?" class="wide_container" method="post">
			<?php 
				if(isset($msg))
					echo '<div class="warning martop">'.$msg.'</div>';
					
				if(isset($errors))
					foreach($errors as $error)
						echo '<div class="martop">'.$error.'</div>';
			?>
			<h1>Install Littlefoot</h1>
			<h2>Database Access</h2>
			<ul class="vlist">
				<li>Host: <input type="text" value="<?=$host;?>" name="host" value="localhost" /></li>
				<li>User: <input type="text" value="<?=$user;?>"name="user" placeholder="Database Username" /></li>
				<li>Pass: <input type="password" name="pass" placeholder="User's Password" /></li>
				<li>Database Name: <input type="text" value="<?=$dbname;?>" name="dbname" placeholder="Name of database" /></li>
				<?php if(is_file('config.php')): ?>
				<li><label for="">Overwrite Config File:</label> <input class="check" type="checkbox" name="overwrite" checked="checked" /></li>
				<?php endif; ?>

				<?php if(is_file('config.php')): ?>
				<li><label for="">Re-install Base Data:</label> <input class="check" type="checkbox" name="data" /></li>
				<?php else: ?>
				<li><label for=""><input class="check" type="checkbox" name="data" checked="checked" /> Install Base Data (uncheck this if you are just remaking a lost config):</label></li>
				<?php endif; ?>
			</ul>
			<h2>Default Credentials</h2>
			<ul class="vlist">
				<li>Username: <strong>admin</strong></li>
				<li>Password: <strong>pass</li>
			</ul>
			<button class="green">Install</button>
			<a class="blue button marbot" target="_blank" href="http://littlefootcms.com/">View User Guide</a>
		</form>
	</body>
</html>




<?php /* ?>
<html>
        <head>
                <title>LittlefootCMS Installer</title>
                <style type="text/css">
                        body { font-family: Arial }
                        div#installer { width: 500px; border: 1px solid #000; padding: 20px; margin: 100px auto 0}
                        h1 { text-align: center; }
                        h2 { margin: 10px 0; }
                        ul { list-style: none; margin: 0; padding: 0; }
                        li label { display: block; font-size: 12px; color: #333 }
                        input { width: 100%; border: 1px solid #999; padding: 10px; margin-bottom: 5px; }
                        input.check { width: auto; }
                        input.submit { padding: 10px 0; font-size: 20px; margin-top: 20px; }
                        form { margin: 0}
                        #warning_check { width: auto; }

                        .ini_warning {
                                background: #AAAADD;
                                border: medium solid #0000FF;
                                color: #3333CC;
                                display: block;
                                font-weight: bold;
                                margin: 10px 0;
                                padding: 10px;
                        }

                        .ini_error {
                                background: #DDAAAA;
                                border: medium solid #FF0000;
                                color: #CC3333;
                                display: block;
                                font-weight: bold;
                                margin: 10px 0;
                                padding: 10px;
                        }
        </style>
        </head>
        <body>
                <div id="installer">
                        <h1>LittlefootCMS Installer</h1>
                        <form action="" method="post">
								<?php if(isset($msg)) {
									echo '<div class="ini_warning">'.$msg.'</div>';
								} ?>
								
								<?php

                                if(isset($errors))
                                {
                                        foreach($errors as $error)
                                                echo '<div class="ini_error">'.$error.'</div>';
                                }
                        ?>
								
                                <h2>Configure database credentials below:</h2>
                                <ul>
                                        <li><label for="">Hostname:</label> <input type="text" name="host" value="localhost" /></li>
                                        <li><label for="">Username:</label> <input type="text" name="user"/></li>
                                        <li><label for="">Password:</label> <input type="password" name="pass"/></li>
                                        <li><label for="">Database Name:</label> <input type="text" name="dbname" /></li>

                                        <?php if(is_file('config.php')): ?>
                                        <li><label for="">Overwrite Config File:</label> <input class="check" type="checkbox" name="overwrite" checked="checked" /></li>
                                        <?php endif; ?>

                                        <?php if(is_file('config.php')): ?>
                                        <li><label for="">Re-install Base Data:</label> <input class="check" type="checkbox" name="data" /></li>
                                        <?php else: ?>
                                        <li><label for="">Install Base Data (uncheck this if you are just remaking a lost config):</label> <input class="check" type="checkbox" name="data" checked="checked" /></li>
                                        <?php endif; ?>
                        
                                </ul>
<!--                            <h2>Configure user credentials:</h2>
                                <ul>
                                        <li>Admin Username: <input type="text" name="adminuser" value="admin" /></li>
                                        <li>Admin Password: <input type="text" name="adminpass" /></li>
                                </ul> -->
                                <?php
                                       if(isset($warnings))
                                       {
                                             echo '<h3>Warning</h3>';
                                                        echo 'Ignore warnings and install anyway <input type="checkbox" name="warning_check" id="warning_check" />';
                                                        foreach($warnings as $warning)
                                                                echo '<div class="ini_warning">'.$warning.'</div>';
                                                }
                                        ?>

										
								<h2>Default user credentials</h2>
								<p>Username: <strong>admin</strong><br />
								Password: <strong>pass</strong></p>
                                
								<input class="submit" type="submit" value="Install LittlefootCMS" />
                        </form>
                </div>
        </body>
</html>*/