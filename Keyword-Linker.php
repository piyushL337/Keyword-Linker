<?php
/*
Plugin Name: Keyword Linker
Plugin URI: https://github.com/piyushL337/
Description: Advanced plugin that stores tags as keywords and creates intelligent internal links in your content.
Version: 2.0
Author: PIYUSH JSOHI
Author URI: https://github.com/piyushL337/
*/

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Default settings for the plugin
 */
function keyword_linker_default_options() {
    return array(
        'max_links_per_keyword' => 3,
        'max_links_per_post' => 10,
        'case_sensitive' => false,
        'open_new_tab' => false,
        'add_nofollow' => false,
        'prevent_self_linking' => true,
        'link_css_class' => 'keyword-link',
        'enabled_post_types' => array('post'),
        'skip_headings' => true,
        'custom_keywords' => array()
    );
}

/**
 * Get plugin options with defaults
 */
function keyword_linker_get_options() {
    $options = get_option('keyword_linker_options', array());
    return wp_parse_args($options, keyword_linker_default_options());
}

/**
 * Admin menu
 */
function keyword_linker_admin_menu() {
    add_options_page(
        'Keyword Linker Settings',
        'Keyword Linker',
        'manage_options',
        'keyword-linker',
        'keyword_linker_settings_page'
    );
}
add_action('admin_menu', 'keyword_linker_admin_menu');

/**
 * Register settings
 */
function keyword_linker_register_settings() {
    register_setting('keyword_linker_options', 'keyword_linker_options', 'keyword_linker_validate_options');
}
add_action('admin_init', 'keyword_linker_register_settings');

/**
 * Validate options
 */
function keyword_linker_validate_options($input) {
    $validated = array();
    
    $validated['max_links_per_keyword'] = absint($input['max_links_per_keyword']);
    $validated['max_links_per_post'] = absint($input['max_links_per_post']);
    $validated['case_sensitive'] = !empty($input['case_sensitive']);
    $validated['open_new_tab'] = !empty($input['open_new_tab']);
    $validated['add_nofollow'] = !empty($input['add_nofollow']);
    $validated['prevent_self_linking'] = !empty($input['prevent_self_linking']);
    $validated['skip_headings'] = !empty($input['skip_headings']);
    $validated['link_css_class'] = sanitize_text_field($input['link_css_class']);
    $validated['enabled_post_types'] = !empty($input['enabled_post_types']) ? $input['enabled_post_types'] : array('post');
    
    // Parse custom keywords
    $custom_keywords = array();
    if (!empty($input['custom_keywords_text'])) {
        $lines = explode("\n", $input['custom_keywords_text']);
        foreach ($lines as $line) {
            $line = trim($line);
            if (empty($line)) continue;
            
            $parts = explode('|', $line);
            if (count($parts) >= 2) {
                $keyword = trim($parts[0]);
                $url = trim($parts[1]);
                if (!empty($keyword) && !empty($url)) {
                    $custom_keywords[] = array(
                        'keyword' => $keyword,
                        'url' => esc_url_raw($url)
                    );
                }
            }
        }
    }
    $validated['custom_keywords'] = $custom_keywords;
    
    return $validated;
}

/**
 * Settings page
 */
