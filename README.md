# Keyword Linker - Advanced Internal Linking Plugin

The Keyword Linker WordPress plugin is an advanced and powerful tool that intelligently links keywords in your articles to other articles on your website. With extensive customization options and smart linking algorithms, it helps improve SEO, user experience, and content discoverability.

## Features

### 🎯 Core Functionality
- **Automatic Keyword Detection**: Automatically uses post tags as keywords
- **Custom Keywords**: Add manual keyword/URL pairs for external or specific internal links
- **Smart Linking**: Intelligently places links throughout your content
- **Self-Link Prevention**: Prevents posts from linking to themselves

### ⚙️ Advanced Options

#### Link Control
- **Max Links Per Keyword**: Limit how many times each keyword is linked (default: 3)
- **Max Links Per Post**: Set maximum total links per post (default: 10)
- **Case Sensitivity**: Choose between case-sensitive or case-insensitive matching
- **Skip Headings**: Option to exclude keyword linking in heading tags (h1-h6)

#### Link Attributes
- **Open in New Tab**: Add `target="_blank"` to links
- **Nofollow Support**: Add `rel="nofollow"` attribute to links
- **Custom CSS Classes**: Add custom CSS classes to keyword links for styling

#### Content Protection
- **Existing Link Protection**: Never modifies existing links in content
- **Heading Protection**: Optional exclusion of heading tags from keyword linking
- **Smart Pattern Matching**: Uses word boundaries to avoid partial matches

#### Post Type Control
- **Multi-Post Type Support**: Enable for posts, pages, or custom post types
- **Selective Activation**: Choose exactly which post types should have linking

### 📋 How to Use

1. **Install & Activate**: Upload and activate the plugin in WordPress
2. **Configure Settings**: Go to Settings → Keyword Linker
3. **Adjust Options**: 
   - Set your preferred max links per keyword/post
   - Enable/disable case sensitivity
   - Configure link attributes (new tab, nofollow, CSS class)
   - Select which post types to process
4. **Add Custom Keywords** (optional):
   - Format: `keyword | URL` (one per line)
   - Example: `WordPress | https://wordpress.org`
5. **Tag Your Posts**: The plugin automatically uses post tags as keywords
6. **Publish**: Keywords will be automatically linked in your content!

## Settings Overview

### Basic Settings
- **Max Links Per Keyword**: Controls link density per keyword (1-100)
- **Max Links Per Post**: Prevents over-optimization (1-100)
- **Case Sensitive**: Match keywords exactly or ignore case
- **Prevent Self Linking**: Stops posts linking to themselves (recommended)
- **Skip Headings**: Don't add links in h1-h6 tags

### Link Attributes
- **Open in New Tab**: External link behavior
- **Add Nofollow**: SEO control for keyword links
- **CSS Class**: Custom styling for your keyword links

### Post Types
Select which post types (Posts, Pages, Custom) should have keyword linking enabled.

### Custom Keywords
Add manual keyword-to-URL mappings for:
- External resources
- Specific internal pages
- Priority keywords you want to control

## Examples

### Custom Keywords Format
```
WordPress | https://wordpress.org
SEO Guide | https://yoursite.com/seo-guide
Marketing Tips | https://yoursite.com/marketing
```

### Use Cases
1. **Internal SEO**: Link related posts automatically
2. **Content Hub**: Create topic clusters with automatic linking
3. **Resource Pages**: Link to important external resources
4. **Product Links**: Link product mentions to product pages
5. **Authority Building**: Link to relevant high-authority content

## Technical Details

- **Version**: 2.0
- **Requires**: WordPress 5.0+
- **PHP**: 7.0+
- **Smart Matching**: Uses regex with word boundaries
- **Performance**: Efficient with caching-friendly design
- **Security**: Properly sanitized and escaped output

## Changelog

### Version 2.0
- ✨ Complete rewrite with advanced options
- ✨ Added comprehensive admin settings page
- ✨ Custom keyword/URL pair support
- ✨ Configurable link limits (per keyword and per post)
- ✨ Case sensitivity options
- ✨ Link attribute controls (target, nofollow, CSS class)
- ✨ Post type filtering
- ✨ Heading protection option
- ✨ Existing link protection
- ✨ Self-linking prevention
- ✨ Improved performance with smart algorithms
- 🐛 Fixed duplicate metadata on post save
- 🐛 Fixed over-linking issues
- 🔒 Enhanced security with proper sanitization

### Version 1.0
- Initial release with basic functionality

## Support

For issues, feature requests, or contributions, please visit:
https://github.com/piyushL337/Keyword-Linker

---

**Note**: This plugin helps with internal linking and SEO, but use it responsibly. Over-optimization can negatively impact user experience and SEO. The default settings are conservative and recommended for most use cases.
