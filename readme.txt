=== Plugin Name ===
Contributors: BarryCarlyon
Tags: jetpack, twitter
Requires at least: 3.4.0
Tested up to: 3.4.1
Stable tag: 1.7.0.0
License: GPLv2
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Extras for WordPress Jetpack.

== Description ==

This plugin adds extra bits an pieces to [WordPress Jetpack](http://wordpress.org/extend/plugins/jetpack/)

Which includes the following additions:

*   Ability to control button placement, above, below, or both of the post content, with separate options for the archive page and content display page

This Plugin Used to include a Pinterest sharing button, however this is now Part of JetPack Core.

*   Twitter Button added Via
*   Twitter Button added Related (username/optional description format)
*   Adds the ability to make the Twitter button share the WP.me url, if that JetPack module in use, [As suggested by SkipTweets on Twitter](http://skipsloan.com/?p=175)

Currently removed is:

*   Ability to turn on/off the [DNT Twitter](https://dev.twitter.com/docs/tweet-button#optout) button mode - it is difficult to add without editing chunks of the core


== Installation ==

Requires [WordPress Jetpack](http://wordpress.org/extend/plugins/jetpack/)

1. Install either via the WordPress.org plugin directory, or by uploading the files to your server
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Settings are inline with existing JetPack settings pages

== Screenshots ==

1. Main Options

== Changelog ==

= 1.7 =
* Updated display function (sharing)
* Added Twitter Via and Related options for the new Jetpack Sharing Buttons
* Related Supports Username, and optinal description
* Readded WP.me option for Sharing Via Twitter

= 1.6.1.1 =
* Fixed a woopsie in option saving

= 1.6.1.0 =
* Maintainence Fix
* Removed Pinterest button as Supported by JetPack Core
* Class Renames/NameSpacing to Avoid Conflicts
* Moved Twitter Button to a Separate Twitter button, so you can run Core or Extras
* Sanity Check for it JetPack exists or not

= 1.5.0 =
* Rewrote plugin to be a separate plugin,
* Added wp.me support to the Twitter button, so when Tweeting, a Embedded Preview is rendered on Twitter.com [As suggested by SkipTweets on Twitter](http://skipsloan.com/?p=175)

= 1.4.2 =
* Original Release, Whole plugin recplacment,
* Added Pinterest Support,
* Added additional Twitter options, DNT, Data via/Related,
* Button Placement Options
