=== No Longer in Directory ===
Contributors: WhiteFirDesign
Donate link: https://supporters.eff.org/donate
Tags: security, plugins
Requires at least: 3.3.1
Tested up to: 3.4
Stable tag: trunk

Checks for installed plugins that are no longer in the WordPress.org Plugin Directory.

== Description ==

When a plugin is removed from the WordPress.org Plugin Directory no warning is provided in the WordPress admin area if that plugin is installed in a website. Plugins can be removed for the following reasons:

- they are found to break the GPL
- they are found to break the directory rules
- other plugins by the author are found to be a problem and all are removed pending investigation
- the author asks for it to be closed
- the author asks for it to be closed because they are re-releasing under a different name
- it is being investigated after non-specific complaints
- there is a security vulnerability

If the plugin contains a security vulnerability the website could be vulnerable to being exploited until the plugin is deleted from the installation or a security update is released and applied. This plugin adds a page to WordPress to check if any plugins installed in WordPress are on a list of plugins that are no longer in the WordPress.org Plugin Directory so that WordPress administrators are alerted to the issue.

For removed plugins that have a Secunia Advisory for a security vulnerability, a link to that advisory is included in the results of the check.

To insure that plugins that have returned to the WordPress.org Plugin Directory since the list was last updated are not incorrectly warned about, the plugin rechecks the WordPress.org Plugin Directory to confirm any installed plugins that are on the list have not returned to the directory.

The check is done using the plugin's directory (folder) name which could lead to plugins that have never been in the plugin directory to be flagged if they use the same name as a plugin that was in the WordPress.org Plugin Directory. If you become aware of a plugin this happening to please contact us so that we can put a check in place to prevent that from happening anymore.

**Supported Localizations:** Español, Français

Please let us know if you are interested in us adding additional localizations.

== Installation ==

1. Copy plugin files to the plugins folder.

2. Activate the plugin.

3. Click the No Longer in Directory items in the Plugins Menu to see results.

== Screenshots ==

1. Plugin Page

== Changelog ==

= 1.0.4 =

* Refreshed removed plugin list with data from May 3, 2012
* Added French and Spanish localizations

= 1.0.3 =

* Refreshed removed plugin list with data from April 2, 2012

= 1.0.2 =

* Added links to Secunia Advisories
* Added plugins that were removed from Plugin Directory, based on our informing WordPress of unresolved publicly known vulnerabilities in them, to plugin list
* Fixed issue that could cause removed plugins not to be detected when using older versions of PHP

= 1.0.1 =

* Refreshed removed plugin list with data from March 1, 2012

= 1.0 =
* Initial release