<?php

namespace WPGO_Plugins\SVG_Flags;

/*
 *    Main plugin settings page
 */

class Settings
{

    protected $module_roots;

    /* Main class constructor. */
    public function __construct($module_roots, $plugin_data, $custom_plugin_data, $utility)
    {
        $this->module_roots = $module_roots;
				$this->custom_plugin_data = $custom_plugin_data;
				$this->freemius_upgrade_url = $this->custom_plugin_data->freemius_upgrade_url;
        $this->utility = $utility;
				
        $this->pro_attribute = $this->custom_plugin_data->is_premium ? '' : '<span class="pro" title="Upgrade now for immediate access to this feature"><a href="' . $this->freemius_upgrade_url . '">PRO</a></span>';
        $this->settings_slug = $this->custom_plugin_data->settings_pages['settings']['slug'];
        $this->new_features_slug = $this->custom_plugin_data->settings_pages['new-features']['slug'];
        $this->welcome_slug = $this->custom_plugin_data->settings_pages['welcome']['slug'];

        $this->plugin_data = $plugin_data;

        add_action('admin_init', array(&$this, 'init'));
        add_action('admin_menu', array(&$this, 'add_options_page'));
        add_filter('custom_menu_order', array(&$this, 'filter_menu_order')); // enable custom menu ordering
    }

    /* Init plugin options to white list our options. */
    public function init()
    {
        $pfx = $this->custom_plugin_data->plugin_settings_prefix;
        register_setting($pfx . '_plugin_options', $pfx . '_options', array(&$this, 'validate_options'));
    }

    /* Sanitize and validate input. Accepts an array, return a sanitized array. */
    public function validate_options($input)
    {
        // Strip html from textboxes
        // e.g. $input['textbox'] =  wp_filter_nohtml_kses($input['textbox']);
        //$input['txt_page_ids'] = sanitize_text_field( $input['txt_page_ids'] );
        return $input;
    }

    /* Add menu page. */
    public function add_options_page()
    {
        // echo "<pre>";
        // echo ">>>>>>>>>>>> >>>>>>>>>>>> " . $this->settings_slug;
        // echo "</pre>";

        if ($this->custom_plugin_data->menu_type === 'top') {
            // Add main plugin settings page as a top-level menu item
            add_menu_page(
                __('SVG Flags Settings Page', 'svg-flags'),
                __('SVG Flags', 'svg-flags'),
                'manage_options',
                $this->settings_slug,
                array(&$this, 'render_form'),
                'dashicons-flag',
                82
            );
        } else if ($this->custom_plugin_data->menu_type === 'sub') {
            // Add main plugin settings page as a submenu of 'Settings'
            add_options_page(
                __('SVG Flags Settings Page', 'svg-flags'),
                __('SVG Flags', 'svg-flags'),
                'manage_options',
                $this->settings_slug,
                array(&$this, 'render_form')
            );
        }
    }

