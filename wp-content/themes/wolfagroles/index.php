<?php
/**
 * Posts index.
 *
 * @package Wolfagroles
 */
get_header();
?>
<section class="archive-hero"><div class="site-shell"><p class="eyebrow">Экспертные материалы</p><h1><?php single_post_title(); ?></h1><p class="archive-hero__lead">Статьи публикуются с автором, датой актуализации и источниками.</p></div></section>
<section class="section"><div class="site-shell">
<?php if ( have_posts() ) : ?><div class="card-grid">
<?php while ( have_posts() ) : the_post(); get_template_part( 'template-parts/card', 'entity' ); endwhile; ?>
</div><?php the_posts_pagination(); else : ?><div class="empty-state"><p class="empty-state__title">Статьи готовятся</p><p>Публикации появятся после экспертной и фактической проверки.</p></div><?php endif; ?>
</div></section>
<?php get_footer(); ?>
