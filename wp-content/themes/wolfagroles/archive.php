<?php
/**
 * Entity archives.
 *
 * @package Wolfagroles
 */
get_header();
$post_type = get_query_var( 'post_type' );
if ( is_array( $post_type ) ) {
    $post_type = reset( $post_type );
}
?>
<section class="archive-hero">
    <div class="site-shell">
        <?php if ( function_exists( 'wg_breadcrumbs' ) ) { wg_breadcrumbs(); } ?>
        <p class="eyebrow">Каталог подтверждённых данных</p>
        <h1><?php post_type_archive_title(); ?></h1>
        <p class="archive-hero__lead"><?php echo esc_html( wolfagroles_archive_description( $post_type ) ); ?></p>
    </div>
</section>
<section class="section">
    <div class="site-shell">
        <?php if ( have_posts() ) : ?>
            <div class="card-grid">
                <?php while ( have_posts() ) : the_post(); ?>
                    <?php get_template_part( 'template-parts/card', 'entity' ); ?>
                <?php endwhile; ?>
            </div>
            <?php the_posts_pagination(); ?>
        <?php else : ?>
            <div class="empty-state">
                <p class="empty-state__title">Публичные материалы ещё не подтверждены</p>
                <p>Раздел не заполняется шаблонными страницами. Направьте конкретную задачу — возможность выполнения проверят по исходным данным.</p>
                <a class="button" href="<?php echo esc_url( home_url( '/#quote' ) ); ?>">Запросить расчёт</a>
            </div>
        <?php endif; ?>
    </div>
</section>
<?php get_footer(); ?>
