=== Github README ===
Contributors: octalmage, olensmar, ianmjones, nlenkowski
Donate link: http://jason.stallin.gs
Tags: github, embed, shortcode, readme, markdown
Requires at least: 3.0.1
Tested up to: 4.5.2
Stable tag: trunk
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Easily embed GitHub READMEs in pages/posts.

== Description ==

Github README is a plugin that allows you to embed markdown from GitHub in a page or post using a simple shortcode. 

Usage:

**github_readme**

This shortcode embeds the project's readme. 

[github_readme repo="octalmage/Marknote"]

You can also trim lines from the top of the readme using the "trim" option: 

[github_readme repo="octalmage/Marknote" trim="3"]

This is useful for removing titles since your page/post will most likely already have one. 

**github_markdown**

This shortcode embeds any markdown file found in the repository. 

[github_markdown repo="octalmage/Marknote" file="README.md"]

trim, branch, and cache (seconds to cache) also supported.

**github_wikipage**

This shortcode embeds pages from a project's wiki.

[github_wikipage repo="octalmage/Marknote" page="Syntax"]

trim and cache also supported. 

== Screenshots ==

1. Example of the plugin in action on my blog: http://jason.stallin.gs/projects/marknote/


== Changelog ==
= 0.2.0 =
* Improved markdown rendering with MarkdownExtra.

Special thanks to nlenkowski!

= 0.1.1 =
* Add "cache" attribute to the github_readme shortcode.
* Add "branch" attribute to the github_readme shortcode.
* Add the ability to use the `shortcode_atts_{$shortcode}` filter.
* Fix transients so they are properly referenced when attributes are changed.

Special thanks to ianmjones!

= 0.1.0 =
* Added new github_markdown and github_wikipage shortcodes (thanks olensmar!).

= 0.0.3 =
* Fixed plugin name.

= 0.0.2 =
* Fixed plugin header.

= 0.0.1 =
* First Version. Stable so far!
