<?php
/**
 * 404 template.
 *
 * @package Wolfagroles
 */
get_header();
?>
<section class="error-page"><div class="site-shell"><p class="error-code">404</p><h1>Страница не найдена</h1><p>Адрес мог измениться или страница ещё не опубликована.</p><div class="hero__actions"><a class="button" href="<?php echo esc_url( home_url( '/' ) ); ?>">На главную</a><a class="button button--secondary" href="<?php echo esc_url( home_url( '/kontakty/' ) ); ?>">Контакты</a></div></div></section>
<?php get_footer(); ?>
