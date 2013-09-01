<?php

function since($timestamp)
{
	$timestamp = time() - $timestamp;
	$ret = '';
	
	if($timestamp > 86400*30)
		$ret .= (int)($timestamp / (86400*30)) . " months";
	else if($timestamp > 86400)
		$ret .= (int)($timestamp / 86400) . " days";
	else if($timestamp > 3600)
		$ret .= (int)($timestamp / 3600) . " hours";
	else if($timestamp > 60)
		$ret .= (int)($timestamp / 60) . " minutes";
	else
		$ret .= $timestamp . " seconds";
	
	$ret .= " ago";
	
	return $ret;
}

?>
<h2>Blog</h2>
<?php if($this->request->api('me') != 'anonymous' && false): ?>
<form action="%post%" method="post" class="add_thread">
	<textarea name="input"></textarea>
	<input type="hidden" name="access" value="public" />
	<input type="submit" class="submit" value="Create Thread" />
</form>
<?php endif; if(!count($blog)): ?>
<p>No threads to show</p>
<?php else: ?>
<div id="threads">
	<?php $like = array(); foreach($blog as $id => $post): /* loop through blog posts */ ?>
	<div id="thread_<?php echo $id; ?>" class="thread">
		<div class="t_head">
			<?php if($post['owner_id'] == $this->request->api('getuid')): /* show delete button */ ?>
			<a href="%appurl%rmthread/<?php echo $id; ?>/" method="post" class="removethread hrefapi">X</a>
			<?php endif; 
				$like_disp = ''; // Display 'like' button if logged in
				if($this->request->api('me') != 'anonymous')
				{
					$like_disp = '%t_like'.$id.'%';
					$like[] = 't_like'.$id;
				}
			?>
			<h4><?php echo $post['title'] ?></h4>
			<p><?=$post['content'];?></p>
			<br />
			<span class="date">
				<?=' '.$like_disp; ?> +<?=$post['likes'];?> Promotes | 
				<a href="%appurl%view/<?php echo $id; ?>/">View Comments</a> | 
				Posted by <?php echo $post['user'] ?> <?=since(strtotime($post['date']));?>
			</span>			
		</div>
	</div>
	<?php endforeach; ?>
</div>
<?php endif; ?>