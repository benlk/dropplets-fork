## Changelog

### Version 1.0.8
- **NEW**: Infinite scrolling (pagination) of posts.
- **NEW**: Ability to view posts by category.
- **NEW**: New template tag post content.
- **REVISED**: Implementation of PHPass for improved security.
- **REVISED**: Slight style update for the Simple template.
- **FIXED**: Twitter profile images broken as a result of the 1.0 API being retired by Twitter.
- **FIXED**: Fixes an issue where H1 tags were being inserted inside the post title which are already wrapped in H2's.

### Version 1.0.7
- **NEW**: Any plugin added to the "plugins" directory is now auto-included.
- **REVISED**: Post and index caching has been re-implemented with cache invalidation when a new post has been published.
- **FIXED**: You can now install Dropplets within a sub-directory.
- **FIXED**: Post date and title not being set in RSS.
- **FIXED**: Header and footer inject errors on install.
- **FIXED**: Removed duplicate feed links within the "panels" header.

### Version 1.0.6
- **NEW**: Password recovery integration.

### Version 1.0.5
- **NEW**: Open Graph and Twitter Card meta tag support.
- **REVISED**: Improved post cache.
- **REVISED**: Generate .htaccess on install.
- **REVISED**: Added SHA1 password hashing.
- **FIXED**: Header redirects for saving settings, publishing posts and changing templates.
- **FIXED**: Dashboard shouldn't be accessible prior to installation.
