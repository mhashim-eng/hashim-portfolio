<?php
/** Oakline block theme. Original source: GPL-2.0-or-later. */
if (!defined('ABSPATH')) { exit; }
function oakline_resolve($html) {
    $map = array('{{ASSET}}'=>esc_url(get_theme_file_uri('assets')));
    foreach (array('home','about','services','projects','contact') as $slug) {
        $page = get_page_by_path($slug);
        $map['{{'.strtoupper($slug).'}}'] = esc_url($slug === 'home' ? home_url('/') : ($page ? get_permalink($page) : home_url('/'.$slug.'/')));
    }
    return strtr($html, $map);
}
add_action('after_setup_theme', function () { add_theme_support('editor-styles'); add_editor_style('assets/site.css'); add_theme_support('wp-block-styles'); add_theme_support('responsive-embeds'); });
add_action('wp_enqueue_scripts', function () {
    wp_enqueue_style('oakline-site', get_theme_file_uri('assets/site.css'), array(), '1.0.0');
    wp_enqueue_script('oakline-site', get_theme_file_uri('assets/site.js'), array(), '1.0.0', true);
});
add_action('init', function () { register_block_pattern_category('oakline', array('label'=>'Oakline page layouts')); });
add_action('wp_head', function () {
    if (!is_singular('page') || defined('WPSEO_VERSION') || defined('RANK_MATH_VERSION')) { return; }
    $description = get_post_meta(get_queried_object_id(), '_oakline_description', true);
    if ($description) { echo '<meta name="description" content="'.esc_attr($description).'">'."\n"; }
});
function oakline_seed() {
    if (!current_user_can('manage_options')) { return new WP_Error('forbidden','Administrator access required.'); }
    if (get_option('oakline_seeded')) { return new WP_Error('already_done','Demo pages already created. Edit existing pages; your edits will not be overwritten.'); }
    $metadata = json_decode(file_get_contents(get_theme_file_path('content/metadata.json')), true);
    foreach ($metadata as $slug=>$meta) { if (get_page_by_path($slug)) { return new WP_Error('conflict','An existing page uses '.$slug.'. Setup stopped without changing existing pages. Use a fresh demo installation or insert the Oakline patterns manually.'); } }
    $ids=array();
    foreach ($metadata as $slug=>$meta) {
        $id=wp_insert_post(array('post_type'=>'page','post_status'=>'publish','post_title'=>$meta['title'],'post_name'=>$slug,'post_content'=>''),true);
        if (is_wp_error($id)) { return $id; }
        $ids[$slug]=$id;
    }
    foreach ($metadata as $slug=>$meta) {
        wp_update_post(array('ID'=>$ids[$slug],'post_content'=>oakline_resolve(file_get_contents(get_theme_file_path('content/'.$slug.'.html')))));
        update_post_meta($ids[$slug],'_oakline_description',$meta['description']);
    }
    update_option('show_on_front','page'); update_option('page_on_front',$ids['home']);
    update_option('blogname','Oakline Renovations'); update_option('blogdescription','Thoughtful home renovations in Austin, Texas');
    update_option('timezone_string','America/Chicago'); update_option('blog_public',0);
    update_option('oakline_seeded',1); flush_rewrite_rules();
    return $ids;
}
add_action('admin_menu', function () { add_theme_page('Oakline Setup','Oakline Setup','manage_options','oakline-setup','oakline_setup_screen'); });
function oakline_setup_screen() {
    if (!current_user_can('manage_options')) { return; }
    if (isset($_POST['oakline_create'])) { check_admin_referer('oakline_seed'); $result=oakline_seed(); echo '<div class="notice notice-'.(is_wp_error($result)?'error':'success').'"><p>'.esc_html(is_wp_error($result)?$result->get_error_message():'Five editable demo pages created. Search indexing is disabled for this fictional demo.').'</p></div>'; }
    echo '<div class="wrap"><h1>Welcome to Oakline</h1><p>Create five editable Gutenberg pages on a fresh site. This sets the homepage, site title, Austin timezone and disables search indexing. Existing pages are never overwritten.</p><form method="post">'; wp_nonce_field('oakline_seed'); submit_button('Create five demo pages','primary','oakline_create'); echo '</form><h2>Next steps</h2><ol><li>Activate the Oakline Estimates plugin.</li><li>Pages → edit text and replace stock photographs using the Image block.</li><li>Appearance → Editor → edit the header, navigation, footer and global styles.</li><li>Settings → Oakline Estimates → keep demo mode on until the approved recipient and privacy notice are configured.</li><li>Settings → Permalinks → choose Post name. Clear any host cache after changes.</li><li>Before a real launch, replace every placeholder and complete the delivery checklist.</li></ol><p>Demo images are stock inspiration. Do not label them as completed client work. No fabricated testimonials are included.</p></div>';
}
add_action('add_meta_boxes', function () { add_meta_box('oakline-meta','Oakline page description','oakline_meta_box','page','side'); });
function oakline_meta_box($post) { wp_nonce_field('oakline_meta','oakline_meta_nonce'); echo '<p>Basic search description. An SEO plugin, if active, takes precedence.</p><textarea name="oakline_description" style="width:100%" rows="5" maxlength="180">'.esc_textarea(get_post_meta($post->ID,'_oakline_description',true)).'</textarea>'; }
add_action('save_post_page', function ($id) { if (defined('DOING_AUTOSAVE')&&DOING_AUTOSAVE) {return;} if (!isset($_POST['oakline_meta_nonce'])||!wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['oakline_meta_nonce'])),'oakline_meta')||!current_user_can('edit_post',$id)) {return;} if (isset($_POST['oakline_description'])&&is_string($_POST['oakline_description'])) {update_post_meta($id,'_oakline_description',sanitize_text_field(wp_unslash($_POST['oakline_description'])));} });
