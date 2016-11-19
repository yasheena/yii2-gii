yii.yasheenaGii = (function ($) {
	return {
		init: function () {
			// hide left button definition when checkbox column is disabled
			$('form #generator-dat_use_checkboxcolumn').change(function () {
				$('form .field-generator-dat_lft_button').toggle($(this).is(':checked'));
				$('form #generator-dat_lft_button').trigger('change');
			}).change();
			// update form depending on the left button settings
			$('form #generator-dat_lft_button').change(function () {
				// if left button is set to none, hide left button entries table
				$('form .field-generator-lft_table').toggle(
						!$('input[name="Generator[dat_lft_button]"][value="None"]').is(':checked')
						&& $('#generator-dat_use_checkboxcolumn').is(':checked')
					);
				// if it is not a dropdown, hide line for defining dropdown button
				$('form #generator-lft-line-0').toggle(
						$('input[name="Generator[dat_lft_button]"][value="Dropdown"]').is(':checked')
					);
				// if left button is not set to none, hide datafield selection of checkboxcolumn 
				$('form .field-generator-checkbox-field').toggle(
						$('input[name="Generator[dat_lft_button]"][value="None"]').is(':checked')
						&& $('#generator-dat_use_checkboxcolumn').is(':checked')
					);
			}).change();
			// if right button is set to none, hide right button entries table
			$('form #generator-dat_rgt_button').change(function () {
				$('form .field-generator-rgt_table').toggle(
						!$('input[name="Generator[dat_rgt_button]"][value="None"]').is(':checked')
					);
				// if it is not a dropdown, hide line for defining dropdown button
				$('form #generator-rgt-line-0').toggle(
						$('input[name="Generator[dat_rgt_button]"][value="Dropdown"]').is(':checked')
					);
			}).change();
			// update the icon depending on dropdown list for the left und right button entries 
			$('form .generator-button-dropdown').change(function () {
				var id = $(this).attr('id');
				$('form #' + id + '-icon').removeClass();
				if ($(this).val() != '---') {
					$('form #' + id + '-icon').toggleClass('glyphicon glyphicon-' + $(this).val(), true);
				}
			}).change();
		}
	}
})(jQuery);