    /* Display the menu page. */
    public function render_form()
    {
				$tabs_list_html = $this->utility->build_settings_tabs_html($this->plugin_data);
				$tab_classes = FREEMIUS_NAVIGATION === 'tabs' ? ' fs-section fs-full-size-wrapper' : ' no-tabs';
        ?>
   		<div class="wrap main<?php echo $tab_classes; ?>">

			<?php echo $tabs_list_html; ?>

			<div class="wpgo-settings-inner">
				<h1 class="heading"><?php _e('Welcome to SVG Flags!', 'svg-flags');?></h1>
				<div style="margin:20px 0 10px;font-size:14px;line-height:1.4em;">Take a look at the SVG Flags <a href="https://demo.wpgothemes.com/flexr/svg-flags-demo/" target="_blank">live demo</a> to see the plugin in action.</div>

				<div>
					<span><a class="plugin-btn" href="https://demo.wpgothemes.com/flexr/svg-flags-demo/" target="_blank">Launch Demo</a></span>
					<?php	if (svg_flags_fs()->is_premium()): ?>
					<span style="margin-left:10px;"><a class="plugin-btn" href="https://wpgoplugins.com/document/svg-flags-documentation/" target="_blank">Documentation</a></span>
					<?php endif;?>
				</div>

				<h2 style="margin:35px 0 0 0;">SVG Flags Shortcodes</h2>

				<div class="wpgo-expand-box" style="margin-top:20px;">
					<h4>Available Shortcodes & Attributes</h4><button id="shortcodes-btn" class="button">Expand <span style="vertical-align:sub;width:16px;height:16px;font-size:16px;" class="dashicons dashicons-arrow-down-alt2"></span></button>

					<div id="shortcodes-wrap">
						<p>Click on the shortcodes below to view the full documentation for each shortcode. Default values are always used for missing shortcode attributes. i.e. Override only the values you want to change.</p>
						<p style="margin:20px 0 0 0;"><code><a class="code-link" href="https://wpgoplugins.com/document/svg-flags-documentation/#svg-flag" target="_blank">[svg-flag]</a></code> <?php printf(__('Displays a single flag as a background SVG image.', 'svg-flags'));?></p>
						<p>Here are the available shortcode attributes along with default values:</p>
						<ul class="shortcode-attributes">
							<li><code>flag='gb'</code> - The alpha-2 country code for the flag you wish to display. <strong>See the full list <a href="https://www.iban.com/country-codes" target="_blank">here</strong></a>.</li>
							<li><code>size='5'</code> - Controls the width and height of the flag. (Replaces the previous individual width/height attributes).</li>
							<li><code>size_unit='em'</code> - Controls the unit used to render the size of the flag.</li>
							<li><code>square='false'</code> - Display flag in 4:3 ratio (default) or in square format (1:1).</li>
							<li><code>caption='false'</code> - Display a caption with the country name underneath the flag.</li>							
							<li><code>random='false'</code> - Display a random flag on each page load!</li>
							<li><code>inline="false"</code> - Display SVG flag as a block level element (default), or inline with text.</li>
							<li><code>id=''</code> <?php echo $this->pro_attribute; ?> - Specify a unique ID for each flag.</li>
							<li><code>flag_class=''</code> <?php echo $this->pro_attribute; ?> - Add custom CSS classes for easy styling.</li>
							<li><code>tooltip='false'</code> <?php echo $this->pro_attribute; ?> - Enable tooltips when flag hovered over.</li>
							<li><code>custom_tooltip=''</code> <?php echo $this->pro_attribute; ?> - Displays custom text when the flag is hovered over. For this to have any effect the <code>tooltip="true"</code> attribute needs to be specified.</li>
							<li><code>custom_caption=''</code> <?php echo $this->pro_attribute; ?> - Displays custom flag caption.</li>
						</ul>

						<p style="margin:25px 0 0 0;"><code><a class="code-link" href="https://wpgoplugins.com/document/svg-flags-documentation/#svg-flag-image" target="_blank">[svg-flag-image]</a></code> <?php printf(__('Displays a single flag as an SVG image. Contains more flexible display options than the [svg-flag] shortcode.', 'svg-flags'));?></p>
						<p>Here are the available shortcode attributes along with default values:</p>
						<ul class="shortcode-attributes">
							<li><code>flag='gb'</code> - The alpha-2 country code for the flag you wish to display. <strong>See the full list <a href="https://www.iban.com/country-codes" target="_blank">here</strong></a>.</li>
							<li><code>size='5'</code> - Controls the width and height of the flag. (Replaces the previous individual width/height attributes).</li>
							<li><code>size_unit='em'</code> - Controls the unit used to render the size of the flag.</li>
							<li><code>square='false'</code> - Display flag in 4:3 ratio (default) or in square format (1:1).</li>
							<li><code>caption='false'</code> - Display a caption with the country name underneath the flag.</li>							
							<li><code>random='false'</code> - Display a random flag on each page load!</li>
							<li><code>inline="false"</code> - Display SVG flag as a block level element (default), or inline with text.</li>
							<li><code>id=''</code> <?php echo $this->pro_attribute; ?> - Specify a unique ID for each flag.</li>
							<li><code>flag_class=''</code> <?php echo $this->pro_attribute; ?> - Add custom CSS classes for easy styling.</li>
							<li><code>tooltip='false'</code> <?php echo $this->pro_attribute; ?> - Enable tooltips when flag hovered over.</li>
							<li><code>custom_tooltip=''</code> <?php echo $this->pro_attribute; ?> - Displays custom text when the flag is hovered over. For this to have any effect the <code>tooltip="true"</code> attribute needs to be specified.</li>
							<li><code>custom_caption=''</code> <?php echo $this->pro_attribute; ?> - Displays custom flag caption.</li>							
							<li><code>border=''</code> <?php echo $this->pro_attribute; ?> - Add a border around the flag. e.g. <code>1px blue solid</code>.</li>
							<li><code>border_radius=''</code> <?php echo $this->pro_attribute; ?> - Add rounded corners to a flag. e.g. <code>3px</code>.</li>
							<li><code>padding=''</code> <?php echo $this->pro_attribute; ?> - Add custom padding between the flag and border.</li>
							<li><code>margin=''</code> <?php echo $this->pro_attribute; ?> - Add custom margin outside of the flag border.</li>
						</ul>
					</div>
				</div>

				<h2 style="margin:35px 0 0 0;">SVG Flags Blocks</h2>
				<div class="wpgo-expand-box">
					<h4 style="margin-top:5px;display:inline-block;margin-bottom:10px;">Available Blocks and Settings</h4><button id="blocks-btn" class="button">Expand <span style="vertical-align:sub;width:16px;height:16px;font-size:16px;" class="dashicons dashicons-arrow-down-alt2"></span></button>
					<div id="blocks-wrap">
						<p>Blocks are a fantastic alternative to using shortcodes as they allow you to add content visually rather than having to remember all the available shortcode attributes.</p>
						<h3 style="margin:20px 0 0 0;">SVG Flag Block</h3>
						<p>This block is a direct replacement to the <code>[svg-flag]</code>shortcode and displays a single flag as a background SVG image.</p>
						<p><img style="width:450px;" src="<?php echo $this->module_roots['uri'] . '/assets/images/svg-flag-block-test-free-settings.png'; ?>"></p>
						<p>Available block settings are:</p>
						<ul class="shortcode-attributes">
							<li><code>flag='gb'</code> - The alpha-2 country code for the flag you wish to display. <strong>See the full list <a href="https://www.iban.com/country-codes" target="_blank">here</strong></a>.</li>
							<li><code>size='5'</code> - Controls the width and height of the flag. (Replaces the previous individual width/height attributes).</li>
							<li><code>size_unit='em'</code> - Controls the unit used to render the size of the flag.</li>
							<li><code>square='false'</code> - Display flag in 4:3 ratio (default) or in square format (1:1).</li>
							<li style="opacity:0.35;"><code>caption='false'</code> <em>(coming soon)</em> - Display a caption with the country name underneath the flag.</li>							
							<li><code>random='false'</code> - Display a random flag on each page load!</li>
							<li style="opacity:0.35;"><code>inline="false"</code> <em>(coming soon)</em> - Display SVG flag as a block level element (default), or inline with text.</li>
							<li><code>id=''</code> <?php echo $this->pro_attribute; ?> - Specify a unique ID for each flag.</li>
							<li><code>flag_class=''</code> <?php echo $this->pro_attribute; ?> - Add custom CSS classes for easy styling.</li>
							<li><code>tooltip='false'</code> <?php echo $this->pro_attribute; ?> - Enable tooltips when flag hovered over.</li>
							<li style="opacity:0.35;"><code>custom_tooltip=''</code> <?php echo $this->pro_attribute; ?> <em>(coming soon)</em> - Displays custom text when the flag is hovered over. For this to have any effect the <code>tooltip="true"</code> attribute needs to be specified.</li>
							<li style="opacity:0.35;"><code>custom_caption=''</code> <?php echo $this->pro_attribute; ?> <em>(coming soon)</em> - Displays custom flag caption.</li>
						</ul>
					</div>
				</div>

				<div style="margin-top:25px;"></div>

				<table class="form-table">

					<?php do_action('plugin_settings_row_section_1');?>

					<tr valign="top">
						<th scope="row">Try our other top plugins!</th>
						<td>
							<table class="other-plugins-tbl">
								<tr><td><a class="plugin-image-link" href="https://wpgoplugins.com/plugins/simple-sitemap/" target="_blank"><img src="<?php echo $this->module_roots['uri']; ?>/assets/images/simple-sitemap-thumb.png"></a></td></tr>
								<tr><td class="plugin-text-link"><div><h3><a href="https://wpgoplugins.com/plugins/simple-sitemap/" target="_blank">Simple Sitemap</a></h3></div></td></tr>
							</table>
							<table class="other-plugins-tbl">
								<tr><td><a class="plugin-image-link" href="https://wpgoplugins.com/plugins/flexible-faqs/" target="_blank"><img src="<?php echo $this->module_roots['uri']; ?>/assets/images/flexible-faq-thumb.png"></a></td></tr>
								<tr><td class="plugin-text-link"><div><h3><a href="https://wpgoplugins.com/plugins/flexible-faqs/" target="_blank">Flexible FAQs</a></h3></div></td></tr>
							</table>
							<table class="other-plugins-tbl">
								<tr><td><a class="plugin-image-link" href="https://wpgoplugins.com/plugins/content-censor/" target="_blank"><img src="<?php echo $this->module_roots['uri']; ?>/assets/images/content-censor-thumb.png"></a></td></tr>
								<tr><td class="plugin-text-link"><div><h3><a href="https://wpgoplugins.com/plugins/content-censor/" target="_blank">Content Censor</a></h3></div></td></tr>
							</table>
							<table class="other-plugins-tbl">
								<tr><td><a class="plugin-image-link" href="https://wpgoplugins.com/plugins/seo-media-manager/" target="_blank"><img src="<?php echo $this->module_roots['uri']; ?>/assets/images/seo-media-manager-thumb.png"></a></td></tr>
								<tr><td class="plugin-text-link"><div><h3><a href="https://wpgoplugins.com/plugins/seo-media-manager/" target="_blank">SEO Media Manager</a></h3></div></td></tr>
							</table>
						</td>
					</tr>

					<tr valign="top">
						<th scope="row">Read all about it!</th>
						<td>
							<p>Subscribe to our newsletter for news and updates about the latest development work. Be the first to find out about future projects and exclusive promotions.</p>
							<div><a class="plugin-btn" target="_blank" href="http://eepurl.com/bXZmmD">Sign Me Up!</a></div>
						</td>
					</tr>

					<tr valign="top">
						<th scope="row">Keep in touch...</th>
						<td>
							<div><p style="margin-bottom:10px;">Come and say hello. I'd love to hear from you!</p>
								<span><a class="social-link" href="http://www.twitter.com/dgwyer" title="Follow us on Twitter" target="_blank"><img src="<?php echo $this->module_roots['uri']; ?>/assets/images/twitter.png" /></a></span>
								<span><a class="social-link" href="https://www.facebook.com/wpgoplugins/" title="Our Facebook page" target="_blank"><img src="<?php echo $this->module_roots['uri']; ?>/assets/images/facebook.png" /></a></span>
								<span><a class="social-link" href="https://www.youtube.com/channel/UCWzjTLWoyMgtIfpDgJavrTg" title="View our YouTube channel" target="_blank"><img src="<?php echo $this->module_roots['uri']; ?>/assets/images/yt.png" /></a></span>
								<span><a style="text-decoration:none;" title="Need help with ANY aspect of WordPress? We're here to help!" href="https://wpgoplugins.com/need-help-with-wordpress/" target="_blank"><span style="margin-left:-2px;color:#d41515;font-size:39px;line-height:32px;width:39px;height:39px;" class="dashicons dashicons-sos"></span></a></span>
							</div>
						</td>
					</tr>

					<tr valign="top">
						<th scope="row">Report any issues</th>
						<td>
							<div style="margin-bottom:50px;"><p>Please <a href="https://wpgoplugins.com/contact" target="_blank">report</a> any plugin issues, or suggest additional features. We read every single message. <span style="font-weight:bold;">All feedback is welcome!</span></p></div>
						</td>
					</tr>

				</table>

			</div>

		</div>
		<?php
}

