<?php
/** Aurelia block theme. Original source: GPL-2.0-or-later. */
if (!defined('ABSPATH')) { exit; }
function aurelia_resolve($html) {
    $map = array('{{ASSET}}'=>esc_url(get_theme_file_uri('assets')));
    foreach (array('home','about','services','service','booking','team','gallery','reviews','journal','article','contact','faq','policies','thank-you') as $slug) {
        $page = get_page_by_path($slug);
        $map['{{'.strtoupper($slug).'}}'] = esc_url($slug === 'home' ? home_url('/') : ($page ? get_permalink($page) : home_url('/'.$slug.'/')));
    }
    return strtr($html, $map);
}
add_action('after_setup_theme', function () { add_theme_support('editor-styles'); add_editor_style('assets/site.css'); add_theme_support('wp-block-styles'); add_theme_support('responsive-embeds'); });
add_action('wp_enqueue_scripts', function () {
    wp_enqueue_style('aurelia-site', get_theme_file_uri('assets/site.css'), array(), '1.0.0');
    wp_enqueue_script('aurelia-site', get_theme_file_uri('assets/app.js'), array(), '1.0.0', true);
    wp_localize_script('aurelia-site','AURELIA',array('base'=>trailingslashit(home_url('/')),'assets'=>trailingslashit(get_theme_file_uri('assets')),'demo'=>!function_exists('au_settings')||!empty(au_settings()['demo']),'endpoint'=>rest_url('aurelia/v1'),'config'=>function_exists('au_config')?au_config():array(),'articles'=>json_decode(file_get_contents(get_theme_file_path('assets/articles.json')),true))); 
});
add_action('init', function () { register_block_pattern_category('aurelia', array('label'=>'Aurelia page layouts')); });
add_action('wp_head', function () {
    if (!is_singular('page') || defined('WPSEO_VERSION') || defined('RANK_MATH_VERSION')) { return; }
    $description = get_post_meta(get_queried_object_id(), '_aurelia_description', true);
    if ($description) { echo '<meta name="description" content="'.esc_attr($description).'">'."\n"; }
});
function aurelia_seed() {
    if (!current_user_can('manage_options')) { return new WP_Error('forbidden','Administrator access required.'); }
    if (get_option('aurelia_seeded')) { return new WP_Error('already_done','Demo pages already created. Edit existing pages; your edits will not be overwritten.'); }
    $metadata = json_decode(file_get_contents(get_theme_file_path('content/metadata.json')), true);
    foreach ($metadata as $slug=>$meta) { if (get_page_by_path($slug)) { return new WP_Error('conflict','An existing page uses '.$slug.'. Setup stopped without changing existing pages. Use a fresh demo installation or insert the Aurelia patterns manually.'); } }
    $ids=array();
    foreach ($metadata as $slug=>$meta) {
        $id=wp_insert_post(array('post_type'=>'page','post_status'=>'publish','post_title'=>$meta['title'],'post_name'=>$slug,'post_content'=>''),true);
        if (is_wp_error($id)) { return $id; }
        $ids[$slug]=$id;
    }
    foreach ($metadata as $slug=>$meta) {
        wp_update_post(array('ID'=>$ids[$slug],'post_content'=>aurelia_resolve(file_get_contents(get_theme_file_path('content/'.$slug.'.html')))));
        update_post_meta($ids[$slug],'_aurelia_description',$meta['description']);
    }
    update_option('show_on_front','page'); update_option('page_on_front',$ids['home']);
    update_option('blogname','Aurelia'); update_option('blogdescription','Thoughtful aesthetics and wellness in Miami');
    update_option('timezone_string','America/New_York'); update_option('blog_public',0);
    foreach(json_decode(file_get_contents(get_theme_file_path('assets/articles.json')),true) as $a){
      if(!get_page_by_path($a[0],OBJECT,'post')){wp_insert_post(array('post_type'=>'post','post_status'=>'publish','post_name'=>$a[0],'post_title'=>$a[1],'post_content'=>'<!-- wp:paragraph --><p>'.esc_html($a[3]).'</p><!-- /wp:paragraph --><!-- wp:paragraph --><p>'.esc_html($a[4]).'</p><!-- /wp:paragraph -->'));}
    }
    update_option('aurelia_seeded',1); flush_rewrite_rules();
    return $ids;
}
add_action('admin_menu', function () { add_theme_page('Aurelia Setup','Aurelia Setup','manage_options','aurelia-setup','aurelia_setup_screen'); });
function aurelia_setup_screen() {
    if (!current_user_can('manage_options')) { return; }
    if (isset($_POST['aurelia_create'])) { check_admin_referer('aurelia_seed'); $result=aurelia_seed(); echo '<div class="notice notice-'.(is_wp_error($result)?'error':'success').'"><p>'.esc_html(is_wp_error($result)?$result->get_error_message():'Demo pages and journal posts created. Search indexing is disabled for this fictional demo.').'</p></div>'; }
    echo '<div class="wrap"><h1>Welcome to Aurelia</h1><p>Create 14 editable pages and four journal posts on a fresh site. This sets the homepage, site title, Miami timezone and disables search indexing. Existing pages are never overwritten.</p><form method="post">'; wp_nonce_field('aurelia_seed'); submit_button('Create demo content','primary','aurelia_create'); echo '</form><h2>Next steps</h2><ol><li>Activate the Aurelia Studio Booking plugin.</li><li>Pages → edit text and replace stock photographs using the Image block.</li><li>Appearance → Editor → edit the header, navigation, footer and global styles.</li><li>Aurelia Studio → keep demo mode on until the approved recipient and privacy notice are configured.</li><li>Settings → Permalinks → choose Post name. Clear any host cache after changes.</li><li>Before a real launch, replace every placeholder and complete the delivery checklist.</li></ol><p>Demo images are stock inspiration. Do not label them as completed client work. No fabricated testimonials are included.</p></div>';
}
add_action('add_meta_boxes', function () { add_meta_box('aurelia-meta','Aurelia page description','aurelia_meta_box','page','side'); });
function aurelia_meta_box($post) { wp_nonce_field('aurelia_meta','aurelia_meta_nonce'); echo '<p>Basic search description. An SEO plugin, if active, takes precedence.</p><textarea name="aurelia_description" style="width:100%" rows="5" maxlength="180">'.esc_textarea(get_post_meta($post->ID,'_aurelia_description',true)).'</textarea>'; }
add_action('save_post_page', function ($id) { if (defined('DOING_AUTOSAVE')&&DOING_AUTOSAVE) {return;} if (!isset($_POST['aurelia_meta_nonce'])||!wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['aurelia_meta_nonce'])),'aurelia_meta')||!current_user_can('edit_post',$id)) {return;} if (isset($_POST['aurelia_description'])&&is_string($_POST['aurelia_description'])) {update_post_meta($id,'_aurelia_description',sanitize_text_field(wp_unslash($_POST['aurelia_description'])));} });

add_shortcode('aurelia_journal',function(){ $out='<div class="journal-grid">';foreach(get_posts(array('numberposts'=>20,'post_status'=>'publish')) as $p){$out.='<article><a href="'.esc_url(home_url('/article/?id='.$p->post_name)).'"><img src="'.esc_url(get_theme_file_uri('assets/studio.webp')).'" alt="Stock editorial inspiration" loading="lazy"><h2>'.esc_html($p->post_title).'</h2><p>Read the journal ↗</p></a></article>';}return $out.'</div>';});
add_shortcode('aurelia_article',function(){ $slug=sanitize_title(wp_unslash($_GET['id']??''));$p=$slug?get_page_by_path($slug,OBJECT,'post'):null;if(!$p||$p->post_status!=='publish'){return '<p>Select an article from the journal.</p>';}return '<article><h2>'.esc_html($p->post_title).'</h2>'.apply_filters('the_content',$p->post_content).'</article>';});
