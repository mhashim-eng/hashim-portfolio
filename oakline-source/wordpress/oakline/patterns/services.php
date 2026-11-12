<?php
/**
 * Title: Oakline Services layout
 * Slug: oakline/services
 * Categories: oakline
 * Inserter: true
 */
if (!defined("ABSPATH")) { exit; }
echo oakline_resolve(file_get_contents(get_theme_file_path("content/services.html")));
