=== Rooftop CMS ===
Contributors: The Rooftop CMS team
Requires at least: WordPress 4.1
Tested up to: WordPress 4.3-trunk
Version: 1.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
Tags: rooftop

== Description ==
Rooftop base theme

== Installation ==

Add to your site's theme folder and enable on your network.

To use the theme functions in a child theme, set the Template to 'rooftop' in your child theme styles.css template comment.
Your implementation theme should hook into the 'init' hook (with a priority > 10).

Like this:

function setup_custom_features_content_type() {
    RooftopCMS::addContentType('Feature');
    RooftopCMS::addTaxonomy('Feature Detail', 'all');
}
add_action('init', 'setup_custom_features_content_type', 20);

== Copyright ==

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.

== Changelog ==

= 1.0 =
* Released: ...

Initial release
