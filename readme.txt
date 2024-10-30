=== BlockBuddy for Gutenberg ===
Contributors: aaronrutley
Tags: gutenberg, block
Stable tag: 0.1
Requires at least: 5.0.0
Tested up to: 5.1.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Gutenberg Block to easily query and display content from any post type!

== Description ==

=== Custom Query Block ==

Use our first block, Custom Query Block to easily show a grid of posts from any post type, using our UI to performa a simple custom query.

The Custom Query Block comes with 3 basic templates which are stored in the plugin `/templates` directory.

You can override or create your own templates for the block to use by creating a `/template-parts/blocks/` sub folder in your theme.
The plugin will scan active themes for this directory and pull in all PHP templates from this directory so you can select them
via the Custom Query Block inspector settings.

Templates can contain anything you like and you'll have access to the `$attributes` array which has all the selection data for
that particular block, as well as the `$post_query` object for that block.

= Features =
* Developer Friendly
* User Friendly
* Custom Templates

== Screenshots ==

== Changelog ==

= 0.1 =
* Initial Release
