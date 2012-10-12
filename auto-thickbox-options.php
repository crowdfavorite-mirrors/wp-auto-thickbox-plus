<?php
/*
 * Auto ThickBox Plus Options
 * Copyright (C) 2010-2012 attosoft <http://attosoft.info/en/>
 * This file is distributed under the same license as the Auto ThickBox Plus package.
 * attosoft <contact@attosoft.info>, 2010.
 */

class auto_thickbox_options {

	// Auto ThickBox Plus Options
	function register_options_page() {
		add_options_page('Auto ThickBox Plus ' . $this->util->__('Settings'), 'Auto ThickBox Plus', 'manage_options', 'auto-thickbox-plus', array(&$this, 'options_page'));
		add_meta_box( 'general-box', $this->util->__('General'), array(&$this, 'general_metabox'), $this->settings_page_type, 'normal' );
		add_meta_box( 'action-box', $this->util->__('Action', 'Actions'), array(&$this, 'action_metabox'), $this->settings_page_type, 'normal' );
		add_meta_box( 'view-box', ucfirst($this->util->__('View', 'view')), array(&$this, 'view_metabox'), $this->settings_page_type, 'normal' );
		add_meta_box( 'text-box', $this->util->__('Text'), array(&$this, 'text_metabox'), $this->settings_page_type, 'normal' );
		add_meta_box( 'image-box', $this->texts['image'], array(&$this, 'image_metabox'), $this->settings_page_type, 'normal' );
		add_meta_box( 'effect-box', $this->util->__('Effect') . ' (' . $this->util->__('beta') . ')', array(&$this, 'effect_metabox'), $this->settings_page_type, 'normal' );
		add_meta_box( 'about-box', $this->util->__('About'), array(&$this, 'about_metabox'), $this->settings_page_type, 'normal' );
		if (isset($_SERVER['HTTP_REFERER']) && strpos($_SERVER['HTTP_REFERER'], 'post_id=' . $this->options['post_id']) !== false) {
			add_filter('gettext', array(&$this, 'replace_insert_button'), 20, 3);
			register_post_type('auto-thickbox-plus', array('label' => 'Auto ThickBox Plus'));
		}
	}

	function replace_insert_button($translated_text, $text, $domain) {
		return $text == 'Insert into Post' ? $this->util->__('Insert Image', 'Insert an Image', 'Insert') : $translated_text;
	}

	function register_scripts() {
		$this->has_slider = function_exists('wp_script_is') && wp_script_is('jquery-ui-slider', 'registered');
		$deps = array('postbox', 'farbtastic', 'thickbox', 'media-upload');
		if ($this->has_slider) $deps[] = 'jquery-ui-slider';
		wp_enqueue_script('auto-thickbox', $this->util->plugins_url('auto-thickbox.js'), $deps, AUTO_THICKBOX_PLUS_VER, true);
	}

	function register_styles() {
		wp_enqueue_style('auto-thickbox', $this->util->plugins_url('auto-thickbox.css'), array('farbtastic', 'thickbox'), AUTO_THICKBOX_PLUS_VER);
	}

	function options_page() {
?>
<div class="wrap">
	<?php screen_icon(); ?>
	<h2>Auto ThickBox Plus <?php $this->util->_e('Settings'); ?></h2>
	<form method="post" action="options.php" name="form" novalidate>
	<?php settings_fields( $this->option_group ); ?>
		<div id="poststuff" class="metabox-holder">
		<?php
				wp_nonce_field( 'meta-box-order', 'meta-box-order-nonce', false );
				wp_nonce_field( 'closedpostboxes', 'closedpostboxesnonce', false );
				do_meta_boxes( $this->settings_page_type, 'normal', null );
		?>
		</div>
		<p class="submit">
			<input type="submit" class="button-primary" value="<?php $this->util->_e('Save Changes') ?>" />
			<input type="submit" class="button-primary" value="<?php $this->util->_e('Reset') ?>" name="reset" />
		</p>
	</form>
</div>
<?php
	}

	function general_metabox() {
		$builtin_thickbox = $this->options['thickbox_type'] == 'built-in';
?>
<table class="form-table">
	<tr>
		<th scope="row"><?php $this->util->_e('Display Style'); ?></th>
		<td>
			<label><input type="radio" name="auto-thickbox-plus[thickbox_style]" value="single"<?php $this->util->checked($this->options['thickbox_style'], 'single'); ?> />
			<?php $this->util->_e('Single Image'); ?></label>
			(<a href="<?php echo $this->util->plugins_url('screenshot-1.jpg'); ?>" class="thickbox-image" title="<?php $this->util->_e('Single Image'); ?>"><?php $this->util->_e('Preview'); ?></a>)
			<label class="boundary"><input type="radio" name="auto-thickbox-plus[thickbox_style]" value="gallery"<?php $this->util->checked($this->options['thickbox_style'], 'gallery'); ?> />
			<?php $this->util->_e('Gallery Images'); ?></label>
			(<a href="<?php echo $this->util->plugins_url('screenshot-2.jpg'); ?>" class="thickbox-image" title="<?php $this->util->_e('Gallery Images'); ?>" rel="gallery"><?php $this->util->_e('Preview'); ?></a>)
		</td>
	</tr>
	<tr>
		<th scope="row"></th>
		<td>
			<label><input type="checkbox" name="auto-thickbox-plus[wp_gallery]"<?php $this->util->checked($this->options['wp_gallery'], 'on'); ?> />
			<?php $this->util->_e('Set a different gallery-id for each WordPress Gallery'); ?> (<code>[gallery link="file"]</code>)</label>
		</td>
	</tr>
	<tr>
		<th scope="row"><?php $this->util->_e('Auto ThickBox'); ?></th>
		<td>
			<label><input type="radio" name="auto-thickbox-plus[auto_thickbox]" value="filter"<?php $this->util->checked($this->options['auto_thickbox'], 'filter'); ?> />
			<?php $this->util->_e('WordPress Filters'); ?> (<?php echo $this->texts['content_etc']; ?>)</label><br />
			<label><input type="radio" name="auto-thickbox-plus[auto_thickbox]" value="js"<?php $this->util->checked($this->options['auto_thickbox'], 'js'); ?> />
			<?php $this->util->_e('JavaScript'); ?> (<?php $this->util->_e('Whole Page'); ?>)</label><br />
			<label><input type="radio" name="auto-thickbox-plus[auto_thickbox]" value="disabled"<?php $this->util->checked($this->options['auto_thickbox'], 'disabled'); ?> />
			<?php $this->util->_e('Disabled'); ?></label>
		</td>
	</tr>
	<tr>
		<th scope="row"></th>
		<td>
			<label><input type="checkbox" name="auto-thickbox-plus[thickbox_img]"<?php $this->util->checked($this->options['thickbox_img'], 'on'); ?> />
			<?php $this->util->_e('Image links to images'); ?> (<code>&lt;a href="image">&lt;img src="thumbnail" />&lt;/a></code>)</label><br />
			<label><input type="checkbox" name="auto-thickbox-plus[thickbox_text]"<?php $this->util->checked($this->options['thickbox_text'], 'on'); ?> />
			<?php $this->util->_e('Text links to images'); ?> (<code>&lt;a href="image">Text&lt;/a></code>)</label><br />
			<label><input type="checkbox" name="auto-thickbox-plus[thickbox_target]"<?php $this->util->checked($this->options['thickbox_target'], 'on'); ?> />
			<?php $this->util->_e('Links with target attribute'); ?> (<code>&lt;a target="_blank"></code>)</label>
		</td>
	</tr>
	<tr>
		<th scope="row"><?php $this->util->_e('No ThickBox'); ?></th>
		<td>
			<label><input type="text" name="auto-thickbox-plus[no_thickbox]" value="<?php echo $this->options['no_thickbox']; ?>" class="regular-text" /><br />
			<?php $this->util->_e('* Input class attribute values separated by spaces'); ?> (<code>&lt;a class="nothickbox"></code>)</label>
		</td>
	</tr>
	<tr>
		<th scope="row"><?php $this->util->_e('Auto Resize'); ?></th>
		<td>
			<label class="item"><input type="checkbox" name="auto-thickbox-plus[auto_resize_img]"<?php $this->util->checked($this->options['auto_resize_img'], 'on'); ?> />
			<?php echo $this->texts['image']; ?></label>
			<label class="item"><input type="checkbox" name="auto-thickbox-plus[auto_resize_html]"<?php $this->util->checked($this->options['auto_resize_html'], 'on'); ?> />
			HTML</label>
		</td>
	</tr>
	<tr>
		<th scope="row"><?php $this->util->_e('ThickBox Resources'); ?></th>
		<td>
			<select name="auto-thickbox-plus[thickbox_type]" onchange="disablePlaceOption(this)">
				<option value="modified"<?php selected(!$builtin_thickbox); ?>><?php $this->util->_e('Modified ThickBox'); ?></option>
				<option value="built-in"<?php selected($builtin_thickbox); ?>><?php $this->util->_e('Built-in ThickBox'); ?></option>
			</select>
		</td>
	</tr>
	<tr>
		<th scope="row"></th>
		<td>
			<label class="item"><input type="radio" name="auto-thickbox-plus[script_place]" value="header"<?php $this->util->checked($this->options['script_place'], 'header'); $this->util->disabled($builtin_thickbox); ?> />
			<?php $this->util->_e('Header'); ?></label>
			<label class="item"><input type="radio" name="auto-thickbox-plus[script_place]" value="footer"<?php $this->util->checked($this->options['script_place'], 'footer'); $this->util->disabled($builtin_thickbox); ?> />
			<?php $this->util->_e('Footer'); ?></label>
		</td>
	</tr>
	<tr>
		<th scope="row"><?php $this->util->_e('Mobile Support'); ?> (<?php $this->util->_e('beta'); ?>)</th>
		<td>
			<label class="item"><input type="radio" name="auto-thickbox-plus[mobile_support]" value="no_margin"<?php $this->util->checked($this->options['mobile_support'], 'no_margin'); ?> />
			<?php $this->util->_e('No Window Margin'); ?></label>
			<label class="item"><input type="radio" name="auto-thickbox-plus[mobile_support]" value="no_thickbox"<?php $this->util->checked($this->options['mobile_support'], 'no_thickbox'); ?> />
			<?php $this->util->_e('No ThickBox'); ?></label>
		</td>
	</tr>
	<tr>
		<th scope="row"></th>
		<td>
			<label class="item"><?php $this->util->_e('Width'); ?>
			<input type="number" min="0" step="10" name="auto-thickbox-plus[small_width]" value="<?php echo $this->options['small_width']; ?>" class="small-text" /> px</label>
			<label class="item boundary"><?php $this->util->_e('Height'); ?>
			<input type="number" min="0" step="10" name="auto-thickbox-plus[small_height]" value="<?php echo $this->options['small_height']; ?>" class="small-text" /> px</label>
		</td>
	</tr>
</table>
<?php
	}

