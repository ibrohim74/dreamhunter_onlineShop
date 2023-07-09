<?php
/*
Plugin Name:    QR Code Tag for WC order emails, POS receipt emails, PDF invoices, PDF packing slips, Blog posts, Custom post types and Pages (from goaskle.com)
Plugin URI:     https://goaskle.com/en/full-description-of-the-features-of-the-qr-code-tag-from-goaskle-com-plugin/
Description:    QR Code tag generator for Woocommerce orders, POS, PDF, emails. Using modes: automatic for Woocommerce, manual for: widget, shortcode and tooltip.
Version:        1.9.15
Author:         Goaskle.com
Author URI:     https://15.to
Text Domain: qr-code-tag-for-wc-from-goaskle-com
Domain Path: /lang

Copyright 2022-2023  Goaskle.com <info@Goaskle.com>

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

/*
 * Plugin Coding Standard:
 *  This plugin obeys the Zend Framework Coding Standard for PHP 
 *  (http://framework.zend.com/manual/en/coding-standard.html)
 *  (http://codex.wordpress.org/WordPress_Coding_Standards)
 * 
 * $Id: qr-code-tag-goaskle-com.php 76 2021-03-11 07:53:22Z Goaskle.com $
 */
/*
error_reporting(E_ALL);
ini_set('display_errors', 1);
*/
// create global object for template use, etc.
global $qrcodetag_from_goaskle_com;

// load class file
require_once (dirname(__FILE__).'/lib/qrct/QrctWp.php');

// create & initialize QR Code Tag plugin for WordPress
$qrcodetag_from_goaskle_com = new QrctWp_from_Goaskle_Com(__FILE__);
