<?php
/**
 * Entity card.
 *
 * @package Wolfagroles
 */
$short_answer = get_post_meta( get_the_ID(), 'wg_short_answer', true );
?>
<article <?php post_class( 'entity-card' ); ?>>
    <a class="entity-card__link" href="<?php the_permalink(); ?>">
        <span class="entity-card__type"><?php echo esc_html( get_post_type_object( get_post_type() )->labels->singular_name ); ?></span>
        <h2><?php the_title(); ?></h2>
        <p><?php echo esc_html( $short_answer ? $short_answer : wp_trim_words( get_the_excerpt(), 22 ) ); ?></p>
        <span class="entity-card__more">Подробнее <span aria-hidden="true">→</span></span>
    </a>
</article>
