=== Content Flow Manager ===
Contributors: classic40
Author: Lagostechboy (classic40)
Author URI: https://lagostechboy.com
Tags: editorial workflow, content approval, multi-author, editorial review, publishing workflow, admin control
Requires at least: 6.0
Tested up to: 6.9
Stable tag: 1.0.2
Requires PHP: 8.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

A professional editorial content approval workflow for WordPress. Perfect for multi-author sites that require strong editorial and administrative control before publishing.

== Description ==

**Content Flow Manager** adds a structured, professional editorial review workflow to WordPress.

It is designed for **multi-author blogs, editorial teams, news platforms, content agencies, and organizations** that need to review, approve, or reject content *before* it is published.

Instead of relying on informal checks, chat messages, or external tools, Content Flow Manager introduces a clear approval process directly inside the WordPress editor.

---

### Why Content Flow Manager?

WordPress is powerful — but out of the box, it needs a true editorial approval layer.

On multi-author sites, this often leads to:
- Posts being published accidentally
- Feedback scattered across emails or chat tools
- No clear approval history
- Editors lacking visibility into content status

**Content Flow Manager bridges this gap** by introducing a structured review step between content creation and publication — without changing how authors write.

---

### Real-World Use Cases

**1. Multi-Author Blogs**  
Authors create content freely while editors retain full control over what gets published.  
Posts are submitted for review, feedback is given directly in WordPress, and only approved content goes live.

**2. News & Editorial Websites**  
Designed for editorial teams that need accountability and traceability.  
Editors can approve or reject stories with notes, creating a clear approval trail for every post.

**3. Corporate & Marketing Teams**  
Marketing teams draft announcements, campaigns, or updates while managers review and approve content before publishing — preventing mistakes and maintaining brand consistency.

**4. Educational & Community Platforms**  
Ideal for platforms where instructors, moderators, or administrators must review submitted content before it becomes public.

**5. Agencies Managing Client Content**  
Writers prepare content while clients or internal reviewers approve final versions, keeping everything auditable and centralized inside WordPress.

---

### How It Works (Simple Flow)

1. An author creates or updates a post
2. The post is marked as **Pending Review**
3. An editor or administrator reviews the content
4. The reviewer **approves or rejects** the post with a note
5. The author sees feedback and can revise accordingly
6. Approved content is published with full traceability

No emails. No spreadsheets. No confusion.

Everything happens inside WordPress.

---

### Why I Built This Plugin

I built Content Flow Manager after working on multi-author and editorial WordPress sites where content quality mattered, but proper approval workflows were missing.

Too often, review processes lived outside WordPress — in chats, emails, or shared documents — leading to confusion, missed feedback, and accidental publishing.

This plugin was built to:
- Keep the entire editorial process inside WordPress
- Provide clear accountability for approvals
- Give authors transparent feedback
- Offer a clean, extensible foundation developers can trust

The goal is not to replace WordPress — but to **complete it** for serious content teams.

---

### Key Features

* Editorial review system with **approve / reject** actions
* Reviewer notes and feedback visible to authors
* Clear **pending review** state after content submission
* Author dashboard to track review status and feedback
* Admin/editor dashboard showing full approval history
* Review activity logs stored in a custom database table
* Secure implementation using nonces, sanitization, and capability checks
* Cron-based reminders for pending reviews
* Developer-friendly, extensible architecture using modern PHP

---

== Installation ==

1. Upload the `content-flow-manager` folder to the `/wp-content/plugins/` directory.
2. Activate the plugin through the **Plugins** menu in WordPress.
3. The plugin will automatically create its required database table on activation.
4. Create or edit a post and use the **Review Status** panel to manage approvals.

---

== Frequently Asked Questions ==

= Who can approve or reject content? =
Users with editorial capabilities (Editors and Administrators by default) can review and approve content.

= Can authors see reviewer feedback? =
Yes. Authors can view reviewer notes and approval status directly from their dashboard.

= Is this suitable for multi-author sites? =
Absolutely. Content Flow Manager is specifically designed for multi-author and editorial team workflows.

= Does it support custom post types? =
Yes. Any post type that supports the editor and standard WordPress capabilities can be included.

= Can developers extend the workflow? =
Yes. The plugin is built with a clean architecture that allows developers to extend states, hooks, and logic.

---

== Screenshots ==

1. Post list view showing content marked as **Pending Review**
2. Post editor review panel where editors/admins approve or reject content with notes
3. Author dashboard view displaying review feedback and content status
4. Admin/editor dashboard showing full content review and approval history

---

== Changelog ==

= 1.0.2 =
* Fixed issue where restoring a trashed post caused an error
* Improved workflow handling for system-triggered post state changes

= 1.0.1 =
* Minor security improvements
* Internal workflow refinements

= 1.0.0 =
* Initial release
* Editorial workflow system implemented
* Review logging database schema added
* Admin and author dashboards introduced
* Cron-based review reminders
* Secure and extensible architecture

---

== Upgrade Notice ==

= 1.0.2 =
* Fixed issue where restoring a trashed post caused an error
* Improved workflow handling for system-triggered post state changes

---

== Developer Notes ==

* Namespace: `Lagostechboy\EditorialWorkflow`
* Modern PHP with strict typing
* PSR-4–style autoloading
* Clean separation of concerns (Workflow, Hooks, Admin UI, Database)
* GPL licensed and WordPress.org compliant

