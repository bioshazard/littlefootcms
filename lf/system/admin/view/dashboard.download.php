<h2><a href="%appurl%">Apps</a> / Download</h2>
<div class="row">
	<div class="col-6 pull-right">
		<p>Applications with a link can be installed. Those that are not links are already installed.</p>
	</div>
	<div class="col-6">
		<ul class="efvlist">
		<?php foreach($apps as $app => $ignore): ?> 	
			<li>
			<?php if(!isset($files[$app])): ?>
				<a href="%appurl%getappfromnet/<?=$app;?>/"><?=$app;?></a>
			<?php else: ?>
				<?=$app;?> [<a href="%appurl%getappfromnet/<?=$app;?>/update/">Update</a>]
			</li>
			<?php endif; ?>
		 <?php endforeach; ?>
		</ul>
	</div>
</div>