<?php 
echo '<h2>
	<a href="%appurl%">Bugtrack</a> / 
	<a href="'.$this->instbase.'">'.$post['project'].'</a> / 
	<a href="'.$this->instbase.'cat/'.$post['category'].'/">'.$post['category'].'</a> / 
	<a href="'.$this->instbase.'view/'.$post['id'].'/">'.$post['title'].' ('.$post['id'].')</a>
</h2>';
?>

<div id="categories">
	<h4>Categories</h4>
	<form action="%appurl%addcategory/" method="post"><input type="text" name="category" placeholder="New category" /></form>
	<ul>
	<?php if($categories)
		foreach($categories as $category)
		{
			$category = $category['category'];
			
			$active = '';
			if($category == $post['category']) $active = ' class="active"';
			
			echo '<li'.$active.'>[<a href="%appurl%rmcategory/'.urlencode($this->inst).'/'.urlencode($category).'/">x</a>] 
				<a href="%appurl%'.urlencode($this->inst).'/cat/'.urlencode($category).'/">'.$category.'</a></li>';
		}
	?>
	</ul>
</div>

<style type="text/css">
	#note { padding-right: 20px; }
	#note .title { padding: 5px; font-size: 22px; width: 100%; margin-top: 10px; }
	#note .content { border: 1px solid #000; margin: 10px 0; padding: 10px; }
	#note .edit { border: 1px solid #000; margin: 10px 0; padding: 10px; width: 100%; }
	#postlist ul { margin: 0; padding: 0; }
	#postlist ul li { border-left: dotted 1px #000; padding: 5px 10px; margin: 5px 0;}
	#postlist > ul > li { border: none; padding-left: 0; }
</style>

<div id="note">

	<?php if(isset($vars[2]) && $vars[2] == 'edit') { 
	
	// Ticket status
	$modes = array('open', 'closed');
	$options = '';
	foreach($modes as $status)
	{
		$selected = '';
		if($status == $post['status'])
			$selected = ' selected="selected"';
		
		$options .= '<option'.$selected.' value="'.$status.'">'.$status.'</option>';
	}
	
	$cat_options = '';
	foreach($categories as $category)
	{
		$selected = '';
		if($post['category'] == $category['category'])
			$selected = ' selected="selected"';
		
		$cat_options .= '<option'.$selected.' value="'.$category['category'].'">'.$category['category'].'</option>';
	}
	
	?>
	
	<form action="<?php echo $this->instbase.'view/'.$post['id'].'/'; ?>" method="post">
		<input type="submit" value="Update" /> 
		Status: <select name="status" id="">
			<?php echo $options; ?>
		</select>
		Category <select name="category" id=""><?php echo $cat_options; ?></select> or <input type="text" placeholder="New Category" name="newcat" />
		<input class="title" type="text" name="title" value="<?php echo $post['title']; ?>" />
		<textarea name="content" class="edit"><?php echo $post['content']; ?></textarea>
	</form>
	
	<?php } else { ?>
	<p>
		<?php echo '[<a onclick="return confirm(\'Do you really want to delete this note?\');"  href="%appurl%'.urlencode($post['project']).'/rm/'.$post['id'].'/">x</a>] '; ?> 
		[<a href="<?=$this->instbase;?>view/<?=$post['id'];?>/edit/">edit</a>] 
		Status: <?=$post['status'];?>
	</p>
	<p class="content"><strong><?php echo $post['user']; ?></strong> at <?php echo date("F j, Y g:i a", strtotime($post['date'])); ?><br />	
	<?php echo nl2br($post['content']); ?></p>
	<?php } ?>
	
	
	<?php
		
		
/*
	$cat = '';
	foreach($posts as $post)
	{
		if($cat != $post['category'])
		{
			$cat = $post['category'];
			echo '<h4>'.$cat.'</h4>';
		}
		
		echo '<li>
			[<a onclick="return confirm(\'Do you really want to delete this note?\');"  href="%appurl%'.urlencode($post['project']).'rmnote/'.$post['id'].'/">x</a>] 
			<a href="%appurl%'.urlencode($post['project']).'/view/'.$post['id'].'/">'.$post['title'].'</a>
		</li>';
	}*/
	?>
	
	
	
	<?php echo $comments; ?>
</div>