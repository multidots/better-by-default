/**
 * File admin.js.
 *
 * Handles admin scripts
 */
(function ($) {
	'use strict';

	jQuery(document).ready(function($){
		$('.better-by-default-color-picker, .color-picker').wpColorPicker();
	});

	jQuery(document).ready(function ($) {
		//upload logo button
		$(document).on('click', '.better_by_default_img_upload', function (e) {
			e.preventDefault();
			const currentParent = $(this);
			const customUploader = wp
				.media({
					title: 'Select Image',
					button: {
						text: 'Use This Image',
					},
					library: {
						type: 'image' // Restrict media library to images only
					},
					multiple: false, // Set this to true to allow multiple files to be selected
				})
				.on('select', function () {
					const attachment = customUploader
						.state()
						.get('selection')
						.first()
						.toJSON();
					currentParent
						.siblings('.better_by_default_img')
						.attr('src', attachment.url);
					currentParent.siblings('.better_by_default_img').attr('width', '250');
					currentParent.siblings('.better_by_default_img').attr('height', '140');
					currentParent.siblings('.better_by_default_img_url').val(attachment.url);
					currentParent.siblings('.better_by_default_img_remove').show();
					currentParent.find('button').text('Edit Logo');
				})
				.open();
		});
	
		//remove logo button
		$(document).on('click', '.better_by_default_img_remove', function (e) {
			e.preventDefault();
			const currentParent = $(this);
			currentParent.siblings('.better_by_default_img').removeAttr('src');
			currentParent.siblings('.better_by_default_img').removeAttr('width');
			currentParent.siblings('.better_by_default_img').removeAttr('height');
			currentParent.siblings('.better_by_default_img_url').removeAttr('value');
			currentParent.hide();
			currentParent.parents('.image-upload-wrap').find('.better_by_default_img_upload button').text('Upload Logo');
		});
	
		//color picker custom js.
		$('[class="color-picker"]').wpColorPicker({
			hide: false,
		});
	});

})(jQuery);