	function action_metabox() {
		$click_end_disabled = !in_array($this->options['click_img'], array('next', 'prev_next'));
		$click_range_disabled = $this->options['click_img'] != 'prev_next';
?>
<table class="form-table">
	<tr>
		<th scope="row"><?php $this->util->_e('Mouse Click'); ?></th>
		<th scope="row"><?php echo $this->texts['image']; ?></th>
		<td>
			<label class="item"><input type="radio" name="auto-thickbox-plus[click_img]" value="close"<?php $this->util->checked($this->options['click_img'], 'close'); ?> onclick="disableClickOption(this)" />
			<?php echo $this->texts['close']; ?></label>
			<label class="item"><input type="radio" name="auto-thickbox-plus[click_img]" value="none"<?php $this->util->checked($this->options['click_img'], 'none'); ?> onclick="disableClickOption(this)" />
			<?php echo $this->texts['none']; ?></label>
			<label class="item"><input type="radio" name="auto-thickbox-plus[click_img]" value="next"<?php $this->util->checked($this->options['click_img'], 'next'); ?> onclick="disableClickOption(this)" />
			<?php echo $this->texts['next2']; ?></label>
			<label class="item"><input type="radio" name="auto-thickbox-plus[click_img]" value="prev_next"<?php $this->util->checked($this->options['click_img'], 'prev_next'); ?> onclick="disableClickOption(this)" />
			<?php echo "{$this->texts['prev2']} / {$this->texts['next2']}"; ?></label>
			<label class="item"><input type="radio" name="auto-thickbox-plus[click_img]" value="open"<?php $this->util->checked($this->options['click_img'], 'open'); ?> onclick="disableClickOption(this)" />
			<?php echo $this->texts['open']; ?></label>
			<label class="item"><input type="radio" name="auto-thickbox-plus[click_img]" value="download"<?php $this->util->checked($this->options['click_img'], 'download'); ?> onclick="disableClickOption(this)" />
			<?php $this->util->_e('Download'); ?> (<?php $this->util->_e('beta'); ?>)</label>
			<label class="item"><input type="radio" name="auto-thickbox-plus[click_img]" value="expand_shrink"<?php $this->util->checked($this->options['click_img'], 'expand_shrink'); ?> onclick="disableClickOption(this)" />
			<?php $this->util->_e('Expand'); ?> / <?php $this->util->_e('Shrink'); ?></label>
		</td>
	</tr>
	<tr>
		<th scope="row"></th>
		<th scope="row"><?php echo "{$this->texts['image']} ({$this->texts['first2']} / {$this->texts['last2']})"; ?></th>
		<td>
			<label class="item"><input type="radio" name="auto-thickbox-plus[click_end]" value="close"<?php $this->util->checked($this->options['click_end'], 'close'); $this->util->disabled($click_end_disabled); ?> />
			<?php echo $this->texts['close']; ?></label>
			<label class="item"><input type="radio" name="auto-thickbox-plus[click_end]" value="none"<?php $this->util->checked($this->options['click_end'], 'none'); $this->util->disabled($click_end_disabled); ?> />
			<?php echo $this->texts['none']; ?></label>
			<label class="item"><input type="radio" name="auto-thickbox-plus[click_end]" value="loop"<?php $this->util->checked($this->options['click_end'], 'loop'); $this->util->disabled($click_end_disabled); ?> />
			<?php $this->util->_e('Loop'); ?></label>
		</td>
	</tr>
	<tr>
		<th scope="row"></th>
		<th scope="row"><?php $this->util->_e('Clickable Range'); ?></th>
		<td class="slider">
			<input type="number" min="0" max="50" step="5" name="auto-thickbox-plus[click_range]" value="<?php echo $this->options['click_range']; ?>" id="click-range" class="small-text"<?php $this->util->disabled($click_range_disabled); ?> />
			<span>%</span>
			<?php if ($this->has_slider): ?>
				<div id="click-range-slider"></div>
			<?php else: ?>
				<span>[0 - 50]</span>
			<?php endif; ?>
			<div style="clear:both"></div>
		</td>
	</tr>
	<tr>
		<th scope="row"></th>
		<th scope="row"><?php $this->util->_e('Background'); ?></th>
		<td>
			<label class="item"><input type="radio" name="auto-thickbox-plus[click_bg]" value="close"<?php $this->util->checked($this->options['click_bg'], 'close'); ?> />
			<?php echo $this->texts['close']; ?></label>
			<label class="item"><input type="radio" name="auto-thickbox-plus[click_bg]" value="none"<?php $this->util->checked($this->options['click_bg'], 'none'); ?> />
			<?php echo $this->texts['none']; ?></label>
		</td>
	</tr>
	<tr>
		<th scope="row"><?php $this->util->_e('Mouse Wheel'); ?></th>
		<th scope="row"><?php echo $this->texts['image']; ?></th>
		<td>
			<label class="item"><input type="radio" name="auto-thickbox-plus[wheel_img]" value="prev_next"<?php $this->util->checked($this->options['wheel_img'], 'prev_next'); ?> />
			<?php echo "{$this->texts['prev2']} / {$this->texts['next2']}"; ?></label>
			<label class="item"><input type="radio" name="auto-thickbox-plus[wheel_img]" value="scale"<?php $this->util->checked($this->options['wheel_img'], 'scale'); ?> />
			<?php $this->util->_e('Scale'); ?></label>
			<label class="item"><input type="radio" name="auto-thickbox-plus[wheel_img]" value="scroll"<?php $this->util->checked($this->options['wheel_img'], 'scroll'); ?> />
			<?php $this->util->_e('Scroll'); ?></label>
			<label class="item"><input type="radio" name="auto-thickbox-plus[wheel_img]" value="none"<?php $this->util->checked($this->options['wheel_img'], 'none'); ?> />
			<?php echo $this->texts['none']; ?></label>
		</td>
	</tr>
	<tr>
		<th scope="row"></th>
		<th scope="row"><?php $this->util->_e('Background'); ?></th>
		<td>
			<label class="item"><input type="radio" name="auto-thickbox-plus[wheel_bg]" value="scroll"<?php $this->util->checked($this->options['wheel_bg'], 'scroll'); ?> />
			<?php $this->util->_e('Scroll'); ?></label>
			<label class="item"><input type="radio" name="auto-thickbox-plus[wheel_bg]" value="none"<?php $this->util->checked($this->options['wheel_bg'], 'none'); ?> />
			<?php echo $this->texts['none']; ?></label>
		</td>
	</tr>
	<tr>
		<th scope="row"><?php $this->util->_e('Drag &amp; Drop'); ?></th>
		<th scope="row"><?php $this->util->_e('Window'); ?> (<?php echo $this->texts['image']; ?>)</th>
		<td>
			<label class="item"><input type="checkbox" name="auto-thickbox-plus[drag_img_move]"<?php $this->util->checked($this->options['drag_img_move'], 'on'); ?> />
			<?php $this->util->_e('Move'); ?></label>
			<label class="item"><input type="checkbox" name="auto-thickbox-plus[drag_img_resize]"<?php $this->util->checked($this->options['drag_img_resize'], 'on'); ?> />
			<?php $this->util->_e('Resize'); ?></label>
		</td>
	</tr>
	<tr>
		<th scope="row"></th>
		<th scope="row"><?php $this->util->_e('Window'); ?> (HTML)</th>
		<td>
			<label class="item"><input type="checkbox" name="auto-thickbox-plus[drag_html_move]"<?php $this->util->checked($this->options['drag_html_move'], 'on'); ?> />
			<?php $this->util->_e('Move'); ?></label>
			<label class="item"><input type="checkbox" name="auto-thickbox-plus[drag_html_resize]"<?php $this->util->checked($this->options['drag_html_resize'], 'on'); ?> />
			<?php $this->util->_e('Resize'); ?></label>
		</td>
	</tr>
	<tr>
		<th scope="row"><?php $this->util->_e('Keyboard Shortcuts', 'Keyboard shortcuts'); ?></th>
		<th scope="row"><?php echo $this->texts['close']; ?></th>
		<td>
			<label class="item"><input type="checkbox" name="auto-thickbox-plus[key_close_esc]"<?php $this->util->checked($this->options['key_close_esc'], 'on'); ?> />
				Esc</label>
			<label class="item"><input type="checkbox" name="auto-thickbox-plus[key_close_enter]"<?php $this->util->checked($this->options['key_close_enter'], 'on'); ?> />
				Enter</label>
		</td>
	</tr>
	<tr>
		<th scope="row"></th>
		<th scope="row"><?php echo $this->texts['prev2']; ?></th>
		<td>
			<label class="item"><input type="checkbox" name="auto-thickbox-plus[key_prev_angle]"<?php $this->util->checked($this->options['key_prev_angle'], 'on'); ?> />
				< ( , )</label>
			<label class="item"><input type="checkbox" name="auto-thickbox-plus[key_prev_left]"<?php $this->util->checked($this->options['key_prev_left'], 'on'); ?> />
			<?php $this->util->_e('Left'); ?></label>
			<label class="item"><input type="checkbox" name="auto-thickbox-plus[key_prev_tab]"<?php $this->util->checked($this->options['key_prev_tab'], 'on'); ?> />
				Shift + Tab</label>
			<label class="item"><input type="checkbox" name="auto-thickbox-plus[key_prev_space]"<?php $this->util->checked($this->options['key_prev_space'], 'on'); ?> />
				Shift + <?php $this->util->_e('Space'); ?></label>
			<label class="item"><input type="checkbox" name="auto-thickbox-plus[key_prev_bs]"<?php $this->util->checked($this->options['key_prev_bs'], 'on'); ?> />
				BackSpace</label>
		</td>
	</tr>
	<tr>
		<th scope="row"></th>
		<th scope="row"><?php echo $this->texts['next2']; ?></th>
		<td>
			<label class="item"><input type="checkbox" name="auto-thickbox-plus[key_next_angle]"<?php $this->util->checked($this->options['key_next_angle'], 'on'); ?> />
				> ( . )</label>
			<label class="item"><input type="checkbox" name="auto-thickbox-plus[key_next_right]"<?php $this->util->checked($this->options['key_next_right'], 'on'); ?> />
			<?php $this->util->_e('Right'); ?></label>
			<label class="item"><input type="checkbox" name="auto-thickbox-plus[key_next_tab]"<?php $this->util->checked($this->options['key_next_tab'], 'on'); ?> />
				Tab</label>
			<label class="item"><input type="checkbox" name="auto-thickbox-plus[key_next_space]"<?php $this->util->checked($this->options['key_next_space'], 'on'); ?> />
			<?php $this->util->_e('Space'); ?></label>
		</td>
	</tr>
	<tr>
		<th scope="row"></th>
		<th scope="row"><?php echo "{$this->texts['first2']} / {$this->texts['last2']}"; ?></th>
		<td>
			<label class="item"><input type="checkbox" name="auto-thickbox-plus[key_end_home_end]"<?php $this->util->checked($this->options['key_end_home_end'], 'on'); ?> />
				Home / End</label>
		</td>
	</tr>
</table>
<?php
	}