function keyword_linker_settings_page() {
    if (!current_user_can('manage_options')) {
        return;
    }
    
    $options = keyword_linker_get_options();
    
    // Convert custom keywords to text format for display
    $custom_keywords_text = '';
    if (!empty($options['custom_keywords'])) {
        foreach ($options['custom_keywords'] as $item) {
            $custom_keywords_text .= $item['keyword'] . ' | ' . $item['url'] . "\n";
        }
    }
    
    ?>
    <div class="wrap">
        <h1>Keyword Linker Settings</h1>
        <form method="post" action="options.php">
            <?php settings_fields('keyword_linker_options'); ?>
            
            <h2>Basic Settings</h2>
            <table class="form-table">
                <tr>
                    <th scope="row">Max Links Per Keyword</th>
                    <td>
                        <input type="number" name="keyword_linker_options[max_links_per_keyword]" 
                               value="<?php echo esc_attr($options['max_links_per_keyword']); ?>" 
                               min="1" max="100" />
                        <p class="description">Maximum number of times to link each keyword in a post.</p>
                    </td>
                </tr>
                <tr>
                    <th scope="row">Max Links Per Post</th>
                    <td>
                        <input type="number" name="keyword_linker_options[max_links_per_post]" 
                               value="<?php echo esc_attr($options['max_links_per_post']); ?>" 
                               min="1" max="100" />
                        <p class="description">Maximum total number of keyword links to add per post.</p>
                    </td>
                </tr>
                <tr>
                    <th scope="row">Case Sensitive</th>
                    <td>
                        <label>
                            <input type="checkbox" name="keyword_linker_options[case_sensitive]" 
                                   value="1" <?php checked($options['case_sensitive'], true); ?> />
                            Match keywords with case sensitivity
                        </label>
                    </td>
                </tr>
                <tr>
                    <th scope="row">Prevent Self Linking</th>
                    <td>
                        <label>
                            <input type="checkbox" name="keyword_linker_options[prevent_self_linking]" 
                                   value="1" <?php checked($options['prevent_self_linking'], true); ?> />
                            Prevent posts from linking to themselves
                        </label>
                    </td>
                </tr>
                <tr>
                    <th scope="row">Skip Headings</th>
                    <td>
                        <label>
                            <input type="checkbox" name="keyword_linker_options[skip_headings]" 
                                   value="1" <?php checked($options['skip_headings'], true); ?> />
                            Don't add links inside heading tags (h1-h6)
                        </label>
                    </td>
                </tr>
            </table>
            
            <h2>Link Attributes</h2>
            <table class="form-table">
                <tr>
                    <th scope="row">Open in New Tab</th>
                    <td>
                        <label>
                            <input type="checkbox" name="keyword_linker_options[open_new_tab]" 
                                   value="1" <?php checked($options['open_new_tab'], true); ?> />
                            Add target="_blank" to links
                        </label>
                    </td>
                </tr>
                <tr>
                    <th scope="row">Add Nofollow</th>
                    <td>
                        <label>
                            <input type="checkbox" name="keyword_linker_options[add_nofollow]" 
                                   value="1" <?php checked($options['add_nofollow'], true); ?> />
                            Add rel="nofollow" to links
                        </label>
                    </td>
                </tr>
                <tr>
                    <th scope="row">CSS Class</th>
                    <td>
                        <input type="text" name="keyword_linker_options[link_css_class]" 
                               value="<?php echo esc_attr($options['link_css_class']); ?>" 
                               class="regular-text" />
                        <p class="description">CSS class to add to keyword links.</p>
                    </td>
                </tr>
            </table>
            
            <h2>Post Types</h2>
            <table class="form-table">
                <tr>
                    <th scope="row">Enable For Post Types</th>
                    <td>
                        <?php
                        $post_types = get_post_types(array('public' => true), 'objects');
                        foreach ($post_types as $post_type) {
                            if ($post_type->name === 'attachment') continue;
                            $checked = in_array($post_type->name, $options['enabled_post_types']);
                            ?>
                            <label style="display: block;">
                                <input type="checkbox" 
                                       name="keyword_linker_options[enabled_post_types][]" 
                                       value="<?php echo esc_attr($post_type->name); ?>" 
                                       <?php checked($checked, true); ?> />
                                <?php echo esc_html($post_type->label); ?>
                            </label>
                            <?php
                        }
                        ?>
                        <p class="description">Select which post types should have keyword linking enabled.</p>
                    </td>
                </tr>
            </table>
            
            <h2>Custom Keywords</h2>
            <table class="form-table">
                <tr>
                    <th scope="row">Custom Keyword/URL Pairs</th>
                    <td>
                        <textarea name="keyword_linker_options[custom_keywords_text]" 
                                  rows="10" 
                                  class="large-text code"><?php echo esc_textarea($custom_keywords_text); ?></textarea>
                        <p class="description">
                            Add custom keyword and URL pairs. Format: <code>keyword | URL</code> (one per line)<br>
                            Example: <code>WordPress | https://wordpress.org</code>
                        </p>
                    </td>
                </tr>
            </table>
            
            <?php submit_button(); ?>
        </form>
    </div>
    <?php
}

/**
 * Save post keywords (from tags) - improved to prevent duplicates
 */
function keyword_linker_save_post($post_id) {
    // Check if this is an autosave
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }
    
    // Check user permissions
    if (!current_user_can('edit_post', $post_id)) {
        return;
    }
    
    // Check if post type is enabled
    $options = keyword_linker_get_options();
    $post_type = get_post_type($post_id);
    if (!in_array($post_type, $options['enabled_post_types'])) {
        return;
    }
    
    // Delete existing keyword metadata to prevent duplicates
    delete_post_meta($post_id, 'keyword');
    delete_post_meta($post_id, 'keyword_link');
    
    // Get the tags for the post
    $tags = get_the_tags($post_id);
    
    if (!$tags || is_wp_error($tags)) {
        return;
    }
    
    // Loop through the tags and add them as keywords
    foreach ($tags as $tag) {
        add_post_meta($post_id, 'keyword', sanitize_text_field($tag->name));
        add_post_meta($post_id, 'keyword_link', get_permalink($post_id));
    }
}
add_action('save_post', 'keyword_linker_save_post');

/**
 * Process content and add keyword links - advanced version
 */
