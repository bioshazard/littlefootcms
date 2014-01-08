<h2>Site Manager</h2>

<script type="text/javascript">

$(document).ready(function() {

	// Expand / Collapse
	$('#actions li ol').parent().prepend('<a href="#" class="toggle">+</a> ');
	$('#actions .toggle').click(function() {
		$(this).parent().find('>ol').toggle('fast');
	});

	$('#actions li ol').hide();
	
	$.each($('#actions li ol'), function ( key, value ) {
		if($(value).find('.selected').length > 0)
		{
			$(this).show();
		}
	});

});
</script>

<div id="actions">
        <h3>Navigation</h3>
        <p>Manage your website's nav menu. Click on the nav item title to edit it, click [x] to delete it, and click (Admin) to manage the associated app.</p>
        <?php
                if(isset($nav['html']))
                {
                        echo $nav['html'];
                }
                else
                        echo '<p>- No nav set -</p>';
        ?>
        <h3>Hidden</h3>
        <p>This works just like the nav menu manager above, but these nav items will be hidden from nav menu of your website. This feature is useful for hiding apps like /signup, /secret-blog</p>
        <?php
                if(isset($hooks['html']))
                        echo $hooks['html'];
                else
                        echo '<p>- No hidden nav items set -</p>';
        ?>
</div>

<div id="appgallery">
        <h3>App Gallery (<a href="%appurl%download/">Store</a>)</h3>
        <p>Install apps packaged as .zip files or download apps from the store. Click on the name of an app to attach it to the website.</p>

        <ul class="applist">
		<li>
			<form enctype="multipart/form-data" action="%appurl%install/" method="post">
				<input type="hidden" name="MAX_FILE_SIZE" value="55000000" />
				<h4>Upload:</h4>
				<input type="file" name="app" />
				<br />
				<?=$install;?>
			</form>
		</li>
        <?php
                foreach(scandir($pwd) as $file)
                {
                        if($file == '.' || $file == '..') continue;

                        $app = $pwd.'/'.$file;

                        if(is_dir($app)):
                                ?>
                                <li>
                                        <div class="right_header">
                                                <a onclick="return confirm('Do you really want to delete this?');" href="%appurl%delapp/<?=$file;?>/">x</a>
                                        </div>
                                        <div class="left_header">
                                                <a href="%appurl%linkapp/<?=$file;?>/"><?=$file;?></a>
                                        <div>
										<div style="clear:both"></div>
                                </li>
                        <?php

                        endif;
                        if(isset($vars['app']) && $vars['app'] == $file)
                                $save = $file;
                }
        ?>
        </ul>
</div>