	function view_metabox() {
		$bgcolor_title_trans = $this->options['bgcolor_title'] == 'transparent';
		$bgcolor_cap_trans = $this->options['bgcolor_cap'] == 'transparent';
		$bgcolor_img_trans = $this->options['bgcolor_img'] == 'transparent';
		$bgcolor_html_trans = $this->options['bgcolor_html'] == 'transparent';
		$bgcolor_bg_trans = $this->options['bgcolor_bg'] == 'transparent';
		$border_win_none = $this->options['border_win'] == 'none';
		$border_img_tl_none = $this->options['border_img_tl'] == 'none';
		$border_img_br_none = $this->options['border_img_br'] == 'none';
		$border_gallery_none = $this->options['border_gallery'] == 'none';
		$box_shadow_win_none = $this->options['box_shadow_win'] == 'none';
		$txt_shadow_title_none = $this->options['txt_shadow_title'] == 'none';
		$txt_shadow_cap_none = $this->options['txt_shadow_cap'] == 'none';
		$text_sel_color = $this->util->__('Select a Color', 'Select a color');
?>
<table class="form-table">
	<tr>
		<th scope="row"><?php $this->util->_e('Position'); ?></th>
		<th scope="row"><?php $this->util->_e('Title'); ?></th>
		<td>
			<label class="item"><input type="radio" name="auto-thickbox-plus[position_title]" value="top"<?php $this->util->checked($this->options['position_title'], 'top'); ?> onclick="disableHoverOption(this)" />
			<?php $this->util->_e('Top'); ?></label>
			<label class="item"><input type="radio" name="auto-thickbox-plus[position_title]" value="bottom"<?php $this->util->checked($this->options['position_title'], 'bottom'); ?> onclick="disableHoverOption(this)" />
			<?php $this->util->_e('Bottom'); ?></label>
			<label class="item"><input type="radio" name="auto-thickbox-plus[position_title]" value="none"<?php $this->util->checked($this->options['position_title'], 'none'); ?> onclick="disableHoverOption(this)" />
			<?php echo $this->texts['none']; ?></label>
		</td>
	</tr>
	<tr>
		<th scope="row"></th>
		<th scope="row"><?php $this->util->_e('Caption'); ?></th>
		<td>
			<label class="item"><input type="radio" name="auto-thickbox-plus[position_cap]" value="top"<?php $this->util->checked($this->options['position_cap'], 'top'); ?> onclick="disableHoverOption(this)" />
			<?php $this->util->_e('Top'); ?></label>
			<label class="item"><input type="radio" name="auto-thickbox-plus[position_cap]" value="bottom"<?php $this->util->checked($this->options['position_cap'], 'bottom'); ?> onclick="disableHoverOption(this)" />
			<?php $this->util->_e('Bottom'); ?></label>
			<label class="item"><input type="radio" name="auto-thickbox-plus[position_cap]" value="none"<?php $this->util->checked($this->options['position_cap'], 'none'); ?> onclick="disableHoverOption(this)" />
			<?php echo $this->texts['none']; ?></label>
		</td>
	</tr>
	<tr>
		<th scope="row"><?php $this->util->_e('Size'); ?></th>
		<th scope="row"><?php $this->util->_e('Window'); ?> (HTML)</th>
		<td>
			<label class="item"><?php $this->util->_e('Width'); ?>
			<input type="number" min="0" step="10" name="auto-thickbox-plus[win_width]" value="<?php echo $this->options['win_width']; ?>" class="small-text" /> px</label>
			<label class="item boundary"><?php $this->util->_e('Height'); ?>
			<input type="number" min="0" step="10" name="auto-thickbox-plus[win_height]" value="<?php echo $this->options['win_height']; ?>" class="small-text" /> px</label>
		</td>
	</tr>
	<tr>
		<th scope="row"><a href="<?php $this->util->_e('https://developer.mozilla.org/en/CSS/position'); ?>" target="_blank"><?php $this->util->_e('Position'); ?></a></th>
		<th scope="row"><?php $this->util->_e('Window'); ?></th>
		<td>
			<label class="item"><input type="radio" name="auto-thickbox-plus[position_win]" value="fixed"<?php $this->util->checked($this->options['position_win'], 'fixed'); ?> />
			<?php $this->util->_e('Fixed'); ?></label>
			<label class="item"><input type="radio" name="auto-thickbox-plus[position_win]" value="absolute"<?php $this->util->checked($this->options['position_win'], 'absolute'); ?> />
			<?php $this->util->_e('Absolute'); ?></label>
		</td>
	</tr>
	<tr>
		<th scope="row"><a href="<?php $this->util->_e('https://developer.mozilla.org/en/CSS/font-family'); ?>" target="_blank"><?php echo ucwords($this->util->__('Font Family', 'Font family')); ?></a></th>
		<th scope="row"><?php $this->util->_e('Title'); ?></th>
		<td>
			<input type="text" name="auto-thickbox-plus[font_title]" value="<?php echo $this->util->esc_attr($this->options['font_title']); ?>" style="width:70%" />
			<label><input type="checkbox" name="auto-thickbox-plus[font_weight_title]" value="bold"<?php $this->util->checked($this->options['font_weight_title'], 'bold'); ?> />
			<?php $this->util->_e('Bold'); ?></label>
		</td>
	</tr>
	<tr>
		<th scope="row"></th>
		<th scope="row"><?php $this->util->_e('Caption'); ?></th>
		<td>
			<input type="text" name="auto-thickbox-plus[font_cap]" value="<?php echo $this->util->esc_attr($this->options['font_cap']); ?>" style="width:70%" />
			<label><input type="checkbox" name="auto-thickbox-plus[font_weight_cap]" value="bold"<?php $this->util->checked($this->options['font_weight_cap'], 'bold'); ?> />
			<?php $this->util->_e('Bold'); ?></label>
		</td>
	</tr>
	<tr>
		<th scope="row"><a href="<?php $this->util->_e('https://developer.mozilla.org/en/CSS/font-size'); ?>" target="_blank"><?php echo ucwords($this->util->__('Font Size', 'Font size')); ?></a></th>
		<th scope="row"><?php $this->util->_e('Title'); ?></th>
		<td>
			<input type="number" min="0" name="auto-thickbox-plus[font_size_title]" value="<?php echo $this->options['font_size_title']; ?>" class="small-text" /> px
		</td>
	</tr>
	<tr>
		<th scope="row"></th>
		<th scope="row"><?php $this->util->_e('Caption'); ?></th>
		<td>
			<input type="number" min="0" name="auto-thickbox-plus[font_size_cap]" value="<?php echo $this->options['font_size_cap']; ?>" class="small-text" /> px
		</td>
	</tr>
	<tr>
		<th scope="row"></th>
		<th scope="row"><?php $this->util->_e('Navigation'); ?></th>
		<td>
			<input type="number" min="0" name="auto-thickbox-plus[font_size_nav]" value="<?php echo $this->options['font_size_nav']; ?>" class="small-text" /> px
		</td>
	</tr>
	<tr>
		<th scope="row"><a href="<?php $this->util->_e('https://developer.mozilla.org/en/CSS/color'); ?>" target="_blank"><?php $this->util->_e('Text Color'); ?></a></th>
		<th scope="row"><?php $this->util->_e('Title'); ?></th>
		<td>
			<input type="text" class="colortext" name="auto-thickbox-plus[color_title]" value="<?php echo $this->options['color_title']; ?>" />
			<a href="#" class="pickcolor colorpreview hide-if-no-js"></a>
			<input type="button" class="pickcolor button hide-if-no-js" value="<?php echo $text_sel_color; ?>" />
			<br /><div class="colorpicker"></div>
		</td>
	</tr>
	<tr>
		<th scope="row"></th>
		<th scope="row"><?php $this->util->_e('Caption'); ?></th>
		<td>
			<input type="text" class="colortext" name="auto-thickbox-plus[color_cap]" value="<?php echo $this->options['color_cap']; ?>" />
			<a href="#" class="pickcolor colorpreview hide-if-no-js"></a>
			<input type="button" class="pickcolor button hide-if-no-js" value="<?php echo $text_sel_color; ?>" />
			<br /><div class="colorpicker"></div>
		</td>
	</tr>
	<tr>
		<th scope="row"></th>
		<th scope="row"><?php $this->util->_e('Navigation'); ?></th>
		<td>
			<input type="text" class="colortext" name="auto-thickbox-plus[color_nav]" value="<?php echo $this->options['color_nav']; ?>" />
			<a href="#" class="pickcolor colorpreview hide-if-no-js"></a>
			<input type="button" class="pickcolor button hide-if-no-js" value="<?php echo $text_sel_color; ?>" />
			<br /><div class="colorpicker"></div>
		</td>
	</tr>
	<tr>
		<th scope="row"><a href="<?php $this->util->_e('https://developer.mozilla.org/en/CSS/background-color'); ?>" target="_blank"><?php $this->util->_e('Background Color'); ?></a></th>
		<th scope="row"><?php $this->util->_e('Title'); ?></th>
		<td>
			<input type="text" class="colortext" name="auto-thickbox-plus[bgcolor_title]" value="<?php echo $this->options['bgcolor_title']; ?>"<?php $this->util->disabled($bgcolor_title_trans); ?> />
			<a href="#" class="pickcolor colorpreview hide-if-no-js"></a>
			<input type="button" class="pickcolor button hide-if-no-js" value="<?php echo $text_sel_color; ?>" />
			<label><input type="checkbox" name="auto-thickbox-plus[bgcolor_title]" value="transparent"<?php $this->util->checked($bgcolor_title_trans); ?> onclick="disableOption(this)" />
			<?php $this->util->_e('Transparent'); ?></label>
			<br /><div class="colorpicker"></div>
		</td>
	</tr>
	<tr>
		<th scope="row"></th>
		<th scope="row"><?php $this->util->_e('Caption'); ?></th>
		<td>
			<input type="text" class="colortext" name="auto-thickbox-plus[bgcolor_cap]" value="<?php echo $this->options['bgcolor_cap']; ?>"<?php $this->util->disabled($bgcolor_cap_trans); ?> />
			<a href="#" class="pickcolor colorpreview hide-if-no-js"></a>
			<input type="button" class="pickcolor button hide-if-no-js" value="<?php echo $text_sel_color; ?>" />
			<label><input type="checkbox" name="auto-thickbox-plus[bgcolor_cap]" value="transparent"<?php $this->util->checked($bgcolor_cap_trans); ?> onclick="disableOption(this)" />
			<?php $this->util->_e('Transparent'); ?></label>
			<br /><div class="colorpicker"></div>
		</td>
	</tr>
	<tr>
		<th scope="row"></th>
		<th scope="row"><?php $this->util->_e('Window'); ?> (<?php echo $this->texts['image']; ?>)</th>
		<td>
			<input type="text" class="colortext" name="auto-thickbox-plus[bgcolor_img]" value="<?php echo $this->options['bgcolor_img']; ?>"<?php $this->util->disabled($bgcolor_img_trans); ?> />
			<a href="#" class="pickcolor colorpreview hide-if-no-js"></a>
			<input type="button" class="pickcolor button hide-if-no-js" value="<?php echo $text_sel_color; ?>" />
			<label><input type="checkbox" name="auto-thickbox-plus[bgcolor_img]" value="transparent"<?php $this->util->checked($bgcolor_img_trans); ?> onclick="disableOption(this)" />
			<?php $this->util->_e('Transparent'); ?></label>
			<br /><div class="colorpicker"></div>
		</td>
	</tr>
	<tr>
		<th scope="row"></th>
		<th scope="row"><?php $this->util->_e('Window'); ?> (HTML)</th>
		<td>
			<input type="text" class="colortext" name="auto-thickbox-plus[bgcolor_html]" value="<?php echo $this->options['bgcolor_html']; ?>"<?php $this->util->disabled($bgcolor_html_trans); ?> />
			<a href="#" class="pickcolor colorpreview hide-if-no-js"></a>
			<input type="button" class="pickcolor button hide-if-no-js" value="<?php echo $text_sel_color; ?>" />
			<label><input type="checkbox" name="auto-thickbox-plus[bgcolor_html]" value="transparent"<?php $this->util->checked($bgcolor_html_trans); ?> onclick="disableOption(this)" />
			<?php $this->util->_e('Transparent'); ?></label>
			<br /><div class="colorpicker"></div>
		</td>
	</tr>
	<tr>
		<th scope="row"></th>
		<th scope="row"><?php $this->util->_e('Background'); ?></th>
		<td>
			<input type="text" class="colortext" name="auto-thickbox-plus[bgcolor_bg]" value="<?php echo $this->options['bgcolor_bg']; ?>"<?php $this->util->disabled($bgcolor_bg_trans); ?> />
			<a href="#" class="pickcolor colorpreview hide-if-no-js"></a>
			<input type="button" class="pickcolor button hide-if-no-js" value="<?php echo $text_sel_color; ?>" />
			<label><input type="checkbox" name="auto-thickbox-plus[bgcolor_bg]" value="transparent"<?php $this->util->checked($bgcolor_bg_trans); ?> onclick="disableOption(this)" />
			<?php $this->util->_e('Transparent'); ?></label>
			<br /><div class="colorpicker"></div>
		</td>
	</tr>
	<tr>
		<th scope="row"><a href="<?php $this->util->_e('https://developer.mozilla.org/en/CSS/margin'); ?>" target="_blank"><?php $this->util->_e('Margin'); ?></a></th>
		<th scope="row"><?php $this->util->_e('Window'); ?> (<?php echo $this->texts['image']; ?>)</th>
		<td>
			<input type="number" min="0" name="auto-thickbox-plus[margin_win_img]" value="<?php echo $this->options['margin_win_img']; ?>" class="small-text" /> px
		</td>
	</tr>
	<tr>
		<th scope="row"></th>
		<th scope="row"><?php $this->util->_e('Window'); ?> (HTML)</th>
		<td>
			<input type="number" min="0" name="auto-thickbox-plus[margin_win_html]" value="<?php echo $this->options['margin_win_html']; ?>" class="small-text" /> px
		</td>
	</tr>
	<tr>
		<th scope="row"></th>
		<th scope="row"><?php echo $this->texts['image']; ?></th>
		<td>
			<input type="number" min="0" name="auto-thickbox-plus[margin_img]" value="<?php echo $this->options['margin_img']; ?>" class="small-text" /> px
		</td>
	</tr>
	<tr>
		<th scope="row"><a href="<?php $this->util->_e('https://developer.mozilla.org/en/CSS/border'); ?>" target="_blank"><?php $this->util->_e('Border'); ?></a></th>
		<th scope="row"><?php $this->util->_e('Window'); ?></th>
		<td>
			<input type="number" min="0" name="auto-thickbox-plus[border_width_win]" value="<?php echo $this->options['border_width_win']; ?>" class="small-text"<?php $this->util->disabled($border_win_none); ?> /> px
			<select name="auto-thickbox-plus[border_style_win]"<?php $this->util->disabled($border_win_none); ?> style="margin:1px 3px">
				<?php $this->border_style_listbox('border_style_win'); ?>
			</select>
			<input type="text" class="colortext" name="auto-thickbox-plus[border_color_win]" value="<?php echo $this->options['border_color_win']; ?>"<?php $this->util->disabled($border_win_none); ?> />
			<a href="#" class="pickcolor colorpreview hide-if-no-js"></a>
			<input type="button" class="pickcolor button hide-if-no-js" value="<?php echo $text_sel_color; ?>" />
			<label><input type="checkbox" name="auto-thickbox-plus[border_win]" value="none"<?php $this->util->checked($border_win_none); ?> onclick="disableBorderOption(this)" />
			<?php echo $this->texts['none']; ?></label>
			<br /><div class="colorpicker"></div>
		</td>
	</tr>
	<tr>
		<th scope="row"></th>
		<th scope="row"><?php echo $this->texts['image']; ?> (<?php $this->util->_e('Top left'); ?>)</th>
		<td>
			<input type="number" min="0" name="auto-thickbox-plus[border_width_img_tl]" value="<?php echo $this->options['border_width_img_tl']; ?>" class="small-text"<?php $this->util->disabled($border_img_tl_none); ?> /> px
			<select name="auto-thickbox-plus[border_style_img_tl]"<?php $this->util->disabled($border_img_tl_none); ?> style="margin:1px 3px">
				<?php $this->border_style_listbox('border_style_img_tl'); ?>
			</select>
			<input type="text" class="colortext" name="auto-thickbox-plus[border_color_img_tl]" value="<?php echo $this->options['border_color_img_tl']; ?>"<?php $this->util->disabled($border_img_tl_none); ?> />
			<a href="#" class="pickcolor colorpreview hide-if-no-js"></a>
			<input type="button" class="pickcolor button hide-if-no-js" value="<?php echo $text_sel_color; ?>" />
			<label><input type="checkbox" name="auto-thickbox-plus[border_img_tl]" value="none"<?php $this->util->checked($border_img_tl_none); ?> onclick="disableBorderOption(this)" />
			<?php echo $this->texts['none']; ?></label>
			<br /><div class="colorpicker"></div>
		</td>
	</tr>
	<tr>
		<th scope="row"></th>
		<th scope="row"><?php echo $this->texts['image']; ?> (<?php $this->util->_e('Bottom right'); ?>)</th>
		<td>
			<input type="number" min="0" name="auto-thickbox-plus[border_width_img_br]" value="<?php echo $this->options['border_width_img_br']; ?>" class="small-text"<?php $this->util->disabled($border_img_br_none); ?> /> px
			<select name="auto-thickbox-plus[border_style_img_br]"<?php $this->util->disabled($border_img_br_none); ?> style="margin:1px 3px">
				<?php $this->border_style_listbox('border_style_img_br'); ?>
			</select>
			<input type="text" class="colortext" name="auto-thickbox-plus[border_color_img_br]" value="<?php echo $this->options['border_color_img_br']; ?>"<?php $this->util->disabled($border_img_br_none); ?> />
			<a href="#" class="pickcolor colorpreview hide-if-no-js"></a>
			<input type="button" class="pickcolor button hide-if-no-js" value="<?php echo $text_sel_color; ?>" />
			<label><input type="checkbox" name="auto-thickbox-plus[border_img_br]" value="none"<?php $this->util->checked($border_img_br_none); ?> onclick="disableBorderOption(this)" />
			<?php echo $this->texts['none']; ?></label>
			<br /><div class="colorpicker"></div>
		</td>
	</tr>
	<tr>
		<th scope="row"></th>
		<th scope="row"><?php echo $this->texts['wp_gallery']; ?></th>
		<td>
			<input type="number" min="0" name="auto-thickbox-plus[border_width_gallery]" value="<?php echo $this->options['border_width_gallery']; ?>" class="small-text"<?php $this->util->disabled($border_gallery_none); ?> /> px
			<select name="auto-thickbox-plus[border_style_gallery]"<?php $this->util->disabled($border_gallery_none); ?> style="margin:1px 3px">
				<?php $this->border_style_listbox('border_style_gallery'); ?>
			</select>
			<input type="text" class="colortext" name="auto-thickbox-plus[border_color_gallery]" value="<?php echo $this->options['border_color_gallery']; ?>"<?php $this->util->disabled($border_gallery_none); ?> />
			<a href="#" class="pickcolor colorpreview hide-if-no-js"></a>
			<input type="button" class="pickcolor button hide-if-no-js" value="<?php echo $text_sel_color; ?>" />
			<label><input type="checkbox" name="auto-thickbox-plus[border_gallery]" value="none"<?php $this->util->checked($border_gallery_none); ?> onclick="disableBorderOption(this)" />
			<?php echo $this->texts['none']; ?></label>
			<br /><div class="colorpicker"></div>
		</td>
	</tr>
	<tr>
		<th scope="row"><a href="<?php $this->util->_e('https://developer.mozilla.org/en/CSS/border-radius'); ?>" target="_blank"><?php $this->util->_e('Border Radius'); ?></a></th>
		<th scope="row"><?php $this->util->_e('Window'); ?></th>
		<td>
			<input type="number" min="0" name="auto-thickbox-plus[radius_win]" value="<?php echo $this->options['radius_win']; ?>" class="small-text" /> px
		</td>
	</tr>
	<tr>
		<th scope="row"></th>
		<th scope="row"><?php echo $this->texts['image']; ?></th>
		<td>
			<input type="number" min="0" name="auto-thickbox-plus[radius_img]" value="<?php echo $this->options['radius_img']; ?>" class="small-text" /> px
		</td>
	</tr>
	<tr>
		<th scope="row"><a href="<?php $this->util->_e('https://developer.mozilla.org/en/CSS/opacity'); ?>" target="_blank"><?php $this->util->_e('Opacity'); ?></a></th>
		<th scope="row"><?php $this->util->_e('Background'); ?></th>
		<td class="slider">
			<input type="number" min="0" max="1" step="0.05" name="auto-thickbox-plus[opacity_bg]" value="<?php echo $this->options['opacity_bg']; ?>" class="small-text" />
			<?php if ($this->has_slider): ?>
				<label class="opacity-trans"><?php $this->util->_e('Transparent'); ?></label>
				<div class="opacity-slider"></div>
				<label class="opacity-opaque"><?php $this->util->_e('Opaque'); ?></label>
			<?php else: ?>
				<span>[0 - 1]</span>
			<?php endif; ?>
			<div style="clear:both"></div>
		</td>
	</tr>
	<tr>
		<th scope="row"></th>
		<th scope="row"><?php $this->util->_e('Thumbnail'); ?></th>
		<td class="slider">
			<input type="number" min="0" max="1" step="0.05" name="auto-thickbox-plus[opacity_thumb]" value="<?php echo $this->options['opacity_thumb']; ?>" class="small-text" />
			<?php if ($this->has_slider): ?>
				<label class="opacity-trans"><?php $this->util->_e('Transparent'); ?></label>
				<div class="opacity-slider"></div>
				<label class="opacity-opaque"><?php $this->util->_e('Opaque'); ?></label>
			<?php else: ?>
				<span>[0 - 1]</span>
			<?php endif; ?>
			<div style="clear:both"></div>
		</td>
	</tr>
	<tr>
		<th scope="row"><a href="<?php $this->util->_e('https://developer.mozilla.org/en/CSS/box-shadow'); ?>" target="_blank"><?php $this->util->_e('Box Shadow'); ?></a></th>
		<th scope="row"><?php $this->util->_e('Window'); ?></th>
		<td>
			<input type="text" name="auto-thickbox-plus[box_shadow_win]" value="<?php echo $this->options['box_shadow_win']; ?>" size="27"<?php $this->util->disabled($box_shadow_win_none); ?> />
			<label><input type="checkbox" name="auto-thickbox-plus[box_shadow_win]" value="none"<?php $this->util->checked($box_shadow_win_none); ?> onclick="disableOption(this)" />
			<?php echo $this->texts['none']; ?></label>
		</td>
	</tr>
	<tr>
		<th scope="row"><a href="<?php $this->util->_e('https://developer.mozilla.org/en/CSS/text-shadow'); ?>" target="_blank"><?php $this->util->_e('Text Shadow'); ?></a></th>
		<th scope="row"><?php $this->util->_e('Title'); ?></th>
		<td>
			<input type="text" name="auto-thickbox-plus[txt_shadow_title]" value="<?php echo $this->options['txt_shadow_title']; ?>" size="27"<?php $this->util->disabled($txt_shadow_title_none); ?> />
			<label><input type="checkbox" name="auto-thickbox-plus[txt_shadow_title]" value="none"<?php $this->util->checked($txt_shadow_title_none); ?> onclick="disableOption(this)" />
			<?php echo $this->texts['none']; ?></label>
		</td>
	</tr>
	<tr>
		<th scope="row"></th>
		<th scope="row"><?php $this->util->_e('Caption'); ?></th>
		<td>
			<input type="text" name="auto-thickbox-plus[txt_shadow_cap]" value="<?php echo $this->options['txt_shadow_cap']; ?>" size="27"<?php $this->util->disabled($txt_shadow_cap_none); ?> />
			<label><input type="checkbox" name="auto-thickbox-plus[txt_shadow_cap]" value="none"<?php $this->util->checked($txt_shadow_cap_none); ?> onclick="disableOption(this)" />
			<?php echo $this->texts['none']; ?></label>
		</td>
	</tr>
</table>
<?php
	}

