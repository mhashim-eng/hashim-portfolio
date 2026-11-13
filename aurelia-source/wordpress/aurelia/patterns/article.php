<?php
/**
 * Title: Aurelia Journal Article
 * Slug: aurelia/article
 * Categories: aurelia
 */
if(!defined("ABSPATH")){exit;}
echo aurelia_resolve(file_get_contents(get_theme_file_path("content/article.html")));