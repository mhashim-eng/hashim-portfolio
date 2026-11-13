<?php
/**
 * Title: Aurelia About
 * Slug: aurelia/about
 * Categories: aurelia
 */
if(!defined("ABSPATH")){exit;}
echo aurelia_resolve(file_get_contents(get_theme_file_path("content/about.html")));