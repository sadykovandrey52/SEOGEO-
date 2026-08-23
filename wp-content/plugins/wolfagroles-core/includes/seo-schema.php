<?php
/**
 * Metadata, canonical URLs, robots, schema and basic hardening.
 *
 * @package Wolfagroles_Core
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

remove_action( 'wp_head', 'rel_canonical' );
remove_action( 'wp_head', 'wp_generator' );
add_filter( 'xmlrpc_enabled', '__return_false' );

function wg_current_url() {
    if ( is_front_page() ) {
        return home_url( '/' );
    }
    if ( is_singular() ) {
        return get_permalink();
    }
    if ( is_post_type_archive() ) {
        $type = get_query_var( 'post_type' );
        if ( is_array( $type ) ) {
            $type = reset( $type );
        }
        return get_post_type_archive_link( $type );
    }
    if ( is_home() ) {
        $posts_page = (int) get_option( 'page_for_posts' );
        return $posts_page ? get_permalink( $posts_page ) : home_url( '/stati/' );
    }
    return home_url( '/' );
}

function wg_meta_title() {
    if ( is_singular() ) {
        $custom = get_post_meta( get_queried_object_id(), 'wg_seo_title', true );
        if ( $custom ) {
            return $custom;
        }
        return single_post_title( '', false ) . ' — ООО «Вольфагролес»';
    }
    if ( is_post_type_archive() ) {
        return post_type_archive_title( '', false ) . ' — ООО «Вольфагролес»';
    }
    if ( is_home() ) {
        return 'Статьи — ООО «Вольфагролес»';
    }
    return get_bloginfo( 'name' );
}

function wg_meta_description() {
    if ( is_singular() ) {
        $custom = get_post_meta( get_queried_object_id(), 'wg_seo_description', true );
        if ( $custom ) {
            return $custom;
        }
        $excerpt = get_the_excerpt( get_queried_object_id() );
        if ( $excerpt ) {
            return wp_strip_all_tags( $excerpt );
        }
    }
    if ( is_post_type_archive() && function_exists( 'wolfagroles_archive_description' ) ) {
        return wolfagroles_archive_description( get_query_var( 'post_type' ) );
    }
    return 'ООО «Вольфагролес»: приём B2B-запросов с чертежами и спецификациями для проверки и расчёта.';
}

function wg_filter_document_title( $title ) {
    if ( is_admin() || is_feed() ) {
        return $title;
    }
    return wg_meta_title();
}
add_filter( 'pre_get_document_title', 'wg_filter_document_title', 20 );

function wg_output_metadata() {
    if ( is_admin() || is_feed() || is_search() || is_404() ) {
        return;
    }
    $url         = wg_current_url();
    $title       = wg_meta_title();
    $description = wg_meta_description();
    $image       = is_singular() && has_post_thumbnail() ? get_the_post_thumbnail_url( get_queried_object_id(), 'large' ) : '';
    echo "\n" . '<link rel="canonical" href="' . esc_url( $url ) . '">' . "\n";
    echo '<meta name="description" content="' . esc_attr( $description ) . '">' . "\n";
    echo '<meta property="og:locale" content="ru_RU">' . "\n";
    echo '<meta property="og:type" content="' . esc_attr( is_singular( 'post' ) ? 'article' : 'website' ) . '">' . "\n";
    echo '<meta property="og:title" content="' . esc_attr( $title ) . '">' . "\n";
    echo '<meta property="og:description" content="' . esc_attr( $description ) . '">' . "\n";
    echo '<meta property="og:url" content="' . esc_url( $url ) . '">' . "\n";
    echo '<meta property="og:site_name" content="ООО «Вольфагролес»">' . "\n";
    if ( $image ) {
        echo '<meta property="og:image" content="' . esc_url( $image ) . '">' . "\n";
    }
}
add_action( 'wp_head', 'wg_output_metadata', 2 );

function wg_robots_directives( $robots ) {
    $environment = function_exists( 'wp_get_environment_type' ) ? wp_get_environment_type() : 'production';
    $noindex     = 'production' !== $environment || is_search() || is_attachment();
    if ( is_singular() && get_post_meta( get_queried_object_id(), 'wg_noindex', true ) ) {
        $noindex = true;
    }
    if ( is_singular( array_keys( wg_content_types() ) ) && 'confirmed' !== get_post_meta( get_queried_object_id(), 'wg_fact_status', true ) ) {
        $noindex = true;
    }
    if ( is_post_type_archive() ) {
        $type  = get_query_var( 'post_type' );
        if ( is_array( $type ) ) {
            $type = reset( $type );
        }
        $count = wp_count_posts( $type );
        if ( ! $count || empty( $count->publish ) ) {
            $noindex = true;
        }
    }
    if ( $noindex ) {
        $robots['noindex']  = true;
        $robots['nofollow'] = true;
        unset( $robots['index'], $robots['follow'] );
    }
    return $robots;
}
add_filter( 'wp_robots', 'wg_robots_directives', 20 );

function wg_organization_schema() {
    return array(
        '@type'   => 'Organization',
        '@id'     => home_url( '/#organization' ),
        'name'    => wg_get_setting( 'company_full' ),
        'legalName' => wg_get_setting( 'company_full' ),
        'url'     => home_url( '/' ),
        'telephone' => wg_get_setting( 'phone' ),
        'email'   => wg_get_setting( 'email' ),
        'taxID'   => wg_get_setting( 'inn' ),
        'address' => array(
            '@type'           => 'PostalAddress',
            'streetAddress'   => 'ул. Шамотная, д. 6, строение 1',
            'addressLocality' => 'Подольск',
            'addressRegion'   => 'Московская область',
            'postalCode'      => '142101',
            'addressCountry'  => 'RU',
        ),
        'contactPoint' => array(
            '@type'       => 'ContactPoint',
            'telephone'   => wg_get_setting( 'phone' ),
            'email'       => wg_get_setting( 'email' ),
            'contactType' => 'customer service',
            'areaServed'  => array( 'Москва', 'Московская область' ),
            'availableLanguage' => 'ru',
        ),
    );
}

function wg_breadcrumb_schema() {
    if ( ! function_exists( 'wg_get_breadcrumb_items' ) ) {
        return null;
    }
    $items = wg_get_breadcrumb_items();
    if ( count( $items ) < 2 ) {
        return null;
    }
    $elements = array();
    foreach ( $items as $index => $item ) {
        $elements[] = array(
            '@type'    => 'ListItem',
            'position' => $index + 1,
            'name'     => $item['name'],
            'item'     => $item['url'],
        );
    }
    return array(
        '@type'           => 'BreadcrumbList',
        '@id'             => wg_current_url() . '#breadcrumbs',
        'itemListElement' => $elements,
    );
}

function wg_output_schema() {
    if ( is_admin() || is_feed() || is_search() || is_404() ) {
        return;
    }
    $url   = wg_current_url();
    $graph = array(
        wg_organization_schema(),
        array(
            '@type'     => 'WebSite',
            '@id'       => home_url( '/#website' ),
            'url'       => home_url( '/' ),
            'name'      => 'ООО «Вольфагролес»',
            'publisher' => array( '@id' => home_url( '/#organization' ) ),
            'inLanguage'=> 'ru-RU',
        ),
        array(
            '@type'      => 'WebPage',
            '@id'        => $url . '#webpage',
            'url'        => $url,
            'name'       => wg_meta_title(),
            'description'=> wg_meta_description(),
            'isPartOf'   => array( '@id' => home_url( '/#website' ) ),
            'about'      => array( '@id' => home_url( '/#organization' ) ),
            'inLanguage' => 'ru-RU',
        ),
    );
    $breadcrumbs = wg_breadcrumb_schema();
    if ( $breadcrumbs ) {
        $graph[] = $breadcrumbs;
    }

    $faq = array();
    if ( is_front_page() || is_page( 'faq' ) ) {
        $faq = wg_home_faq();
    } elseif ( is_singular() ) {
        $faq = wg_decode_json_list( get_post_meta( get_queried_object_id(), 'wg_faq_json', true ) );
    }
    if ( $faq ) {
        $main_entities = array();
        foreach ( $faq as $item ) {
            if ( ! empty( $item['question'] ) && ! empty( $item['answer'] ) ) {
                $main_entities[] = array(
                    '@type'          => 'Question',
                    'name'           => wp_strip_all_tags( $item['question'] ),
                    'acceptedAnswer' => array( '@type' => 'Answer', 'text' => wp_strip_all_tags( $item['answer'] ) ),
                );
            }
        }
        if ( $main_entities ) {
            $graph[] = array( '@type' => 'FAQPage', '@id' => $url . '#faq', 'mainEntity' => $main_entities );
        }
    }

    if ( is_singular( 'service' ) && 'confirmed' === get_post_meta( get_queried_object_id(), 'wg_fact_status', true ) ) {
        $graph[] = array(
            '@type'       => 'Service',
            '@id'         => $url . '#service',
            'name'        => get_the_title(),
            'description' => get_post_meta( get_queried_object_id(), 'wg_short_answer', true ),
            'provider'    => array( '@id' => home_url( '/#organization' ) ),
            'areaServed'  => array( 'Москва', 'Московская область' ),
            'url'         => $url,
        );
    }
    if ( is_singular( 'post' ) ) {
        $graph[] = array(
            '@type'            => 'BlogPosting',
            '@id'              => $url . '#article',
            'headline'         => get_the_title(),
            'datePublished'    => get_the_date( DATE_W3C ),
            'dateModified'     => get_the_modified_date( DATE_W3C ),
            'mainEntityOfPage' => array( '@id' => $url . '#webpage' ),
            'publisher'        => array( '@id' => home_url( '/#organization' ) ),
            'author'           => array( '@type' => 'Person', 'name' => get_the_author() ),
        );
    }
    if ( is_post_type_archive() && have_posts() ) {
        global $wp_query;
        $elements = array();
        foreach ( $wp_query->posts as $index => $post ) {
            $elements[] = array( '@type' => 'ListItem', 'position' => $index + 1, 'url' => get_permalink( $post ), 'name' => get_the_title( $post ) );
        }
        $graph[] = array( '@type' => 'ItemList', '@id' => $url . '#items', 'itemListElement' => $elements );
    }
    echo "\n" . '<script type="application/ld+json">' . wp_json_encode( array( '@context' => 'https://schema.org', '@graph' => $graph ), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ) . '</script>' . "\n";
}
add_action( 'wp_head', 'wg_output_schema', 20 );

function wg_filter_robots_txt( $output, $public ) {
    if ( ! $public ) {
        return "User-agent: *\nDisallow: /\n";
    }
    $output  = "User-agent: *\n";
    $output .= "Disallow: /wp-admin/\nAllow: /wp-admin/admin-ajax.php\n";
    $output .= "Disallow: /*?s=\nDisallow: /*?preview=\nDisallow: /wp-content/uploads/wolfagroles-leads/\n";
    $output .= 'Sitemap: ' . home_url( '/wp-sitemap.xml' ) . "\n";
    return $output;
}
add_filter( 'robots_txt', 'wg_filter_robots_txt', 20, 2 );

function wg_security_headers() {
    if ( headers_sent() ) {
        return;
    }
    header( 'X-Content-Type-Options: nosniff' );
    header( 'Referrer-Policy: strict-origin-when-cross-origin' );
    header( 'X-Frame-Options: SAMEORIGIN' );
    header( 'Permissions-Policy: camera=(), microphone=(), geolocation=()' );
}
add_action( 'send_headers', 'wg_security_headers' );

function wg_redirect_attachment_pages() {
    if ( is_attachment() ) {
        wp_safe_redirect( home_url( '/' ), 301 );
        exit;
    }
}
add_action( 'template_redirect', 'wg_redirect_attachment_pages' );
