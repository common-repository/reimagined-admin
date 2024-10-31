<?php
/**
Plugin Name: Reimagined
Plugin URI: http://roundmedia.co.uk
Description: A Reimagined WordPress Admin Theme
Version: 1.0
Author: ciaranbelfast
Author URI: http://www.roundmedia.co.uk
License: GPLv2 or later
*/

/*
 This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
*/

class Reimagined_Admin_Theme {

	private $menus,
					$submenus,
					$settings,
					$settings_name = 'reimagined_admin_theme_option'
					;
/* Actions*/
	function __construct() {
		add_action( 'admin_menu', array( $this, 'reimagine_add_menu' ) );		
		add_action( 'admin_init', array( $this, 'reimagine_register_settings' ), 20 );		
		add_action( 'admin_enqueue_scripts', array( $this, 'reimagine_admin_scripts' ), 20 );
		add_action( 'admin_enqueue_scripts', array( $this, 'reimagine_admin_back' ), 20 );
		add_action( 'admin_bar_menu', array( $this, 'reimagine_admin_bar'), 999 );
		
		add_action( 'login_enqueue_scripts', array( $this, 'reimagine_login' ), 50 );
		add_action( 'login_enqueue_scripts', array( $this, 'reimagine_login_back'), 50 );
		add_action('wp_ajax_update_menu_positions', 'reimagined_update_menu_positions');
		add_action('admin_enqueue_scripts', 'reimagined_admin_enqueues');


/* Filters */

add_filter('custom_menu_order', 'reimagined_custom_menu_order');
		add_filter('menu_order', 'reimagined_custom_menu_order');
		add_filter( 'admin_footer_text', array( $this, 'reimagine_admin_footer' ), 40 );
		add_filter( 'update_footer', array( $this, 'reimagine_admin_footer' ), 40 );
		add_filter( 'gettext_with_context', array( $this, 'disable_open_sans' ), 888, 4 );
		
	}

/* Functions */	
	
