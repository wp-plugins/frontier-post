=== Frontier Post ===
Contributors: finnj
Donate link: 
Tags: frontend, frontend post, frontend edit, frontier, frontier post, my post widget, posts, widget
Requires at least: 3.4.0
Tested up to: 3.6
Stable tag: 1.4.9
License: GPL v3 or later
 
Fast, easy & secure Front End management of posts. Add, Edit, Delete posts from frontend - My Posts Widget
  
== Description ==

WordPress Frontier Post Plugin enables adding, deleting and editing standard posts from frontend.

Intention of the Frontier Post plugin is to enable front end posting and editing on your blog. Allowing your users to create content easy, with no need to go into the back-end.
Editors and Administrators can use Frontier to edit posts from the frontend (Can be enabled/disabled in settings), and at the same time go to the backend for more advanced options.

Frontier Post is intentionally made simple :)

= Usage = 
Add short-code [frontier-post] in a page content after install and activativation of the plugin

= Main Features =
* Create posts with media directly from frontend
* Users can delete their own posts (Setting) 
* Users can edit their own posts (Setting)
* Post can be edited in frontend directly - standard edit link (Can be enabled/disabled in settings)
* My Posts Widget 
* My Approvals Widget
* Capabilities are aligned with Wordpress standard.
* Excerpts editable (Can be enabled/disabled in settings)
* Edit Categories (dropdown or multiselect)
* Tags (Can be enabled/disabled in settings)
* Supports Wordpress Post Status Transitions
* 4 editor options for frontend editing (Full, Simple-Visual, Simple-Html or Text-Only)
* Users must be logged in to post

= My Posts Widget =
* Show logged-in users posts (Author)
 * My Posts
 * Comments to users posts
 * Experpts of comments
* Link: Create New Post 

= My Approvals Widget =
* Shows pending approval actions including link to approval (will only show for administrators)
 * Number of post approvals pending
 * Number of drafts (optional)
 * Number of comment approvals pending
 * Number of comments marked as spam

= Translations =
* Danish
* Russian (samaks)
* Chinese (beezeeking)

Let me know what you think, and if you have enhancement requests or problems let me know through support area

== Installation ==

1. Upload `frontier-post` to the `/wp-content/plugins/`  directory or search for Frontier Post from add plugin.
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Add a page with the shortcode: [frontier-post] in the content (will be added during activation)
4. Add the page to your menu
5: Update Frontier Post settings (settings menu)

== Frequently Asked Questions ==

= Known Issues and limitations =
* No Custom Fields
* Only for standard posts, not custom post types
* If limited administrator access is selected for a profile in Theme My Login, media uploads will fail for this profile.


= Widgets =
* Widgets are not cached as content is based on current logged in user. 
* Widget queries are index optimized, but queries will execute each time they are shown.
* Might have a performance impact on large blogs with high activity.
 * Consider not to place widgets on front page.
* If you experience performance issues with widgets, create a support issue (preferably with suggestions to remedy this :) )


= Testing =
* Frontier post is mainly tested with:
* Wordpress 3.5.1
 * [Suffusion Theme](http://wordpress.org/extend/themes/suffusion/)
 * [Theme My Login](http://wordpress.org/extend/plugins/theme-my-login/)
 * and sometimes with twenty twelve theme...
* iPad & iPhone: Safari & Chrome - Windows 7: IE9, Firefox & Chrome

= Translations =
* Please post a link in support to translation files and I will include them in next release.

 = Clenup =
 * On deactivation: no cleanup.
 * On deletion options are deleted, and role capabilities are removed.
 * If you accidently delete the frontier-post plugin folder, you should:
  * Delete all options starting with frontier_post
  * Remove all capabilities starting with frontier


== Screenshots ==

1. Frontier post list
2. Add/Edit post form 
3. Frontier Post settings
4. Frontier My Posts Widget: Settings, My posts, Comments & comments excerpts (with different themes)

== Changelog ==

= 1.4.9 =
* Issue with svn, new tag created

= 1.4.8 =
* New Editor options for frontend editing - Full, Simple-Visual, Simple-Html or Text-Only
* Category: Multi-select, dropdown or hidden
* Media upload can be disabled per role
* Drafts: Can be restricted so user have to submit for approval

= 1.3.3 =
* Fixed security issue with add new post
* Chinese translation
* Russian translation

= 1.3.2 =
* Fixed hardcoded urls in My Approvals widget

= 1.3 =
* Supports Wordpress Post Status Transitions (draft/pending/publish)
* New Widget: My Approvals

= 1.2.2 =
* Fixed error in user_post_list query

= 1.2.1 =
* Fixed error in user_post_list query

= 1.2 =
* New My Posts Widget
* Added multi select for Categories
* Added support for Excerpts (Can be enabled/disabled in settings)
* Added support for Tags (Can be enabled/disabled in settings)
* Improved media upload

= 1.1.2 =
* Fixed upgrade problem

= 1.1.1 =
* Danish translation added

= 1.1 =
* Added check for comments on edit and delete based on settings
* Added support for excerpts (Can be enabled/disabled in settings)
* Added role-based capabilities
* Added ability to use Frontier Post edit directly from post using standard edit link
* Added link to page containing page in shortcode

= 1.0.1 =
* Added pagination to list of authors posts.

= 1.0.0 =
* Initial release


== Upgrade Notice ==
None