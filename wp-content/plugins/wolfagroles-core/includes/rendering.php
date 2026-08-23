<?php
/**
 * Public rendering helpers.
 *
 * @package Wolfagroles_Core
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

function wg_decode_json_list( $raw ) {
    if ( ! $raw ) {
        return array();
    }
    $decoded = json_decode( html_entity_decode( $raw, ENT_QUOTES, 'UTF-8' ), true );
    return is_array( $decoded ) ? $decoded : array();
}

function wg_render_specs( $post_id ) {
    $specs = wg_decode_json_list( get_post_meta( $post_id, 'wg_specs_json', true ) );
    if ( ! $specs ) {
        return;
    }
    echo '<section class="technical-specs" aria-labelledby="technical-specs-title"><h2 id="technical-specs-title">Подтверждённые характеристики</h2><div class="table-scroll"><table class="spec-table"><caption class="screen-reader-text">Технические характеристики</caption><thead><tr><th scope="col">Параметр</th><th scope="col">Значение</th><th scope="col">Единица</th></tr></thead><tbody>';
    foreach ( $specs as $row ) {
        if ( empty( $row['parameter'] ) || ! isset( $row['value'] ) || '' === (string) $row['value'] ) {
            continue;
        }
        echo '<tr><th scope="row">' . esc_html( $row['parameter'] ) . '</th><td>' . esc_html( $row['value'] ) . '</td><td>' . esc_html( isset( $row['unit'] ) ? $row['unit'] : '' ) . '</td></tr>';
    }
    echo '</tbody></table></div></section>';
}

function wg_render_entity_faq( $post_id ) {
    $faq = wg_decode_json_list( get_post_meta( $post_id, 'wg_faq_json', true ) );
    if ( ! $faq ) {
        return;
    }
    echo '<section class="entity-faq" aria-labelledby="entity-faq-title"><h2 id="entity-faq-title">Частые вопросы</h2><div class="accordion-list">';
    foreach ( $faq as $item ) {
        if ( empty( $item['question'] ) || empty( $item['answer'] ) ) {
            continue;
        }
        echo '<details><summary>' . esc_html( $item['question'] ) . '</summary><div>' . wp_kses_post( wpautop( $item['answer'] ) ) . '</div></details>';
    }
    echo '</div></section>';
    wg_render_related_entities( $post_id );
}

function wg_render_related_entities( $post_id ) {
    $ids = array_filter( array_map( 'absint', explode( ',', (string) get_post_meta( $post_id, 'wg_related_ids', true ) ) ) );
    if ( ! $ids ) {
        return;
    }
    $items = get_posts(
        array(
            'post__in'       => $ids,
            'post_type'      => array_keys( wg_content_types() ),
            'post_status'    => 'publish',
            'posts_per_page' => 8,
            'orderby'        => 'post__in',
        )
    );
    if ( ! $items ) {
        return;
    }
    echo '<section class="related-entities"><h2>Связанные страницы</h2><ul>';
    foreach ( $items as $item ) {
        echo '<li><a href="' . esc_url( get_permalink( $item ) ) . '">' . esc_html( get_the_title( $item ) ) . '</a></li>';
    }
    echo '</ul></section>';
}

function wg_get_breadcrumb_items() {
    $items = array( array( 'name' => 'Главная', 'url' => home_url( '/' ) ) );
    if ( is_front_page() ) {
        return $items;
    }
    if ( is_singular() ) {
        $post_type = get_post_type();
        if ( 'post' === $post_type ) {
            $posts_page = (int) get_option( 'page_for_posts' );
            if ( $posts_page ) {
                $items[] = array( 'name' => get_the_title( $posts_page ), 'url' => get_permalink( $posts_page ) );
            }
        } elseif ( 'page' !== $post_type ) {
            $object = get_post_type_object( $post_type );
            if ( $object && $object->has_archive ) {
                $items[] = array( 'name' => $object->labels->name, 'url' => get_post_type_archive_link( $post_type ) );
            }
        }
        $items[] = array( 'name' => get_the_title(), 'url' => get_permalink() );
    } elseif ( is_post_type_archive() ) {
        $items[] = array( 'name' => post_type_archive_title( '', false ), 'url' => home_url( add_query_arg( array(), $GLOBALS['wp']->request ) ) );
    }
    return $items;
}

function wg_breadcrumbs() {
    $items = wg_get_breadcrumb_items();
    if ( count( $items ) < 2 ) {
        return;
    }
    echo '<nav class="breadcrumbs" aria-label="Хлебные крошки"><ol>';
    foreach ( $items as $index => $item ) {
        $last = $index === count( $items ) - 1;
        echo '<li>';
        if ( $last ) {
            echo '<span aria-current="page">' . esc_html( $item['name'] ) . '</span>';
        } else {
            echo '<a href="' . esc_url( $item['url'] ) . '">' . esc_html( $item['name'] ) . '</a>';
        }
        echo '</li>';
    }
    echo '</ol></nav>';
}

function wg_faq_shortcode() {
    ob_start();
    echo '<div class="accordion-list">';
    foreach ( wg_home_faq() as $item ) {
        echo '<details><summary>' . esc_html( $item['question'] ) . '</summary><div>' . wp_kses_post( wpautop( $item['answer'] ) ) . '</div></details>';
    }
    echo '</div><p><a class="button" href="' . esc_url( home_url( '/#quote' ) ) . '">Задать вопрос по задаче</a></p>';
    return ob_get_clean();
}
add_shortcode( 'wolfagroles_faq', 'wg_faq_shortcode' );

function wg_html_sitemap_shortcode() {
    $output = '<h2>Основные страницы</h2><ul>';
    $pages  = get_pages( array( 'post_status' => 'publish', 'sort_column' => 'menu_order,post_title' ) );
    foreach ( $pages as $page ) {
        $output .= '<li><a href="' . esc_url( get_permalink( $page ) ) . '">' . esc_html( get_the_title( $page ) ) . '</a></li>';
    }
    $output .= '</ul><h2>Разделы</h2><ul>';
    foreach ( wg_content_types() as $type => $data ) {
        $output .= '<li><a href="' . esc_url( get_post_type_archive_link( $type ) ) . '">' . esc_html( $data[0] ) . '</a></li>';
    }
    $output .= '</ul><p>Машиночитаемая XML-карта WordPress доступна по адресу <a href="' . esc_url( home_url( '/wp-sitemap.xml' ) ) . '">/wp-sitemap.xml</a>.</p>';
    return $output;
}
add_shortcode( 'wolfagroles_html_sitemap', 'wg_html_sitemap_shortcode' );
