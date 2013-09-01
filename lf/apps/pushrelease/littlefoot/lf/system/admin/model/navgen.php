<?php // Littlefoot CMS - Copyright (c) 2013, Joseph Still. All rights reserved. See license.txt for product license information.function build_menu($menu, $edit, $parent = -1, $depth = -1, $prefix = '', $snip = 0){	//echo '<pre>';	$variable1 = NULL;	$nav['select'] = '';		static $release = 0;	$items = $menu[$parent];		$html = '<ol>';	if(count($items) > 0)	foreach($items as $item)	{		if(isset($edit['id']) && $item['id'] == $edit['id'])		{			$snip = 1; // dont take these nav items as options since we have them currently selected			$release = $depth;			$variable1 = $item['id'];		}				$newprefix = $prefix . '/'. $item['alias'];				if(!$snip)		{				$select = '';			if(isset($edit['parent']) && $item['id'] == $edit['parent'])				$select = ' selected="selected"';						$word = str_repeat("/ ", substr_count($prefix, '/')).$item['alias'];			$nav['select'] .= '<option value="'.$item['id'].'" '.$select.'>'.$word.'</option>';		}				$html .= '<li';		if($variable1 == $item['id'])			$html .= ' class="selected"';		$html .= '>';					$apphtml = $item['app'];				//$apphtml .= ' (<a href="%appurl%main/'.$item['id'].'/">Edit</a>)';				if(is_file(ROOT.'apps/'.$item['app'].'/admin.php'))			$apphtml .= ' (<a href="%baseurl%apps/manage/'.$item['app'].'/">Admin</a>)';					// set postion for nav item		if($variable1 == $item['id'])		{			$pos = '<input type="text" name="position"  style="width: 20px;" value="';			//if(isset($save['position'])) 				$pos .= $edit['position']; 			//else $pos .= 1;			$pos .= '" /> ';						$label = 'Label: <input type="text" name="label" value="'.$item['label'].'" />';		}		else 		{			$pos = $item['position'].'. ';			$label = '<a href="%appurl%main/'.$item['id'].'/#nav_'.$item['alias'].'">'.$item['label'].'</a>';		}								if($variable1 == $item['id'])			$html .= '<form action="%appurl%update/" method="post">';					$html .= $pos.'[<a id="nav_'.$item['alias'].'" onclick="return confirm(\'Do you really want to delete this?\');" href="%baseurl%apps/rm/'.$item['id'].'/">x</a>]				'.$label.' - Apps: '.$apphtml;				if($variable1 == $item['id'])			$html .= '%editform%</form>';		/*		if($variable1 == $item['id'])			$html .= ' ( <a href="%baseurl%menu/view/">Deselect</a> )';*/				// if a parent id is set in the array, print the child objects		if(isset($menu[$item['id']]))		{			$output = build_menu($menu, $edit, $item['id'], $depth+1, $newprefix, $snip);			$html .= $output['html'];			$nav['select'] .= $output['select'];		}				$html .= '</li>';		if($release == $depth)			$snip = 0;	}	$html .= '</ol>';		$nav['html'] = $html;	return $nav;}function build_hidden($items, $edit){	//echo '<pre>';	$variable1 = NULL;	$nav['select'] = '';		static $release = 0;		$html = '<ul>';	if(count($items) > 0)		foreach($items as $item)		{			$selected = (isset($edit['id']) && $edit['id'] == $item['id']);			if($selected)			{				$select = '';				if(isset($edit['parent']) && $item['id'] == $edit['parent'])					$select = ' selected="selected"';			}						$html .= '<li';			if($selected)				$html .= ' class="selected"';			$html .= '					>[<a onclick="return confirm(\'Do you really want to delete this?\');" href="%baseurl%apps/rm/'.$item['id'].'/">x</a>]					<a href="%baseurl%menu/view/'.$item['id'].'/">'.$item['label'].'</a>			';						if($selected)				$html .= ' ( <a href="%baseurl%menu/view/">Deselect</a> )';						$html .= '</li>';		}	$html .= '</ul>';	$hidden['html'] = $html;	return $hidden;}?>