	function border_style_listbox($name) {
		foreach(array('dotted', 'dashed', 'solid', 'double', 'groove', 'ridge', 'inset', 'outset') as $value) {
			echo "<option value='{$value}'";
			selected($this->options[$name], $value);
			echo ">{$value}</option>";
		}
	}

	function text_metabox() {
?>
<table class="form-table">
	<tr>
		<th scope="row"><?php $this->util->_e('Title'); ?></th>
		<td>
			<input type="hidden" name="auto-thickbox-plus[ref_title]" value="<?php echo $this->options['ref_title']; ?>" />
			<ol class="sortable">
				<?php $this->sortable_items($this->options['ref_title']); ?>
			</ol>
		</td>
	</tr>
	<tr>
		<th scope="row"><?php $this->util->_e('Caption'); ?></th>
		<td>
			<input type="hidden" name="auto-thickbox-plus[ref_cap]" value="<?php echo $this->options['ref_cap']; ?>" />
			<ol class="sortable">
				<?php $this->sortable_items($this->options['ref_cap']); ?>
			</ol>
		</td>
	</tr>
</table>
<?php
	}

	function sortable_items($refs) {
		$text_link = ucfirst($this->util->__('Link', 'Links'));
		foreach (explode(',', $refs) as $ref) {
			switch (trim($ref, "'")) {
				case "link-title": echo "<li class='ui-state-default' id='link-title'>{$text_link} - " . $this->util->__('Title') . " (<code>a@title</code>)</li>"; break;
				case "link-name": echo "<li class='ui-state-default' id='link-name'>{$text_link} - " . $this->util->__('Name') . " (<code>a@name</code>)</li>"; break;
				case "blank": echo "<li class='ui-state-default' id='blank'>" . $this->util->__('Blank') . "</li>"; break;
				case "img-title": echo "<li class='ui-state-default' id='img-title'>{$this->texts['image']} - " . $this->util->__('Title') . " (<code>img@title</code>)</li>"; break;
				case "img-alt": echo "<li class='ui-state-default' id='img-alt'>{$this->texts['image']} - " . $this->util->__('Alternate Text') . " (<code>img@alt</code>)</li>"; break;
				case "img-cap": echo "<li class='ui-state-default' id='img-cap'>{$this->texts['image']} - " . $this->util->__('Caption') . " (<code>@class='wp-caption-text'</code>)</li>"; break;
				case "img-desc": echo "<li class='ui-state-default' id='img-desc'>{$this->texts['image']} - " . $this->util->__('Description') . " (<code>img@longdesc</code>)</li>"; break;
				case "img-name": echo "<li class='ui-state-default' id='img-name'>{$this->texts['image']} - " . $this->util->__('Name') . " (<code>img@name</code>)</li>"; break;
			}
		}
	}

