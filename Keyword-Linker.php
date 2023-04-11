<?php
/*
Plugin Name: Keyword Linker
Plugin URI: https://github.com/piyushL337/
Description: This plugin stores tags as keywords and links them to articles.
Version: 1.0
Author: PIYUSH JSOHI
Author URI: https://github.com/piyushL337/
*/
function keyword_linker_save_post($post_id) {
  // Get the tags for the post
  $tags = get_the_tags($post_id);

  // Loop through the tags and add them as keywords
  foreach ($tags as $tag) {
    add_post_meta($post_id, 'keyword', $tag->name);
    add_post_meta($post_id, 'keyword_link', get_permalink($post_id));
  }
}
add_action('save_post', 'keyword_linker_save_post');

function keyword_linker_content($content) {
  global $post;

  // Get the keywords and links for the post
  $post_id = $post->ID;
  $keywords = get_post_meta($post_id, 'keyword');
  $links = get_post_meta($post_id, 'keyword_link');

  // Loop through the keywords and create a link for each one
  foreach ($keywords as $i => $keyword) {
    $link = $links[$i];
    $pattern = '/\b' . preg_quote($keyword, '/') . '\b/';
    $replacement = '<a href="' . $link . '">' . $keyword . '</a>';
    $content = preg_replace($pattern, $replacement, $content);
  }

  return $content;
}
add_filter('the_content', 'keyword_linker_content');
