=== Frontier Post ===
Contributors: finnj
Donate link: 
Tags: frontend, frontend post, frontend edit, frontier, post widget, posts, widget, Danish
Requires at least: 3.4.0
Tested up to: 3.6
Stable tag: 1.6.2
License: GPL v3 or later
 
Simple, Fast & Secure frontend management of posts - Add, Edit, Delete posts from frontend - My Posts Widget
  
== Description ==

WordPress Frontier Post Plugin enables simple full featured management of standard posts from frontend for all user roles.

Intention of the Frontier Post plugin is to enable front end posting and editing on your blog. Allowing your users to create content easy, with no need to go into the back-end.
Editors and Administrators can use Frontier to edit posts from the frontend (Can be enabled/disabled in settings), and at the same time go to the backend for more advanced options.

Frontier Post is intentionally made simple :)

= Usage = 
Add short-code [frontier-post] in a page content after install and activation of the plugin

= Main Features =
* Create posts with media directly from frontend
* Users can delete their own posts (Optional) 
* Users can edit their own posts (Optional)
* Post can be edited in frontend directly - Using standard edit link (Optional)
* My Posts Widget 
* My Approvals Widget
* Capabilities are aligned with Wordpress standard.
* Excerpts editable (Optional)
* Edit Categories (dropdown or multiselect)
* Default category per role
* Tags (Optional)
* Supports Wordpress Post Status Transitions
* 4 editor options for frontend editing (Full, Simple-Visual, Simple-Html or Text-Only)
* Editor enhancements: Smiley (emoticons), Table control and Search & Replace 
* Disable Admin bar per role (Optional)
* User defined templates for forms
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
* Spanish (Hasmin)
* Polish (Thomasz)

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

= Template Forms =
* At the moment this functionality is Beta !
* You can copy the forms located in the forms directory of the plugin to your theme
** create a subdirectory in you theme (or child theme) folder: /plugins/frontier-post/ - Example: wordpress/wp-content/themes/twentytwelve/plugins/frontier-post/


= Editor =
* At the moment this functionality is Beta !
* The following tinymce modules are loaded: emotions, searchreplace & table.
* Standard wordpress button setup
 * 1: bold, italic, strikethrough, bullist, numlist, blockquote, justifyleft, justifycenter, justifyright, link, unlink, wp_more, spellchecker, fullscreen, wp_adv
 * 2: formatselect, underline, justifyfull, forecolor, pastetext, pasteword, removeformat, charmap, outdent, indent, undo, redo, wp_help
 * 3: Empty
 * 4: Empty
* Suggested button setup (Default on Frontier Post install)
 * 1: bold, italic, underline, strikethrough, bullist, numlist, blockquote, justifyleft, justifycenter, justifyright, link, unlink, wp_more, spellchecker, fullscreen, wp_adv
 * 2: emotions, formatselect, justifyfull, forecolor, pastetext, pasteword, removeformat, charmap, outdent, indent, undo, redo, wp_help
 * 3: search,replace,|,tablecontrols
 * 4: Empty
* Documentation:
 * [tinymce ](http://www.tinymce.com/wiki.php/TinyMCE3x:Buttons/controls/)
 * [Wordpress Codex](http://codex.wordpress.org/TinyMCE/)

= Widgets =
* Widgets are not cached as content is based on current logged in user. 
* Widget queries are index optimized, but queries will execute each time they are shown.
* Might have a performance impact on large blogs with high activity.
 * Consider not to place widgets on front page.
* If you experience performance issues with widgets, create a support issue (preferably with suggestions to remedy this :) )


= Testing =
* Frontier post is mainly tested with:
* Wordpress 3.7.1
 * [Suffusion Theme](http://wordpress.org/extend/themes/suffusion/)
 * and sometimes with twenty thirteen theme...
* iPad & iPhone: Safari & Chrome - Windows 7: IE9, Firefox & Chrome

= Translations =
* Please post a link in support to translation files and I will include them in next release.

 = Cleanup =
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

= Planned =

= 2.0.3 =
* Template forms: Forms can be copied (and changed) to theme folder - See FAQ
* Option: Exclude categories by ID from dropdowns on form
* Option: Email to list of emails on post for approval
* Option: Email to Author when post is approved (Pending to Publish)
* Save button on Frontier edit form (so user can save post and stay on form)
* Submit button: New setting to decide if user is taken to My Posts or to the actual post when a new post is submitted or edited. 
* Featured Image support.


= 1.6.2 =
* Translation fixes (Thanks: Thomasz Bednarek)
* Updated translations: Danish, Spanish, Polish & Russian)
* Added suggested buttons for editor in settings page.
* frontier_fix_list.php removed

= 1.6.0 =
* Temp version to be able correct the post_data issue (frontier_fix_list.php).

= 1.5.9 =
* Fixed issue where post_status was set to display value instead of value, meaning post was updated with translated value. Posts still in db, but does not show up in WP

= 1.5.7 =
* Bug: Post status changed to draft if post status was not selectable (as with a published post), hidden input field added to hold post_status
* Preview link added to My Posts list for posts that are not published (Link to unpublished posts was removed in 1.5.1)

= 1.5.6 =
* New buttons on editor: Smileys, search & replace and table control
* Frontend Author role added (Same capabilities as Author, makes it possible to distinguish between Author and Frontend Author) 
* Bug in My Posts fixed (comments from post showing), wp_reset_postdata() added in end of frontier_list.php
* Spanish Translation (hasmin)

= 1.5.1 =
* Option to hide admin bar
* Default category per role
* Only redirect edit to frontend for standard post type (not pages and custom post types)
* Du not show dropdown for status with only 1 option, only show value
* Added missing closing tags for ul and div in my approvals widget 

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