<?php
/*
Plugin Name: Joke of the Day Advanced
Plugin URI: http://www.joke-db.com/widgets/wordpress
Description: Places a Joke of the Day on your Wordpress blog. Features include the ability to only show jokes containing a particular keyword, a switch between 'clean' or 'dirty' jokes, as well as an option to change the current joke early if you get tired of it. Jokes are loaded via ajax, so it will never slow down the page load.
Version: 1.2
Author: Andy Corman
Author URI: http://www.joke-db.com
*/

function wp_widget_jotda($args) {

	extract($args);
	$options = get_option('widget_jotda');
	$title = $options['title'];  
	$type = $options['type'];
	$seed = $options['seed'];
	$keywords = $options['keywords'];
	
	if(empty($type)) $type = 'clean';
	if(empty($seed)) $seed = 0; 
	if(empty($keywords)) $keywords = 'all';

	$javascript_src = "http://www.joke-db.com/widgets/src/wp/" . $type . '/' . $keywords . '/' . $seed;
?>
	
	<aside id="jotda" class="widget">
		<?php $title ? print('<h3 class="widget-title">' . $title . '</h3>') : null; ?>
		<p><i id="jotda-output"></i><br />
		<sub id="jotda-source">via: <a href="http://www.joke-db.com/" target="_blank" id="jotda-link" rel="nofollow">The Internet Joke Database</a></sub></p>
	</aside>
	
	<script>
		(function(){
			var script = document.createElement('script');
			script.setAttribute('type', 'text/javascript');
			script.setAttribute('async', 'true');
			script.setAttribute('src', '<?php echo $javascript_src; ?>');
			var target = document.getElementById('jotda-output');
			target.parentNode.insertBefore(script, target);
			
			// If unable to connect to joke server, display error in 2.5 seconds
			setTimeout(function(){
				if(document.getElementById('jotda-output').innerHTML == ''){
					document.getElementById('jotda-output').innerHTML = 'Unable to connect to joke server. Bad connection, or server is currently undergoing maintenance.';
				}
			}, 2500);
		})();
	</script>
	<?php
}

function wp_widget_jotda_control() {
	$options = $newoptions = get_option('widget_jotda');
	if ( $_POST["jotda-submit"] ) {
		$newoptions['title'] = strip_tags(stripslashes($_POST["jotda-title"]));
		$newoptions['type'] = stripslashes($_POST["jotda-type"]);
		$newoptions['keywords'] = stripslashes($_POST["jotda-keywords"]);
		if($_POST["jotda-changejoke"] == 'yes') $newoptions['seed'] = $options['seed'] + 1;
	}
	if ( $options != $newoptions ) {
		$options = $newoptions;
		update_option('widget_jotda', $options);
	}
	$title = attribute_escape($options['title']);
	$type = attribute_escape($options['type']);
	$keywords = attribute_escape($options['keywords']);
	$maxlength = attribute_escape($options['maxlength']);
	
?>
	<p>
		<label for="jotda-title"><?php _e('Widget Title'); ?></label>
		<br /><input style="width: 200px;" id="jotda-title" name="jotda-title" type="text" value="<?php echo $title; ?>" />
	</p>
	
	<p>
		<label for="jotda-select"><?php _e('Joke Content'); ?></label><br />
		<select id="jotda-type" name="jotda-type">
			<option value="clean" <?php if($type == 'clean') echo ' selected="selected"'; ?>>Clean (G Rated)</option>
			<option value="dirty" <?php if($type == 'dirty') echo ' selected="selected"'; ?>>Dirty (PG-13 and up)</option>
			<option value="all" <?php if($type == 'all') echo ' selected="selected"'; ?>>All (G to R)</option>
		</select>
	</p>
		
	<p>
		<label for="jotda-keywords"><?php _e('Find jokes that contain this word<br />(leave blank for all)'); ?></label>
		<br /><input style="width: 200px;" id="jotda-keywords" name="jotda-keywords" type="text" value="<?php echo $keywords; ?>" />	
	</p>
	
	<p>
		<label for="jotda-changejoke"><?php _e('Change Current Joke'); ?></label><br />
		<select id="jotda-changejoke" name="jotda-changejoke">
			<option value="no" selected="selected">No</option>
			<option value="yes">Yes</option>
		</select>
	</p>
	
	<input type="hidden" id="jotda-submit" name="jotda-submit" value="1" />
<?php
}

function wp_widget_jotda_register() {
	$dimension = array('height' => 180, 'width' => 200);
	$class = array('classname' => 'widget_jotda');
	wp_register_sidebar_widget('jotda', __('Joke of the Day Adv.'), 'wp_widget_jotda', $class);
	wp_register_widget_control('jotda', __('Joke of the Day Adv.'), 'wp_widget_jotda_control', $dimension);
}

add_action('plugins_loaded','wp_widget_jotda_register');

?>