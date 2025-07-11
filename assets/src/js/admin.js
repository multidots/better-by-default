// Styles
import '../sass/admin.scss';

// Javascript
import './admin/index';
import './admin/password-preview';

var $ = jQuery;

// Tabbing JS
jQuery(document).ready(function ($) {
	/**
	 * Replace for the above code.
	 */
	var getHeightofAdminBar = $('#wpadminbar').height();
	$('#better-by-default-header').css({
		"top": getHeightofAdminBar + "px",
		"z-index": "99"
	});
	window.onload = function() {
		var inputvar = document.getElementById('better_by_default_protect_option[failed_login_attempts]');
		if(inputvar){
			inputvar.min = 2;
		}
	}
	// End of anam code.

	// Tab View
	const $tabButtons = $('.tab-button');
	const $tabContents = $('.tab-content');
	const $tabButtonsContainer = $('.tab-buttons');
	$('.tab-contents').addClass('loading');

	$tabButtons.on('click', function (e) {
		e.preventDefault();
		const tab = $(this).data('tab');

		$tabButtons.removeClass('active');
		$tabContents.removeClass('active');

		$('.tab-contents').removeClass('loading');
		$(this).addClass('active');
		$(`.tab-content[data-tab="${tab}"]`).addClass('active');

		$('.tab-contents').animate({ scrollTop: 0 }, 'smooth');
		setBbdCookie('better_by_default_setting_tab', tab, 1); // expires in 1 day
	});

	$tabButtons.first().addClass('active');
	$tabContents.first().addClass('active');
	$('.tab-contents').removeClass('loading');

	// For small screen sizes, tab to accordion
	if ($(window).width() <= 767) {
		$tabContents.each(function () {
			let tab = $(this).data('tab');
			$(this).prepend('<span class="accordion-title">' + tab + '</span>');
		});
		$tabContents.each(function () {
			let $accordionTitle = $(this).find('.accordion-title');
			$accordionTitle.on('click', function () {
				let $currentActive = $('.tab-content.active');
				if (
					$currentActive.length > 0 &&
					$currentActive[0] === $(this).closest('.tab-content')[0]
				) {
					$currentActive.removeClass('active');
				} else {
					$tabContents.removeClass('active');
					$(this).closest('.tab-content').addClass('active');
				}
				$('html, body').animate(
					{
						scrollTop:
							$(this).closest('.tab-content').offset().top - 150,
					},
					500
				);
			});
		});
	}

	// defualt tab open
	// Open tab set in 'better_by_default_setting_tab' cookie set on saving changes. Defaults to simplify tab when cookie is empty
	var betterByDefaultTabHash = getBbdCookie('better_by_default_setting_tab');

	if (typeof betterByDefaultTabHash === 'undefined') {
		$(
			'.main-better-by-default-wrap .tab-button[data-tab="simplify"]'
		).trigger('click');
	} else {
		$(
			'.main-better-by-default-wrap .tab-button[data-tab="' +
				betterByDefaultTabHash +
				'"]'
		).trigger('click');
	}

	$('.maintenance-page-heading input').keypress(function (e) {
		if (e.which == 13) return false;
	});

	// Initialize data tables
	var table = $('#login-attempts-log, #activity-log').DataTable({
		pageLength: 10,
		order: [[2, 'desc']],
		language: {
			emptyTable: betterByDefaultConfig.dataTable.emptyTable,
			info: betterByDefaultConfig.dataTable.info,
			infoEmpty: betterByDefaultConfig.dataTable.infoEmpty,
			infoFiltered: betterByDefaultConfig.dataTable.infoFiltered,
			lengthMenu: betterByDefaultConfig.dataTable.lengthMenu,
			search: betterByDefaultConfig.dataTable.search,
			zeroRecords: betterByDefaultConfig.dataTable.zeroRecords,
			paginate: {
				first: betterByDefaultConfig.dataTable.paginate.first,
				last: betterByDefaultConfig.dataTable.paginate.last,
				next: betterByDefaultConfig.dataTable.paginate.next,
				previous: betterByDefaultConfig.dataTable.paginate.previous,
			},
		},
	});

	// Content Management Tab

	// Simplify Tab
	$('.bbd-disable-dashboard-widgets').appendTo(
		'.fields-simplify > table > tbody'
	);
	$('.disable-welcome-panel-in-dashboard').appendTo(
		'.fields-simplify .bbd-disable-dashboard-widgets .better-by-default-subfields'
	);
	$('.disabled-dashboard-widgets').appendTo(
		'.fields-simplify .bbd-disable-dashboard-widgets .better-by-default-subfields'
	);
	$('.bbd-disable-dashboard-widgets').appendTo(
		'.fields-simplify > table > tbody'
	);
	$('.disable-auto-update').appendTo('.fields-simplify > table > tbody');
	$('.disable-comments').appendTo('.fields-simplify > table > tbody');
	$('.disable-post-tags').appendTo('.fields-simplify > table > tbody');
	$('.remove-dahboard-widgets').appendTo('.fields-simplify > table > tbody');
	$('.cleanup-plugin-menus').appendTo('.fields-simplify > table > tbody');
	$('.theme-menu-visibility').appendTo('.fields-simplify > table > tbody');
	$('.tools-menu-visibility').appendTo('.fields-simplify > table > tbody');
	$('.profile-menu-visibility').appendTo('.fields-simplify > table > tbody');
	$('.custom-admin-footer-text').appendTo('.fields-simplify > table > tbody');
	$('.custom-admin-footer-left').appendTo(
		'.fields-simplify .custom-admin-footer-text .better-by-default-subfields'
	);
	reinitWpEditor(
		'better_by_default_simplify_option--custom_admin_footer_left'
	);
	$('.custom-admin-footer-right').appendTo(
		'.fields-simplify .custom-admin-footer-text .better-by-default-subfields'
	);
	reinitWpEditor(
		'better_by_default_simplify_option--custom_admin_footer_right'
	);
	$('.hide-admin-bar').appendTo('.fields-simplify > table > tbody');
	$('.hide-admin-bar-for').appendTo(
		'.fields-simplify .hide-admin-bar .better-by-default-subfields'
	);
	$('.customize-list-tables').appendTo('.fields-simplify > table > tbody');
	$('.extra-list-table-columns').appendTo(
		'.fields-simplify .customize-list-tables .better-by-default-subfields'
	);
	$('.enable-search-by-title').appendTo('.fields-simplify  > table > tbody');
	$('.enable-last-login-column').appendTo('.fields-simplify > table > tbody');

	// Personalize tab
	$('.admin-color-branding').appendTo('.fields-personalize > table > tbody');
	$('.admin-color-scheme-base-color').appendTo(
		'.fields-personalize .admin-color-branding .better-by-default-subfields'
	);
	$('.admin-color-scheme-icon-color').appendTo(
		'.fields-personalize .admin-color-branding .better-by-default-subfields'
	);
	$('.admin-color-scheme-text-color').appendTo(
		'.fields-personalize .admin-color-branding .better-by-default-subfields'
	);
	$('.admin-color-scheme-highlight-color').appendTo(
		'.fields-personalize .admin-color-branding .better-by-default-subfields'
	);
	$('.admin-color-scheme-accent-color').appendTo(
		'.fields-personalize .admin-color-branding .better-by-default-subfields'
	);
	$('.admin-color-scheme-link-color').appendTo(
		'.fields-personalize .admin-color-branding .better-by-default-subfields'
	);
	$('.site-identity-on-login-page').appendTo(
		'.fields-personalize > table > tbody'
	);
	$('.login-highlight-color').appendTo(
		'.fields-personalize .site-identity-on-login-page .better-by-default-subfields'
	);
	$('.login-highlight-color-hover').appendTo(
		'.fields-personalize .site-identity-on-login-page .better-by-default-subfields'
	);
	$('.login-header-image-url').appendTo(
		'.fields-personalize .site-identity-on-login-page .better-by-default-subfields'
	);
	$('.login-header-image-size').appendTo(
		'.fields-personalize .site-identity-on-login-page .better-by-default-subfields'
	);
	$('.disable-back-to-blog-link').appendTo(
		'.fields-personalize .site-identity-on-login-page .better-by-default-subfields'
	);
	$('.site-identity-description').appendTo(
		'.fields-personalize .site-identity-on-login-page .better-by-default-subfields'
	);
	$('.user-account-style').appendTo('.fields-personalize > table > tbody');
	$('.enable-duplication').appendTo('.fields-personalize > table > tbody');
	$('.customize-admin-menu').appendTo('.fields-personalize > table > tbody');
	$('.custom-menu-order').appendTo(
		'.fields-personalize .customize-admin-menu .better-by-default-subfields'
	);
	$('.disable-block-editor').appendTo('.fields-personalize > table > tbody');
	$('.disable-block-editor-for').appendTo(
		'.fields-personalize .disable-block-editor .better-by-default-subfields'
	);

	// Performance Tab.
	$('.disable-legacy-css').appendTo('.fields-performance > table > tbody');
	$('.disable-obscure-wp-head-items').appendTo(
		'.fields-performance > table > tbody'
	);
	$('.remove-shortlinks').appendTo(
		'.fields-performance .disable-obscure-wp-head-items .better-by-default-subfields'
	);
	$('.remove-rss-links').appendTo(
		'.fields-performance .disable-obscure-wp-head-items .better-by-default-subfields'
	);
	$('.remove-rest-api-links').appendTo(
		'.fields-performance .disable-obscure-wp-head-items .better-by-default-subfields'
	);
	$('.remove-rsd-wlw-links').appendTo(
		'.fields-performance .disable-obscure-wp-head-items .better-by-default-subfields'
	);
	$('.remove-oembed-links').appendTo(
		'.fields-performance .disable-obscure-wp-head-items .better-by-default-subfields'
	);
	$('.remove-generator-tag').appendTo(
		'.fields-performance .disable-obscure-wp-head-items .better-by-default-subfields'
	);
	$('.remove-emoji-scripts').appendTo(
		'.fields-performance .disable-obscure-wp-head-items .better-by-default-subfields'
	);
	$('.remove-pingback').appendTo(
		'.fields-performance .disable-obscure-wp-head-items .better-by-default-subfields'
	);
	$('.enable-lazy-load-embeds').appendTo(
		'.fields-performance > table > tbody'
	);
	$('.enable-critical-css').appendTo('.fields-performance > table > tbody');
	$('.lazy-load-description').appendTo(
		'.fields-performance .enable-lazy-load-embeds .better-by-default-subfields'
	);
	$('.lazy-load-youtube').appendTo(
		'.fields-performance .enable-lazy-load-embeds .better-by-default-subfields'
	);
	$('.lazy-load-iframe').appendTo(
		'.fields-performance .enable-lazy-load-embeds .better-by-default-subfields'
	);
	$('.common-critical-css').appendTo(
		'.fields-performance .enable-critical-css .better-by-default-subfields'
	);
	$('.critical-css-description').appendTo(
		'.fields-performance .enable-critical-css .better-by-default-subfields'
	);
	$('.critical-css-for').appendTo(
		'.fields-performance .enable-critical-css .better-by-default-subfields'
	);

	// Protect Tab
	$('.limit-login-attempts').appendTo('.fields-protect > table > tbody');
	$('.failed-login-attempts')
		.appendTo(
			'.fields-protect .limit-login-attempts .better-by-default-subfields'
		)
		.addClass('protect_row');
	$('.login-lockout-maxcount')
		.appendTo(
			'.fields-protect .limit-login-attempts .better-by-default-subfields'
		)
		.addClass('protect_row');
	$('.login-attempts-log-table')
		.appendTo(
			'.fields-protect .limit-login-attempts .better-by-default-subfields'
		)
		.addClass('protect_row');
	$('.disable-xml-rpc').appendTo('.fields-protect > table > tbody');
	$('.security-headers').appendTo('.fields-protect > table > tbody');
	$('.x-frame-options')
		.appendTo(
			'.fields-protect .security-headers .better-by-default-subfields'
		)
		.addClass('protect_row_security');
	$('.x-content-type-options')
		.appendTo(
			'.fields-protect .security-headers .better-by-default-subfields'
		)
		.addClass('protect_row_security');
	$('.x-xss-protection')
		.appendTo(
			'.fields-protect .security-headers .better-by-default-subfields'
		)
		.addClass('protect_row_security');
	$('.referrer-policy')
		.appendTo(
			'.fields-protect .security-headers .better-by-default-subfields'
		)
		.addClass('protect_row_security');
	$('.content-security-policy')
		.appendTo(
			'.fields-protect .security-headers .better-by-default-subfields'
		)
		.addClass('protect_row_security');
	$('.permissions-policy')
		.appendTo(
			'.fields-protect .security-headers .better-by-default-subfields'
		)
		.addClass('protect_row_security');
	$('.x-pingback')
		.appendTo(
			'.fields-protect .security-headers .better-by-default-subfields'
		)
		.addClass('protect_row_security');
	$('.x-hacker')
		.appendTo(
			'.fields-protect .security-headers .better-by-default-subfields'
		)
		.addClass('protect_row_security');
	$('.x-powered-by')
		.appendTo(
			'.fields-protect .security-headers .better-by-default-subfields'
		)
		.addClass('protect_row_security');

	$('.rest-api-access-control').appendTo('.fields-protect > table > tbody');
	$('.rest-api-access-control-options').appendTo(
		'.fields-protect .rest-api-access-control .better-by-default-subfields'
	);
	$('.change-login-url').appendTo('.fields-protect > table > tbody');
	$('.custom-login-slug').appendTo(
		'.fields-protect .change-login-url .better-by-default-subfields'
	);
	$('.change-login-url-description').appendTo(
		'.fields-protect .change-login-url .better-by-default-subfields'
	);
	$('.reserved-usernames').appendTo('.fields-protect > table > tbody');
	$('.storng-password').appendTo('.fields-protect > table > tbody');

	//Utility Tab
	$('.default-template-network-site').appendTo(
		'.fields-miscellaneous > table > tbody'
	);
	$('.password-protection').appendTo('.fields-miscellaneous > table > tbody');
	$('.maintenance-mode').appendTo('.fields-miscellaneous > table > tbody');
	$('.maintenance-page-heading').appendTo(
		'.fields-miscellaneous .maintenance-mode .better-by-default-subfields'
	);
	$('.maintenance-page-description').appendTo(
		'.fields-miscellaneous .maintenance-mode .better-by-default-subfields'
	);
	$('.activity-log').appendTo('.fields-miscellaneous > table > tbody');
	$('.activity-log-description').appendTo(
		'.fields-miscellaneous .activity-log .better-by-default-subfields'
	);

	// Block Editor Tab

	$('.enable-public-page-preview').appendTo(
		'.fields-miscellaneous > table > tbody'
	);

	$('.disable-crawling').appendTo('.fields-miscellaneous > table > tbody');

	$('.cache-functionality').appendTo('.fields-miscellaneous > table > tbody');
	$('.flush-object-cache').appendTo(
		'.fields-miscellaneous .cache-functionality .better-by-default-subfields'
	);
	$('.page-cache').appendTo(
		'.fields-miscellaneous .cache-functionality .better-by-default-subfields'
	);
	$('.page-cache-description').appendTo(
		'.fields-miscellaneous .cache-functionality .better-by-default-subfields'
	);

	// About Tab
	$('.better-by-default-about-page').appendTo(
		'.fields-about > table > tbody'
	);

	// Show all / less toggler for field options | Modified from https://codepen.io/symonsays/pen/rzgEgY
	$(
		'.better-by-default-field-with-options.field-show-more > .show-more'
	).click(function (e) {
		e.preventDefault();

		var $this = $(this);
		var expandText = 'More';
		var collapseText = 'Less';
		$this.toggleClass('show-more');

		if ($this.hasClass('show-more')) {
			$this.parent().find('.wrapper-show-more').removeClass('opened');
			$this.html(expandText + ' +');
		} else {
			$this.parent().find('.wrapper-show-more').addClass('opened');
			$this.html(collapseText + ' -');
		}
	});

	// Reinitialize TinyMCE editor after toggling subfields
	function reinitWpEditor(id) {
		if ('undefined' !== typeof tinymce) {
			tinymce.execCommand('mceRemoveEditor', true, id);
			var init = tinymce.extend({}, tinyMCEPreInit.mceInit[id]);
			try {
				tinymce.init(init);
			} catch (e) {}
			$('textarea[id="' + id + '"]')
				.closest('form')
				.find('input[type="submit"]')
				.click(function () {
					if (getUserSetting('editor') == 'tmce') {
						var id = mce.find('textarea').attr('id');
						tinymce.execCommand('mceRemoveEditor', false, id);
						tinymce.execCommand('mceAddEditor', false, id);
					}
					return true;
				});
		}
	}

	// Show or hide subfields on document ready and on toggle click

	function subfieldsToggler(
		fieldId,
		fieldClass,
		sectionName,
		sortableId,
		codeMirrorInstances
	) {
		var optionField = document.getElementById(
			sectionName + '[' + fieldId + ']'
		);
		if (optionField) {
			// Show/hide subfields on document ready, depending on if module is enabled or not
			if (optionField.checked) {
				$('.' + fieldClass + ' .better-by-default-subfields').show();
				if (
					document.querySelector(
						'.' +
							fieldClass +
							' .better-by-default-subfield-select-inner'
					)
				) {
					$(
						'.' +
							fieldClass +
							' .better-by-default-subfield-select-inner'
					).show();
				}
				$(
					'.better-by-default-toggle.' +
						fieldClass +
						' td .better-by-default-field-with-options'
				).addClass('is-enabled');
				if (codeMirrorInstances) {
					Object.keys(codeMirrorInstances).forEach(function (key) {
						if (codeMirrorInstances[key]) {
							codeMirrorInstances[key].refresh();
						}
					});
				}
			} else {
				$('.' + fieldClass + ' .better-by-default-subfields').hide();
				if (
					document.querySelector(
						'.' +
							fieldClass +
							' .better-by-default-subfield-select-inner'
					)
				) {
					$(
						'.' +
							fieldClass +
							' .better-by-default-subfield-select-inner'
					).hide();
				}
			}

			// Show/hide subfields on toggle click
			optionField.addEventListener('click', (event) => {
				if (event.target.checked) {
					$(
						'.' + fieldClass + ' .better-by-default-subfields'
					).fadeIn();
					if (
						document.querySelector(
							'.' +
								fieldClass +
								' .better-by-default-subfield-select-inner'
						)
					) {
						$(
							'.' +
								fieldClass +
								' .better-by-default-subfield-select-inner'
						).show();
					}
					$(
						'.' +
							fieldClass +
							' .better-by-default-field-with-options'
					).toggleClass('is-enabled');
					if (document.getElementById(sortableId)) {
						// Initialize sortable elements: https://api.jqueryui.com/sortable/
						$('#' + sortableId).sortable();
					}
					if (codeMirrorInstances) {
						Object.keys(codeMirrorInstances).forEach(
							function (key) {
								if (codeMirrorInstances[key]) {
									codeMirrorInstances[key].refresh();
								}
							}
						);
					}
				} else {
					$(
						'.' + fieldClass + ' .better-by-default-subfields'
					).hide();
					if (
						document.querySelector(
							'.' +
								fieldClass +
								' .better-by-default-subfield-select-inner'
						)
					) {
						$(
							'.' +
								fieldClass +
								' .better-by-default-subfield-select-inner'
						).hide();
					}
					$(
						'.' +
							fieldClass +
							' .better-by-default-field-with-options'
					).toggleClass('is-enabled');
				}
			});
		}
	}

	//Simplify
	subfieldsToggler(
		'bbd_disable_dashboard_widgets',
		'bbd-disable-dashboard-widgets',
		'better_by_default_simplify_option'
	);
	subfieldsToggler(
		'custom_admin_footer_text',
		'custom-admin-footer-text',
		'better_by_default_simplify_option'
	);
	subfieldsToggler(
		'hide_admin_bar',
		'hide-admin-bar',
		'better_by_default_simplify_option'
	);
	subfieldsToggler(
		'customize_list_tables',
		'customize-list-tables',
		'better_by_default_simplify_option'
	);

	// Personalize
	subfieldsToggler(
		'site_identity_on_login_page',
		'site-identity-on-login-page',
		'better_by_default_personalize_option'
	);
	subfieldsToggler(
		'admin_color_branding',
		'admin-color-branding',
		'better_by_default_personalize_option'
	);
	subfieldsToggler(
		'customize_admin_menu',
		'customize-admin-menu',
		'better_by_default_personalize_option'
	);
	subfieldsToggler(
		'disable_block_editor',
		'disable-block-editor',
		'better_by_default_personalize_option'
	);

	// performance
	subfieldsToggler(
		'disable_obscure_wp_head_items',
		'disable-obscure-wp-head-items',
		'better_by_default_performance_option'
	);
	subfieldsToggler(
		'enable_lazy_load_embeds',
		'enable-lazy-load-embeds',
		'better_by_default_performance_option'
	);
	subfieldsToggler(
		'enable_critical_css',
		'enable-critical-css',
		'better_by_default_performance_option'
	);

	//protect
	subfieldsToggler(
		'change_login_url',
		'change-login-url',
		'better_by_default_protect_option'
	);
	subfieldsToggler(
		'limit_login_attempts',
		'limit-login-attempts',
		'better_by_default_protect_option'
	);
	subfieldsToggler(
		'rest_api_access_control',
		'rest-api-access-control',
		'better_by_default_protect_option'
	);

	subfieldsToggler(
		'security_headers',
		'security-headers',
		'better_by_default_protect_option'
	);
	subfieldsToggler(
		'maintenance_mode',
		'maintenance-mode',
		'better_by_default_miscellaneous_option'
	);
	subfieldsToggler(
		'activity_log',
		'activity-log',
		'better_by_default_miscellaneous_option'
	);
	subfieldsToggler(
		'cache_functionality',
		'cache-functionality',
		'better_by_default_miscellaneous_option'
	);
});

