<ul class="breadcrumb">
	<li class="active">Forums</li>
</ul>

<table class="table" width="100%">
	<tr>
		<th>Subject</th>
		<th>Threads</th>
		<th>Comments</th>
	</tr>
<?php foreach($boards as $board): ?>
	<tr>
		<td width="85%"><a href="%appurl%board/<?=$board['id'];?>/"><?=$board['title'];?></a></td>
		<td>13</td>
		<td>1337</td>
	</tr>
<?php endforeach; ?>
</table>