<?php

namespace WPGO_Plugins\SVG_Flags;

/*
 *	Hook callback functions relevant to the free version of the plugin.
 *  This file will not be included in the pro version.
 * 
 *  Update: It's not strictly necessary to remove free only code from the pro version via hooks but it keeps things cleaner. I've left in this file as an example of how to do it, but it's probably enough to just 'hide' free only code using if(svg_flags_fs()->is_premium()) { // free only code here } in the main code.
*/

class Hooks {

	protected $module_roots;

	/* Main class constructor. */
	public function __construct($module_roots) {

		$this->module_roots = $module_roots;
		add_action( 'plugin_settings_row_section_1', array( &$this, 'add_donation_content' ) );
	}

	// Display a table row on the plugin settings page for the free version only
	public function add_donation_content() {
	if (!svg_flags_fs()->is_premium()) :
	?>
		<tr valign="top">
			<th scope="row">Help support this plugin</th>
			<td>
				<div style="float:left;"><a style="margin-right:10px;line-height:0;display:block;" href="#" target="_blank"><img style="box-shadow:0 10px 16px 0 rgba(0,0,0,0.2),0 6px 20px 0 rgba(0,0,0,0.19);width:75px;border-radius:2px;border:2px white solid;" src="<?php echo $this->module_roots['uri']; ?>/assets/images/david.png"></a></div>
				<p style="margin-top:0;">Hi there, I'm David. I spend a lot of time developing FREE WordPress plugins like this one. If you like SVG Flags, and use it on your website, please consider making a <a href="https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=6R94JSPJE9358" target="_blank">donation</a> to help fund continued development (and to keep Dexter in doggy biscuits!).</p>
			</td>
		</tr>
	<?php
	endif;
	}

} /* End class definition */