// Menu Reorder
$(document).ready(function () {
	// ----- Menu Ordering -----

	// Initialize sortable elements for parent menu items: https://api.jqueryui.com/sortable/
	$('#custom-admin-menu').sortable({
		items: '> li',
		opacity: 0.6,
		placeholder: 'sortable-placeholder',
		tolerance: 'pointer',
		revert: 250,
	});

	// Get the default/current menu order
	let menuOrder = $('#custom-admin-menu').sortable('toArray').toString();

	// Set hidden input value for saving in options
	if (
		document.getElementById(
			'better_by_default_personalize_option[custom_menu_order]'
		)
	) {
		document.getElementById(
			'better_by_default_personalize_option[custom_menu_order]'
		).value = menuOrder;
	}

	// Save custom order into a comma-separated string, triggerred after each drag and drop of menu item
	// https://api.jqueryui.com/sortable/#event-update
	// https://api.jqueryui.com/sortable/#method-toArray
	$('#custom-admin-menu').on('sortupdate', function (event, ui) {
		// Get the updated menu order
		let menuOrder = $('#custom-admin-menu').sortable('toArray').toString();

		// Set hidden input value for saving in options
		if (
			document.getElementById(
				'better_by_default_personalize_option[custom_menu_order]'
			)
		) {
			document.getElementById(
				'better_by_default_personalize_option[custom_menu_order]'
			).value = menuOrder;
		}
	});

	// ----- Parent Menu Item Hiding -----

	// Prepare constant to store IDs of menu items that will be hidden
	if (
		document.getElementById(
			'better_by_default_personalize_option[custom_menu_hidden]'
		) != null
	) {
		var hiddenMenuItems = document
			.getElementById(
				'better_by_default_personalize_option[custom_menu_hidden]'
			)
			.value.split(','); // array
	} else {
		var hiddenMenuItems = []; // array
	}

	// Detect which menu items are being checked. Ref: https://stackoverflow.com/a/3871602
	Array.from(
		document.getElementsByClassName('parent-menu-hide-checkbox')
	).forEach(function (item, index, array) {
		item.addEventListener('click', (event) => {
			if (event.target.checked) {
				// Add ID of menu item to array
				hiddenMenuItems.push(event.target.dataset.menuItemId);
			} else {
				// Remove ID of menu item from array
				const start = hiddenMenuItems.indexOf(
					event.target.dataset.menuItemId
				);
				const deleteCount = 1;
				hiddenMenuItems.splice(start, deleteCount);
			}

			// Set hidden input value
			if (
				document.getElementById(
					'better_by_default_personalize_option[custom_menu_hidden]'
				)
			) {
				document.getElementById(
					'better_by_default_personalize_option[custom_menu_hidden]'
				).value = hiddenMenuItems;
			}
		});
	});

	// Clicking on header save button
	$('.better-by-default-save-button').click(function (e) {
		e.preventDefault();

		// Prepare variable to store ID-Title pairs of menu items
		var customMenuTitles = []; // empty array

		// Initialize other variables
		var menuItemId = '';
		var customTitle = '';

		// Save default/custom title values. Ref: https://stackoverflow.com/a/3871602
		Array.from(
			document.getElementsByClassName('menu-item-custom-title')
		).forEach(function (item, index, array) {
			menuItemId = item.dataset.menuItemId;
			customTitle = item.value;
			customMenuTitles.push(menuItemId + '__' + customTitle);
		});

		// Set hidden input value
		if (
			document.getElementById(
				'better_by_default_personalize_option[custom_menu_titles]'
			)
		) {
			document.getElementById(
				'better_by_default_personalize_option[custom_menu_titles]'
			).value = customMenuTitles;
		}

		var menuIcons = [];
		$('li.menu-item').each(function (index) {
			var menuId = $(this).attr('id');
			var menuIcon = $(this).attr('data-menu-icon');
			menuIcons[menuId] = 'undefined' !== typeof menuIcon ? menuIcon : '';
		});
		$(
			'#better_by_default_personalize_option[custom_menu_icons_hidden]'
		).val(JSON.stringify(menuIcons));

		console.log(JSON.stringify(menuIcons));
	});
}); // End of $(document).ready()