	function image_metabox() {
		$img_prev_none = $this->options['img_prev'] == 'none';
		$img_prev = !$img_prev_none ? $this->options['img_prev'] : $this->options_def['img_prev'];
		$img_next_none = $this->options['img_next'] == 'none';
		$img_next = !$img_next_none ? $this->options['img_next'] : $this->options_def['img_next'];
		$img_first_none = $this->options['img_first'] == 'none';
		$img_first = !$img_first_none ? $this->options['img_first'] : $this->options_def['img_first'];
		$img_last_none = $this->options['img_last'] == 'none';
		$img_last = !$img_last_none ? $this->options['img_last'] : $this->options_def['img_last'];
		$img_close_none = $this->options['img_close'] == 'none';
		$img_close = !$img_close_none ? $this->options['img_close'] : $this->options_def['img_close'];
		$img_close_btn_none = $this->options['img_close_btn'] == 'none';
		$img_close_btn = !$img_close_btn_none ? $this->options['img_close_btn'] : $this->options_def['img_close_btn'];
		$img_load_none = $this->options['img_load'] == 'none';
		$img_load = !$img_load_none ? $this->options['img_load'] : $this->options_def['img_load'];
		$text_sel_file = $this->util->__('Select a File', 'Select File');
		echo "<script type='text/javascript'>/* <![CDATA[ */var post_id = {$this->options['post_id']};/* ]]> */</script>\n";
?>
<table class="form-table">
	<tr>
		<th scope="row"><?php echo $this->texts['prev2']; ?></th>
		<td>
			<input type="text" name="auto-thickbox-plus[img_prev]" value="<?php echo $img_prev; ?>" style="width:70%"<?php $this->util->disabled($img_prev_none); ?> />
			<input type="button" class="media-uploader button" value="<?php echo $text_sel_file; ?>" />
			<label><input type="checkbox" name="auto-thickbox-plus[img_prev]" value="none"<?php $this->util->checked($img_prev_none); ?> onclick="disableOption(this)" />
			<?php echo $this->texts['none']; ?></label>
		</td>
	</tr>
	<tr>
		<th scope="row"><?php echo $this->texts['next2']; ?></th>
		<td>
			<input type="text" name="auto-thickbox-plus[img_next]" value="<?php echo $img_next; ?>" style="width:70%"<?php $this->util->disabled($img_next_none); ?> />
			<input type="button" class="media-uploader button" value="<?php echo $text_sel_file; ?>" />
			<label><input type="checkbox" name="auto-thickbox-plus[img_next]" value="none"<?php $this->util->checked($img_next_none); ?> onclick="disableOption(this)" />
			<?php echo $this->texts['none']; ?></label>
		</td>
	</tr>
	<tr>
		<th scope="row"><?php echo $this->texts['first2']; ?></th>
		<td>
			<input type="text" name="auto-thickbox-plus[img_first]" value="<?php echo $img_first; ?>" style="width:70%"<?php $this->util->disabled($img_first_none); ?> />
			<input type="button" class="media-uploader button" value="<?php echo $text_sel_file; ?>" />
			<label><input type="checkbox" name="auto-thickbox-plus[img_first]" value="none"<?php $this->util->checked($img_first_none); ?> onclick="disableOption(this)" />
			<?php echo $this->texts['none']; ?></label>
		</td>
	</tr>
	<tr>
		<th scope="row"><?php echo $this->texts['last2']; ?></th>
		<td>
			<input type="text" name="auto-thickbox-plus[img_last]" value="<?php echo $img_last; ?>" style="width:70%"<?php $this->util->disabled($img_last_none); ?> />
			<input type="button" class="media-uploader button" value="<?php echo $text_sel_file; ?>" />
			<label><input type="checkbox" name="auto-thickbox-plus[img_last]" value="none"<?php $this->util->checked($img_last_none); ?> onclick="disableOption(this)" />
			<?php echo $this->texts['none']; ?></label>
		</td>
	</tr>
	<tr>
		<th scope="row"><?php echo $this->texts['close']; ?></th>
		<td>
			<input type="text" name="auto-thickbox-plus[img_close]" value="<?php echo $img_close; ?>" style="width:70%"<?php $this->util->disabled($img_close_none); ?> />
			<input type="button" class="media-uploader button" value="<?php echo $text_sel_file; ?>" />
			<label><input type="checkbox" name="auto-thickbox-plus[img_close]" value="none"<?php $this->util->checked($img_close_none); ?> onclick="disableOption(this)" />
			<?php echo $this->texts['none']; ?></label>
		</td>
	</tr>
	<tr>
		<th scope="row"><?php echo $this->texts['close']; ?> (<?php $this->util->_e('Button'); ?>)</th>
		<td>
			<input type="text" name="auto-thickbox-plus[img_close_btn]" value="<?php echo $img_close_btn; ?>" style="width:70%"<?php $this->util->disabled($img_close_btn_none); ?> />
			<input type="button" class="media-uploader button" value="<?php echo $text_sel_file; ?>" />
			<label><input type="checkbox" name="auto-thickbox-plus[img_close_btn]" value="none"<?php $this->util->checked($img_close_btn_none); ?> onclick="disableOption(this)" />
			<?php echo $this->texts['none']; ?></label>
		</td>
	</tr>
	<tr>
		<th scope="row"><?php $this->util->_e('Loading&#8230;', 'Loading...'); ?></th>
		<td>
			<input type="text" name="auto-thickbox-plus[img_load]" value="<?php echo $img_load; ?>" style="width:70%"<?php $this->util->disabled($img_load_none); ?> />
			<input type="button" class="media-uploader button" value="<?php echo $text_sel_file; ?>" />
			<label><input type="checkbox" name="auto-thickbox-plus[img_load]" value="none"<?php $this->util->checked($img_load_none); ?> onclick="disableOption(this)" />
			<?php echo $this->texts['none']; ?></label>
		</td>
	</tr>
</table>
<?php
	}

