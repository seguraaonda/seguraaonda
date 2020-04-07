<?php

namespace WPGO_Plugins\SVG_Flags;

/*
 *    Utility functions for SVG Flags
 */

class Utility
{

    protected $module_roots;

    /* Main class constructor. */
    public function __construct($module_roots, $custom_plugin_data)
    {
				$this->module_roots = $module_roots;
				$this->custom_plugin_data = $custom_plugin_data;
    }

    public static function build_el_attributes($class_attribute, $style_attribute, $title_attribute)
    {
        $el_attributes = "";
        if (!empty($class_attribute)) {
            $el_attributes .= ' class="';
            foreach ($class_attribute as $key => $value) {
                $el_attributes .= $value;
            }
            $el_attributes .= '"';
        }
        if (!empty($style_attribute)) {
            $el_attributes .= ' style="';
            foreach ($style_attribute as $key => $value) {
                $el_attributes .= $value;
            }
            $el_attributes .= '"';
        }
        if (!empty($title_attribute)) {
            $el_attributes .= ' title="' . $title_attribute . '"';
        }

        return $el_attributes;
    }

    // Build and return tab HTML. Numbered icon is added vi JS.
    public function build_settings_tabs_html($plugin_data)
    {			
        if (FREEMIUS_NAVIGATION === 'menu') {
            return '';
        }
				
        $settings_page_main_url = admin_url() . "options-general.php?page=" . $this->custom_plugin_data->settings_pages['settings']['slug'];
        $settings_page_new_features_url = admin_url() . "options-general.php?page=" . $this->custom_plugin_data->settings_pages['new-features']['slug'];
        $settings_page_welcome_url = admin_url() . "options-general.php?page=" . $this->custom_plugin_data->settings_pages['welcome']['slug'];

        $main_active = (isset($_GET['page']) && ($_GET['page'] === $this->custom_plugin_data->settings_pages['settings']['slug'])) ? ' nav-tab-active' : '';
				$new_features_active = (isset($_GET['page']) && ($_GET['page'] === $this->custom_plugin_data->settings_pages['new-features']['slug'])) ? ' nav-tab-active' : '';
				$welcome_active = (isset($_GET['page']) && ($_GET['page'] === $this->custom_plugin_data->settings_pages['welcome']['slug'])) ? ' nav-tab-active' : '';

        $tabs_list_html = '<h2 class="nav-tab-wrapper"><a href="' . $settings_page_main_url . '" class="nav-tab fs-tab' . $main_active . '">Settings</a><a href="' . $settings_page_new_features_url . '" class="nav-tab fs-tab' . $new_features_active . '">New Features</a><a href="' . $settings_page_welcome_url . '" class="nav-tab fs-tab' . $welcome_active . '">About</a></h2>';

        return $tabs_list_html;
    }

