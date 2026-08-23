<?php
/**
 * Entity/article template.
 *
 * @package Wolfagroles
 */
get_header();
?>
<?php while ( have_posts() ) : the_post(); ?>
<article <?php post_class( 'entity-page' ); ?>>
    <header class="entity-hero">
        <div class="site-shell">
            <?php if ( function_exists( 'wg_breadcrumbs' ) ) { wg_breadcrumbs(); } ?>
            <p class="eyebrow"><?php echo esc_html( get_post_type_object( get_post_type() )->labels->singular_name ); ?></p>
            <h1><?php the_title(); ?></h1>
            <?php $short_answer = get_post_meta( get_the_ID(), 'wg_short_answer', true ); ?>
            <?php if ( $short_answer ) : ?><p class="entity-hero__lead"><?php echo esc_html( $short_answer ); ?></p><?php endif; ?>
            <div class="hero__actions">
                <a class="button" href="#quote">Запросить расчёт</a>
                <a class="button button--secondary" data-analytics-event="lead_phone_click" href="<?php echo esc_url( wolfagroles_phone_href() ); ?>"><?php echo esc_html( wolfagroles_setting( 'phone', '+7 495 221-83-13' ) ); ?></a>
            </div>
        </div>
    </header>
    <div class="site-shell entity-layout">
        <div class="entry-content">
            <?php the_content(); ?>
            <?php if ( function_exists( 'wg_render_specs' ) ) { wg_render_specs( get_the_ID() ); } ?>
            <?php if ( function_exists( 'wg_render_entity_faq' ) ) { wg_render_entity_faq( get_the_ID() ); } ?>
        </div>
        <aside class="entity-sidebar">
            <div class="sidebar-card">
                <h2>Для расчёта</h2>
                <ul>
                    <li>чертёж или спецификация;</li>
                    <li>материал;</li>
                    <li>количество;</li>
                    <li>желаемый срок;</li>
                    <li>особые требования.</li>
                </ul>
                <a class="button button--full" href="#quote">Передать задачу</a>
            </div>
        </aside>
    </div>
    <section class="section section--dark quote-section" id="quote">
        <div class="site-shell quote-layout">
            <div><p class="eyebrow">Расчёт</p><h2>Приложите исходные файлы</h2><p>Форма принимает несколько файлов и фиксирует страницу, с которой отправлена заявка.</p></div>
            <div class="quote-card"><?php echo do_shortcode( '[wolfagroles_quote_form]' ); ?></div>
        </div>
    </section>
</article>
<?php endwhile; ?>
<?php get_footer(); ?>