	function effect_metabox() {
		$effect_title_disabled = $this->options['position_title'] == 'none';
		$effect_cap_disabled = $this->options['position_cap'] == 'none';
		$effect_speed = $this->options['effect_speed'];
		$effect_speed_num = is_numeric($effect_speed);
		switch ($effect_speed) {
			case "fast": $effect_speed = "200"; break;
			case "normal": $effect_speed = "400"; break;
			case "slow": $effect_speed = "600"; break;
		}
?>
<table class="form-table">
	<tr>
		<th scope="row"><?php echo $this->texts['open']; ?></th>
		<td>
			<label class="item"><input type="radio" name="auto-thickbox-plus[effect_open]" value="zoom"<?php $this->util->checked($this->options['effect_open'], 'zoom'); ?> />
			<?php $this->util->_e('Zoom'); ?></label>
			<label class="item"><input type="radio" name="auto-thickbox-plus[effect_open]" value="slide"<?php $this->util->checked($this->options['effect_open'], 'slide'); ?> />
			<?php $this->util->_e('Slide'); ?></label>
			<label class="item"><input type="radio" name="auto-thickbox-plus[effect_open]" value="fade"<?php $this->util->checked($this->options['effect_open'], 'fade'); ?> />
			<?php $this->util->_e('Fade'); ?></label>
			<label class="item"><input type="radio" name="auto-thickbox-plus[effect_open]" value="none"<?php $this->util->checked($this->options['effect_open'], 'none'); ?> />
			<?php echo $this->texts['none']; ?></label>
		</td>
	</tr>
	<tr>
		<th scope="row"><?php echo $this->texts['close']; ?></th>
		<td>
			<label class="item"><input type="radio" name="auto-thickbox-plus[effect_close]" value="zoom"<?php $this->util->checked($this->options['effect_close'], 'zoom'); ?> />
			<?php $this->util->_e('Zoom'); ?></label>
			<label class="item"><input type="radio" name="auto-thickbox-plus[effect_close]" value="slide"<?php $this->util->checked($this->options['effect_close'], 'slide'); ?> />
			<?php $this->util->_e('Slide'); ?></label>
			<label class="item"><input type="radio" name="auto-thickbox-plus[effect_close]" value="fade"<?php $this->util->checked($this->options['effect_close'], 'fade'); ?> />
			<?php $this->util->_e('Fade'); ?></label>
			<label class="item"><input type="radio" name="auto-thickbox-plus[effect_close]" value="none"<?php $this->util->checked($this->options['effect_close'], 'none'); ?> />
			<?php echo $this->texts['none']; ?></label>
		</td>
	</tr>
	<tr>
		<th scope="row"><?php $this->util->_e('Transition'); ?></th>
		<td>
			<label class="item"><input type="radio" name="auto-thickbox-plus[effect_trans]" value="zoom"<?php $this->util->checked($this->options['effect_trans'], 'zoom'); ?> />
			<?php $this->util->_e('Zoom'); ?></label>
			<label class="item"><input type="radio" name="auto-thickbox-plus[effect_trans]" value="slide"<?php $this->util->checked($this->options['effect_trans'], 'slide'); ?> />
			<?php $this->util->_e('Slide'); ?></label>
			<label class="item"><input type="radio" name="auto-thickbox-plus[effect_trans]" value="fade"<?php $this->util->checked($this->options['effect_trans'], 'fade'); ?> />
			<?php $this->util->_e('Fade'); ?></label>
			<label class="item"><input type="radio" name="auto-thickbox-plus[effect_trans]" value="none"<?php $this->util->checked($this->options['effect_trans'], 'none'); ?> />
			<?php echo $this->texts['none']; ?></label>
		</td>
	</tr>
	<tr>
		<th scope="row"><?php $this->util->_e('Title'); ?></th>
		<td>
			<label class="item"><input type="radio" name="auto-thickbox-plus[effect_title]" value="zoom"<?php $this->util->checked($this->options['effect_title'], 'zoom'); $this->util->disabled($effect_title_disabled); ?> onclick="disableHideInitOption(this)" />
			<?php $this->util->_e('Zoom'); ?></label>
			<label class="item"><input type="radio" name="auto-thickbox-plus[effect_title]" value="slide"<?php $this->util->checked($this->options['effect_title'], 'slide'); $this->util->disabled($effect_title_disabled); ?> onclick="disableHideInitOption(this)" />
			<?php $this->util->_e('Slide'); ?></label>
			<label class="item"><input type="radio" name="auto-thickbox-plus[effect_title]" value="fade"<?php $this->util->checked($this->options['effect_title'], 'fade'); $this->util->disabled($effect_title_disabled); ?> onclick="disableHideInitOption(this)" />
			<?php $this->util->_e('Fade'); ?></label>
			<label class="item"><input type="radio" name="auto-thickbox-plus[effect_title]" value="none"<?php $this->util->checked($this->options['effect_title'], 'none'); $this->util->disabled($effect_title_disabled); ?> onclick="disableHideInitOption(this)" />
			<?php echo $this->texts['none']; ?></label>
			<label class="item boundary"><input type="checkbox" name="auto-thickbox-plus[hide_title]"<?php $this->util->checked($this->options['hide_title'], 'on'); $this->util->disabled($this->options['effect_title'], 'none'); ?> />
			<?php $this->util->_e('Hide initially'); ?></label>
		</td>
	</tr>
	<tr>
		<th scope="row"><?php $this->util->_e('Caption'); ?></th>
		<td>
			<label class="item"><input type="radio" name="auto-thickbox-plus[effect_cap]" value="zoom"<?php $this->util->checked($this->options['effect_cap'], 'zoom'); $this->util->disabled($effect_cap_disabled); ?> onclick="disableHideInitOption(this)" />
			<?php $this->util->_e('Zoom'); ?></label>
			<label class="item"><input type="radio" name="auto-thickbox-plus[effect_cap]" value="slide"<?php $this->util->checked($this->options['effect_cap'], 'slide'); $this->util->disabled($effect_cap_disabled); ?> onclick="disableHideInitOption(this)" />
			<?php $this->util->_e('Slide'); ?></label>
			<label class="item"><input type="radio" name="auto-thickbox-plus[effect_cap]" value="fade"<?php $this->util->checked($this->options['effect_cap'], 'fade'); $this->util->disabled($effect_cap_disabled); ?> onclick="disableHideInitOption(this)" />
			<?php $this->util->_e('Fade'); ?></label>
			<label class="item"><input type="radio" name="auto-thickbox-plus[effect_cap]" value="none"<?php $this->util->checked($this->options['effect_cap'], 'none'); $this->util->disabled($effect_cap_disabled); ?> onclick="disableHideInitOption(this)" />
			<?php echo $this->texts['none']; ?></label>
			<label class="item boundary"><input type="checkbox" name="auto-thickbox-plus[hide_cap]"<?php $this->util->checked($this->options['hide_cap'], 'on'); $this->util->disabled($this->options['effect_cap'], 'none'); ?> />
			<?php $this->util->_e('Hide initially'); ?></label>
		</td>
	</tr>
	<tr>
		<th scope="row"><?php $this->util->_e('Speed'); ?></th>
		<td>
			<label class="item"><input type="radio" name="auto-thickbox-plus[effect_speed]" value="fast"<?php $this->util->checked($this->options['effect_speed'], 'fast'); ?> onclick="updateEffectSpeed(this)" />
			<?php $this->util->_e('Fast'); ?></label>
			<label class="item"><input type="radio" name="auto-thickbox-plus[effect_speed]" value="normal"<?php $this->util->checked($this->options['effect_speed'], 'normal'); ?> onclick="updateEffectSpeed(this)" />
			<?php $this->util->_e('Normal'); ?></label>
			<label class="item"><input type="radio" name="auto-thickbox-plus[effect_speed]" value="slow"<?php $this->util->checked($this->options['effect_speed'], 'slow'); ?> onclick="updateEffectSpeed(this)" />
			<?php $this->util->_e('Slow'); ?></label>
			<label class="item"><input type="radio" name="auto-thickbox-plus[effect_speed]" value="number"<?php $this->util->checked($effect_speed_num); ?> onclick="updateEffectSpeed(this)" />
				<input type="number" min="0" step="100" name="auto-thickbox-plus[effect_speed]" value="<?php echo $effect_speed; ?>"<?php $this->util->disabled(!$effect_speed_num); ?> class="small-text" /> ms</label>
		</td>
	</tr>
</table>
<?php
	}