	function reimagine_add_menu() {
		add_submenu_page( 'themes.php', 'Reimagined', 'Reimagined', 'manage_options', 'reimagined_admin', array( $this, 'settings' ) ); 
	}
	function reimagine_register_settings() {
		
		$this->settings = get_option( $this->settings_name );
		register_setting( 'reimagined_admin', $this->settings_name );
	}
	function get_setting($arg){
		return ( (isset( $this->settings[$arg] ) && trim($this->settings[$arg]) !== '') ? $this->settings[$arg] : NULL);
	}
	function settings() {
		?>
		

		<script type="text/javascript">
			jQuery( document ).ready(function() {
				jQuery(document).on('click', '.box > h3, .box > h4, .toggle', function(){
					jQuery(this).next( ".hide" ).toggle();
				});
			});
		</script>

		<form method="post" id="form" action="options.php">
		<?php settings_fields( 'reimagined_admin' ); ?>
		<div class="wrap">
			<h2>Reimagined Admin Theme</h2>
			
			<div class="row reimagine clearfix">
				<div class="col col-8">
		
					<h3 class="a-reimagine"><span>Dash Icons & Colour Schemes</span></h3>
					<p class="no-reimagine text-reimagine">Change the WordPress Dash Icons and Default Appearance.</p>
					<div class="clearfix">
						<div class="box">
							<h4><span>Appearance</span></h4>
							<div class="box-body reimagine hide show">								
						
								<p>
									Icons Sets:<br>
									<label>
										<input name="<?php echo $this->settings_name; ?>[dash_icon]" type="radio" value="dashicons" <?php if ($this->get_setting('dash_icon') == 'dashicons' || $this->get_setting('dash_icon') !='default' ) echo 'checked="checked" '; ?>> 
										Custom Icon Set
									</label>
									<br>
									<label>
										<input name="<?php echo $this->settings_name; ?>[dash_icon]" type="radio" value="default" <?php if ($this->get_setting('dash_icon') == 'default') echo 'checked="checked" '; ?>> 
										Default WordPress Icon Set
									</label>
									<br>
									<br>
									<label>URL of Admin Background:
										<input name="<?php echo $this->settings_name; ?>[admin_back]" value="<?php echo $this->get_setting('admin_back'); ?>" type="text" class="widefat">
									</label>
									<br>
									<br>
									<label>
										<input name="<?php echo $this->settings_name; ?>[theme_color]" type="radio" value="Reimagined" <?php if ($this->get_setting('theme_color') == 'reimagined' || $this->get_setting('theme_color') !='default' ) echo 'checked="checked" '; ?>> 
										Reimagined
									</label>
									<br>
									<label>
										<input name="<?php echo $this->settings_name; ?>[theme_color]" type="radio" value="default" <?php if ($this->get_setting('theme_color') == 'default') echo 'checked="checked" '; ?>> 
										Default WordPress Colours
									</label>
								</p>
								<p><input type="submit" class="button button-primary a-reimagine" value="<?php _e('Save') ?>" /></p>
							</div>
						</div>
					</div>					
					<h3 class="a-reimagine"><span>WordPress Login Style</span></h3>
					<p class="no-reimagine text-reimagine">Customise the WordPress Login page</p>
					<div class="clearfix">
						<div class="box">
							<h4><span>WordPress Login Style</span></h4>
							<div class="box-body reimagine">
								<p>
									<label>URL of Logo
										<input name="<?php echo $this->settings_name; ?>[login_screen_logo]" value="<?php echo $this->get_setting('login_screen_logo'); ?>" type="text" class="widefat">
									</label>
									<label>URL of Background
										<input name="<?php echo $this->settings_name; ?>[login_back]" value="<?php echo $this->get_setting('login_back'); ?>" type="text" class="widefat">
									</label>
								</p>
								<p><input type="submit" class="button button-primary a-reimagine" value="<?php _e('Save') ?>" /></p>
							</div>
						</div>
					</div>
					
										<h3 class="a-reimagine"><span>WordPress Admin Bar</span></h3>
					<p class="no-reimagine text-reimagine">Allows you to customize the admin top bar</p>
					<div class="clearfix">
						<div class="box">
							<h4><span>WordPress Site Name & Logo</span></h4>
							<div class="box-body reimagine">
								<p>
									<label>
											Top Bar Logo (Enter Direct URL)
											<input name="<?php echo $this->settings_name; ?>[admin_bar_logo]" type="text" value="<?php echo $this->get_setting('admin_bar_logo'); ?>" class="widefat">
										</label>
									</p>
									<p>
										<label>
											Link (Link to an external page)
											<input name="<?php echo $this->settings_name; ?>[admin_bar_name_links]" type="text" value="<?php echo $this->get_setting('admin_bar_name_links'); ?>" class="widefat">
										</label>
									</p>
									<p>
										<label>
											Name Of Site
											<input name="<?php echo $this->settings_name; ?>[bar_name]" type="text" value="<?php echo $this->get_setting('bar_name'); ?>" class="widefat">
										</label>
									</p>
									<p>
										<label>
											<input name="<?php echo $this->settings_name; ?>[admin_bar_name_hide]" type="checkbox" <?php if ( $this->get_setting('admin_bar_name_hide') == true ) echo 'checked="checked" '; ?>> 
											Hide 'Name of Site'
										</label>
								</p>
								<p><input type="submit" class="button button-primary a-reimagine" value="<?php _e('Save') ?>" /></p>
							</div>
						</div>
					</div>
					<h3 class="a-reimagine"><span>WordPress Admin Quicklinks</span></h3>
					<p class="no-reimagine text-reimagine">Customize the Quicklinks in the WP Admin Bar</p>
					<div class="clearfix">
						<div class="box">
							<h4><span>WordPress Admin Bar Links</span></h4>
							<div class="box-body reimagine">
								<p>
									<fieldset>
											<label>
												<input name="<?php echo $this->settings_name; ?>[admin_bar_updates_hide]" type="checkbox" <?php if ($this->get_setting('admin_bar_updates_hide') == true) echo 'checked="checked" '; ?>> 
												Remove 'Updates' From Admin Bar
											</label>
											<br>
											<label>
												<input name="<?php echo $this->settings_name; ?>[admin_bar_comments_hide]" type="checkbox" <?php if ($this->get_setting('admin_bar_comments_hide') == true) echo 'checked="checked" '; ?>> 
												Remove 'Comments' From Admin Bar
											</label>
											<br>
											<label>
												<input name="<?php echo $this->settings_name; ?>[admin_bar_new_hide]" type="checkbox" <?php if ($this->get_setting('admin_bar_new_hide') == true) echo 'checked="checked" '; ?>> 
												Remove 'New' From Admin Bar
											</label>
										</fieldset>
								</p>
								<p><input type="submit" class="button button-primary a-reimagine" value="<?php _e('Save') ?>" /></p>
							</div>
						</div>
					</div>
					<h3 class="a-reimagine"><span>WordPress Admin Footer</span></h3>
					<p class="no-reimagine text-reimagine">Change the WordPress Admin Footer</p>
					<div class="clearfix">
						<div class="box">
							<h4><span>WordPress Admin Footer & Version No.</span></h4>
							<div class="box-body reimagine">
								<p>
									<label>Copyright Text
										<input name="<?php echo $this->settings_name; ?>[admin_footer_text]" value="<?php echo $this->get_setting('admin_footer_text'); ?>" type="text" class="widefat">
									</label>
								</p>
								<p>
									<label>
										<input name="<?php echo $this->settings_name; ?>[admin_footer_text_hide]" type="checkbox" <?php if ($this->get_setting('admin_footer_text_hide') == true) echo 'checked="checked" '; ?>> 
										Hide 'Copyright Text'
									</label>
								</p>
								<p>
									<label>Version No.
										<input name="<?php echo $this->settings_name; ?>[admin_footer_version]" value="<?php echo $this->get_setting('admin_footer_version'); ?>" type="text" class="widefat">
									</label>
								</p>
								<p>
									<label>
										<input name="<?php echo $this->settings_name; ?>[admin_footer_version_hide]" type="checkbox" <?php if ($this->get_setting('admin_footer_version_hide') == true) echo 'checked="checked" '; ?>> 
										Hide 'Version No.'
									</label>
								</p>
								<p><input type="submit" class="button button-primary a-reimagine" value="<?php _e('Save') ?>" /></p>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		</form>
		<?php
	}
	