// Toggle Hidden Menu

$(document).ready(function () {
	$('#dashicon-picker .dashicons').on('click', function () {
		var value = $(this).data('value');
		$(this).parents('li.menu-item').attr('data-menu-icon', value);
		$(this).parents('.menu-styling ').find('input').val(value);
		$(this)
			.parents('li.menu-item')
			.find('#dashicon-picker .dashicons')
			.removeClass('active');
		$(this).addClass('active');

		//$('#dashicon-picker .dashicon').css('background', ''); // Reset background
		//$(this).addClass('background', '#f1f1f1'); // Highlight selected icon
	});

	$(document).on(
		'click',
		'.dashboard-item-toggle .toggle-style',
		function () {
			var toggleClick = $(this).attr('data-name');
			if (!$(this).hasClass('active')) {
				$('.toggle-style').removeClass('active');
				$('.menu-item-color-wrapper').hide();
				$('.dashicons-list-wrapper').hide();
				if (toggleClick == 'select-color') {
					$(this)
						.parents('.item-inner-block')
						.find('.menu-item-color-wrapper')
						.show();
					$(this).addClass('active');
				} else {
					$(this)
						.parents('.item-inner-block')
						.find('.dashicons-list-wrapper')
						.show();
					$(this).addClass('active');
				}
			} else {
				$('.toggle-style').removeClass('active');
				$('.menu-item-color-wrapper').hide();
				$('.dashicons-list-wrapper').hide();
			}
		}
	);

	$('#toplevel_page_better_by_default_hide_hidden_menu').hide();

	if (
		$(
			'#hide-status-for-toplevel_page_better_by_default_show_hidden_menu:checked'
		).length > 0
	) {
		$('#toplevel_page_better_by_default_show_hidden_menu').addClass(
			'hidden better_by_default_hidden_menu'
		);
	} else {
		$('#toplevel_page_better_by_default_show_hidden_menu').removeClass(
			'hidden better_by_default_hidden_menu'
		);
	}

	if (
		$(
			'#hide-status-for-toplevel_page_better_by_default_hide_hidden_menu:checked'
		).length > 0
	) {
		$('#toplevel_page_better_by_default_hide_hidden_menu').addClass(
			'hidden better_by_default_hidden_menu'
		);
	} else {
		$('#toplevel_page_better_by_default_hide_hidden_menu').removeClass(
			'hidden better_by_default_hidden_menu'
		);
	}

	// Show hidden menu items

	$('#toplevel_page_better_by_default_show_hidden_menu a').on(
		'click',
		function (e) {
			e.preventDefault();
			$('#toplevel_page_better_by_default_show_hidden_menu').hide();
			if (
				!$(
					'#hide-status-for-toplevel_page_better_by_default_hide_hidden_menu:checked'
				).length > 0
			) {
				$('#toplevel_page_better_by_default_hide_hidden_menu').show();
			}
			$('.menu-top.better_by_default_hidden_menu').toggleClass('hidden');
			$('.wp-menu-separator.better_by_default_hidden_menu').toggleClass(
				'hidden'
			);

			$(document).trigger('wp-window-resized');
		}
	);

	// Hide menu items set for hiding

	$('#toplevel_page_better_by_default_hide_hidden_menu a').on(
		'click',
		function (e) {
			e.preventDefault();
			$('#toplevel_page_better_by_default_show_hidden_menu').show();
			$('#toplevel_page_better_by_default_hide_hidden_menu').hide();
			$('.menu-top.better_by_default_hidden_menu').toggleClass('hidden');
			$('.wp-menu-separator.better_by_default_hidden_menu').toggleClass(
				'hidden'
			);

			$(document).trigger('wp-window-resized');
		}
	);
});

