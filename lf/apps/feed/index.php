<?php

$sql = "SELECT * FROM io_threads";
$this->db->query($sql);
$blog = $this->db->fetchall();

$sql = "SELECT * FROM lf_gallery WHERE album = 'Actors'";
$this->db->query($sql);
$actors = $this->db->fetchall();

$sql = "SELECT * FROM lf_gallery WHERE album = 'Crew'";
$this->db->query($sql);
$crew = $this->db->fetchall();

/*

foreach blog, 
	square with image and title
	date overlay on hover

*/

echo '<h2>Blog Posts</h2>';

// Print blog posts
foreach($blog as $post)
{
	preg_match('/"([^"]+.jpg)"/', $post['content'], $match);
	
	$bg = 'http://dev4.bioshazard.com/littlefoot/lf/apps/feed/keep/1692321469_635c0fc79e_b-288x221.jpg';
	if(isset($match[1]))
		$bg = $match[1];

	?>
	<div style="width: 200px; background: #000; overflow: hidden; margin-bottom: 10px; float: left; margin-right: 10px;" class="overlay" >
		<a href="%baseurl%blog/view/<?=$post['id'];?>/">
			<p style="z-index: 100; font-weight: bold; position: absolute; float: left; color: white; width: 130px; font-size: 15px; display: none; background: url(%relbase%lf/media/transparent.png); width: 200px; height: 200px;">
				<span style="display: block; padding: 20px;">
					<?=$post['title'];?><br /> 
					<?php 
						$desc = preg_replace('/<[^>]+>/', '', $post['content']); 
						
						echo substr($desc, 0, 50);
						if(strlen($desc) > 50) 
							echo "...";
						else if($desc == '')
							echo "-No comment-";
							
						echo '<br />'.date('M d, Y',strtotime($post['date'])); 
					?>
				</span>
			</p>
		</a>
		<div style="height: 200px">
			<span style="background: url(%relbase%lf/media/transparent.png); display: block; position: absolute; z-index: 99; float: left; color: white; padding: 20px; width: 160px; font-size: 16px;">
				<?=$post['title'];?>
			</span>
			<img height="200px" src="<?=$bg;?>" alt="" />
		</div>
	</div>
	<style type="text/css">
		.overlay:hover p { display: block !important; }
		.overlay:hover div span { display: none !important; }
	</style>
	<?php

}
?><div style="clear: both"></div><?php
echo '<h2>Actors</h2>';

// Print blog posts
foreach($actors as $actor)
{
	$bg = '%relbase%lf/media/gallery/'.strtolower(str_replace(' ', '_', $actor['album'])).'/'.$actor['img'];

	?>
	<div style="width: 200px; background: #000; overflow: hidden; margin-bottom: 10px; float: left; margin-right: 10px;" class="overlay" >
		<a href="%baseurl%actors/">
			<p style="z-index: 100; font-weight: bold; position: absolute; float: left; color: white; width: 130px; font-size: 15px; display: none; background: url(%relbase%lf/media/transparent.png); width: 200px; height: 200px;">
				<span style="display: block; padding: 20px;">
					<?=$actor['title'];?><br />
				<?php 
					
					$desc = preg_replace('/<[^>]+>/', '', $actor['desc']); 
					
					echo substr($desc, 0, 40);
					if(strlen($desc) > 40) 
						echo "...";
					else if($desc == '')
						echo "No comment";
				?>
				</span>
				</p>
		</a>
		<div style="height: 200px">
			<span style="background: url(%relbase%lf/media/transparent.png); display: block; position: absolute; z-index: 99; float: left; color: white; padding: 20px; width: 160px; font-size: 16px;">
				<?=$actor['title'];?>
			</span>
			<img height="200px" src="<?=$bg;?>" alt="" />
		</div>
	</div>
	<style type="text/css">
		.overlay:hover p { display: block !important; }
		.overlay:hover div span { display: none !important; }
	</style>
	<?php

}
?><div style="clear: both"></div><?php
echo '<h2>Crew</h2>';

// Print blog posts
foreach($crew as $member)
{
	$bg = '%relbase%lf/media/gallery/'.strtolower(str_replace(' ', '_', $member['album'])).'/'.$member['img'];

	?>
	<div style="width: 200px; background: #000; overflow: hidden; margin-bottom: 10px; float: left; margin-right: 10px;" class="overlay" >
		<a href="%baseurl%crew/">
			<p style="z-index: 100; font-weight: bold; position: absolute; float: left; color: white; width: 130px; font-size: 15px; display: none; background: url(%relbase%lf/media/transparent.png); width: 200px; height: 200px;">
				<span style="display: block; padding: 20px;">
					
				<?=$member['title'];?><br />
				<?php 
					
					$desc = preg_replace('/<[^>]+>/', '', $member['desc']); 
					
					echo substr($desc, 0, 40);
					if(strlen($desc) > 40) 
						echo "...";
					else if($desc == '')
						echo "No comment";
				?>
				</span>
				</p>
		</a>
		<div style="height: 200px">
			<span style="background: url(%relbase%lf/media/transparent.png); display: block; position: absolute; z-index: 99; float: left; color: white; padding: 20px; width: 160px; font-size: 16px;">
				<?=$member['title'];?>
			</span>
			<img height="200px" src="<?=$bg;?>" alt="" />
		</div>
	</div>
	<style type="text/css">
		.overlay:hover p { display: block !important; }
		.overlay:hover div span { display: none !important; }
	</style>
	<?php

}
?><div style="clear: both"></div>