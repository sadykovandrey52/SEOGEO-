<?php
/**
 * Theme bootstrap.
 *
 * @package Wolfagroles
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

define( 'WOLFAGROLES_THEME_VERSION', '1.0.0' );

function wolfagroles_theme_setup() {
    load_theme_textdomain( 'wolfagroles', get_template_directory() . '/languages' );
    add_theme_support( 'title-tag' );
    add_theme_support( 'post-thumbnails' );
    add_theme_support( 'responsive-embeds' );
    add_theme_support( 'align-wide' );
    add_theme_support( 'editor-styles' );
    add_theme_support(
        'html5',
        array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script' )
    );
    add_theme_support(
        'custom-logo',
        array(
            'height'      => 56,
            'width'       => 220,
            'flex-height' => true,
            'flex-width'  => true,
        )
    );
    register_nav_menus(
        array(
            'primary' => __( 'Основное меню', 'wolfagroles' ),
            'footer'  => __( 'Меню в подвале', 'wolfagroles' ),
        )
    );
}
add_action( 'after_setup_theme', 'wolfagroles_theme_setup' );

function wolfagroles_enqueue_assets() {
    wp_enqueue_style(
        'wolfagroles-main',
        get_template_directory_uri() . '/assets/css/main.css',
        array(),
        WOLFAGROLES_THEME_VERSION
    );
    wp_enqueue_script(
        'wolfagroles-main',
        get_template_directory_uri() . '/assets/js/main.js',
        array(),
        WOLFAGROLES_THEME_VERSION,
        true
    );
}
add_action( 'wp_enqueue_scripts', 'wolfagroles_enqueue_assets' );

function wolfagroles_setting( $key, $fallback = '' ) {
    if ( function_exists( 'wg_get_setting' ) ) {
        return wg_get_setting( $key, $fallback );
    }
    return $fallback;
}

function wolfagroles_phone_href() {
    return 'tel:' . preg_replace( '/[^0-9+]/', '', wolfagroles_setting( 'phone', '+7 495 221-83-13' ) );
}

function wolfagroles_archive_description( $post_type ) {
    $descriptions = array(
        'service'      => 'Здесь публикуются только подтверждённые направления работ. Для проверки конкретной задачи направьте чертёж и спецификацию.',
        'product_item' => 'Типы изделий добавляются после подтверждения технологии, материалов и примеров выполненных работ.',
        'material'     => 'Материалы публикуются только после подтверждения фактической возможности обработки.',
        'industry'     => 'Отраслевые решения описываются на основе реальных задач и подтверждённых производственных возможностей.',
        'equipment'    => 'Раздел содержит только подтверждённое оборудование с источником и датой проверки характеристик.',
        'case_study'   => 'В разделе размещаются реальные проекты с задачей, исходными данными, операциями, результатом и датой.',
    );
    return isset( $descriptions[ $post_type ] ) ? $descriptions[ $post_type ] : '';
}

function wolfagroles_body_classes( $classes ) {
    if ( is_front_page() ) {
        $classes[] = 'is-front-page';
    }
    return $classes;
}
add_filter( 'body_class', 'wolfagroles_body_classes' );
