<?php

namespace WPGO_Plugins\SVG_Flags;

/*
 *    Run upgrade routine(s) when plugin updated to new (higher) version
 */

class Flags_Upgrade
{

    protected $module_roots;

    /* Main class constructor. */
    public function __construct($module_roots, $custom_plugin_data)
    {

        $this->module_roots = $module_roots;
				$this->custom_plugin_data = $custom_plugin_data;

        add_action('plugins_loaded', array(&$this, 'upgrade_routine'));
    }

    /**
     * Setup transient for admin notice to be displayed
     */
    public function upgrade_routine($r)
    {
		
        // only run upgrade routine on admin pages
        if (!is_admin()) {
            return;
        }

        // only run on plugin settings pages
        if (isset($_GET['page'])) {
            $main_settings_page = 'svg-flags-wpgoplugins';
            $pos = strpos($_GET['page'], $main_settings_page);
            if ($pos !== 0) {
                return;
            }
        } else {
            return;
        }

        $optionStr = 'svg-flags-plugin-version';
        $plugin_data = get_plugin_data($this->module_roots['file'], false, false);
        $current_version = $plugin_data['Version'];
        $stored_version = get_option($optionStr, 'none');
				$opt_pfx = $this->custom_plugin_data->db_option_prefix;

        // if db option doesn't exist yet
        if ($stored_version === 'none') {
						add_option($optionStr, $current_version);
						$stored_version = get_option($optionStr, 'none');
        }

				//echo "<br>>>>>>>>>>>>>>>>>>>>> upgrade.php (BEFORE): [" . $current_version . "][" . $stored_version . "]<br>";

				// run upgrade routine if current plugin version greater than stored version
				if (version_compare($current_version, $stored_version, '>')) {

					//echo ">>>>>>>>>>>>>>>>>>>> upgrade.php: TRUE<br>"; 
					// if a new plugin version has been installed
					update_option($opt_pfx . '-new-features-numbered-icon', 'true');
					update_option($opt_pfx . '-plugin-version', $current_version);
        } else {
					//echo ">>>>>>>>>>>>>>>>>>>> upgrade.php: FALSE<br>"; 
				}

				// if for some reason a plugin version is detected that is less than the stored version, update stored version
				if (version_compare($current_version, $stored_version, '<')) {
					update_option($opt_pfx . '-plugin-version', $current_version);
					//echo ">>>>>>>>>>>>>>>>>>>> upgrade.php: CORRECTED ERROR<br>"; 
				}

				//echo ">>>>>>>>>>>>>>>>>>>>> upgrade.php (AFTER): [" . $current_version . "][" . get_option($optionStr, 'none') . "]<br>";

        return;
    }

    // DELETE CODE BELOW WHEN WE HAVE THE UPGRADE ROUTINE

    /* Admin Notice first time plugin is activated. */
    public function admin_notice()
    {
        // Perform tasks after plugin updated
        if ($version != $current_version) {
            update_option('ss_plugin_version', $current_version);
            set_transient('svg-flags-admin-notice', true, 60);
        }

        /* Only display admin notice if transient exists */
        if (get_transient('svg-flags-admin-notice')) {
            ?>
			<div class="updated notice is-dismissible">
				<p>*NEW* SVG Flags live demo gallery added to plugin <a href="<?php echo get_admin_url() . 'options-general.php?page=svg-flags-wpgoplugins'; ?>"><strong>settings</strong></a> page. What will you create today?</p>
			</div>
			<?php
delete_transient('svg-flags-admin-notice');
        }
    }

    /* Runs only when the plugin is activated. */
    public function set_redirect_transient()
    {
        set_transient('svg-flags-redirect', true, 60);
    }

    /**
     * Redirect automatically to plugin settings page
     */
    public function redirect_settings_page()
    {
        // only do this if the user can activate plugins
        if (!current_user_can('manage_options')) {
            return;
        }

        // don't do anything if the transient isn't set
        if (!get_transient('svg-flags-redirect')) {
            return;
        }

        delete_transient('svg-flags-redirect');
        wp_safe_redirect(admin_url('options-general.php?page=svg-flags-wpgoplugins'));
        exit;
    }

} /* End class definition */