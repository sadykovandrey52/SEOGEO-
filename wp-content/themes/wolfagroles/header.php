<?php
/**
 * Site header.
 *
 * @package Wolfagroles
 */
?><!doctype html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo( 'charset' ); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<a class="skip-link" href="#main-content"><?php esc_html_e( 'Перейти к содержанию', 'wolfagroles' ); ?></a>
<header class="site-header" data-site-header>
    <div class="header-strip">
        <div class="site-shell header-strip__inner">
            <span><?php esc_html_e( 'Приём B2B-запросов: Москва и Московская область', 'wolfagroles' ); ?></span>
            <a data-analytics-event="lead_email_click" href="mailto:<?php echo esc_attr( wolfagroles_setting( 'email', 'savkinayus@wolfagro.ru' ) ); ?>"><?php echo esc_html( wolfagroles_setting( 'email', 'savkinayus@wolfagro.ru' ) ); ?></a>
        </div>
    </div>
    <div class="site-shell header-main">
        <div class="site-brand">
            <?php if ( has_custom_logo() ) : ?>
                <?php the_custom_logo(); ?>
            <?php else : ?>
                <a class="site-brand__name" href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home">
                    <span>ВОЛЬФАГРОЛЕС</span>
                    <small>производственные задачи по чертежам</small>
                </a>
            <?php endif; ?>
        </div>
        <button class="menu-toggle" type="button" aria-expanded="false" aria-controls="primary-menu" data-menu-toggle>
            <span class="menu-toggle__label"><?php esc_html_e( 'Меню', 'wolfagroles' ); ?></span>
            <span aria-hidden="true" class="menu-toggle__icon"></span>
        </button>
        <nav class="primary-nav" id="primary-menu" aria-label="<?php esc_attr_e( 'Основная навигация', 'wolfagroles' ); ?>" data-menu>
            <?php
            wp_nav_menu(
                array(
                    'theme_location' => 'primary',
                    'container'      => false,
                    'fallback_cb'    => 'wp_page_menu',
                    'menu_class'     => 'primary-nav__list',
                    'depth'          => 2,
                )
            );
            ?>
        </nav>
        <div class="header-actions">
            <a class="header-phone" data-analytics-event="lead_phone_click" href="<?php echo esc_url( wolfagroles_phone_href() ); ?>"><?php echo esc_html( wolfagroles_setting( 'phone', '+7 495 221-83-13' ) ); ?></a>
            <a class="button button--small" href="<?php echo esc_url( home_url( '/#quote' ) ); ?>"><?php esc_html_e( 'Запросить расчёт', 'wolfagroles' ); ?></a>
        </div>
    </div>
</header>
<main id="main-content">
