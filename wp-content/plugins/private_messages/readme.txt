=== Astoundify Private Messages ===
Contributors: Astoundify
Requires at least: 4.7.0
Tested up to: 4.9.0
Stable tag: 1.9.1
License: GNU General Public License v3.0

Keep user interfaction on-site. As a site owner you want to make sure your users stay on your website as long as possible. Allow your users to easily communicate one-on-one by sending private messages to eachother directly.

= Support Policy =

We will happily patch any confirmed bugs with this plugin, however, We will not offer support for:

1. Customisations of this plugin or any plugins it relies upon
2. CSS Styling (this is customisation work)

If you need help with customisation you will need to find and hire a developer capable of making the changes.

== Installation ==

To install this plugin, please refer to the guide here: [http://codex.wordpress.org/Managing_Plugins#Manual_Plugin_Installation](http://codex.wordpress.org/Managing_Plugins#Manual_Plugin_Installation)

== Changelog ==

= 1.9.1: October 31, 2017 =

* Fix: Avoid error when no application method exists.
* Fix: Correct text domains.
* Fix: Send emails as HTML.

= 1.9.0: October 2, 2017 =

* New: Default "To" field to current author on author archives.
* New: Option to show a login link to guests.
* Fix: Ensure setting defaults are set.

= 1.8.0: August 24, 2017 =

* New: Updated plugin setup process.
* New: Add filters to pm_get_new_message_url().
* New: Filter user display name with `pm_user_display_name`
* Fix: Do not attempt to display avatars if globally disabled.
* Fix: Update coding standards.
* Fix: Use generic "Link" plugin in TinyMCE to avoid displaying site-wide content.
* Fix: Star and Trash icon when WordPress admin bar is not showing.
* Fix: Do not display reply form when thread is deleted.

= 1.7.0: August 1, 2017 = 

* Fix: Undefined PHP function.
* Fix: Save subject setting properly.
* Fix: Properly output URLs in emails.
* Fix: Update subject setting input type.

= 1.6.0: July 10, 2017 =

* New: Update project structure.
* New: Add automatic updater.
* Fix: Capability checks when viewing messages.

= 1.5.0: June 8, 2017 =

* New: Use "Compose Reply" form directly in WP Job Manager apply form.
* New: Introduce toggle to disable WP Job Manager integration.
* Fix: Ensure new message notification is sent to the correct recipient.
* Fix: HTML classname consistency.

= 1.4.0: May 8, 2017 =

* New: AJAX message submission.
* New: Allow messages and message threads to be archived.
* New: Add pagination to message thread dashboard.
* New: Add "Mark all Read" button in message dashboard.
* Fix: Strip slashes from message content.
* Fix: Ensure select2 library can be translated.

= 1.3.0: March 22, 2017 =

* New: Add attachments to images.
* Fix: Exclude current user from search results.
* Fix: Ensure replies are not held for moderation.
* Fix: Resize wp_editor instances properly.

= 1.2.0: March 2, 2017 =

* New: Allow recipient list to be filtered when searching.
* New: Pull translations from https://astoundify.com/glotpress/projects/private-messages/
* Fix: Ensure all user email addresses are hidden.

= 1.1.0: February 2, 2017 =

* New: Hide user's email addresses to encourage on-site communication.
* New: UI stylel adjustments.
* Fix: Do not attempt to display a thread with no message.s
* Fix: WooCommerce product rating compatibility.
* Fix: PHP 5.2 compatibility.
* Fix: Ensure version number is always defined.
* Fix: Ensure settinsg are always read from an array.

= 1.0.0: January 19, 2017 =

* New: Initial Release!
