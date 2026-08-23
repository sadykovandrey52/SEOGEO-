<?php
/**
 * Content types, taxonomies and editable fact fields.
 *
 * @package Wolfagroles_Core
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

function wg_content_types() {
    return array(
        'service'      => array( 'Услуги', 'Услуга', 'uslugi' ),
        'product_item' => array( 'Изделия', 'Изделие', 'izdeliya' ),
        'material'     => array( 'Материалы', 'Материал', 'materialy' ),
        'industry'     => array( 'Отрасли', 'Отрасль', 'otrasli' ),
        'equipment'    => array( 'Оборудование', 'Оборудование', 'oborudovanie' ),
        'case_study'   => array( 'Проекты', 'Проект', 'proekty' ),
    );
}

function wg_register_content_model() {
    foreach ( wg_content_types() as $type => $data ) {
        register_post_type(
            $type,
            array(
                'labels' => array(
                    'name'          => $data[0],
                    'singular_name' => $data[1],
                    'add_new_item'  => 'Добавить: ' . $data[1],
                    'edit_item'     => 'Редактировать: ' . $data[1],
                ),
                'public'       => true,
                'show_in_rest' => true,
                'has_archive'  => $data[2],
                'rewrite'      => array( 'slug' => $data[2], 'with_front' => false ),
                'menu_icon'    => 'dashicons-admin-generic',
                'supports'     => array( 'title', 'editor', 'excerpt', 'thumbnail', 'revisions', 'page-attributes', 'author' ),
            )
        );
    }

    register_taxonomy(
        'service_category',
        array( 'service', 'product_item', 'case_study' ),
        array(
            'labels'       => array( 'name' => 'Категории услуг', 'singular_name' => 'Категория услуг' ),
            'public'       => true,
            'show_in_rest' => true,
            'hierarchical' => true,
            'rewrite'      => array( 'slug' => 'kategoriya-uslug' ),
        )
    );
    register_taxonomy(
        'material_type',
        array( 'material', 'service', 'product_item', 'case_study' ),
        array(
            'labels'       => array( 'name' => 'Типы материалов', 'singular_name' => 'Тип материала' ),
            'public'       => true,
            'show_in_rest' => true,
            'hierarchical' => true,
            'rewrite'      => array( 'slug' => 'tip-materiala' ),
        )
    );
    register_taxonomy(
        'industry_type',
        array( 'industry', 'service', 'product_item', 'case_study' ),
        array(
            'labels'       => array( 'name' => 'Типы отраслей', 'singular_name' => 'Тип отрасли' ),
            'public'       => true,
            'show_in_rest' => true,
            'hierarchical' => true,
            'rewrite'      => array( 'slug' => 'tip-otrasli' ),
        )
    );

    $post_types = array_merge( array_keys( wg_content_types() ), array( 'post', 'page' ) );
    foreach ( $post_types as $post_type ) {
        wg_register_post_meta_fields( $post_type );
    }
}
add_action( 'init', 'wg_register_content_model' );

function wg_register_post_meta_fields( $post_type ) {
    $text_fields = array( 'wg_fact_status', 'wg_source', 'wg_verified_on', 'wg_short_answer', 'wg_specs_json', 'wg_faq_json', 'wg_related_ids', 'wg_seo_title', 'wg_seo_description' );
    foreach ( $text_fields as $key ) {
        register_post_meta(
            $post_type,
            $key,
            array(
                'type'              => 'string',
                'single'            => true,
                'show_in_rest'      => true,
                'sanitize_callback' => 'sanitize_textarea_field',
                'auth_callback'     => function () { return current_user_can( 'edit_posts' ); },
            )
        );
    }
    register_post_meta(
        $post_type,
        'wg_noindex',
        array(
            'type'          => 'boolean',
            'single'        => true,
            'show_in_rest'  => true,
            'auth_callback' => function () { return current_user_can( 'edit_posts' ); },
        )
    );
}

function wg_add_fact_meta_box() {
    $types = array_merge( array_keys( wg_content_types() ), array( 'post', 'page' ) );
    foreach ( $types as $type ) {
        add_meta_box( 'wg-facts', 'Факты, SEO и связанные сущности', 'wg_render_fact_meta_box', $type, 'normal', 'high' );
    }
}
add_action( 'add_meta_boxes', 'wg_add_fact_meta_box' );

function wg_render_fact_meta_box( $post ) {
    wp_nonce_field( 'wg_save_facts', 'wg_facts_nonce' );
    $values = array();
    foreach ( array( 'wg_fact_status', 'wg_source', 'wg_verified_on', 'wg_short_answer', 'wg_specs_json', 'wg_faq_json', 'wg_related_ids', 'wg_seo_title', 'wg_seo_description', 'wg_noindex' ) as $key ) {
        $values[ $key ] = get_post_meta( $post->ID, $key, true );
    }
    ?>
    <p><label for="wg-fact-status"><strong>Статус утверждения</strong></label><br>
        <select id="wg-fact-status" name="wg_fact_status">
            <option value="needs_data" <?php selected( $values['wg_fact_status'], 'needs_data' ); ?>>НУЖНЫ ДАННЫЕ</option>
            <option value="confirmed" <?php selected( $values['wg_fact_status'], 'confirmed' ); ?>>ПОДТВЕРЖДЕНО</option>
            <option value="do_not_publish" <?php selected( $values['wg_fact_status'], 'do_not_publish' ); ?>>НЕ ПУБЛИКОВАТЬ</option>
        </select>
    </p>
    <p><label for="wg-source"><strong>Источник факта</strong></label><br><input class="widefat" id="wg-source" name="wg_source" value="<?php echo esc_attr( $values['wg_source'] ); ?>"></p>
    <p><label for="wg-verified"><strong>Дата проверки (ГГГГ-ММ-ДД)</strong></label><br><input id="wg-verified" name="wg_verified_on" type="date" value="<?php echo esc_attr( $values['wg_verified_on'] ); ?>"></p>
    <p><label for="wg-short-answer"><strong>Прямой ответ 40–80 слов</strong></label><br><textarea class="widefat" rows="4" id="wg-short-answer" name="wg_short_answer"><?php echo esc_textarea( $values['wg_short_answer'] ); ?></textarea></p>
    <p><label for="wg-specs"><strong>Характеристики JSON</strong></label><br><textarea class="widefat code" rows="6" id="wg-specs" name="wg_specs_json" placeholder='[{"parameter":"Материал","value":"...","unit":""}]'><?php echo esc_textarea( $values['wg_specs_json'] ); ?></textarea></p>
    <p><label for="wg-faq"><strong>FAQ JSON</strong></label><br><textarea class="widefat code" rows="6" id="wg-faq" name="wg_faq_json" placeholder='[{"question":"...","answer":"..."}]'><?php echo esc_textarea( $values['wg_faq_json'] ); ?></textarea></p>
    <p><label for="wg-related"><strong>ID связанных записей через запятую</strong></label><br><input class="widefat" id="wg-related" name="wg_related_ids" value="<?php echo esc_attr( $values['wg_related_ids'] ); ?>"></p>
    <hr>
    <p><label for="wg-seo-title"><strong>SEO Title</strong></label><br><input class="widefat" id="wg-seo-title" name="wg_seo_title" value="<?php echo esc_attr( $values['wg_seo_title'] ); ?>"></p>
    <p><label for="wg-seo-description"><strong>Meta Description</strong></label><br><textarea class="widefat" rows="3" id="wg-seo-description" name="wg_seo_description"><?php echo esc_textarea( $values['wg_seo_description'] ); ?></textarea></p>
    <p><label><input type="checkbox" name="wg_noindex" value="1" <?php checked( (bool) $values['wg_noindex'] ); ?>> noindex</label></p>
    <?php
}

function wg_save_fact_meta_box( $post_id ) {
    if ( ! isset( $_POST['wg_facts_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['wg_facts_nonce'] ) ), 'wg_save_facts' ) ) {
        return;
    }
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
        return;
    }
    if ( ! current_user_can( 'edit_post', $post_id ) ) {
        return;
    }
    $allowed_statuses = array( 'needs_data', 'confirmed', 'do_not_publish' );
    $status = isset( $_POST['wg_fact_status'] ) ? sanitize_key( $_POST['wg_fact_status'] ) : 'needs_data';
    update_post_meta( $post_id, 'wg_fact_status', in_array( $status, $allowed_statuses, true ) ? $status : 'needs_data' );
    foreach ( array( 'wg_source', 'wg_verified_on', 'wg_short_answer', 'wg_specs_json', 'wg_faq_json', 'wg_related_ids', 'wg_seo_title', 'wg_seo_description' ) as $key ) {
        $value = isset( $_POST[ $key ] ) ? sanitize_textarea_field( wp_unslash( $_POST[ $key ] ) ) : '';
        update_post_meta( $post_id, $key, $value );
    }
    update_post_meta( $post_id, 'wg_noindex', isset( $_POST['wg_noindex'] ) ? 1 : 0 );
}
add_action( 'save_post', 'wg_save_fact_meta_box' );

function wg_prevent_unconfirmed_publish( $data, $postarr ) {
    $types = array_keys( wg_content_types() );
    if ( ! in_array( $data['post_type'], $types, true ) || 'publish' !== $data['post_status'] || empty( $postarr['ID'] ) ) {
        return $data;
    }
    $status = isset( $_POST['wg_fact_status'] )
        ? sanitize_key( wp_unslash( $_POST['wg_fact_status'] ) )
        : get_post_meta( (int) $postarr['ID'], 'wg_fact_status', true );
    if ( 'confirmed' !== $status ) {
        $data['post_status'] = 'draft';
        add_filter( 'redirect_post_location', 'wg_add_publish_notice', 99 );
    }
    return $data;
}
add_filter( 'wp_insert_post_data', 'wg_prevent_unconfirmed_publish', 10, 2 );

function wg_add_publish_notice( $location ) {
    remove_filter( 'redirect_post_location', 'wg_add_publish_notice', 99 );
    return add_query_arg( 'wg_unconfirmed', '1', $location );
}

function wg_admin_publish_notice() {
    if ( isset( $_GET['wg_unconfirmed'] ) ) {
        echo '<div class="notice notice-warning is-dismissible"><p>Публикация отменена: сначала установите статус «ПОДТВЕРЖДЕНО» и заполните источник.</p></div>';
    }
}
add_action( 'admin_notices', 'wg_admin_publish_notice' );
