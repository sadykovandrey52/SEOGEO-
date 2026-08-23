<?php
/**
 * Page template.
 *
 * @package Wolfagroles
 */
get_header();
?>
<article class="page-shell site-shell">
    <?php if ( function_exists( 'wg_breadcrumbs' ) ) { wg_breadcrumbs(); } ?>
    <?php while ( have_posts() ) : the_post(); ?>
        <header class="entry-header">
            <p class="eyebrow">ООО «Вольфагролес»</p>
            <h1><?php the_title(); ?></h1>
        </header>
        <div class="entry-content">
            <?php the_content(); ?>
        </div>
    <?php endwhile; ?>
</article>
<?php get_footer(); ?>
