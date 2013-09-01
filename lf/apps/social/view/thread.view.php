<?php

function parsepost($posts, $uid, $parent = 0)
{	
	$items = $posts[$parent];
	$html = '<ul>';
	
	foreach($items as $item)
	{
		$html .= '<li>';
	
		$parent = '';
		if($item['reply'] != 0)
			$parent = '<a href="%appurl%thread/'.$item['thread'].'/#reply_'.$item['reply'].'">Parent</a>';
	
		$x = '';
		if($item['owner'] == $uid)
			$x = '[<a href="%appurl%rm/post/'.$item['id'].'/">x</a>] ';
		
		$html .= $x.'<strong>';
		
		if($item['owner'] != 0)
			$html .= '<a href="%baseurl%profile/'.$item['owner'].'/">';
		
		$html .= $item['user'];
		
		if($item['owner'] != 0)
			$html .= '</a>';
		
		$html .= '</strong> at '.date("F j, Y g:i a",strtotime($item['date'])).' '.$parent.'<a id="reply_'.$item['id'].'" href="#"></a><br />'.$item['content'];
	
		if(isset($posts[$item['id']]))
			$html .= parsepost($posts, $uid, $item['id']);
		
		$html .= '</li>';
	}
	
	$html .= '</ul>';
	return $html;
}

?>

<ul class="breadcrumb">
	<li>
		<a href="%appurl%">Forums</a> <span class="divider">/</span>
	</li>
	<li>
		<a href="%appurl%board/<?=$thread['board'];?>/"><?=$thread['board_title'];?></a> <span class="divider">/</span>
	</li>
	<li class="active"><?=$thread['subject'];?></li>
</ul>






<?php 
	if(isset($this->request->get['success']))
		echo '<div class="alert alert-success"><i class="icon-trash"></i> Message erased <a class="close" data-dismiss="alert" href="#">×</a></div>';
?>
<div id="postlist">
<?=parsepost($posts, $this->request->api('getuid'));?>
</div>




<div class="hero-unit">
<h3>Post Reply</h3><br>
	<form action="%post%add/post/<?=$thread['id'];?>/" method="post">

			<select name="reply">
				<option>Reply to thread</option>
				<?=$options;?>
			</select>
			<br />
		<textarea class="span12" name="msg" id="" rows="6"></textarea>
		
		<div class="form-actions">
			<button type="submit" class="btn btn-primary" value="0">Post</button>
			<button class="btn">Cancel</button>
		</div>
		
	</form>
</div>