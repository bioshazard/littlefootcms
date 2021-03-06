<h2>Sitemap Manager: <?php echo isset($save['label']) ? $save['label'] : 'All Pages'; ?></h2>
<div id="actions">
	<h3>Navigation</h3>
	<p>Navigation items are used to link apps to user requests in the URL and display their output in the selected skin.</p>
	<p>Nav from item: manage admin of (app)</p>
	<?php
		if(isset($nav['html']))
		{
			echo $nav['html'];
		}
		else
			echo '<p>- No nav set -</p>';
	?>
	<h3>Hidden</h3>
	<p>Hidden nav items are used when you want to add a function, but don't want it on the nav menu.</p>
	<?php
		if(isset($hooks['html']))
			echo $hooks['html'];
		else
			echo '<p>- No hidden nav items set -</p>';
	?>
</div>

<div style="float: left; width: 280px; padding: 5px; ">
<p>This tool can be used to create new navigation items. If the position of the item is set to 0, it is registered as url link, but does not show on the main nav menu</p>

<h3>
	<?php if($save == '') { $action = 'create'; ?>
		Create a new navigation item
	<?php } else { $action = 'update'; ?>
		Update Item
	<?php } ?>
</h3>

<form action="%appurl%<?=$action;?>/menu/" method="post">
	<ul>
		<li>
			Path: <select name="parent">
				<optgroup label="Select Base">
					<option value="-1">domain.com</option>
					<?=$nav['select'];?>
				</optgroup>
				</select>
			/ <input type="text" name="alias"  style="width: 75px;" value="<?php if(isset($save['alias'])) echo $save['alias']; ?>"/>
		</li>
			<li>Page Title: <input type="text" name="title" value="<?php if(isset($save['title'])) echo $save['title']; ?>" /></li>
		<li>Nav Label: <input type="text" name="label" value="<?php if(isset($save['label'])) echo $save['label']; ?>" /></li>
		<li>
			Position: <input type="text" name="position"  style="width: 25px;" value="<?php if(isset($save['position'])) echo $save['position']; else echo 1; ?>" /> 
			Template: 
				<select name="template">
					<?=$template_select;?>
				</select>
			App? <input type="checkbox" name="app" <?php if(isset($save['app']) && $save['app'] == 0) echo ''; else echo 'checked="checked"'; ?> /> 
			
		</li>
			<li><?php if(isset($save['id'])) echo '<input type="hidden" name="id" value="'.$save['id'].'">'; ?><input type="submit" value="<?=ucfirst($action);?>" /> <?php if(isset($save['label'])) echo '( <a href="%baseurl%menu/view/">Deselect</a> )';?> </li>
	</ul>
</form>

<h4>Linked Apps</h4>
<p>Below is a list of apps currently linked to <?php echo isset($save['label']) ? 'the selected item on the right.' : 'all pages.' ;?></p>
<ul>
	<?=$apps;?>
</ul>

<h4>Link new app</h4>
<form action="%baseurl%menu/newapp/" method="post">
	<ul>
		<li>App: <?=$app_list;?> <input type="submit" value="New Link ..." />	<input type="hidden" name="include" value="<?php echo isset($save['id']) ? $save['id'] : '%' ;?>" /> </li>
	</ul>
</form>
/div>