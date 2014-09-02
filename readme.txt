=== Language option for ACF4+ Fields ===
Contributors: VoiD2008
Tags: acf, multylanguage
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=WBHZUEALQ2RAN
License: GPLv2
License URI: http://www.gnu.org/licenses/gpl-2.0.html
Requires at least: 3.0
Tested up to: 3.9.2
Stable tag: 1.3.0

Adding language option to ACF fields plugin.

== Screenshots ==

1. Setting the "Advanced Custom Fields" field group language

2. Setting the "Advanced Custom Fields" single field language

== Description ==

This plugin allows to select language for fields to be shown.
WPML,xili-language,Polylang plugins supported.

== Installation ==

Use WordPress' Add New Plugin feature, searching "Language option for ACF4+ Fields", or download the archive and:

1. Unzip the archive on your computer  
2. Upload `acf_wpml_fields` directory to the `/wp-content/plugins/` directory
3. Activate the plugin through the 'Plugins' menu in WordPress

== Frequently Asked Questions ==

= The plugin doesn't work, why? =

The plugin requires ACF (Advanced Custom Fields) plugin and (Polylang or xili-language or WPML) plugin to be installed and activated.

= How to change language of plugin =

By default plugin uses get_locale() function. If you wish to override language, just put this code to your functions.php

# add_filter('acfwpml_load_langs','acfwpml_override_lang');
# function acfwpml_override_lang(){
#	return 'en_US';
# }

== Upgrade Notice ==

== Changelog ==

= 1.3.0 =
* Added ACF Field Groups language option

= 1.2.1 =
* Added localization en/ru

= 1.2.0 = 
* Added WPML plugin support

= 1.1.0 = 
* Added xili-language plugin support

= 1.0.1 =
* Added Polylang plugin support

== Contribute ==

If you find this useful and you if you want to contribute, there are three ways:

   1. You can write me (void2008dev[at]gmail.com or [here](https://github.com/VoiD2008/acf_wpml_fields)) and submit your bug reports, suggestions and requests for features;
   2. Using the plugin is free, but if you want you can send me some money with PayPal [here](https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=WBHZUEALQ2RAN)