/*
 * JavaScript for Auto ThickBox Plus
 * Copyright (C) 2010-2012 attosoft <http://attosoft.info/en/>
 * This file is distributed under the same license as the Auto ThickBox Plus package.
 * attosoft <contact@attosoft.info>, 2012.
 */

jQuery(function($) {
	postboxes.add_postbox_toggles(pagenow);

	$(".colorpicker").each(function() {
		var text = $(this).prevAll(".colortext");
		var checkbox = $(this).prevAll("label").children("input:checkbox");
		var preview = $(this).prevAll(".colorpreview");
		var fb = $.farbtastic(this);
		if (text.val()[0] == '#') fb.setColor(text.val()); // color in hex
		preview.css("backgroundColor", text.val());
		fb.linkTo(function(color) {
			text.val(color);
			preview.css("backgroundColor", color);
			text.parent().find(":disabled").removeAttr("disabled");
			checkbox.filter(":checked").removeAttr("checked");
		});
	});
	$(".pickcolor").click(function() { $(this).nextAll(".colorpicker").show(); return false; });
	$(document).mousedown(function() { $(".colorpicker:visible").hide(); });

	if ($.isFunction($().slider)) {
		$(".opacity-slider").each(function() {
			var text = $(this).prevAll("input[type='number']");
			$(this).slider({
				max: 1,
				step: 0.05,
				value: text.val(),
				slide: function(event, ui) { text.val(ui.value); },
				change: function(event, ui) { text.val(ui.value); }
			});
		});
		$(".opacity-trans").click(function() { $(this).next(".opacity-slider").slider("value", 0); });
		$(".opacity-opaque").click(function() { $(this).prev(".opacity-slider").slider("value", 1); });

		$("#click-range-slider").slider({
			max: 50,
			step: 5,
			value: $("#click-range").val(),
			slide: function(event, ui) { $("#click-range").val(ui.value); },
			change: function(event, ui) { $("#click-range").val(ui.value); },
			disabled: $("#click-range:disabled").length > 0
		});
	}

	$("ol.sortable").sortable({
		placeholder: "sortable-placeholder",
		helper: "clone",
		opacity: 0.65,
		update: function(event, ui) { $(this).prev("input:hidden").val("'" + $(this).sortable("toArray").join("','") + "'"); }
	});
	if ($.isFunction($().disableSelection))
		$("ol.sortable").disableSelection();

	$(".media-uploader").each(function() {
		var text = $(this).prevAll("input:text");
		var checkbox = $(this).nextAll("label").children("input:checkbox");
		$(this).click(function() {
			formfield = text.attr('name');
			tb_show(this.value, 'media-upload.php?type=image&post_id=' + post_id + '&TB_iframe');
			window.send_to_editor = function(html) {
				imgurl = $('img', html).attr('src') || $(html).attr('src'); // type/library or type_url
				text.val(imgurl);
				tb_remove();
				text.filter(":disabled").removeAttr("disabled");
				checkbox.filter(":checked").removeAttr("checked");
			}
			return false;
		});
	});

	tb_init("a.thickbox-image"); // prevent href from being modified by media-upload.js
	var tb_position2 = tb_position;
	tb_position = function() {
		if ($("#TB_iframeContent").length) { // tb_position() in media-upload.js
			tb_position2();
		} else { // tb_position() in built-in thickbox.js
			var isIE6 = typeof document.body.style.maxHeight === "undefined";
			jQuery("#TB_window").css({marginLeft: '-' + parseInt((TB_WIDTH / 2),10) + 'px', width: TB_WIDTH + 'px'});
			if ( ! isIE6 ) { // take away IE6
				jQuery("#TB_window").css({marginTop: '-' + parseInt((TB_HEIGHT / 2),10) + 'px'});
			}
		}
	};
});

function updateEffectSpeed(radio) {
	var text = document.form[radio.name][document.form[radio.name].length - 1]
	text.disabled = radio.value != "number";
	switch (radio.value) {
		case "fast": text.value = "200"; break;
		case "normal": text.value = "400"; break;
		case "slow": text.value = "600"; break;
	}
	if (!text.disabled && jQuery.browser.mozilla)
		text.focus();
}

function disableHoverOption(radio) {
	var name = radio.name.replace('position_', 'effect_');
	var disabled = radio.value == 'none';
	for (var i = 0; i < document.form[name].length; i++)
		document.form[name][i].disabled = disabled;
}

function disableHideInitOption(radio) {
	var name = radio.name.replace('effect_', 'hide_');
	document.form[name].disabled = radio.value == 'none';
}

function disablePlaceOption(select) {
	for (var i = 0; i < document.form['auto-thickbox-plus[script_place]'].length; i++)
		document.form['auto-thickbox-plus[script_place]'][i].disabled = select.value == 'built-in';
	if (select.value == 'built-in')
		document.form['auto-thickbox-plus[script_place]'][1].checked = true;
}

function disableClickOption(radio) {
	var disabled = radio.value != "next" && radio.value != "prev_next";
	for (var i = 0; i < document.form['auto-thickbox-plus[click_end]'].length; i++)
		document.form['auto-thickbox-plus[click_end]'][i].disabled = disabled;

	disabled = radio.value != "prev_next";
	document.form['auto-thickbox-plus[click_range]'].disabled = disabled;
	if (jQuery.isFunction(jQuery().slider))
		jQuery("#click-range-slider").slider("option", "disabled", disabled);
}

function disableBorderOption(checkbox) {
	document.form[checkbox.name.replace('border_', 'border_width_')].disabled = checkbox.checked;
	document.form[checkbox.name.replace('border_', 'border_style_')].disabled = checkbox.checked;
	document.form[checkbox.name.replace('border_', 'border_color_')].disabled = checkbox.checked;
}

function disableOption(checkbox) {
	document.form[checkbox.name][0].disabled = checkbox.checked;
}