	function about_metabox() {
?>
<ul class="about">
	<li class="wp"><a href="<?php $this->util->_e('http://attosoft.info/en/'); ?>blog/auto-thickbox-plus/" target="_blank"><?php $this->util->_e('Visit plugin site', 'Visit plugin homepage'); ?></a></li>
	<li class="star"><a href="http://wordpress.org/extend/plugins/auto-thickbox-plus/" target="_blank"><?php $this->util->_e('Put rating stars or vote compatibility (works/broken)'); ?></a></li>
	<li class="forum"><a href="http://wordpress.org/support/plugin/auto-thickbox-plus" target="_blank"><?php $this->util->_e('View support forum or post a new topic'); ?></a></li>
	<li class="l10n"><a href="http://wordpress.org/extend/plugins/auto-thickbox-plus/other_notes/#Localization" target="_blank"><?php $this->util->_e('Translate the plugin into your language'); ?></a></li>
	<li class="donate"><a href="<?php $this->util->_e('http://attosoft.info/en/'); ?>donate/" target="_blank"><?php $this->util->_e('Donate to support plugin development'); ?></a></li>
	<li class="contact"><a href="<?php $this->util->_e('http://attosoft.info/en/'); ?>contact/" target="_blank"><?php $this->util->_e('Contact me if you have any feedback'); ?></a></li>
</ul>
<?php
	}

