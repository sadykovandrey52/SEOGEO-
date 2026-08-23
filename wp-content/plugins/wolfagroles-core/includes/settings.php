<?php
/**
 * Global settings.
 *
 * @package Wolfagroles_Core
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

function wg_setting_defaults() {
    return array(
        'company_full'       => 'Общество с ограниченной ответственностью «Вольфагролес»',
        'company_short'      => 'ООО «Вольфагролес»',
        'ogrn'               => '1037700057625',
        'inn'                => '7701037940',
        'kpp'                => '503601001',
        'address'            => '142101, Московская область, г. Подольск, ул. Шамотная, д. 6, строение 1',
        'phone'              => '+7 495 221-83-13',
        'email'              => 'savkinayus@wolfagro.ru',
        'director'           => 'Щетинин Дмитрий Сергеевич',
        'bank'               => 'АО РАЙФФАЙЗЕНБАНК',
        'bik'                => '044525700',
        'account'            => '40702810700001400092',
        'correspondent'      => '30101810200000000700',
        'okved'              => '52.24; 68.20; 52.10',
        'okpo'               => '05912970',
        'working_hours'      => '',
        'latitude'           => '',
        'longitude'          => '',
        'map_route_url'      => '',
        'form_recipient'     => 'savkinayus@wolfagro.ru',
        'max_files'          => 8,
        'max_file_mb'        => 20,
        'file_retention_days'=> 1,
        'yandex_metric_id'   => '',
        'ga4_id'             => '',
    );
}

function wg_get_settings() {
    $saved = get_option( 'wg_settings', array() );
    return wp_parse_args( is_array( $saved ) ? $saved : array(), wg_setting_defaults() );
}

function wg_get_setting( $key, $fallback = '' ) {
    $settings = wg_get_settings();
    return isset( $settings[ $key ] ) && '' !== $settings[ $key ] ? $settings[ $key ] : $fallback;
}

function wg_sanitize_settings( $input ) {
    $defaults = wg_setting_defaults();
    $output   = array();
    $email_keys = array( 'email', 'form_recipient' );
    $url_keys   = array( 'map_route_url' );
    $int_keys   = array( 'max_files', 'max_file_mb', 'file_retention_days' );

    foreach ( $defaults as $key => $default ) {
        $value = isset( $input[ $key ] ) ? $input[ $key ] : $default;
        if ( in_array( $key, $email_keys, true ) ) {
            $output[ $key ] = sanitize_email( $value );
        } elseif ( in_array( $key, $url_keys, true ) ) {
            $output[ $key ] = esc_url_raw( $value );
        } elseif ( in_array( $key, $int_keys, true ) ) {
            $output[ $key ] = max( 1, absint( $value ) );
        } elseif ( in_array( $key, array( 'latitude', 'longitude' ), true ) ) {
            $output[ $key ] = preg_replace( '/[^0-9.\-]/', '', (string) $value );
        } elseif ( in_array( $key, array( 'yandex_metric_id' ), true ) ) {
            $output[ $key ] = preg_replace( '/[^0-9]/', '', (string) $value );
        } else {
            $output[ $key ] = sanitize_text_field( $value );
        }
    }
    return $output;
}

function wg_register_settings() {
    register_setting(
        'wg_settings_group',
        'wg_settings',
        array(
            'type'              => 'array',
            'sanitize_callback' => 'wg_sanitize_settings',
            'default'           => wg_setting_defaults(),
        )
    );
}
add_action( 'admin_init', 'wg_register_settings' );

function wg_add_settings_page() {
    add_options_page(
        'Вольфагролес',
        'Вольфагролес',
        'manage_options',
        'wolfagroles-settings',
        'wg_render_settings_page'
    );
}
add_action( 'admin_menu', 'wg_add_settings_page' );

function wg_render_settings_page() {
    if ( ! current_user_can( 'manage_options' ) ) {
        return;
    }
    $settings = wg_get_settings();
    $fields   = array(
        'company_full'        => 'Полное название',
        'company_short'       => 'Краткое название',
        'phone'               => 'Телефон',
        'email'               => 'E-mail',
        'address'             => 'Адрес',
        'working_hours'       => 'Режим работы',
        'latitude'            => 'Широта',
        'longitude'           => 'Долгота',
        'map_route_url'       => 'Ссылка «Построить маршрут»',
        'form_recipient'      => 'Получатель заявок',
        'max_files'           => 'Максимум файлов',
        'max_file_mb'         => 'Максимум одного файла, МБ',
        'file_retention_days' => 'Срок хранения временных файлов, дней',
        'yandex_metric_id'    => 'ID Яндекс Метрики',
        'ga4_id'              => 'GA4 Measurement ID',
    );
    ?>
    <div class="wrap">
        <h1>Настройки ООО «Вольфагролес»</h1>
        <p>Контакты и реквизиты ниже являются единым источником для шаблонов и структурированных данных. Поля аналитики не подключают счётчики автоматически: сначала настройте согласие на cookies.</p>
        <form action="options.php" method="post">
            <?php settings_fields( 'wg_settings_group' ); ?>
            <table class="form-table" role="presentation">
                <?php foreach ( $fields as $key => $label ) : ?>
                    <tr>
                        <th scope="row"><label for="wg-<?php echo esc_attr( $key ); ?>"><?php echo esc_html( $label ); ?></label></th>
                        <td><input class="regular-text" id="wg-<?php echo esc_attr( $key ); ?>" name="wg_settings[<?php echo esc_attr( $key ); ?>]" value="<?php echo esc_attr( $settings[ $key ] ); ?>" type="<?php echo in_array( $key, array( 'max_files', 'max_file_mb', 'file_retention_days' ), true ) ? 'number' : 'text'; ?>"></td>
                    </tr>
                <?php endforeach; ?>
            </table>
            <?php submit_button(); ?>
        </form>
    </div>
    <?php
}
