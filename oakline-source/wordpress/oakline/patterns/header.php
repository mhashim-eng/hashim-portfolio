<?php
/**
 * Title: Oakline header
 * Slug: oakline/header
 * Categories: oakline
 * Block Types: core/template-part/header
 */
if (!defined('ABSPATH')) { exit; }
?>
<!-- wp:group {"className":"announcement"} --><div class="wp-block-group announcement"><!-- wp:paragraph --><p>TRANSFORM YOUR HOME WITH CONFIDENCE · AUSTIN, TEXAS</p><!-- /wp:paragraph --></div><!-- /wp:group -->
<!-- wp:group {"className":"site-header wp-header"} --><div class="wp-block-group site-header wp-header"><!-- wp:group {"className":"wrap nav-wrap"} --><div class="wp-block-group wrap nav-wrap"><!-- wp:group --><div class="wp-block-group"><!-- wp:site-title {"level":0} /--><!-- wp:paragraph {"className":"brand-subtitle"} --><p class="brand-subtitle">RENOVATIONS · AUSTIN, TEXAS</p><!-- /wp:paragraph --></div><!-- /wp:group -->
<!-- wp:navigation {"overlayMenu":"mobile","layout":{"type":"flex","justifyContent":"right"}} -->
<?php foreach (array('home'=>'Home','about'=>'About','services'=>'Services','projects'=>'Projects','contact'=>'Contact') as $slug=>$title) { echo '<!-- wp:navigation-link '.wp_json_encode(array('label'=>$title,'url'=>html_entity_decode(oakline_resolve('{{'.strtoupper($slug).'}}')),'kind'=>'custom')).' /-->'; } ?>
<!-- /wp:navigation -->
<!-- wp:buttons --><div class="wp-block-buttons"><!-- wp:button {"className":"is-style-outline header-cta"} --><div class="wp-block-button is-style-outline header-cta"><a class="wp-block-button__link wp-element-button" href="<?php echo oakline_resolve('{{CONTACT}}'); ?>">Get a Free Estimate ↗</a></div><!-- /wp:button --></div><!-- /wp:buttons -->
</div><!-- /wp:group --></div><!-- /wp:group -->
