/*
 * QR Code Tag _from_goaskle_com Wordpress Plugin Javascript jQuery Handler v1.1
 * https://Goaskle.com
 *
 * Copyright (c) 2021 Goaskle.com
 *
 * Dual licensed under the MIT and GPL licenses:
 *   http://www.opensource.org/licenses/mit-license.php
 *   http://www.gnu.org/licenses/gpl.html
 */;
jQuery(document).ready(function($) {
	$("span.qrcttooltip_from_goaskle_com").tooltip({
	   	bodyHandler: function() {
			return $("<img/>").attr("src", this.tooltipText); 
	   	},
		track: true, showURL: false, delay: 1, id: "qrcttooltip_from_goaskle_com"
	})
  });