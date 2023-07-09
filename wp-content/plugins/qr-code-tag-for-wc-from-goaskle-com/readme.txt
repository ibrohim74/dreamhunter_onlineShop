=== QR Code Tag for WC order emails, POS receipt emails, PDF invoices, PDF packing slips, Blog posts, Custom post types and Pages (from goaskle.com) ===
Contributors: www.15.to
Donate link: https://goaskle.com/en/donation/
Tags: qrcode, widget, shortcode, woocommerce, qr code, mobile, google, barcode, scan, tooltip, popup, invoice, receipt, pdf, acf, custom template, selling tickets, hash
Requires at least: 4.4
Tested up to: 6.2
Stable tag: 1.9.15
License: GPLv3 or later
License URI: http://www.gnu.org/licenses/gpl-3.0.html
Text Domain: qr-code-tag-for-wc-from-goaskle-com
Domain Path: /lang

Generates QR codes for Woocommerce automatically and inserts them into every Woocommerce email, PDF invoice and POS receipt.
Also you can use QR Codes (Google API or QR Code Lib) anywhere in your blog, using Widget, using Tooltip, using Shortcode or even with a PHP function or in any other place.

== Description ==

<a href="https://goaskle.com/en/test-qr-code/">LIVE DEMO</a>

<a href="https://goaskle.com/en/docs/wordpress-plugins-from-goaskle-com/qr-code-tag-for-woocommerce-and-wordpress-from-goaskle-com/how-to-use-plugin-qr-code-tag-for-wordpress-and-woocommerce/">HELP (HOW TO USE, DOCUMENTATION)</a>

!!! QR Code Template Editor in admin area in settings of plugin !!! - DONE ( from version 1.9.3 )

* Generates QR codes for Woocommerce orders automatically / dynamically ( Manual mode ) and inserts them into every Woocommerce email, PDF invoice and POS receipt. Starting from version 1.9.3 you can use the following variables to create your own data template for the QR Code generated for each order in Woocommerce:

{order_id}, {full_customer_name}, {total_amount}, {total_amount_no_curr}, {vat_amount}, {vat_number}, {order_date}, {order_date_year}, {order_date_mon}, {order_date_day}, {order_time}, {wc_website}, {wc_order_items_name1}, {wc_order_items_qty1}, {wc_order_items_price1},  {wc_order_items_subtot1} , {wc_order_items_name2}, {wc_order_items_qty2}, {wc_order_items_price2},  {wc_order_items_subtot2}, {wc_order_items_template1}, {hash_gaqr}

More detailed read here:
<a href="https://goaskle.com/en/docs/wordpress-plugins-from-goaskle-com/qr-code-tag-for-woocommerce-and-wordpress-from-goaskle-com/how-to-use-plugin-qr-code-tag-for-wordpress-and-woocommerce/">HELP (HOW TO USE, DOCUMENTATION)</a>

* Also you can use QR Codes (Google API or QR Code Lib) anywhere in your blog, using Widget, using Tooltip, using Shortcode or even with a PHP function or in any other place.

QR code images compatible with Gmail (not cutted).

<b>These plugins are already supported out of the box:</b>

* WooCommerce (All order emails)
* WooCommerce PDF Invoices & Packing Slips (Emails + Files)
* Kadence WooCommerce Email Designer (Emails)
* YITH Point of Sale for WooCommerce (Receipts)
* Advanced Custom Fields ( ACF - fields from products of Woocommerce )
* Print Invoice & Delivery Notes for WooCommerce By Tyche Softwares

In accordance with current legal requirements (Due Covid-19 in some countries), we have created a standard customer data template:

* The data for the template is generated dynamically based on the data from each order in WooCommerce. 
* At the same time, we left the opportunity for you to create QR codes based on your specified static data. Use one of 3 methods: shortcode, widget or tooltip. 

Instructions for developers are provided to place QR codes inside any place in the template of your Wordpress-based website using special php function!

The dynamic data of the automatic template is shown in brackets:

