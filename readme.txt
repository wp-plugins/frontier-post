=== Frontier Post ===
Contributors: finnj
Donate link: 
Tags: frontend, frontend post, frontend edit, frontier, post, widget, posts, taxonomy, Danish
Requires at least: 3.4.0
Tested up to: 4.1.0
Stable tag: 3.3.5
License: GPL v3 or later
 
Simple, Fast & Secure frontend management of posts - Add, Edit, Delete posts from frontend - Full featured frontend management of posts.
  
== Description ==

WordPress Frontier Post Plugin enables simple full featured management of standard posts from frontend for all user roles.

Related plugins: [Frontier Buttons](http://wordpress.org/plugins/frontier-buttons/) |  [Frontier Set Featured plugin ](http://wordpress.org/plugins/frontier-set-featured/) 

Intention of the Frontier Post plugin is to enable front end posting and editing on your blog. Allowing your users to create content easy, with no need to go into the back-end.
Editors and Administrators can use Frontier to edit posts from the frontend (Can be enabled/disabled in settings), and at the same time go to the backend for more advanced options.

Frontier Post is intentionally made simple - But it is highly configuable if you want to extend it :)

= Usage = 
* Short-code [frontier-post] in a page content after install and activation of the plugin - Then review settings and capabilities
* Short code parameters  [http://wpfrontier.com/frontier-post-shortcodes/](http://wpfrontier.com/frontier-post-shortcodes/)

= Main Features =
* Create posts with media directly from frontend
* Users can delete their own posts (Optional) 
* Users can edit their own posts (Optional)
* Post can be edited in frontend directly - Using standard edit link (Optional)
* My Posts Widget 
* My Approvals Widget
* Capabilities are aligned with Wordpress standard.
* Excerpts editable (Optional)
* Edit Categories (dropdown, multiselect, checkbox or radio button)
* Default category per role
* Allowed categories per role
* Widget to enable post creation link on category archive pages
* Tags (Optional)
* Supports Wordpress Post Status Transitions (draft, pending, private & publish)
* Customizable editor layout using: [Frontier Buttons plugin](http://wordpress.org/plugins/frontier-buttons/)
* Disable Admin bar per role (Optional)
* User defined templates for forms
* Users must be logged in to post
* Multiple pages with frontier-post shortcode can be used.
* Supports external management of capabilities (tested with User Role Editor)
* Title can be hidden on certain pages by adding comma separated list of page IDs
* Add ID column to list of categories (optional)
* Hide post status (optional)
* frontier-post.css can be placed in template (child theme dir - [See here](http://wpfrontier.com/frontier-post-templates-css/) ), allowing for customm css rules.
* New in version 3.3.x
 * Support for taxonomies
 * Custom Post Types
 * New Form Layout
 * Edit Pages
 * Documentation: [www.wpfrontier.com](http://wpfrontier.com/)


= My Posts Widget =
* Show logged-in users posts (Author)
 * My Posts
 * Comments to users posts
 * Excerpts of comments
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
* French (pabaly)
* Dutch (fredwier)

Let me know what you think, and if you have enhancement requests or problems let me know through support area

== Installation ==

1. [Install plugin](http://www.wpbeginner.com/beginners-guide/step-by-step-guide-to-install-a-wordpress-plugin-for-beginners/)
2. Activate the plugin
3. Place page "My Posts" (Created on activation) in the menu.
4: Review (and update) Frontier Post settings & capabilities

== Frequently Asked Questions ==

[http://wpfrontier.com/frontier-post-faq/](http://wpfrontier.com/frontier-post-faq/)


== Screenshots ==

1. Frontier post list
2. Add/Edit post form 
3. Frontier Post settings
4. Frontier My Posts Widget: Settings, My posts, Comments & comments excerpts (with different themes)
5. Frontier Post capabilities
6. Frontier Post advanced settings

== Changelog ==

= 3.3.5 =
* NEW: Support for custom taxonomies (no coding necessary)
* NEW: Support for custom post types (no coding necessary)
* NEW: Support for custom fields using template forms, filters and actions.
* Settings has be re-organized
* Added custom login text under advanced option
* Changed to use get_stylesheet_directory_uri() instead of bloginfo functions that is manipulated by WPML
* Moved tags, featured image & excerpt to fieldset layout in new form
* Migration of old settings added
* Activation script updated
* Uninstall script update - clean up of old entries on options table
* Added select of form in advanced option (standard/simple/old)
* Changed css to support fieldset in safari and chrome
* Restrict frontier-post shortcode to pages
* Re-introduced output buffering
* updated frontier_post_tax_form.php with float left fieldsets

= 3.0.7 =
* Fixed capability messages not showing

= 3.0.6 =
* Fixed link to post on frontier_list_form, missing double quotes
* Fixed single category dropdown did not respect excluded categories.

= 3.0.5 =
* Support for custom post types in lists, and in forms using template forms, no support for custom fields. 
* Added 2 new shortcode parameters: frontier_add_post_type & frontier_list_post_types
 * Example usage: [frontier-post frontier_add_post_type="page" frontier_list_post_types="post,page"]

= 3.0.5 =
* Changed _wpnonce name and action to frontier post specific to resolve possible conflict
* Added "add_args" => false  to pagination var in frontier_list_form.php due to wp 4.1 bug (trac ticket 30831) 
* Added div tags to columns in frontier_list_form.php to allow custom css rules
* Updated frontier_cann_add, frontier_can_edit & frontier_can_delete functions to ensure access works correctly
* Added shortcode parameters: frontier_list_text_before & frontier_edit_text_before to display text on forms before shortcode output
* Updated example forms accordingly
* Fixed loading of css if placed in active theme dir (your-active-theme/plugins/frontier-post/)

= 3.0.2 =
* Fixed issue with users being able to publish without necessary capability
* Fixed: Allow users with wordpress standard capability (Admins & editors) edit_other_posts to edit other users post from frontend
* Fixed: Allow users with wordpress standard capability Admins & editors) delete_other_posts to delete other users post from frontend


= 3.0.1 =
* Fixed issue with Save and preview - Call changed to: include_once(frontier_load_form("frontier_post_preview_form.php"))

= 3.0.0 =
* Multiple categories can now be used in shortcode parms - use double quotes around comma separated list
* Re-designed page transition logic. Removed output buffering (ob_start() etc), and changed it to a flow in php.
* Added preview page due to new transition logic not allowing redirects to standard preview page
* Added option to hide page title for certain pages.
* Added message on the frontend for add/update/delete - Must be enabled in settings.
* Tested with 4.1
* Added option to show IDs for categories in the admin panel list
* Removed post status column from list posts if short code parameter: frontier_list_all_posts="true"
* Removed count of users posts text from list posts if short code parameter: frontier_list_all_posts="true"
* Added option for editor height (default 300)
* Fixed return from delete post, so returned to calling list page
* New function: frontier_post_wp_editor_args to allow change of editor options
* Call to wp_editor in frontier_form to use new function, to enable config of editor in templates
* Added hidden field post_categories to frontier_form.php to keep categories if category field is removed from form
* Updated logic for set capabilities, and disabled capability set on plugin activate, if external management of capabilities is enabled
* Added integration with Frontier Buttons calling function: theme_advanced_buttons1
* Disable submit buttons individually in settings
* If user has capability "delete_other_posts" (Administrators & Editors) always allow them allow them to delete posts from frontend.
* If user has capability "edit_others_posts" (Administrators & Editors) always allow them allow them to edit posts from frontend.
* If all posts are being displayed in frontier-list, show author instead of category
* Cmt heading in frontier list replaced by comment icon as heading was confusing.
* frontier_post.css will be loaded from template directory if present
* Added shortcode parameter [frontier-post frontier_list_all_posts="frontier_list_all_posts"] - Will list all published posts, not only from current user, can be combined with frontier_list_cat_id 
* Added shortcode parameter [frontier-post frontier_return_text="Save & return to category"] - Will change text on Save & Return button
* Added shortcode parameter [frontier-post frontier_list_cat_id=7] to allow for the list of the users post to be limited to one category
* Cleaned frontier_form.php, added switch for category display type
* Changed HTML output to functions for multi and checkbox
* Added category in shortcode ex:  [frontier-post frontier_cat_id=7]
* Enabled support for capabilties can be managed from other plugin (User Role Editor)
* Added widget New post from Category - The widget can be added to a category page, and will take the category from that page


= 2.6.1 =
* Removed .container (added in 2.6.0) from css as it might conflict 

= 2.6.0 =
* Added option for categories as checkbox list
* Fixed issue, Post status dropdown didnt show correct status.
* Added function frontier_tax_list() to prepare support for taxonomies

= 2.5.5 =
* Fixed My Approvals Widget & My Posts Widget - Logical values (checkbox) did not save

= 2.5.4 =
* NEW option: Default post status
* Option: Allow users to change status from Published - Fixed and works as designed
* Corrected error where mce buttons didnt work for WP versions prior to WP 3.9

= 2.5.1 =
* tinyMCE editor buttons moved to separate plugin Frontier Buttons from Wordpress version 3.9
* Dutch translation

= 2.1.2 =
* Support for Private posts
* New setting: Allow users to change status from Published
* Redirect to frontier list post page after login (thanks: newtonsongbird)
* Fixed: Frontier Edit now respects max days set in Frontier settings

= 2.1.0 =
* Short code parameters:
 * frontier_mode: Option to set frontier_mode=add using this parameter will enable to show add form directly in page - Usage: [frontier-post frontier_mode=add]
 * frontier_parent_cat_id: Option only to show child categories of the parent category in dropdowns - Usage: [frontier-post frontier_parent_cat_id=7]
 * Combined: [frontier-post mode=add frontier_parent_cat_id=7] where 7 is the category id
* option to show link to login page after message: Please login - Link used: wp_login_url()

= 2.0.7 =
* Images was not properly attached to post, fixed
* Featured image need the post to be saved once to work, fixed
 * User still needs to press save to view featured image

= 2.0.5 =
* Wordcount added (TinyMCE plugin, you need to enable custom editor buttons)
* French translation added (thanks to pabaly)

= 2.0.4 =
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

If you are upgrading to version > 3.3.x, then please test as settings and forms has been changed.