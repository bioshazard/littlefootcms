<?=$this->partial('acl.header', array('active' => 'user'));?>


<div class="row">
	<form action="%appurl%add/user" method="post">
		<div class="col-2">
			<select name="affects" id="">
				<option value="">Select User/Group</option>
				<optgroup label="Groups">
					<?php foreach($groups as $group): ?>
					<option value="<?=$group;?>"><?=$group;?></option>
					<?php endforeach; ?>
				</optgroup>
				<optgroup label="Users">
					<?php foreach($users as $id => $user): ?>
					<option value="<?=$id;?>"><?=$user;?></option>
					<?php endforeach; ?>
				</optgroup>
			</select>
		</div>
		<div class="col-2">
			<select name="action" id="">
				<option value="">Select Nav</option>
				<?php foreach($actions[1] as $action): ?>
				<option value="<?=$action;?>">/<?=$action;?></option>
				<?php endforeach; ?>
			</select>
		</div>
		<div class="col-2">
			<input type="text" name="appurl" placeholder="(optional) app url" />
		</div>
		<div class="col-2">
			<input type="radio" name="perm" value="1" checked />Allow
			<input type="radio" name="perm" value="0" />Deny
		</div>
		<div class="col-2">
			<button type="submit" class="green">Add New</button>
		</div>
	</form>
</div>


<?php if($this->hasnotice()): ?>
<div class="notice marbot"><?=$this->notice();?></div>
<?php endif; ?>

<table class="table">
	<tr class="gray light">
		<th>Action</th>
		<th>Permission</th>
		<th>Affects</th>
		<th>Edit</th>
		<th>Delete</th>
	</tr>
	<?php
		if($acls)
		foreach($acls as $acl):
		
		if(isset($users[$acl['affects']]))
			$acl['affects'] = 'User / '.$users[$acl['affects']];
		else
			$acl['affects'] = 'Group / '.$acl['affects'];
	?>
	<tr class="text-center">
		<td><?=$acl['action'];?></td>
		<td><?=$acl['perm']?'Allow':'Deny';?></td>
		<td><?=$acl['affects'];?></td>
		<td>Edit</td>
		<td><a <?=jsprompt();?> href="%appurl%rm/user/<?=$acl['id'];?>" class="x">Delete</a></td>
	</tr>
	<?php endforeach; ?>
</table>