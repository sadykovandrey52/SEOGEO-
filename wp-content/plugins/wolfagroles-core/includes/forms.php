<?php
/**
 * Quote form, validation, secure temporary files and notifications.
 *
 * @package Wolfagroles_Core
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

function wg_allowed_file_types() {
    return array(
        'pdf'  => array( 'application/pdf' ),
        'dxf'  => array( 'application/dxf', 'image/vnd.dxf', 'application/octet-stream', 'text/plain' ),
        'dwg'  => array( 'image/vnd.dwg', 'application/acad', 'application/octet-stream' ),
        'step' => array( 'application/step', 'application/octet-stream', 'text/plain' ),
        'stp'  => array( 'application/step', 'application/octet-stream', 'text/plain' ),
        'xls'  => array( 'application/vnd.ms-excel', 'application/octet-stream' ),
        'xlsx' => array( 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 'application/zip' ),
        'doc'  => array( 'application/msword', 'application/octet-stream' ),
        'docx' => array( 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'application/zip' ),
        'zip'  => array( 'application/zip', 'application/x-zip-compressed', 'application/octet-stream' ),
    );
}

function wg_quote_form_shortcode() {
    $max_files = min( 12, max( 1, (int) wg_get_setting( 'max_files', 8 ) ) );
    $max_mb    = min( 50, max( 1, (int) wg_get_setting( 'max_file_mb', 20 ) ) );
    $status    = isset( $_GET['lead'] ) ? sanitize_key( wp_unslash( $_GET['lead'] ) ) : '';
    ob_start();
    if ( 'success' === $status ) {
        echo '<div class="form-status" role="status">Заявка передана. Сохраните ID: <strong>' . esc_html( isset( $_GET['lead_id'] ) ? sanitize_text_field( wp_unslash( $_GET['lead_id'] ) ) : '' ) . '</strong>.</div>';
    } elseif ( 'error' === $status ) {
        echo '<div class="form-status form-status--error" role="alert">Не удалось отправить заявку. Проверьте обязательные поля и файлы или свяжитесь по телефону.</div>';
    }
    ?>
    <form class="quote-form" data-quote-form action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" method="post" enctype="multipart/form-data" novalidate>
        <input type="hidden" name="action" value="wg_submit_quote">
        <?php wp_nonce_field( 'wg_submit_quote', 'wg_quote_nonce' ); ?>
        <input type="hidden" name="source_url" value="">
        <input type="hidden" name="referrer" value="">
        <?php foreach ( array( 'utm_source', 'utm_medium', 'utm_campaign', 'utm_content', 'utm_term' ) as $utm ) : ?>
            <input type="hidden" name="<?php echo esc_attr( $utm ); ?>" value="">
        <?php endforeach; ?>
        <div class="honeypot" aria-hidden="true"><label>Оставьте поле пустым<input type="text" name="website" tabindex="-1" autocomplete="off"></label></div>
        <div class="form-row">
            <div class="form-field"><label for="wg-name">Имя</label><input id="wg-name" name="name" autocomplete="name" maxlength="100"></div>
            <div class="form-field"><label for="wg-company">Компания</label><input id="wg-company" name="company" autocomplete="organization" maxlength="160"></div>
        </div>
        <div class="form-row">
            <div class="form-field"><label for="wg-phone">Телефон</label><input id="wg-phone" name="phone" type="tel" autocomplete="tel" inputmode="tel" maxlength="40" aria-describedby="wg-channel-help"></div>
            <div class="form-field"><label for="wg-email">E-mail</label><input id="wg-email" name="email" type="email" autocomplete="email" maxlength="180" aria-describedby="wg-channel-help"></div>
        </div>
        <p class="form-help" id="wg-channel-help">Заполните минимум один канал связи: телефон или e-mail.</p>
        <div class="form-field"><label for="wg-description">Описание задачи</label><textarea id="wg-description" name="description" required maxlength="6000"></textarea></div>
        <div class="form-row">
            <div class="form-field"><label for="wg-quantity">Количество</label><input id="wg-quantity" name="quantity" maxlength="120"></div>
            <div class="form-field"><label for="wg-deadline">Желаемый срок</label><input id="wg-deadline" name="desired_deadline" maxlength="120"></div>
        </div>
        <div class="form-field">
            <label for="wg-files">Файлы</label>
            <input id="wg-files" data-quote-files type="file" name="files[]" multiple accept=".pdf,.dxf,.dwg,.step,.stp,.xls,.xlsx,.doc,.docx,.zip" aria-describedby="wg-files-help">
            <p class="form-help" id="wg-files-help">До <?php echo esc_html( $max_files ); ?> файлов, не более <?php echo esc_html( $max_mb ); ?> МБ каждый: PDF, DXF, DWG, STEP/STP, XLS/XLSX, DOC/DOCX, ZIP.</p>
        </div>
        <label class="form-consent"><input type="checkbox" name="consent" value="1" required><span>Согласен(на) на <a href="<?php echo esc_url( home_url( '/soglasie-na-obrabotku-personalnyh-dannyh/' ) ); ?>" target="_blank" rel="noopener">обработку персональных данных</a> и ознакомлен(а) с <a href="<?php echo esc_url( home_url( '/politika-konfidencialnosti/' ) ); ?>" target="_blank" rel="noopener">политикой</a>.</span></label>
        <button class="button button--full" type="submit">Отправить на расчёт</button>
        <p class="form-help">Отправка формы не является подтверждением цены, срока или возможности выполнения.</p>
    </form>
    <?php
    return ob_get_clean();
}
add_shortcode( 'wolfagroles_quote_form', 'wg_quote_form_shortcode' );

function wg_quote_client_ip() {
    return isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) : 'unknown';
}

function wg_quote_redirect( $status, $lead_id = '' ) {
    $source = isset( $_POST['source_url'] ) ? esc_url_raw( wp_unslash( $_POST['source_url'] ) ) : home_url( '/#quote' );
    $parts  = wp_parse_url( $source );
    $host   = isset( $parts['host'] ) ? $parts['host'] : '';
    $home   = wp_parse_url( home_url( '/' ), PHP_URL_HOST );
    if ( ! $host || $host !== $home ) {
        $source = home_url( '/#quote' );
    }
    $source = preg_replace( '/#.*$/', '', $source );
    $source = remove_query_arg( array( 'lead', 'lead_id' ), $source );
    $source = add_query_arg( array( 'lead' => $status, 'lead_id' => $lead_id ), $source );
    wp_safe_redirect( $source . '#quote' );
    exit;
}

function wg_handle_quote_submission() {
    if ( ! isset( $_POST['wg_quote_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['wg_quote_nonce'] ) ), 'wg_submit_quote' ) ) {
        wg_quote_redirect( 'error' );
    }
    if ( ! empty( $_POST['website'] ) ) {
        wg_quote_redirect( 'success', 'accepted' );
    }

    $rate_key = 'wg_rate_' . hash( 'sha256', wp_salt() . wg_quote_client_ip() );
    $attempts = (int) get_transient( $rate_key );
    if ( $attempts >= 3 ) {
        wg_quote_redirect( 'error' );
    }
    set_transient( $rate_key, $attempts + 1, 10 * MINUTE_IN_SECONDS );

    $fields = array(
        'name'             => isset( $_POST['name'] ) ? sanitize_text_field( wp_unslash( $_POST['name'] ) ) : '',
        'company'          => isset( $_POST['company'] ) ? sanitize_text_field( wp_unslash( $_POST['company'] ) ) : '',
        'phone'            => isset( $_POST['phone'] ) ? sanitize_text_field( wp_unslash( $_POST['phone'] ) ) : '',
        'email'            => isset( $_POST['email'] ) ? sanitize_email( wp_unslash( $_POST['email'] ) ) : '',
        'description'      => isset( $_POST['description'] ) ? sanitize_textarea_field( wp_unslash( $_POST['description'] ) ) : '',
        'quantity'         => isset( $_POST['quantity'] ) ? sanitize_text_field( wp_unslash( $_POST['quantity'] ) ) : '',
        'desired_deadline' => isset( $_POST['desired_deadline'] ) ? sanitize_text_field( wp_unslash( $_POST['desired_deadline'] ) ) : '',
        'source_url'       => isset( $_POST['source_url'] ) ? esc_url_raw( wp_unslash( $_POST['source_url'] ) ) : '',
        'referrer'         => isset( $_POST['referrer'] ) ? esc_url_raw( wp_unslash( $_POST['referrer'] ) ) : '',
    );

    if ( ( ! $fields['phone'] && ! $fields['email'] ) || ! $fields['description'] || empty( $_POST['consent'] ) ) {
        wg_quote_redirect( 'error' );
    }
    if ( $fields['email'] && ! is_email( $fields['email'] ) ) {
        wg_quote_redirect( 'error' );
    }

    $lead_id     = 'WG-' . gmdate( 'Ymd-His' ) . '-' . strtoupper( wp_generate_password( 4, false, false ) );
    $attachments = wg_process_quote_files();
    if ( is_wp_error( $attachments ) ) {
        wg_quote_redirect( 'error' );
    }

    $utm = array();
    foreach ( array( 'utm_source', 'utm_medium', 'utm_campaign', 'utm_content', 'utm_term' ) as $key ) {
        $utm[ $key ] = isset( $_POST[ $key ] ) ? sanitize_text_field( wp_unslash( $_POST[ $key ] ) ) : '';
    }

    $message = "ID заявки: {$lead_id}\n";
    foreach ( $fields as $label => $value ) {
        $message .= $label . ': ' . $value . "\n";
    }
    foreach ( $utm as $label => $value ) {
        $message .= $label . ': ' . $value . "\n";
    }
    $message .= 'Файлов: ' . count( $attachments ) . "\n";
    $headers = array( 'Content-Type: text/plain; charset=UTF-8' );
    if ( $fields['email'] ) {
        $headers[] = 'Reply-To: ' . $fields['email'];
    }
    $sent = wp_mail(
        wg_get_setting( 'form_recipient', wg_get_setting( 'email', 'savkinayus@wolfagro.ru' ) ),
        'Новая заявка на расчёт ' . $lead_id,
        $message,
        $headers,
        $attachments
    );

    foreach ( $attachments as $path ) {
        if ( is_file( $path ) ) {
            wp_delete_file( $path );
        }
    }
    if ( ! $sent ) {
        error_log( 'Wolfagroles quote mail failure: ' . $lead_id ); // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log
        wg_quote_redirect( 'error' );
    }
    if ( $fields['email'] ) {
        wp_mail(
            $fields['email'],
            'Заявка получена: ' . $lead_id,
            "Ваша заявка передана ООО «Вольфагролес».\nID: {$lead_id}\n\nЭто автоматическое подтверждение получения формы, а не подтверждение цены, срока или возможности выполнения.",
            array( 'Content-Type: text/plain; charset=UTF-8' )
        );
    }
    delete_transient( $rate_key );
    wg_quote_redirect( 'success', $lead_id );
}
add_action( 'admin_post_nopriv_wg_submit_quote', 'wg_handle_quote_submission' );
add_action( 'admin_post_wg_submit_quote', 'wg_handle_quote_submission' );

function wg_private_upload_directory() {
    $uploads = wp_upload_dir();
    return trailingslashit( $uploads['basedir'] ) . 'wolfagroles-leads';
}

function wg_ensure_private_upload_directory() {
    $directory = wg_private_upload_directory();
    wp_mkdir_p( $directory );
    if ( is_dir( $directory ) ) {
        if ( ! file_exists( $directory . '/index.php' ) ) {
            file_put_contents( $directory . '/index.php', "<?php\n// Silence is golden.\n" ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_file_put_contents
        }
        if ( ! file_exists( $directory . '/.htaccess' ) ) {
            file_put_contents( $directory . '/.htaccess', "Options -Indexes\n<FilesMatch \".*\">\nRequire all denied\n</FilesMatch>\n" ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_file_put_contents
        }
    }
    return $directory;
}

function wg_normalize_uploads_array( $files ) {
    $normalized = array();
    if ( empty( $files['name'] ) || ! is_array( $files['name'] ) ) {
        return $normalized;
    }
    foreach ( $files['name'] as $index => $name ) {
        if ( UPLOAD_ERR_NO_FILE === (int) $files['error'][ $index ] ) {
            continue;
        }
        $normalized[] = array(
            'name'     => $name,
            'type'     => $files['type'][ $index ],
            'tmp_name' => $files['tmp_name'][ $index ],
            'error'    => (int) $files['error'][ $index ],
            'size'     => (int) $files['size'][ $index ],
        );
    }
    return $normalized;
}

function wg_process_quote_files() {
    if ( empty( $_FILES['files'] ) ) {
        return array();
    }
    $files     = wg_normalize_uploads_array( $_FILES['files'] ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
    $max_files = min( 12, max( 1, (int) wg_get_setting( 'max_files', 8 ) ) );
    $max_bytes = min( 50, max( 1, (int) wg_get_setting( 'max_file_mb', 20 ) ) ) * MB_IN_BYTES;
    $allowed   = wg_allowed_file_types();
    $directory = wg_ensure_private_upload_directory();
    $stored    = array();

    if ( count( $files ) > $max_files ) {
        return new WP_Error( 'too_many_files' );
    }
    foreach ( $files as $file ) {
        $extension = strtolower( pathinfo( sanitize_file_name( $file['name'] ), PATHINFO_EXTENSION ) );
        if ( UPLOAD_ERR_OK !== $file['error'] || $file['size'] <= 0 || $file['size'] > $max_bytes || ! isset( $allowed[ $extension ] ) || ! is_uploaded_file( $file['tmp_name'] ) ) {
            wg_delete_paths( $stored );
            return new WP_Error( 'invalid_file' );
        }
        $finfo = function_exists( 'finfo_open' ) ? finfo_open( FILEINFO_MIME_TYPE ) : false;
        $mime  = $finfo ? finfo_file( $finfo, $file['tmp_name'] ) : $file['type'];
        if ( $finfo ) {
            finfo_close( $finfo );
        }
        if ( ! in_array( $mime, $allowed[ $extension ], true ) ) {
            wg_delete_paths( $stored );
            return new WP_Error( 'invalid_mime' );
        }
        $destination = trailingslashit( $directory ) . wp_generate_uuid4() . '.' . $extension;
        if ( ! move_uploaded_file( $file['tmp_name'], $destination ) ) {
            wg_delete_paths( $stored );
            return new WP_Error( 'move_failed' );
        }
        chmod( $destination, 0640 ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_chmod
        $stored[] = $destination;
    }
    return $stored;
}

function wg_delete_paths( $paths ) {
    foreach ( $paths as $path ) {
        if ( is_file( $path ) ) {
            wp_delete_file( $path );
        }
    }
}

function wg_cleanup_lead_files() {
    $directory = wg_private_upload_directory();
    $retention = min( 30, max( 1, (int) wg_get_setting( 'file_retention_days', 1 ) ) ) * DAY_IN_SECONDS;
    if ( ! is_dir( $directory ) ) {
        return;
    }
    $iterator = new DirectoryIterator( $directory );
    foreach ( $iterator as $file ) {
        if ( $file->isFile() && ! in_array( $file->getFilename(), array( 'index.php', '.htaccess' ), true ) && $file->getMTime() < time() - $retention ) {
            wp_delete_file( $file->getPathname() );
        }
    }
}
add_action( 'wg_cleanup_lead_files', 'wg_cleanup_lead_files' );