{Customer First Name} {Customer Family Name}
TOTAL AMOUNT {$39.10} - Total order amount from Woocommerce
VAT AMOUNT {$5.10} - Total Tax order amount from Woocommerce
VAT NUMBER {xxxxxxxx} (you can change it in admin: Goto Wordpress Admin -> Settings -> QR code Tag from Goaskle.com) -> Enter yours there
DATE {03/11/2021} - Order date (created) from Woocommerce
TIME {19:03} - Order time (created) from Woocommerce
{https://yourdomain.com} - Your domain where this Woocommerce order was created.

For example:
Ivan Ivanov
TOTAL AMOUNT $39.10
VAT AMOUNT $5.10
VAT NUMBER 1235ABN457
DATE 03/11/2021
TIME 19:03
https://goaskle.com

Features:

For Woocommerce:

* Generates QR codes for Woocommerce automatically based on order or you can disable auto mode at all
* Generates QR codes for Woocommerce manually based on your own template using variables. Check FAQ or Documentation for details
* Inserts QR Codes into every Woocommerce email, PDF invoice and POS receipt
* Attaching to email automatically
* QR code images also compatible with Gmail (visible).
* Support WooCommerce email templates (invoices, order notifications)
* Support WooCommerce PDF Invoices & Packing Slips plugin (showing inside PDF invoices and packing slips)
* Supports Kadence WooCommerce Email Designer plugin and its templates
* Supports YITH Point of Sale for WooCommerce plugin and its email and PDF receipts
* Supports Advanced Custom Fields ( ACF ) plugin for Woocommerce template ( Get value only from Woocommerce product object yet )
* Supports Print Invoice & Delivery Notes for WooCommerce By Tyche Softwares plugin for Woocommerce template
* Generate unique HASH for every order ( useful for ticket sellers ). Validate HASH by customer and by administrator. Edit HASH in order layout in admin. Activate or deactivate HASH. Create list of activations of HASH.

For any (Wordpress posts, pages, sidebars, widgets, tooltips, custom posts):

* Choose your QR Code generator: Google Chart API (online connection required) or QR Code Lib (included)
* Uses cURL if `allow_url_fopen` is disabled (Google Chart API)  
* GIF, PNG or JPEG image output
* All QR Code images are cached
* Use as a Sidebar Widget
* Use the Shortcode `[qrcodetag_from_goaskle_com] Your content [/qrcodetag_from_goaskle_com]` within your posts
* Use the Tooltip mode `[qrcodetag_from_goaskle_com tooltip="content"] Your some text for hover on it [/qrcodetag_from_goaskle_com]` within your posts
* Use the PHP function inside your own template ( see details in documentation or in plugin settings )
* "Best Read Mode" for optimized QR Code image size
* Works with PHP 7.3+, PHP 7.4+, PHP 8+ as well
* Works on symlinked plugin folders
* Available plugin admin interface languages: English, German, Arabic, French
* Changing some colors of the QR code ( since v1.9.15 )

== Installation ==

How to install?

See instruction here:

<a href="https://goaskle.com/en/docs/wordpress-plugins-from-goaskle-com/qr-code-tag-for-wc-order-emails-pos-receipt-emails-pdf-invoices-pdf-packing-slips-blog-posts-custom-post-types-and-pages-from-goaskle-com/installation/">How To Install instructions</a>


Manual instruction ( if automatic installation using plugins directory not working for you ):

1. Upload the full directory into your /wp-content/plugins/ directory, or install it through the admin interface
2. Set write permissions for `/wp-content/plugins/qr-code-tag-for-wc-from-goaskle-com/data` directory
3. Activate the plugin through the 'Plugins' menu in WordPress Admin area
4. Go to the settings page and change the default values (optional)

Requirements:

* PHP7+ or PHP7.3+ or PHP7.4+ or PHP8+ with GD Lib and Curl (with Google Chart API only)
* WordPress 4.4+ (required)
* Woocommerce 3.0+ (not required) (if you want to have QR codes automatically attached to all your Woocommerce order emails, PDF invoices and POS receipts only)


== Frequently Asked Questions ==

= Does your plugin work with Woocommerce plugin? =

Yes, of course. It works in two modes:

AUTOMATIC MODE

you only need to enter your VAT if you have it, if not then clear the VAT Number field in plugin settings
and CLEAR "Woocommerce Data Template" field and then Save options

MANUAL MODE

you need to create your own data template for QR code and enter it in "Woocommerce Data Template" ( field in plugin settings ) using the following variables:

 {order_id}, {full_customer_name}, {total_amount}, {total_amount_no_curr}, {vat_amount}, {vat_number}, {order_date}, {order_date_year}, {order_date_mon}, {order_date_day}, {order_time}, {wc_website}, {wc_order_items_name1}, {wc_order_items_qty1}, {wc_order_items_price1},  {wc_order_items_subtot1} , {wc_order_items_name2}, {wc_order_items_qty2}, {wc_order_items_price2},  {wc_order_items_subtot2}, {wc_order_items_template1}

You can use this template and copy paste and edit as you wish:

Order ID: {order_id}
{full_customer_name}
TOTAL AMOUNT: {total_amount}
VAT AMOUNT: {vat_amount}
VAT NUMBER: {vat_number}
DATE {order_date}
TIME {order_time}
{wc_website}
{wc_order_items_template1}

Don't forget to Save options after changing your template

= What is a QR Code? =
Read <a href="http://en.wikipedia.org/wiki/QR_Code">Wikipedia QR Code article</a>.

= Will there be a PHP4 or lower than PHP7 version? =

No. Please upgrade your PHP installation.

= Does it work with PHP 7.3+ and with PHP 7.4+? =

Yes it does.

= Does it work with PHP 8? =

Yes it does.

= Which image type should I choose? =

PNG is the preferred one. If you're concerned about very old browser, use GIF instead. Or JPEG.

= Which code generator should I choose? =

If you're on a webserver that disallows online connection from within php scripts, you should switch over to the QR Code Lib.

= There is a red image instead of the QR Code image. What's wrong? =

You're `/qr-code-tag-goaskle-com/data/` directory is _not writeable_. Please adjust
your permissions. See <a href="http://codex.wordpress.org/Changing_File_Permissions">Changing File Permissions</a>.

= How to use the tooltip mode? =

See Plugin Help (below Plugin settings in your WordPress administration area).

= There is only a blank page for large posts with your plugin! Why? =

This is a PHP / WordPress problem. See <a href="http://www.undermyhat.org/blog/2009/07/sudden-empty-blank-page-for-large-posts-with-wordpress/">Sudden empty / blank page for large posts with WordPress</a> for problem description and solutions.

= The margin with the Google API differs from that one created by the QR Code Lib. Why? =

Google Chart API creates a different margin. I can't tell you why - ask Google.

= How I can check the generated QR Code? =

You can use the <a href="http://zxing.org/w/decode.jspx">Google ZXing online service</a> 

= Where I can download a barcode reader for my mobile device? =

* <a href="http://zxing.org/">http://zxing.org/</a>
* <a href="http://www.quickmark.com.tw/">http://www.quickmark.com.tw/</a>
* <a href="http://www.i-nigma.mobi/">http://www.i-nigma.mobi/</a>
* <a href="http://reader.kaywa.com/">http://reader.kaywa.com/ </a>
* <a href="http://get.neoreader.com/">http://get.neoreader.com/</a>

= The plugin is not available in (_put a language in here_). Why? =

Because no one translated it yet into this language. How about you? See Plugin Help for translation hints!

== Screenshots ==

1. Widget options
2. Tooltip mode
3. Admin interface
4. Order invoice example (Email)
5. QR code example

== Changelog ==

= 1.9.15 (06 March 2023) =
* New feature: Changing some colors of the QR code ( Plugin settings )

= 1.9.14 (22 February 2023) =
* Fixed some errors with hash

= 1.9.13 (22 February 2023) =
* Added new option for ticket sellers. Added the ability to generate a unique hash for each woocommerce order ( Enable checkbox in Settings page ). Thanks to this function it is possible to check the validity of the information provided by your customer inside the QR code. QR code is very easy to forge, so having the ability to assign each order in woocommerce a unique hash gives protection of customer data and allows you to make sure that this person ordered goods or services on your site and not someone else. Also added the function of activation and deactivation of the hash code with a list of activations in the admin panel inside the order. Also added the ability to validate the hash code for both you and your customers. Also added the ability to edit a unique hash code in order editing mode. Detailed explanation in documentation.
* Fixed some errors

= 1.9.12 (17 February 2023) =
* Added new option for hide QR code for all Woocommerce pages and use with Woocommerce emails only ( checkbox in Settings page )

= 1.9.11 (12 January 2023) =
* Added option to choose which emails in Woocommerce to add QR-code ( in Settings page )
* Fixed timezone issue

= 1.9.10 (06 January 2023) =
* Added Print Invoice & Delivery Notes for WooCommerce By Tyche Softwares plugin support

= 1.9.9 (02 January 2023) =
* Fixed issue from 1.9.8 version
* Added a new checkbox on the plugin settings page in the Wordpress admin area. By enabling this checkbox you can disable adding qr code to all Woocommerce emails except these ones:
new_order, failed_order, customer_on_hold_order, customer_processing_order, customer_completed_order, customer_refunded_order, customer_partially_refunded_order, cancelled_order, customer_invoice.

= 1.9.8 (27 December 2022) =
* Added ACF plugin support and 1 new custom variable ( multi variable ) for Woocommerce Template for ACF fields: {wc_prod_acf_ZZZ} – Get value only from Woocommerce product object yet ( added since 1.9.8 version ) Where ZZZ = your_acf_field_slug. For example: {wc_prod_acf_lecture_full_date} – get ACF product field from order, will be checked and if acf field with slug “lecture_full_date” will be found then value will be shown. ACF is Advanced Custom Fields plugin of WordPress. So you can easy show all your ACF product fields in your data for QR code.

= 1.9.7 (13 December 2022) =
* Added 1 new variable: {wc_order_number} = $order->get_order_number();

= 1.9.6 (13 December 2022) =
* Fixed zero issue ( when order_id only was used )

= 1.9.5 (12 December 2022) =
* Fixed link wrapping for Woocommerce
* Added 3 new variables: {order_date_year} - Year from order date, {order_date_mon} - Month from order date, {order_date_day} - Day from order date

= 1.9.4 (18 November 2022) =
* Added short documentation and removed some files ( old translations )

= 1.9.3 (09 November 2022) =
* Added new variables:
{wc_order_items_name1} - names of all products one by one ( Example: from {wc_order_items_name1} to {wc_order_items_name999} unlimited

{wc_order_items_qty1} - quantities of all products one by one ( Example: from {wc_order_items_qty1} to {wc_order_items_qty999} unlimited

{wc_order_items_price1} - prices of all products one by one ( Example: from {wc_order_items_price1} to {wc_order_items_price999} unlimited

{wc_order_items_subtot1} - subtotals of all products one by one ( Example: from {wc_order_items_subtot1} to {wc_order_items_subtot999} unlimited

{wc_order_items_template1} - predefined template for order items in this format:
Product name1 ( Qty X Price = Subtotal )
Product name2 ( Qty X Price = Subtotal )
etc...

= 1.9.2 (20 October 2022) =
* Added new variable {total_amount_no_curr} - order total without currency.
= 1.9.1 (30 August 2022) =
* Fixed some small issues with translations
= 1.9 (25 August 2022) =
* Added possibility to Disable Auto QR code insert into Woocommerce and related to it plugins
= 1.8 (25 August 2022) =
* Added possibility to use your own template for Woocommerce with the following variables {order_id}, {full_customer_name}, {total_amount}, {vat_amount}, {vat_number}, {order_date}, {order_time}, {wc_website}
= 1.7 (09 December 2021) =
= 1.6 (29 November 2021) =
= 1.5 (27 November 2021) =
= 1.4 (07 November 2021) =
= 1.3 (03 November 2021) =
= 1.2 (03 November 2021) =
= 1.1 (03 November 2021) =
* Initial Release

== Demo ==

See <a href="https://goaskle.com/en/test-qr-code/">this page</a> for a tooltip mode demo.
Move your mouse over the application links.

== Acknowledgements ==

Based on QR Code Tag By Dennis D. Spreen
https://wordpress.org/plugins/qr-code-tag/

It uses:

* <a href="http://code.google.com/intl/de/apis/chart/">Google Chart API</a> for online qr code creation
* Y. Swetakes <a href="http://www.swetake.com/qr/index-e.html">QR Code Library</a> for qr code offline creation
* <a href="https://getbootstrap.com/docs/4.0/components/tooltips/">Bootstrap Tooltip Plugin</a> 