	/* Scripts */
	
	function reimagine_admin_scripts() {		
		wp_register_style( 'font', plugins_url( "css/dashfonts.css", __FILE__ ), array());
		wp_enqueue_style( 'font' );

		wp_register_style( 'style', plugins_url( "css/reimagined.css", __FILE__ ), array());
		wp_enqueue_style( 'style' );

		
		
		if( $this->get_setting('dash_icon') !== 'default' ){
			wp_register_style( 'icon', plugins_url( "css/dashicons.css", __FILE__ ), array());
			wp_enqueue_style( 'icon' );
		}

		if( $this->get_setting('theme_color') !== 'default' ){
			wp_register_style( 'color', plugins_url( "css/adminmenu.css", __FILE__ ), array());
			wp_enqueue_style( 'color' );
		}
		if( $this->get_setting('admin_back') !== 'default' ){
		wp_register_style( 'admin_back', plugins_url( "css/menu-alt.css", __FILE__ ), array());
		wp_enqueue_style( 'admin_back' );
		}
	}
	
	/* Updating Menu & Plugin Specific Commands*/
	
	
	function reimagine_admin_bar(){
		global $wp_admin_bar;

		$all_toolbar_nodes = $wp_admin_bar->get_nodes();

		foreach ( $all_toolbar_nodes as $node ) {
			$args = $node;
			if($args->id == "site-name"){
				$logo = $this->get_setting('admin_bar_logo') ? sprintf('<img src="%s">', $this->get_setting('admin_bar_logo')) : '';
				$hide = $this->get_setting('admin_bar_name_hide') ? "hide" : "";
				$name = $this->get_setting('bar_name') ? $this->get_setting('bar_name') : $args->title;
				$args->title = sprintf('%s <span class="%s">%s</span>', $logo, $hide, $name);				
				$this->get_setting('admin_bar_name_links') && ($args->href = $this->get_setting('admin_bar_name_links'));
			}
			$wp_admin_bar->add_node( $args );
		}
		$wp_admin_bar->remove_node( 'wp-logo' );
		$wp_admin_bar->remove_node( 'view-site' );

		if($this->get_setting('admin_bar_updates_hide')){
				$wp_admin_bar->remove_node('updates');
		}
		if($this->get_setting('admin_bar_comments_hide')){
				$wp_admin_bar->remove_node('comments');
		}
		if($this->get_setting('admin_bar_new_hide')){
				$wp_admin_bar->remove_node('new-content');
		}
		if($this->get_setting('admin_bar_new_hide')){
				$wp_admin_bar->remove_node('new-content');
		}
	}
	function reimagine_admin_footer( $default ){
		if(  strpos($default, 'wordpress') === false ){
			if( $this->get_setting('admin_footer_version_hide') ){
				return '';
			}
			if( $this->get_setting('admin_footer_version') ){
				return $this->get_setting('admin_footer_version');
			}
		}else{
			if( $this->get_setting('admin_footer_text_hide') ){
				return '';
			}
			if( $this->get_setting('admin_footer_text') ){
				return $this->get_setting('admin_footer_text');
			}
		}
		return $default;
	}
	function reimagine_login() {
		add_filter( 'login_headerurl', array( $this, 'login_headerurl' ) );
		add_filter( 'login_headertitle', array( $this, 'login_headertitle' ) );

		$this->settings = get_option( $this->settings_name );
		if( $this->get_setting('login_screen_logo') ){
			?>
	    <style type="text/css">
	      body.login div#login h1 a {
	        background-image: url(<?php echo $this->get_setting('login_screen_logo'); ?>);
	    </style>
			<?php 
		}
	}
	