function keyword_linker_content($content) {
    global $post;
    
    if (!$post) {
        return $content;
    }
    
    $options = keyword_linker_get_options();
    
    // Check if post type is enabled
    if (!in_array(get_post_type($post), $options['enabled_post_types'])) {
        return $content;
    }
    
    // Skip if content is empty or too short
    if (empty($content) || strlen($content) < 50) {
        return $content;
    }
    
    // Collect all keywords and their links
    $keywords_data = array();
    
    // Get automatic keywords from all posts (excluding current post if prevent_self_linking is enabled)
    $args = array(
        'post_type' => $options['enabled_post_types'],
        'posts_per_page' => -1,
        'post_status' => 'publish',
        'fields' => 'ids'
    );
    
    if ($options['prevent_self_linking']) {
        $args['post__not_in'] = array($post->ID);
    }
    
    $posts = get_posts($args);
    
    foreach ($posts as $post_id) {
        $keywords = get_post_meta($post_id, 'keyword', false);
        $links = get_post_meta($post_id, 'keyword_link', false);
        
        if ($keywords && $links) {
            foreach ($keywords as $i => $keyword) {
                if (isset($links[$i]) && !empty($keyword)) {
                    $keywords_data[$keyword] = $links[$i];
                }
            }
        }
    }
    
    // Add custom keywords
    if (!empty($options['custom_keywords'])) {
        foreach ($options['custom_keywords'] as $item) {
            $keywords_data[$item['keyword']] = $item['url'];
        }
    }
    
    // If no keywords found, return original content
    if (empty($keywords_data)) {
        return $content;
    }
    
    // Sort keywords by length (longest first) to match longer phrases before shorter ones
    $keyword_lengths = array_map('strlen', array_keys($keywords_data));
    array_multisort($keyword_lengths, SORT_DESC, $keywords_data);
    
    // Track replacements
    $keyword_count = array();
    $total_replacements = 0;
    
    // Build link attributes
    $link_attrs = array();
    if (!empty($options['link_css_class'])) {
        $link_attrs[] = 'class="' . esc_attr($options['link_css_class']) . '"';
    }
    if ($options['open_new_tab']) {
        $link_attrs[] = 'target="_blank"';
    }
    if ($options['add_nofollow']) {
        $link_attrs[] = 'rel="nofollow"';
    }
    $link_attr_string = !empty($link_attrs) ? ' ' . implode(' ', $link_attrs) : '';
    
    // If skip_headings is enabled, protect heading content
    $heading_placeholders = array();
    if ($options['skip_headings']) {
        $content = preg_replace_callback(
            '/<h[1-6][^>]*>.*?<\/h[1-6]>/is',
            function($matches) use (&$heading_placeholders) {
                $placeholder = '{{HEADING_' . count($heading_placeholders) . '}}';
                $heading_placeholders[$placeholder] = $matches[0];
                return $placeholder;
            },
            $content
        );
    }
    
    // Also protect existing links from being modified
    $link_placeholders = array();
    $content = preg_replace_callback(
        '/<a\s[^>]*>.*?<\/a>/is',
        function($matches) use (&$link_placeholders) {
            $placeholder = '{{LINK_' . count($link_placeholders) . '}}';
            $link_placeholders[$placeholder] = $matches[0];
            return $placeholder;
        },
        $content
    );
    
    // Process each keyword
    foreach ($keywords_data as $keyword => $link) {
        // Check if we've reached max total links
        if ($total_replacements >= $options['max_links_per_post']) {
            break;
        }
        
        // Initialize counter for this keyword
        if (!isset($keyword_count[$keyword])) {
            $keyword_count[$keyword] = 0;
        }
        
        // Check if we've reached max for this keyword
        if ($keyword_count[$keyword] >= $options['max_links_per_keyword']) {
            continue;
        }
        
        // Build regex pattern
        $pattern_keyword = preg_quote($keyword, '/');
        $flags = 'u' . ($options['case_sensitive'] ? '' : 'i');
        $pattern = '/\b(' . $pattern_keyword . ')\b/' . $flags;
        
        // Calculate how many we can replace
        $remaining_for_keyword = $options['max_links_per_keyword'] - $keyword_count[$keyword];
        $remaining_for_post = $options['max_links_per_post'] - $total_replacements;
        $max_replacements = min($remaining_for_keyword, $remaining_for_post);
        
        // Replace with limit
        $replacement = '<a href="' . esc_url($link) . '"' . $link_attr_string . '>$1</a>';
        $replaced_count = 0;
        
        $content = preg_replace_callback(
            $pattern,
            function($matches) use ($replacement, &$replaced_count, $max_replacements) {
                if ($replaced_count < $max_replacements) {
                    $replaced_count++;
                    return str_replace('$1', $matches[1], $replacement);
                }
                return $matches[0];
            },
            $content
        );
        
        $keyword_count[$keyword] += $replaced_count;
        $total_replacements += $replaced_count;
    }
    
    // Restore protected links
    foreach ($link_placeholders as $placeholder => $original) {
        $content = str_replace($placeholder, $original, $content);
    }
    
    // Restore protected headings
    if ($options['skip_headings']) {
        foreach ($heading_placeholders as $placeholder => $original) {
            $content = str_replace($placeholder, $original, $content);
        }
    }
    
    return $content;
}
add_filter('the_content', 'keyword_linker_content', 10);
