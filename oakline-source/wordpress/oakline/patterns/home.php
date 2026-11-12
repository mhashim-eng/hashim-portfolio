<?php
/**
 * Title: Oakline Home layout
 * Slug: oakline/home
 * Categories: oakline
 * Inserter: true
 */
if (!defined("ABSPATH")) { exit; }
echo oakline_resolve(file_get_contents(get_theme_file_path("content/home.html")));