		function reimagine_login_back() {

	$this->settings = get_option( $this->settings_name );
		if( $this->get_setting('login_back') ){
			?>
	    <style type="text/css">
	      body {
	        background: url(<?php echo $this->get_setting('login_back'); ?>);
	      }
	    </style>
		
			<?php 
		}
	}
	
		function reimagine_admin_back() {

	$this->settings = get_option( $this->settings_name );
		if( $this->get_setting('admin_back') ){
			?>
			
	    <style type="text/css">
		
	      body {
	        background: url(<?php echo $this->get_setting('admin_back'); ?>); background-attachment: fixed;
	      }
	    </style>
		
			<?php 
		}
	}

	function login_headerurl() {
		return esc_url( trailingslashit( get_bloginfo( 'url' ) ) );
	}

	function login_headertitle() {
		return esc_attr( get_bloginfo( 'name' ) );
	}
	function disable_open_sans( $translations, $text, $context, $domain ) {
		if ( 'Open Sans font: on or off' == $context && 'on' == $text ) {
			$translations = 'off';
		}
		return $translations;
	}
	function deactivation() {
		delete_option( $this->settings_name );
	}

}
/* Reorder Functions */

function reimagined_update_menu_positions() {
    update_user_meta(get_current_user_id(), get_current_blog_id() . '_reimagined_menu_positions', str_replace('admin.php?page=', '', $_REQUEST['menu_item_positions']));
}

function reimagined_admin_enqueues() {
    wp_enqueue_script('jquery-ui-sortable');
    wp_enqueue_script('reimagined_admin', plugins_url('/js/reimagine-admin.js', __FILE__), array('jquery-ui-sortable'));
}

function reimagined_custom_menu_order($menu_order) {
    if (!$menu_order)
        return true;

    $new_menu_order = get_user_meta(get_current_user_id(), get_current_blog_id() . '_reimagined_menu_positions', true);

    if ($new_menu_order) {
        $new_menu_order = explode(',', $new_menu_order);

        return $new_menu_order;
    } else {
        return $menu_order;
    }
}
new Reimagined_Admin_Theme;