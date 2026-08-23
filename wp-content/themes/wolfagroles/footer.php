<?php
/**
 * Site footer.
 *
 * @package Wolfagroles
 */
?>
</main>
<footer class="site-footer">
    <div class="site-shell footer-grid">
        <section>
            <p class="footer-kicker">ООО «Вольфагролес»</p>
            <p><?php echo esc_html( wolfagroles_setting( 'address', '142101, Московская область, г. Подольск, ул. Шамотная, д. 6, строение 1' ) ); ?></p>
        </section>
        <section>
            <p class="footer-title">Контакты</p>
            <p><a data-analytics-event="lead_phone_click" href="<?php echo esc_url( wolfagroles_phone_href() ); ?>"><?php echo esc_html( wolfagroles_setting( 'phone', '+7 495 221-83-13' ) ); ?></a></p>
            <p><a data-analytics-event="lead_email_click" href="mailto:<?php echo esc_attr( wolfagroles_setting( 'email', 'savkinayus@wolfagro.ru' ) ); ?>"><?php echo esc_html( wolfagroles_setting( 'email', 'savkinayus@wolfagro.ru' ) ); ?></a></p>
        </section>
        <section>
            <p class="footer-title">Документы</p>
            <ul class="footer-links">
                <li><a href="<?php echo esc_url( home_url( '/rekvizity/' ) ); ?>">Реквизиты</a></li>
                <li><a href="<?php echo esc_url( home_url( '/politika-konfidencialnosti/' ) ); ?>">Политика конфиденциальности</a></li>
                <li><a href="<?php echo esc_url( home_url( '/soglasie-na-obrabotku-personalnyh-dannyh/' ) ); ?>">Согласие на обработку данных</a></li>
                <li><a href="<?php echo esc_url( home_url( '/karta-sayta/' ) ); ?>">Карта сайта</a></li>
            </ul>
        </section>
    </div>
    <div class="site-shell footer-bottom">
        <span>© <?php echo esc_html( gmdate( 'Y' ) ); ?> ООО «Вольфагролес»</span>
        <span>ОГРН 1037700057625 · ИНН 7701037940</span>
    </div>
</footer>
<?php wp_footer(); ?>
</body>
</html>
