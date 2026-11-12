<?php
/**
 * Title: Oakline footer
 * Slug: oakline/footer
 * Categories: oakline
 * Block Types: core/template-part/footer
 */
if (!defined('ABSPATH')) { exit; }
?>
<!-- wp:group {"className":"site-footer wp-footer"} --><div class="wp-block-group site-footer wp-footer"><!-- wp:group {"className":"wrap footer-grid"} --><div class="wp-block-group wrap footer-grid"><!-- wp:group --><div class="wp-block-group"><!-- wp:site-title {"level":0} /--><!-- wp:paragraph --><p>Thoughtful renovations.<br>A home that feels more like you.</p><!-- /wp:paragraph --></div><!-- /wp:group --><!-- wp:group --><div class="wp-block-group"><!-- wp:heading --><h2 class="wp-block-heading">Explore</h2><!-- /wp:heading -->
<?php foreach (array('home'=>'Home','about'=>'About','services'=>'Services','projects'=>'Projects','contact'=>'Contact') as $slug=>$title) { echo '<!-- wp:paragraph --><p><a href="'.oakline_resolve('{{'.strtoupper($slug).'}}').'">'.esc_html($title).'</a></p><!-- /wp:paragraph -->'; } ?>
</div><!-- /wp:group --><!-- wp:group --><div class="wp-block-group"><!-- wp:heading --><h2 class="wp-block-heading">Visit & connect</h2><!-- /wp:heading --><!-- wp:paragraph --><p>Austin, Texas<br>Office hours: awaiting approval<br>Phone & email: awaiting approval</p><!-- /wp:paragraph --><!-- wp:paragraph --><p>Social profiles awaiting client approval.</p><!-- /wp:paragraph --></div><!-- /wp:group --></div><!-- /wp:group --><!-- wp:group {"className":"wrap footer-bottom"} --><div class="wp-block-group wrap footer-bottom"><!-- wp:paragraph --><p>Oakline Renovations LLC · Fictional demo</p><!-- /wp:paragraph --><!-- wp:paragraph --><p>Website by Muhammad Hashim</p><!-- /wp:paragraph --></div><!-- /wp:group --><!-- wp:paragraph {"className":"wrap disclaimer"} --><p class="wrap disclaimer">Stock photography is inspiration, not completed Oakline work. Replace demo material before a real launch.</p><!-- /wp:paragraph --></div><!-- /wp:group -->
