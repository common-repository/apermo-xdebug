# Apermo Xdebug #
* Contributors: apermo
* Tags: admin, developer, development, debug, debugging, xdebug
* Requires at least: 4.6.0
* Tested up to: 4.9.6
* Stable tag: 1.2.2
* License: GNU General Public License v2 or later
* License URI: http://www.gnu.org/licenses/gpl-2.0.html

This plugin helps developers that use Xdebug.

## Description ##

This plugin helps you to read Xdebug messages inside the WordPress backend, without the need to adjust them everytime.
It simply indents the Xdebug messages, so that these are no longer partly hidden underneath the admin menu.
And it will also give you links to directly search for the error message on Google or Stackoverflow.

If you have issues or want to help [head over to GitHub](https://github.com/apermo/apermo-xdebug)!

## Installation ##

1. Upload the plugin files to the `/wp-content/plugins/apermo-xdebug` directory, or install the plugin through the WordPress plugins screen directly.
2. Activate the 'Apermo AdminBar' plugin through the 'Plugins' menu in WordPress
3. That's all folks.

## Frequently Asked Questions ##

### Are there any requirements? ###
Yes, of course, you need to have Xdebug active on your webserver.

### Where are the Plugin Options? ###
This plugin does not have any, so just activate it, and relax.

### Can I change or add more search engines? ###
Sure, use the filter `apermo_xdebug_search_urls` and alter the array.
The URL requires to add '+search_url+' since this will be added to the JavaScript of the plugin.
This is raw Javascript added here, it's not ideal, but I think more flexible in case you need to do something more with the search_url string.

### Should I use this on a production website? ###
Since you should not use 'display_errors' on a production system, it makes no sense using this plugin there.
The plugin automatically skips it's execution if display_errors is turned off or Xdebug is not active.
At the current moment I decided against automatically disabling the plugin in this cases, I might reconsider this in the future.

### I have an issue, or I want to help with the development of this plugin. ###
Head over to the [GitHub Repository](https://github.com/apermo/apermo-xdebug) and start reading. Every bit of help is highly appreciated!

### Note on the Version Requirement ###
The Plugin will actually work with lower WordPress Versions, but in that case translations won't work. But you should consider upgrading you WordPress in that case.

## Changelog ##

### 1.2.2 ###
* Bugfix: automatically removed css tag if there was only var_dump and no error

### 1.2.1 ### 
* added support for Xdebug var_dump messages

### 1.2.0 ###
* added a filter to provide the options to add more search engines
* automatically disable(or rather skip execution) if display_errors is turned off

### 1.1.0 ###
* optimized to work inside the Gutenberg view
* automatically disable in case Xdebug is not installed
* automatically remove all CSS if no errors are thrown

### 1.0.1 ###
* Added the Google and Stackoverflow links

### 1.0.0 ###
* Initial Release