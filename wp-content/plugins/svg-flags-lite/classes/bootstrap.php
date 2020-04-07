<?php

namespace WPGO_Plugins\SVG_Flags;

/*
 *    Main bootstrap class
 */
class BootStrap
{
    protected  $module_roots ;
    /* Main class constructor. */
    public function __construct()
    {
        $this->module_roots = Main::$module_roots;
        $this->load_supported_features();
    }
    
    /* Load plugin features. */
    public function load_supported_features()
    {
        $root = $this->module_roots['dir'];
        // load plugin constants/data
        require_once $root . 'classes/constants.php';
        $custom_plugin_data = new Constants( $this->module_roots );
        // echo "<pre>";
        // print_r($custom_plugin_data);
        // echo "</pre>";
        // links on the main plugin index page
        require_once $root . 'classes/utility.php';
        $utility = new Utility( $this->module_roots, $custom_plugin_data );
        $plugin_data = get_plugin_data( $this->module_roots['file'], false, false );
        // data to pass to certain classes
        $new_features_json = Utility::get_new_features_json();
        $new_features_arr = Utility::filter_and_decode_json( $new_features_json );
        // functionality for free version only
        $path = $root . 'classes/hooks.php';
        
        if ( file_exists( $path ) ) {
            require_once $path;
            new Hooks( $this->module_roots );
        }
        
        // enqueue plugin scripts
        require_once $root . 'classes/enqueue-scripts.php';
        new Enqueue_Scripts(
            $this->module_roots,
            $new_features_arr,
            $plugin_data,
            $custom_plugin_data
        );
        // plugin settings pages
        require_once $root . 'classes/settings.php';
        new Settings(
            $this->module_roots,
            $plugin_data,
            $custom_plugin_data,
            $utility
        );
        require_once $root . 'classes/settings-new-features.php';
        new New_Features_Settings(
            $this->module_roots,
            $new_features_arr,
            $plugin_data,
            $custom_plugin_data,
            $utility
        );
        require_once $root . 'classes/settings-welcome.php';
        new Welcome_Settings(
            $this->module_roots,
            $plugin_data,
            $custom_plugin_data,
            $utility
        );
        // register blocks
        require_once $root . 'classes/register-blocks.php';
        new Register_Blocks( $this->module_roots );
        // [svg-flag] shortcode
        require_once $root . 'classes/shortcodes/shortcodes.php';
        new Shortcodes( $this->module_roots, $custom_plugin_data );
        // links on the main plugin index page
        require_once $root . 'classes/links.php';
        new Flags_Links(
            $this->module_roots,
            $plugin_data,
            $custom_plugin_data,
            $utility
        );
        // run upgrade routine when plugin updated to new version
        require_once $root . 'classes/upgrade.php';
        new Flags_Upgrade( $this->module_roots, $custom_plugin_data );
        // localize plugin
        require_once $root . 'classes/localize.php';
        new Localize( $this->module_roots );
    }

}
/* End class definition */