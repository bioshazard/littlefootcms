<?php 

/*

admin/apps/

[Actions]

-Write (followed by redirect to referrer)
Add
Remove
App_Admin_POST

-Read
List
App_Admin

*/

class dashboard
{
	private $isadmin;
	private $request;
	private $html;
	private $pwd;
	private $dbconn;
	
	public function __construct($request, $dbconn)
	{
		$this->db = $dbconn;
		$this->request = $request;
		$this->pwd = ROOT.'apps/';
	}
	
	public function main($vars)
	{
		$this->updatenavcache(); // DEBUG
	
		// admin load edit from ini
		include('model/navgen.php');
			$result = $this->db->query("
				SELECT a.*, a.app as isapp, l.id as lid, l.app, l.section, l.ini
				FROM lf_actions a 
				LEFT JOIN lf_links l
					ON l.include = a.id
				ORDER BY ABS(a.position)
			");

			$save = array();
			while($row = mysql_fetch_assoc($result))
			{
				//print_r($row);
				//echo '<br />';
				
				if(isset($vars[1]) && $row['id'] == $vars[1])
					$save = $row;
				
				if($row['position'] == 0)
					$hidden[] = $row;
				else
					$menu[$row['parent']][$row['position']] = $row;
			}

			if(isset($vars[1])) $edit = $vars[1];
			else				$edit = 0;

			if(isset($menu)) 	$nav = build_menu($menu, $save);
			if(isset($hidden))	$hooks = build_hidden($hidden, $save);
			
		include('model/templateselect.php');
		
		if($save != array())
		{
			//$args = '<input type="text" name="ini" />';
			
			$pwd = ROOT.'apps/';
			//if(is_file($pwd.$save['app'].'/args.php'))
			//	include $pwd.$save['app'].'/args.php';
			
			/* -=-=-=-=-=- %EDITFORM% -=-=-=-=-=-*/
			ob_start();
			include 'view/editform.php';
			$html = ob_get_clean();
			
			$nav['html'] = str_replace('%editform%', $html, $nav['html']);
			if(isset($hooks))
				$hooks['html'] = str_replace('%editform%', $html, $hooks['html']);
		}
		
		$pwd = $this->pwd;
		
		$install = extension_loaded('zip') 
			? '<input type="submit" value="Install" /> ('.ini_get('post_max_size').'/'.ini_get('upload_max_filesize').' Upload Limit)'
			: "<strong>Error: PHP Zip Extension missing.</strong>";
		/*
		include 'model/apps_linked.php';
		linked_app();*/
		include('view/apps.view.php');
	}

	public function linkapp($var)
	{	
		if(!isset($var[1])) return 'invalid arguement';
		
		include('model/navgen.php');
			$result = $this->db->query("
				SELECT a.*, a.app as isapp, l.id as lid, l.app, l.section, l.ini
				FROM lf_actions a 
				LEFT JOIN lf_links l
					ON l.include = a.id
				ORDER BY ABS(a.position)
			");

			$save = '';
			while($row = mysql_fetch_assoc($result))
			{
				//print_r($row); echo '<br />';
				
				if(isset($vars[1]) && $row['id'] == $vars[1])
					$save = $row;
				
				if($row['position'] == 0)
					$hidden[] = $row;
				else
					$menu[$row['parent']][$row['position']] = $row;
			}
			
			if(isset($vars[1])) $edit = $vars[1];
			else				$edit = 0;
			
			$adv = false;
			if(isset($var[2]) && $var[2] == 'adv') $adv = true;
			
			if(isset($menu)) 	$nav = build_menu($menu, $save);
			if(isset($hidden))	$hooks = build_hidden($hidden, $save);
			
		// get template vars
		include('model/templateselect.php');
		
		$pwd = $this->request->absbase.'/apps/';
			
		$args = '<input type="text" name="ini" />';
		
		if(is_file($pwd.$var[1].'/args.php'))
			include $pwd.$var[1].'/args.php';
		
		// if the selected app 
		include 'view/linkapp.php';
	}
	
	private function deleteAll($directory, $empty = false)
	{
		if(substr($directory,-1) == "/") {
			$directory = substr($directory,0,-1);
		}

		if(!file_exists($directory) || !is_dir($directory)) {
			return false;
		} elseif(!is_readable($directory)) {
			return false;
		} else {
			$directoryHandle = opendir($directory);
		   
			while ($contents = readdir($directoryHandle)) {
				if($contents != '.' && $contents != '..') {
					$path = $directory . "/" . $contents;
					
					if(is_dir($path)) {
						$this->deleteAll($path);
					} else {
						unlink($path);
					}
				}
			}
		   
			closedir($directoryHandle);

			if($empty == false) {
				if(!rmdir($directory)) {
					return false;
				}
			}
		   
			return true;
		}
	}	
	
	public function rm($vars)
	{
		// get current position/parent
		$current = $this->db->fetch('SELECT position, parent FROM lf_actions WHERE id = '.intval($vars[1]));
		
		if(isset($current['parent']))
		{
			$this->db->query('DELETE FROM lf_actions WHERE id = '.intval($vars[1]));
			$this->db->query('DELETE FROM lf_links WHERE include = '.intval($vars[1]));
			
			// update positions of all item behind the rm'd sibling
			if($current['position'] > 0)
				$this->db->query('UPDATE lf_actions SET position = position - 1 WHERE parent = '.$current['parent'].' AND position > '.$current['position']);
			
			while(true) // find all orphaned nav items and remove them, loop until all are cleared
			{
				$result = $this->db->query('
					SELECT a.id	FROM `lf_actions` a 
					LEFT JOIN lf_actions b ON a.parent = b.id
					WHERE b.id IS NULL AND a.parent != -1
				');
				
				if(mysql_num_rows($result) == 0) break;
				
				$orphans = array();
				while($row = $this->db->fetch($result))
					$orphans[] = $row['id'];
					
				$this->db->query('DELETE FROM lf_actions WHERE id IN ('.implode(',', $orphans).')');
				$this->db->query('DELETE FROM lf_links WHERE include IN ('.implode(',', $orphans).')');
			}
		}
		
		$this->updatenavcache();
		redirect302();
	}
	
	public function delapp($var)
	{
		$success = preg_match('/[a-z]+/', $var[1], $matches);
		
		if(!$success) return 0;
		
		$app = $this->pwd.$matches[0];
		if(is_dir($app))
			$this->deleteAll($app);
		
		redirect302();
	}
	
	public function manage($var)
	{
		// $var[0] = 'manage'
		$app_name = $var[1];
		
		echo '<h2><a href="%appurl%">Apps</a> / <a href="%appurl%manage/'.$app_name.'/">'.ucfirst($app_name).'</a> / Admin / <a href="%appurl%manage/'.$app_name.'/preview/">Preview</a></h2>';
		$var = array_slice($var, 2); // pass the rest of the vars to the admin.php script
		
		$oldvars = $this->request->vars;
		
		$this->request->vars = $var;
		
		// manage
		preg_match('/[a-z0-9]+/', $this->request->action[2], $matches);		
		$app_path = $this->pwd.$matches[0];
		
		$preview = 'admin';
		$admin = true;
		$urlpreview = '';
		if(isset($var[0]) && $var[0] == 'preview') 
		{
			$preview = 'index';
			$admin = false;
			$var = array_slice($var, 1);
			$urlpreview = 'preview/';
		}
		
		ob_start();
		//if(is_file($app_path.'/'.$preview.'.php'))
		//{ 
			$old = getcwd(); chdir($app_path);
			$database = $this->dbconn;
			$this->request->appurl = $this->request->base.'apps/manage/'.$app_name.'/'.$urlpreview;
			
			echo $this->request->loadapp($app_name, $admin, NULL, $var);
			
			//include($preview.'.php');
			chdir($old);
		//}
		
		$this->request->vars = $oldvars;
		return str_replace('%appurl%', '%appurl%manage/'.$app_name.'/'.$urlpreview, ob_get_clean());
	}
	
	public function download($var)
	{
		echo '<h2><a href="%appurl%">Apps</a> / Download</h2>';
		echo '<p>Applications with a link can be installed. Those that are not links are already installed.</p>';
		
		$apps = file_get_contents('http://littlefootcms.com/files/download/apps/apps.txt');
		$apps = array_flip(explode("\n",$apps,-1));
		$files = array_flip(scandir(ROOT.'apps'));
		
		echo '<ul>';
		foreach($apps as $app => $ignore)
		{	
			echo '<li>';
			
			if(!isset($files[$app])) echo '<a href="%appurl%getappfromnet/'.$app.'/">'.$app.'</a>';
			else echo $app;// no updates, handled on the upgrade system. ' [<a href="%appurl%getappfromnet/'.$app.'/update/">Update</a>]';
			echo '</li>';
		}
		echo '</ul>';
	}
	
	public function getappfromnet($vars)
	{
		$apps = file_get_contents('http://littlefootcms.com/files/download/apps/apps.txt');
		$apps = array_flip(explode("\n",$apps,-1));
		
		if(isset($apps[$vars[2]]))
		{
			print_r($apps[$vars[2]]);
			exit();
		}
		else if(isset($apps[$vars[1]]))
		{
			$files = array_flip(scandir(ROOT.'apps'));
			if(isset($files[$vars[1]])) return 'app already downloaded: '.$vars[1];
			
			$file = 'http://littlefootcms.com/files/download/apps/'.$vars[1].'.zip';
			$dest = ROOT.'apps/'.$vars[1].'.zip';
			echo $file.'<br />';
			echo $dest.'<br />';
			
			// download and unzip into apps/
			downloadFile($file, $dest);
			Unzip( ROOT.'apps/', $vars[1].'.zip' );
			unlink($dest);
			
			$this->installsql($vars[1]);
			
		} else echo "App not found: ".$vars[1];
		
		header('HTTP/1.1 302 Moved Temporarily');
		header('Location: '. $_SERVER['HTTP_REFERER']);
		exit();
	}
	
	public function install($vars)
	{
		header('HTTP/1.1 302 Moved Temporarily');
		header('Location: '. $_SERVER['HTTP_REFERER']);
		
		preg_match('/^([_\-a-zA-Z0-9]+)\.(zip|tar\.gz)/', $_FILES['app']['name'], $match);
		
		if($match[2] != 'zip') return;
		//if($_FILES['app']['type'] != 'application/zip') return;
		if($_FILES['app']['size'] > 55000000) return;
				
		$target =  $this->pwd.$match[1];
		
		if(is_dir($target)) return;
		if(!mkdir($target)) return;
		
		if(!move_uploaded_file($_FILES['app']['tmp_name'], $target.'/install.zip')) 
		{ 
			echo "Sorry, there was a problem uploading your file."; 
			return; 
		}
		else
		{
			//echo "The file ". $match[0]. " has been uploaded";
			$zip = zip_open($target.'/install.zip');
			if($zip)
			{
				while ($zip_entry = zip_read($zip)) { 
				
					if(preg_match('/^(.+)\/$/', zip_entry_name($zip_entry), $file))
					{
						if(!mkdir($target.'/'.$file[1]))
						{
							echo "fail";
						}
					}		
					else if(zip_entry_open($zip, $zip_entry, "r"))
					{
						$buf = zip_entry_read($zip_entry, zip_entry_filesize($zip_entry));
						
						$fp = fopen($target.'/'.zip_entry_name($zip_entry), "w");
						fwrite($fp,"$buf");
						zip_entry_close($zip_entry);
						fclose($fp);
					}
				}
				zip_close($zip);
				unlink($target.'/install.zip');
				$this->installsql($match[1]);
			}
		}
		
		exit();
	}
	
	private function installsql($app)
	{
		$sql = ROOT.'apps/'.$app.'/install.sql';
		if(is_file($sql))
		{
			$this->db->import($sql);
			unlink($sql);
		}
	}
	
	public function upgradesql($app)
	{
		$sql = ROOT.'apps/'.$app.'/upgrade.sql';
		if(is_file($sql))
		{
			$this->db->import($sql);
			unlink($sql);
		}
	}
	
	public function create($vars) // nav/item create
	{
		
		if(!isset($_POST['title'])) // if simple post, auto-set other settings
		{
			if($_POST['alias'] == '') $_POST['alias'] = 'Home';
			
			$_POST['title'] = ucwords($_POST['alias']);
			$_POST['label'] = ucwords($_POST['alias']);
			$_POST['position'] = 9999; // it will auto adjust to the last position below
			$_POST['isapp'] = 'off'; // is not an app by default
			$_POST['template'] = 'default';
		}
		
		$vars = $_POST;
		
		/* -=-=-=-=- Add Nav Item -=-=-=-=- */
		$pos = intval($vars['position']);
		
		if($pos != 0)
		{
			$sql = 'SELECT COUNT(id) FROM lf_actions WHERE parent = '.mysql_real_escape_string($vars['parent']).' AND position != 0';
			$result = $this->db->query($sql);
			$row = mysql_fetch_array($result);
			
			if($row[0] >= $pos)
				$this->db->query('UPDATE lf_actions SET position = position + 1 WHERE parent = '.mysql_real_escape_string($vars['parent']).' AND position >= '.$pos);
			else
				$pos = $row[0] + 1;
		}
		
		/*echo '<pre>';
		print_r($vars);
		print_r($_POST);
		print_r($pos);
		echo '</pre>';*/
		
		
		$id = 'NULL';
		$app = $vars['isapp'] == 'on' ? '1' : '0';
		$insert = array(
			"parent"	=> mysql_real_escape_string($vars['parent']),
			"position"	=> $pos,
			"alias"		=> mysql_real_escape_string($vars['alias']),
			"title"		=> mysql_real_escape_string($vars['title']),
			"label"		=> mysql_real_escape_string($vars['label']),
			"app"		=> $app,
			"template"	=> mysql_real_escape_string($vars['template'])
		);
		
		$sql = "
			INSERT INTO 
				lf_actions	( `id`, `".implode('`, `',array_keys($insert))."`)
				VALUES	( ".$id.", '".implode("', '",array_values($insert))."')
		";
		
		//echo $sql;
		//exit();
		
		if(!isset($_POST['section'])) // simple link
		{
			$_POST['section'] = 'content';
		}
		
		$this->updatenavcache();
		
		/* -=-=-=-=- Add Link to Nav -=-=-=-=- */
		$pwd = $this->request->absbase.'/apps';
		$vars = $this->request->post;
		foreach(scandir($pwd) as $file)
		{
			if($file == '.' || $file == '..') 
				continue;

			if(is_file($pwd.'/'.$file.'/index.php'))
				$app_filter[$file] = $file;
		}
		
		if(isset($app_filter[$_POST['app']]))
			$app = $app_filter[$_POST['app']];
		else
			exit();
		
		// link was valid, move on to running the sql
		$this->db->query($sql);
		$id = $this->db->last();
		
		//$recurse = $vars['recursive'] == 'on' ? 1 : 0;
		$insert = array(
			"include"	=> $id,
			"app"		=> $app,
			"ini"		=> mysql_real_escape_string($_POST['ini']),
			"section"	=> mysql_real_escape_string($_POST['section']),
			"recursive"	=> 0
		);
		
		$sql = "
			INSERT INTO 
				lf_links	( `id`, `".implode('`, `',array_keys($insert))."`)
				VALUES	( NULL, '".implode("', '",array_values($insert))."')
		";
		
		$this->db->query($sql);
		
		// redirect them after this completes
		redirect302($this->request->base.'apps/');
	}

	public function update($vars) // nav/item update
	{
		$post = $_POST;
		
		// save, unset ini
		$id = intval($post['id']);
		$ini = mysql_real_escape_string($post['ini']);
		unset($post['id'], $post['ini']);
		if($post['position'] <= 0) 
		{
			$post['position'] = 0;
			$post['parent'] = -1;
		}
		
		//select current children id's and positions
		$old = $this->db->fetch('SELECT position, parent FROM lf_actions WHERE id = '.$id);
		
		// get # of children of destination parent
		$result = $this->db->fetch('SELECT COUNT(id) as count FROM lf_actions WHERE parent = '.mysql_real_escape_string($post['parent']));
		$count = $result['count'];
		
		// handle parent/position updates
		if($post['parent'] != $old['parent']) // parent updated
		{
			if($post['position'] > $count + 1) // cant be further down than last
				$post['position'] = $count + 1;
				
			// make room in destination parent children: update pos++ where pos > dest[pos]
			if($post['position'] != 0)
			$this->db->query("UPDATE lf_actions SET position = position + 1 WHERE parent = ".intval($post['parent'])." AND position >= ".intval($post['position']));
			
			// move into new parent: update parent = dest[parent] where id = old[id]
			$this->db->query("UPDATE lf_actions SET parent = ".intval($post['parent']).", position = ".intval($post['position'])." WHERE id = ".$id);
			
			// close gap left behind: update pos-- where pos > dest[pos]
			if($old['position'] != 0)
			$this->db->query("UPDATE lf_actions SET position = position - 1 WHERE parent = ".$old['parent']." AND position > ".$old['position']);
		}
		else if($post['position'] != $old['position']) // if moving within current siblings
		{
			if($post['position'] > $count) // cant be further down than last
				$post['position'] = $count;
				
			if($old['position'] == 0) // starting from 0
				$this->db->query('
					UPDATE lf_actions SET position = position + 1 
					WHERE parent = '.$old['parent'].' AND position >= '.intval($post['position'])); // make room for new item
					
			else if($post['position'] == 0) // going to 0
				$this->db->query('
					UPDATE lf_actions SET position = position - 1 
					WHERE parent = '.$old['parent'].' AND position > '.intval($old['position'])); // make room for new item
					
			else if($post['position'] < $old['position']) // moving lower
				$this->db->query('
					UPDATE lf_actions SET position = position + 1 
					WHERE parent = '.$old['parent'].' 
					AND position >= '.intval($post['position']).' 
					AND position < '.$old['position']);
					
			else if($post['position'] > $old['position']) // moving higher
				$this->db->query('
					UPDATE lf_actions SET position = position - 1 
					WHERE parent = '.$old['parent'].' 
					AND position <= '.intval($post['position']).' 
					AND position > '.$old['position']);
			
			// move to place
			$this->db->query("UPDATE lf_actions SET position = ".intval($post['position'])." WHERE id = ".$id);
		}

		// This has already been taken care of above
		unset($post['position'], $post['parent']);
	
		// other data updates that only affect the item itself
		if(!isset($post['section']))
		{
			$update = array();
			foreach($post as $key => $var)
				$update[$key] = mysql_real_escape_string($key)." = '".mysql_real_escape_string($var)."'";
			
			// Move the item
			$sql = "UPDATE lf_actions SET ".implode(', ', $update)." WHERE id = ".$id;
			$this->db->query($sql);
			
			// update ini
			$sql = "UPDATE lf_links SET ini = '".$ini."' WHERE include = ".$id;
			$this->db->query($sql);
		}
		else
		{
			/*$update = array();
			foreach($post as $key => $var)
				$update[$key] = mysql_real_escape_string($key)." = '".mysql_real_escape_string($var)."'";*/
				
			$post['app'] = $post['app'] == 'on' ? '1' : '0';
			$update = array(
//				"parent = 	'".mysql_real_escape_string($post['parent'])."'",
//				"position = ".intval($post['position']),
				"alias = 	'".mysql_real_escape_string($post['alias'])."'",
				"title = 	'".mysql_real_escape_string($post['title'])."'",
				"label = 	'".mysql_real_escape_string($post['label'])."'",
				"app = 		'".$post['app']."'",
				"template = '".mysql_real_escape_string($post['template'])."'"
			);
			
			// Move the item
			$sql = "UPDATE lf_actions SET ".implode(', ', $update)." WHERE id = ".$id;
			$this->db->query($sql);
			
			$update = array(
				"ini = 	'".$ini."'",
				"section = 	'".mysql_real_escape_string($post['section'])."'"
			);
			
			// Move the item
			$sql = "UPDATE lf_links SET ".implode(', ', $update)." WHERE include = ".$id;
			
			//echo $sql;
			$this->db->query($sql);
		}
		$this->updatenavcache();
		redirect302();
	}
	
	public function updatenavcache()
	{
		include 'model/apps.navcache.php';
		
		// Grab all possible actions
		$actions = $this->db->fetchall("SELECT * FROM lf_actions WHERE position != 0 ORDER BY ABS(parent), ABS(position) ASC");
		
		// Make a matrix sorted by parent and position
		$menu = array();
		foreach($actions as $action)
			$menu[$action['parent']][$action['position']] = $action;
		
		$nav = build_nav_cache($menu);
		if(!is_dir(ROOT.'cache')) mkdir(ROOT.'cache', 0777, true); // make if not exists
		file_put_contents(ROOT.'cache/nav.cache.html', $nav);
	}
}

?>
