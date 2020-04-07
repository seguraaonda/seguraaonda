// JavaScript for all admin pages
jQuery(document).ready(function ($) {

	const {hook, new_features_number, nav_status, main_menu_label, menu_type} = svg_flags_admin_menu_data;

	// console.log('hook1', hook, main_menu_label);
	// console.log('A. Hook: ', hook);
	// console.log('B. Numbered icon: ', new_features_number, typeof (new_features_number));

	// add numbered icon to menu/tab label
	if (new_features_number === '0') {
		return; // nothing to see here!
	}

	const new_features_number_html = ' <span class="update-plugins count-' + new_features_number + '"><span class="plugin-count">' + new_features_number + '</span></span>';

	// add numbered icon to menu items
	if (nav_status === 'menu') {
		if (menu_type === 'sub') {
			$('.fs-submenu-item.wpgo-plugins:contains("New Features")').append(new_features_number_html);
			console.log('show numbered icon in top submenu items');
		} else {
			$('.wp-menu-name:contains(' + main_menu_label + ')').append(new_features_number_html);
			$('.wp-submenu li > a:contains("New Features")').append(new_features_number_html);			
			console.log('show numbered icon in top level menu items');
		}
	} else { // tabs
		$('ul#adminmenu li > a:contains(' + main_menu_label + ')').append(new_features_number_html);
	}
});