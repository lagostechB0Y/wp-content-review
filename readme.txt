=== ContentFlow Manager ===
Contributors: lagostechboy
Author: Lagostechboy
Author URI: https://lagostechboy.com
Tags: editorial, content, workflow, review
Requires at least: 6.0
Tested up to: 6.9
Stable tag: 1.0.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Requires PHP: 8.0

Professional editorial content review workflow for WordPress with custom statuses and approval management.

== Description ==
ContentFlow Manager adds a professional editorial review system to WordPress. 
It enables administrators and editors to review, approve, or request changes 
on posts before they go live. Perfect for blogs, news sites, or any team 
collaboration environment.

Key features:
* Custom post review statuses
* Automated reminders for pending reviews
* Admin dashboard table showing review history
* Author dashboard showing review history
* Secure submission with nonces and input sanitization
* Cron-based notifications (WordPress worker)
* Extensible and developer-friendly architecture

== Installation ==
1. Upload the `ContentFlow Manager` folder to the `/wp-content/plugins/` directory.
2. Activate the plugin through the 'Plugins' menu in WordPress.
3. The plugin will automatically create its required database table.
4. Review posts using the new "Review Status" meta box on the post editor.

== Frequently Asked Questions ==
= Can I customize the review statuses?
Yes. Developers can extend the workflow by modifying the StateMachine class 
or hooking into custom actions and filters.

= Will it work with custom post types?
Yes. Any post type that supports 'editor' capabilities can be included.

= How does the reminder cron work?
The plugin schedules an hourly event that checks for posts with 'pending' status 
and can notify editors (logs via WP cron for now).

== Screenshots ==
1. Review meta box on post editor.
2. Review history table in admin dashboard.

== Changelog ==
= 1.0.0 =
* Initial release
* Database schema implemented
* Workflow, hooks, admin UI, cron job, support utilities added

== Upgrade Notice ==
= 1.0.0 =
Initial release. No previous versions.

== Additional Notes for Developers ==
* Namespace: Lagostechboy\EditorialWorkflow
* Autoloaded via PSR-4 compliant function
* Repository: https://github.com/lagostechb0y/content-flow-manager
* GPL licensed