    public static function get_new_features_json()
    {
			return '[
				{
					"type": "pro",
					"banner_url": "new-features-pro-1.png",
					"version": "0.9.0",
					"date": "11th March 2020",
					"title": "New <code>[svg-flag]</code> Shortcode Attribute",
					"description": "<p style=\'margin:15px 0;\'>A new <code>custom_caption</code> shortcode attribute has been added to allow caption text to be updated.</p>",
					"learn_more_url": ""
				},
				{
					"type": "pro",
					"banner_url": "new-features-pro-2.png",
					"version": "0.9.0",
					"date": "11th March 2020",
					"title": "New <code>[svg-flag-image]</code> Shortcode Attributes",
					"description": "<p style=\'margin:15px 0;\'>Here\'s a list of the new attributes:</p><ul><li><code>id</code>&nbsp;- Add unique identifier for each flag.</li><li><code>flag_class</code>&nbsp;- Add custom CSS classes.</li><li><code>tooltip</code>&nbsp;- Show country name when flag hovered over.</li><li><code>custom_tooltip</code>&nbsp;- Display custom tooltip text.</li><li><code>custom_caption</code>&nbsp;- Display custom caption text.</li><li><code>border</code>&nbsp;- Add flag border (e.g. <code>1px blue solid</code>).</li><li><code>border-radius</code>&nbsp;- Add rounded corners to flag.</li><li><code>padding</code>&nbsp;- Add custom padding between border and flag.</li><li><code>margin</code>&nbsp;- Add custom margin outside of border.</li></ul>",
					"learn_more_url": ""
				},
				{
					"type": "pro",
					"banner_url": "new-features-pro-3.png",
					"version": "0.9.0",
					"date": "11th March 2020",
					"title": "New Editor Block Settings",
					"description": "<p style=\'margin:15px 0;\'>Here\'s a list of the new block settings:</p><ul><li><strong>ID</strong> - Unique identifier.</li><li><strong>Flag Class</strong> - Add custom CSS classes.</li><li><strong>Tooltip</strong> - Show country name when flag hovered over.</li><li><strong>Custom Tooltip</strong> - Display custom caption text.</li></ul>",
					"learn_more_url": ""
				},
				{
					"type": "free",
					"banner_url": "new-features-free-1.png",
					"version": "0.9.0",
					"date": "11th March 2020",
					"title": "New <code>[svg-flag]</code> Shortcode Attributes",
					"description": "<p style=\'margin:15px 0;\'>Here\'s a list of the new attributes:</p><ul><li><code>size</code> - Replaces with/height attributes.</li><li><code>size_unit</code> - Set flag size (px, em, vw etc.)</li><li><code>caption</code> - Display country name under SVG flag.</li><li><code>random</code> - Display random flag on each page load.</li><li><code>inline</code> - Display flag inline with text.</li></ul>",
					"learn_more_url": ""
				},
				{
					"type": "free",
					"banner_url": "new-features-free-2.png",
					"version": "0.9.0",
					"date": "11th March 2020",
					"title": "New <code>[svg-flag-image]</code> Shortcode",
					"description": "<p style=\'margin:15px 0;\'>This shortcode adds a flag as an SVG image inside an <code>&lt;img&gt;</code> element (rather than as a background image). Here\'s a list of the supported attributes:</p><ul><li><code>size</code> - Replaces with/height attributes.</li><li><code>size_unit</code> - Set flag size (px, em, vw etc.)</li><li><code>square</code> - Set to true for 1:1 aspect ratio (default is 4:3).</li><li><code>caption</code> - Display country name under SVG flag.</li><li><code>random</code> - Display random flag on each page load.</li><li><code>inline</code> - Display flag inline with text.</li></ul>",
					"learn_more_url": ""
				},
				{
					"type": "free",
					"banner_url": "new-features-free-3.png",
					"version": "0.9.0",
					"date": "11th March 2020",
					"title": "New Editor Block",
					"description": "<p style=\'margin:15px 0;\'>This block replaces the <code>[svg-flag]</code> shortcode to display an SVG flag as background image. Here\'s a list of the currently supported block settings:</p><ul><li><strong>Flag</strong>&nbsp;- Select a specific SVG flag.</li><li><strong>Size</strong>&nbsp;- Specify the numerical size of flag.</li><li><strong>Size Unit</strong>&nbsp;- Select the size unit (px, em, vw etc.)</li><li><strong>Square</strong>&nbsp;- Use 1:1 or 4:3 aspect ratio.</li><li><strong>Caption</strong>&nbsp;- Display country name under SVG flag.</li><li><strong>Random</strong>&nbsp;- Display random flag on each page load.</li><li><strong>Inline</strong>&nbsp;- Display flag inline with text.</li></ul>",
					"learn_more_url": ""
				}
			]';
    }

    public static function filter_and_decode_json($data)
    {
        $new_features = json_decode($data);

        // echo "<pre>";
        // echo gettype($new_features);
        // print_r($new_features);
        // echo "</pre>";
        //echo ">>>>>>>>>>>> >>>>>>>>>>>> type:" . gettype($new_features);

        if (svg_flags_fs()->is_premium()) {
            // remove all free entries
            foreach ($new_features as $key => $new_feature) {
                if ($new_feature->type === 'free') {
                    unset($new_features[$key]);
                }
            }
            $new_features = array_values($new_features); // reindex array
        }

        // echo "<pre>";
        // echo gettype($new_features);
        // print_r($new_features);
        // echo "</pre>";

        return $new_features;
		}
} /* End class definition */