    public function filter_menu_order($custom)
    {
        global $submenu;

        // selectively rearrange for 'tabs', and always for 'menu'
        if (FREEMIUS_NAVIGATION === 'tabs') {
            // don't bother to rearrange unless the submenu page is displayed
            if (
                !isset($_GET['page'])
                || ($_GET['page'] !== $this->new_features_slug && $_GET['page'] !== $this->welcome_slug)
            ) {
                return $custom;
            }
				}
				
        $parent_slug = $this->custom_plugin_data->parent_slug;
        $menu_type = $this->custom_plugin_data->menu_type;
        $pricingpage_index = 0;
        $parent_index = 0;
        $subpage_index1 = 0;
        $subpage_index2 = 0;

				// if global menu array is empty then don't try to reindex. This cis typically empty when the Freemius
				// optin is displayed.
				if(empty($submenu[$parent_slug])) {
					return $custom;
				}

        // store menu indexes of settings pages
        foreach ($submenu[$parent_slug] as $key => $val) {

            //echo "type:" . gettype($key);
            if ($val[2] === $this->settings_slug) {
                $parent_index = $key;
            }
            if ($val[2] === $this->new_features_slug) {
                $subpage_index1 = $key;
            }
            if ($val[2] === $this->welcome_slug) {
                $subpage_index2 = $key;
            }
            if ($val[2] === $this->settings_slug . '-pricing') {
                $pricingpage_index = $key;
            }
        }

        // only reindex new features page if menu type is 'sub'
        if ($menu_type === 'sub') {
            // only reindex if tabs are active and we're on new feature settings page OR tabs are not active
            if (
                (FREEMIUS_NAVIGATION === 'tabs' && $_GET['page'] === $this->new_features_slug)
                || FREEMIUS_NAVIGATION === 'menu'
            ) {
                // find the next available index after the main settings page
                $tmp_parent_index1 = $parent_index;
                while (isset($submenu[$parent_slug][$tmp_parent_index1])) {
                    $tmp_parent_index1++;
                }
                // move new features page to next position after main settings page
                $submenu[$parent_slug][$tmp_parent_index1] = $submenu[$parent_slug][$subpage_index1];
                unset($submenu[$parent_slug][$subpage_index1]);
                ksort($submenu[$parent_slug]);
            }
        }

        // only reindex if tabs are active and we're on welcome settings page OR tabs are not active
        if (
            (FREEMIUS_NAVIGATION === 'tabs' && $_GET['page'] === $this->welcome_slug)
            || FREEMIUS_NAVIGATION === 'menu'
        ) {
            // find the next available index after the pricing page unless tabs are active in which case get next
            // available index after main settings page
            if (FREEMIUS_NAVIGATION === 'tabs') {
                $tmp_parent_index2 = $parent_index;
            } else {
                $tmp_parent_index2 = $pricingpage_index;
            }
            while (isset($submenu[$parent_slug][$tmp_parent_index2])) {
                $tmp_parent_index2++;
            }
            // move welcome page to next position after pricing page
            $submenu[$parent_slug][$tmp_parent_index2] = $submenu[$parent_slug][$subpage_index2];
            unset($submenu[$parent_slug][$subpage_index2]);
            ksort($submenu[$parent_slug]);
				}
				
        // echo "<pre>";
        // echo 'BEFORE:';
        // print_r($submenu[$parent_slug]);
        // //print_r($submenu[$parent_slug][$tmp_parent_index1]);
        // //print_r($submenu[$parent_slug][$tmp_parent_index2]);
        // echo "Settings slug: " . $this->settings_slug . '<br>';
        // echo "Parent slug: " . $parent_slug . "<br>";
        // echo "Pricing-I: " . $pricingpage_index . "<br>";
        // echo "Parent-I: " . $parent_index . "<br>";
        // echo "TPI1: " . $tmp_parent_index1 . "<br>";
        // echo "TPI2: " . $tmp_parent_index2 . "<br>";
        // echo "SI1: " . $subpage_index1 . "<br>";
        // echo "SI2: " . $subpage_index2 . "<br>";
        // echo "</pre>";
        // die();

        return $custom;
    }

} /* End class definition */