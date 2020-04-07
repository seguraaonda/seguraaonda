// JavaScript for the plugin admin settings page
jQuery(document).ready(function ($) {

	// setup event listeners for expandable sections
	['shortcodes', 'blocks'].map(function (section) {
		const btn = $('#' + section + '-btn');
		const wrap = $('#' + section + '-wrap');

		btn.on('click', function () {
			var isHidden = wrap.is(":hidden");
			wrap.toggle(function () {
				if (isHidden) {
					btn.html('Collapse <span style="vertical-align:sub;width:16px;height:16px;font-size:16px;" class="dashicons dashicons-arrow-up-alt2"></span>');
				} else {
					btn.html('Expand <span style="vertical-align:sub;width:16px;height:16px;font-size:16px;" class="dashicons dashicons-arrow-down-alt2"></span>');
				}
			});
		});
	});

	/*
		const pluginUsageBtn = $('#plugin-usage-btn');
		const pluginUsageWrap = $('#plugin-usage-wrap');
	
		pluginUsageBtn.on('click', function() {
			var isHidden = pluginUsageWrap.is( ":hidden" );
			pluginUsageWrap.toggle( function() {
				if(isHidden) {
					pluginUsageBtn.html('Collapse <span style="vertical-align:sub;width:16px;height:16px;font-size:16px;" class="dashicons dashicons-arrow-up-alt2"></span>');
				} else {
					pluginUsageBtn.html('Expand <span style="vertical-align:sub;width:16px;height:16px;font-size:16px;" class="dashicons dashicons-arrow-down-alt2"></span>');
				}
			});
		});
	*/

	// add custom tabs via JS
	// const navTabWrapper = $('.nav-tab-wrapper');
	// const currentTabs = $('.nav-tab-wrapper a');
	// let activeTab = '';

	// if (!currentTabs.hasClass('nav-tab-active')) {
	// 	activeTab = ' nav-tab-active';
	// }

	// const tabs = [
	// 	{
	// 		slug: 'svg-flags-wpgoplugins-new-features',
	// 		class: 'new-features',
	// 		label: 'New Features'
	// 	},
	// 	{
	// 		slug: 'svg-flags-wpgoplugins',
	// 		class: 'home',
	// 		label: 'Settings'
	// 	}
	// ];
	// tabs.forEach(function (item, index) {
	// 	const active = svg_flags_admin_data.settings_page === item.slug ? activeTab : '';

	// 	const tab = '<a href="' + svg_flags_admin_data.admin_url + 'options-general.php?page=' + item.slug + '" class="nav-tab fs-tab svg-flags-lite ' + item.class + '' + active + '">' + item.label + '</a>';
	// 	navTabWrapper.prepend(tab);
	// });

	//console.log('1. Settings page: ', svg_flags_admin_data.settings_page);
	//console.log('2. Numbered icon: ', svg_flags_admin_data.new_features_number, typeof (svg_flags_admin_data.new_features_number));

	// move welcome page tab to the last tab position
	var navTabWrapper = $('.nav-tab-wrapper');
	navTabWrapper.find('.nav-tab:nth-child(3)').appendTo(navTabWrapper);

	if (svg_flags_admin_menu_data.new_features_number === '0') {
		return; // nothing to see here!
	}

	let new_features_number_html = '';

	// add numbered icon to tab label
	if (svg_flags_admin_data.nav_status === 'tabs') {
		let new_features_number_html = ' <span class="new-features-count">' + svg_flags_admin_menu_data.new_features_number + '</span>';
		$('.nav-tab-wrapper .nav-tab:nth-child(2)').append(new_features_number_html);
	}

});