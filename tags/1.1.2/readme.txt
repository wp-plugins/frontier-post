=== Frontier Post ===
Contributors: finnj
Donate link: 
Tags: post from frontend, frontend posting, frontend editing, Frontier
Requires at least: 3.4.0
Tested up to: 3.5.1
Stable tag: 1.1.2
License: GPL v3 or later
 
Front end management of posts. Add, edit & delete posts from frontend - Fast, Easy, Secure and Effective :)
  
== Description ==

WordPress Frontier Post Plugin enables adding, deleting and editing standard posts from frontend.

Intention of the Frontier Post plugin is to enable front end posting and editing on your blog. Allowing your users to create content easy, with no need to go into the back-end.
Editors and Administrators can 

Frontier Post is intentionally made simple :)

= Usage = 
Add short-code [frontier-post] in a page content after install and activate the plugin

= Main Features =
* Stripped from advanced styling and js scripting.
* Is intended to work with themes out-of-the-box
* Users can create posts with media, and categorize posts
* Users can delete their own posts (Setting) 
* Users can edit their own posts (Setting)
* Post can be edited in frontend directly from post (edit link) (Setting)
* Capabilities are aligned with Wordpress standard.
* Excerpts editable (Setting)
* Post thumbnail will take first image added.
* Users must be logged in to post

= Translation =
* Danish

Let me know what you think, and if you have enhancement requests or problems let me know through support area

== Installation ==

1. Upload `frontier-post` to the `/wp-content/plugins/`  directory or search for Frontier Post from add plugin.
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Add a page with the shortcode: [frontier-post] in the content
4. Add the page to your menu
5: Update Frontier Post settings (settings menu)

== Frequently Asked Questions ==

= Known Issues and limitations =
* Only English, might add translation capability if plugin will be popular.
* Only handles single category.
* Issues with media upload on wp 3.5 (wp bug)
* Tags not enabled (yet)
* No Custom Fields
* Supports only published state - New posts are published immediately!.
* Only for standard posts, not custom post types

= Testing =
* Frontier post is mainly tested with:
* Wordpress 3.5.1
 * [Suffusion Theme](http://wordpress.org/extend/themes/suffusion/)
 * [Theme My Login](http://wordpress.org/extend/plugins/theme-my-login/)
 * and sometimes with twenty twelve theme...

 = Clenup =
 * On deactivation: no cleanup.
 * On deletion options are deleted, and role capabilities are removed.
 * If you accidently delete the frontier-post plugin folder, you should
  * Delete all options starting with 


== Screenshots ==

1. Frontier post list
2. Add/Edit post form 
3. Frontier Post settings

== Changelog ==

= 1.1.2 =
* Fixed upgrade problem

= 1.1.1 =
* Danish translation added

= 1.1 =
* Added check for comments on edit and delete based on settings
* Added support for excerpts
* Added role-based capabilities
* Added ability to use Frontier Post edit directly from post using standard edit link
* Added link to page containing page in shortcode

= 1.0.1 =
* Added pagination to list of authors posts.

= 1.0.0 =
* Initial release


== Upgrade Notice ==
None