<?php
*
unction build_apps($links, $save)

$html = '';
while($row = $this->db->fetch($links))
{
	if($row['ini'] == '') $row['ini'] = 'none';
	if($row['include'] == '%') $row['include'] = 'All';
	$html .= '
		<tr>
		<td>
		[<a onclick="return confirm(\'Do you really want to delete this?\');" href="%baseurl%menu/rm/link/'.$row['id'].'/">x</a>] '.$row['app'].' </td><td> '.$row['ini'].' </td><td> '.$row['section'].' </td><td> '.$row['recursive'].'</td></tr>
	';
}
return $html;
 */
function build_apps($links, $save)

$old = NULL;
$html = '
	<table width="100%">
';
while($row = $this->db->fetch($links))
{
	if($row['include'] == '%') $row['include'] = 'All';
	
	if($row['section'] != $old) 
	{ 
		$old = $row['section']; 
		$html .= '
			
			<tr>
				<td colspan="3" style="background: #DDD; padding: 5px; text-align:center"><strong>'.$old.'</strong></td>
			</tr>			
			<tr style="text-align: left">
				<th>rm</th>
				<th>App</th>
				<th>ini</th>
			</tr>
		'; 
	}
	
	$html .= '
		
		<tr>
			<td>[<a onclick="return confirm(\'Do you really want to delete this?\');" href="%baseurl%menu/rm/link/'.$row['id'].'/">x</a>]</td>
			<td>'.$row['app'].'</td>
			<td>'.$row['ini'].'</td>
		</tr>
	';
			// R: '.$row['recursive'].'
}
return $html.'</table>';

?>