	var $util;
	var $options, $options_def;
	var $texts;
	var $has_slider;
	var $settings_page_type = 'settings_page_auto-thickbox-plus';
	var $option_group = 'auto-thickbox-plus-options';

	function auto_thickbox_options(&$auto_thickbox) {
		$this->__construct($auto_thickbox); // for PHP4
	}

	function __construct(&$auto_thickbox) {
		add_action('admin_menu', array(&$this, 'register_options_page'));
		add_action('admin_init', array(&$this, 'register_options'));
		add_action('admin_print_scripts-' . $this->settings_page_type, array(&$this, 'register_scripts'));
		add_action('admin_print_styles-' . $this->settings_page_type, array(&$this, 'register_styles'));

		$this->util = &$auto_thickbox->util;
		$this->options_def = &$auto_thickbox->options_def;
		$this->options = &$auto_thickbox->options;
		$this->texts = &$auto_thickbox->texts;
	}

	function register_options() {
		register_setting( $this->option_group, 'auto-thickbox-plus', array(&$this, 'options_callback') );
	}

	var $checkboxes_on = array('wp_gallery', 'thickbox_img', 'thickbox_text', 'auto_resize_img',
		'key_close_esc', 'key_close_enter',
		'key_prev_angle', 'key_prev_left',
		'key_next_angle', 'key_next_right',
		'key_end_home_end');

	function options_callback($options) {
		if (isset($_POST['reset'])) {
			add_settings_error('general', 'settings_updated', $this->util->__('Settings reset.'), 'updated');
			return $this->options_def;
		}
		foreach ($this->checkboxes_on as $checkbox) {
			if (!isset($options[$checkbox]))
				$options[$checkbox] = 'off';
		}
		return $options;
	}
} # auto_thickbox_options

?>