$(document).ready(function () {
	// Save Changes
	// Clicking on header save button triggers click of the hidden form submit button
	$('.better-by-default-save-button').click(function (e) {
		e.preventDefault();
		if ($('#better_by_default_protect_option[change_login_url]:checked')) {
			if (
				'undefined' ===
					typeof jQuery(
						'input[name="better_by_default_protect_option[custom_login_slug]"]'
					).val() ||
				'' ===
					jQuery(
						'input[name="better_by_default_protect_option[custom_login_slug]"]'
					).val()
			) {
				return true;
			}
		}

		$('.better-by-default-saving-changes').fadeIn();

		// Get current tab's URL hash and save it in cookie
		//var hash = decodeURI(window.location.hash).substr(1); // get hash without the # character
		//Cookies.set('better-by-default_tab', hash, { expires: 1 }); // expires in 1 day

		// Submit the settings form
		$('#better-by-default-submit').click();
	});

	// Flush Object cache Click event
	$(document).on('click', '.flush-object-cache .button', function () {
		var $thisObj = $(this);
		$('#setting-error-settings_updated').remove();
		if (confirm('Are you sure want to flush object cache?')) {
			$thisObj.addClass('fc-loading');
			$.ajax({
				type: 'POST',
				url: betterByDefaultConfig.ajaxUrl,
				data: {
					action: 'flush_cache_object_cache',
					nonce: betterByDefaultConfig.ajax_nonce,
				},
				success: function (response) {
					if (response && response.success) {
						$thisObj.after(
							'<div id="setting-error-settings_updated"><p class="settings_msg settings_success"><strong>Cache flushed successfully for the site!</strong></p></div>'
						);
					} else {
						$thisObj.after(
							'<div id="setting-error-settings_updated"><p class="settings_msg settings_error"><strong>It\'s an error occurred while flushing the cache.</strong></p></div>'
						);
					}
					$thisObj.removeClass('fc-loading');
				},
				error: function (error) {
					$thisObj.after(
						'<div id="setting-error-settings_updated"><p class="settings_msg settings_error"><strong>Failed to connect to the server. Please try again later.</strong></p></div>'
					);
					$thisObj.removeClass('fc-loading');
				},
			});
		} else {
			return false;
		}
	});

	// Purge Page cache Click event
	$(document).on(
		'click',
		'.better-by-default-subfields .page-cache .button',
		function () {
			var $thisObj = $(this);
			var urls = $(
				'.page-cache .better-by-default-subfield-textarea'
			).val();
			// Check if the textarea is empty
			if (!urls) {
				alert(
					'Please enter the URLs in the textfield before purging the cache.'
				);
				return; // Exit the function if no URLs are provided
			}
			if (
				confirm(
					'Are you sure you want to purge the pages cache for the listed pages?'
				)
			) {
				$thisObj.addClass('fc-loading');

				$.ajax({
					type: 'POST',
					url: betterByDefaultConfig.ajaxUrl,
					data: {
						action: 'flush_cache_page_cache',
						nonce: betterByDefaultConfig.ajax_nonce,
						urls: urls,
					},
					success: function (response) {
						// Remove any previous messages
						$('#setting-error-settings_updated').remove();

						// Success message
						if (response && response.success) {
							$thisObj.after(
								'<div id="setting-error-settings_updated"><p class="settings_msg settings_success"><strong>Page cache purged successfully for the listed URLs in the textfield!</strong></p></div>'
							);
						}
						// Error message
						else {
							$thisObj.after(
								'<div id="setting-error-settings_updated"><p class="settings_msg settings_error"><strong>An error occurred while flushing the cache.</strong></p></div>'
							);
						}
						$thisObj.removeClass('fc-loading');
					},
					error: function (error) {
						// Error message
						$thisObj.after(
							'<div id="setting-error-settings_updated"><p class="settings_msg settings_error"><strong>Failed to connect to the server. Please try again later.</strong></p></div>'
						);
						$thisObj.removeClass('fc-loading');
					},
				});
			} else {
				return false;
			}
		}
	);

	$(document).on('click', '.bbd-tab__reset', function (event) {
		event.preventDefault();

		// Confirm before proceeding with the reset.
		if (
			confirm(
				'Are you sure you want to set the Better By Default plugin setting to its default value?'
			)
		) {
			$.ajax({
				type: 'POST',
				url: betterByDefaultConfig.ajaxUrl,
				data: {
					action: 'better_by_default_options',
					nonce: betterByDefaultConfig.ajax_nonce,
				},
				success: function (response) {
					if (response.success) {
						alert(
							'Better By Default plugin option value has been reset.'
						);
						location.reload(); // Reloads the page on success.
					} else {
						alert(
							'An error occurred while resetting the option value.'
						);
						location.reload(); // Reloads the page on error.
					}
				},
				error: function (xhr, status, error) {
					console.error('AJAX Error:', status, error);
					alert('An unexpected error occurred. Please try again.');
					location.reload(); // Reloads the page if there's an AJAX error.
				},
			});
		}
	});
});

// Set Cookie.
function setBbdCookie(cname, cvalue, exdays, type = 'days') {
	const d = new Date();
	var time = exdays * 24 * 60 * 60 * 1000;
	if ('hours' === type) {
		time = exdays * 60 * 60 * 1000;
	}
	d.setTime(d.getTime() + time);
	let expires = 'expires=' + d.toUTCString();

	// Get the current site's pathname
	const sitePath = window.location.pathname.split('/')[1]; // Extract the subsite name
	// Set the cookie with the specific path
	document.cookie =
		cname + '=' + cvalue + ';' + expires + ';path=/' + sitePath;
}

// Get Cookie.
function getBbdCookie(cname) {
	let name = cname + '=';
	let decodedCookie = decodeURIComponent(document.cookie);
	let ca = decodedCookie.split(';');
	for (let i = 0; i < ca.length; i++) {
		let c = ca[i];
		while (c.charAt(0) == ' ') {
			c = c.substring(1);
		}
		if (c.indexOf(name) == 0) {
			return c.substring(name.length, c.length);
		}
	}
	return ''; // Return empty string if cookie not found
}
