===  Hide login page, Hide wp admin - stop attack on login page  ===
Tags: hide login, hide login page, hide wp admin, rename login, rename login url, login secure, wp-login, wp-login.php, custom login url, custom admin url, rename wp admin, hide my wp, hide my wordpress, hide pages
Contributors: webcraftic
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=VDX7JNTQPNPFW
Requires at least: 5.2
Tested up to: 6.2
Requires PHP: 7.0
Stable tag: trunk
License: GPLv2

Hide wp-login.php login page and close wp-admin access to avoid hacker attacks and brute force.

== Description ==

This simple and light plugin safely rename wp-login.php and closes access to the WordPress admin panel. The plugin does not change the code of your site, does not rename files and does not make any changes to your server configuration. It can intercept admin pages requests, which means that it can work on any WordPress site, regardless of your server.

There are several levels of security in the plugin. When changing the login page you will receive an email with an access recovery link if you forget the login page address. In addition, the plugin will take care that your posts and pages addresses do not intersect with the new login page address, since if the addresses are the same, the login page will be looped.

### Why you should hide WP admin and Hide my WordPress? ###

Dozens of bots daily attacks your WordPress admin area at /wp-login.php and /wp-admin/, brute force passwords and wanting to access your admin panel. Even if you are sure that you have created a hard and reliable password, this does not guarantee security and does not relieve your login page overload. The easiest way to hide login page is simply change its address to a unique one that will be known only to you.

### FEATURES ###

* Hide wp-login.php, wp-signup.php and block access
* Hide wp admin directory and block access
* Allows you to rename login url
* Works with permalinks and without
* There is an opportunity to restore access to hidden login page

Functions related to authorization, such as registration, password recovery, registration confirmation will continue to work in usual mode.

If you are using a cache plugin, you should add your new login URL prefix in the stop caching list.

The wp-admin directory and the wp-login.php page will be unavailable, so it's important to create a bookmark or remember new custom login page url. Deactivating this plugin will return your site to its previous usual state.

### THANK FOR THE PLUGINS' AUTHORS ###
We used some plugins features:
WPS Hide Login, WP Hide & Security Enhancer, Hide My WP – WordPress Security Plugin, Easy Hide Login, Hide WP Admin and Login – WordPress Security, Clearfy – WordPress optimization plugin and disable ultimate tweaker, Rename wp-login.php

### RECOMMENDED SEPARATE MODULES ###
We invite you to check out a few other related free plugins that our team has also produced that you may find especially useful:

* [Clearfy – WordPress optimization plugin and disable ultimate tweaker](https://wordpress.org/plugins/clearfy/)
* [WordPress Assets manager, dequeue scripts, dequeue styles](https://wordpress.org/plugins/gonzales/)
* [Disable Comments for Any Post Types (Remove Comments)](https://wordpress.org/plugins/comments-plus/)
* [Disable updates, Disable automatic updates, Updates manager](https://wordpress.org/plugins/webcraftic-updates-manager/)
* [Disable admin notices individually](https://wordpress.org/plugins/disable-admin-notices/ "Disable admin notices individually")
* [Cyrlitera – transliteration of links and file names](https://wordpress.org/plugins/cyrlitera/)

== Installation ==

1. Upload the plugin files to the `/wp-content/plugins/plugin-name` directory, or install the plugin through the WordPress plugins screen directly.
2. Activate the plugin through the 'Plugins' screen in WordPress
3. Go to the general settings and click on the "Hide login page" tab, activate the options and save the settings.

== Frequently Asked Questions ==

=Does the plugin work in multisite mode?=
No, plugin does not support multisites. It is temporary. We will try to add network support in the future.

=I forgot the login page address, what should I do?=
*Method 1:*
Go to your mailbox and find a letter with the subject <b>"[Hide login page] Your New WP Login"</b> and follow the link to restore login access.

*Method 2:*
Find in the database in the wp_options table option named wbcr_hlp_login_path, wbcr_hlp_hide_login_path, wbcr_hlp_hide_wp_admin, wbcr_hlp_cache_options and delete them.

*Method 3:*
Remove hide-login-page plugin from /wp-content/plugins directory. Log in and re-install the Hide login page plugin.

=How to access the registration and password recovery page?=
After changing the login page URL, the registration and password recovery page addresses will be as follows:
http://site.com/login?action=register and http://site.com /login?action=lostpassword

== Screenshots ==
1. Control panel

== Changelog ==
= 1.1.7 (22.03.2023) =
* Fixed: Freemius framework conflict
* Added: Compatibility with Wordpress 6.2

= 1.1.6 (30.05.2022) =
* Added: Compatibility with Wordpress 6.0

= 1.1.5 (24.03.2022) =
* Added: Compatibility with Wordpress 5.9

= 1.1.4 (20.10.2021) =
* Added: Compatibility with Wordpress 5.8
* Fixed: Minor bugs

= 1.1.2 =
* Fixed: Redirect to 403

= 1.1.1 =
* Fixed: Settings were lost after update

= 1.1.0 =
* Added: Compatibilities with Titan Security plugin
* Added:  Added compatibility Wordpress 5.4

= 1.0.8 =
* Fixed: Added compatibility with ithemes sync

= 1.0.7 =
* Fixed: Compatibility with W3 total cache

= 1.0.6 =
* Fixed: Update core framework
* Fixed: When you enter https://site.dev/login, you could open the login form. Now it is impossible to do.
* Added: Compatibility with Clearfy 1.4.2
* Added: Multisite support ready

= 1.0.5 =
* Fixed: Update core framework

= 1.0.4 =
* Fixed: Menu bug

= 1.0.3 =
* Fixed: When requesting the wp-register.php page, a redirect to the hidden login page.

= 1.0.2 =
* Fixed: In some themes working with Woocommerce, there were php errors when trying to request a wp-admin page.
* Update core

= 1.0.1 =
* Fix smull bugs

= 1.0.0 =
* Plugin release
