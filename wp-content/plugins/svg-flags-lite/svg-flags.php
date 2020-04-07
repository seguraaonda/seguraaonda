<?php

/*
Plugin Name: SVG Flags
Plugin URI: http://wordpress.org/plugins/svg-flags-lite/
Description: Easily add beautiful SVG flags to any HTML element!
Version: 0.9.0
Requires PHP: 7.0
Author: David Gwyer
Author URI: http://www.wpgoplugins.com
Text Domain: svg-flags
*/
/*  Copyright 2009 David Gwyer (email : david@wpgoplugins.com)

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 */
namespace WPGO_Plugins\SVG_Flags;

if ( !defined( 'ABSPATH' ) ) {
    exit;
}
// for now hard code this until a helper function is added so we can do something similar to:
// svg_flags_fs()->get_navigation()
define( "FREEMIUS_NAVIGATION", 'tabs' );
// menu|tabs

if ( function_exists( __NAMESPACE__ . '\\svg_flags_fs' ) ) {
    svg_flags_fs()->set_basename( false, __FILE__ );
} else {
    
    if ( !function_exists( __NAMESPACE__ . '\\svg_flags_fs' ) ) {
        // Create a helper function for easy SDK access.
        function svg_flags_fs()
        {
            global  $svg_flags_fs ;
            
            if ( !isset( $svg_flags_fs ) ) {
                // Include Freemius SDK.
                require_once dirname( __FILE__ ) . '/freemius/start.php';
                $svg_flags_fs = fs_dynamic_init( array(
                    'id'             => '5235',
                    'slug'           => 'svg-flags-lite',
                    'premium_slug'   => 'svg-flags-premium',
                    'type'           => 'plugin',
                    'public_key'     => 'pk_336fe1d95cd6f69e27f07834ef2a4',
                    'is_premium'     => false,
                    'navigation'     => FREEMIUS_NAVIGATION,
                    'premium_suffix' => 'Pro',
                    'has_addons'     => false,
                    'has_paid_plans' => true,
                    'menu'           => array(
                    'slug'       => 'svg-flags-wpgoplugins',
                    'first-path' => 'options-general.php?page=svg-flags-wpgoplugins-welcome',
                    'parent'     => array(
                    'slug' => 'options-general.php',
                ),
                ),
                    'is_live'        => true,
                ) );
            }
            
            return $svg_flags_fs;
        }
        
        // Init Freemius.
        svg_flags_fs();
        // Signal that SDK was initiated.
        do_action( 'svg_flags_fs_loaded' );
    }
    
    class Main
    {
        public static  $module_roots ;
        public static function init()
        {
            self::$module_roots = array(
                'dir'  => plugin_dir_path( __FILE__ ),
                'uri'  => plugins_url( '', __FILE__ ),
                'file' => __FILE__,
            );
            $root = self::$module_roots['dir'];
            require_once $root . 'classes/bootstrap.php';
            new BootStrap();
        }
    
    }
    Main::init();
}
