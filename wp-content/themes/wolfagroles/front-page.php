<?php
/**
 * Front page.
 *
 * @package Wolfagroles
 */

get_header();
$phone   = wolfagroles_setting( 'phone', '+7 495 221-83-13' );
$email   = wolfagroles_setting( 'email', 'savkinayus@wolfagro.ru' );
$address = wolfagroles_setting( 'address', '142101, Московская область, г. Подольск, ул. Шамотная, д. 6, строение 1' );
?>
<section class="hero">
    <div class="site-shell hero__grid">
        <div class="hero__copy">
            <p class="eyebrow">Подольск · Москва и Московская область</p>
            <h1>Передайте производственную задачу на проверку и расчёт</h1>
            <p class="hero__lead">ООО «Вольфагролес» принимает B2B-запросы с чертежами и спецификациями. Направьте исходные данные — компания проверит возможность выполнения и подготовит ответ по вашей задаче.</p>
            <div class="hero__actions">
                <a class="button" href="#quote">Запросить расчёт</a>
                <a class="button button--secondary" data-analytics-event="lead_phone_click" href="<?php echo esc_url( wolfagroles_phone_href() ); ?>"><?php echo esc_html( $phone ); ?></a>
            </div>
            <ul class="hero__facts" aria-label="Что можно передать">
                <li>Чертежи и спецификации</li>
                <li>Несколько файлов в одной заявке</li>
                <li>Контекст задачи и желаемый срок</li>
            </ul>
        </div>
        <aside class="hero__panel" aria-label="Данные для расчёта">
            <span class="panel-index">01</span>
            <h2>Что приложить</h2>
            <ol class="compact-steps">
                <li><span>01</span> Чертёж или спецификацию</li>
                <li><span>02</span> Материал и количество</li>
                <li><span>03</span> Требования и желаемый срок</li>
            </ol>
            <a class="text-link" href="<?php echo esc_url( home_url( '/trebovaniya-k-faylam/' ) ); ?>">Требования к файлам <span aria-hidden="true">→</span></a>
        </aside>
    </div>
</section>

<section class="section" aria-labelledby="directions-heading">
    <div class="site-shell">
        <div class="section-heading">
            <div>
                <p class="eyebrow">Направления</p>
                <h2 id="directions-heading">Подтверждённые возможности</h2>
            </div>
            <p>На сайте публикуются только направления, по которым заполнены характеристики, источник и дата проверки.</p>
        </div>
        <?php
        $services = new WP_Query(
            array(
                'post_type'      => 'service',
                'post_status'    => 'publish',
                'posts_per_page' => 6,
                'orderby'        => 'menu_order title',
                'order'          => 'ASC',
                'meta_query'     => array(
                    array(
                        'key'   => 'wg_fact_status',
                        'value' => 'confirmed',
                    ),
                ),
            )
        );
        ?>
        <?php if ( $services->have_posts() ) : ?>
            <div class="card-grid">
                <?php while ( $services->have_posts() ) : $services->the_post(); ?>
                    <?php get_template_part( 'template-parts/card', 'entity' ); ?>
                <?php endwhile; ?>
            </div>
            <?php wp_reset_postdata(); ?>
        <?php else : ?>
            <div class="empty-state">
                <p class="empty-state__title">Список возможностей проходит фактическую проверку</p>
                <p>Чтобы проверить конкретную операцию, материал или изделие, отправьте исходные файлы и описание задачи.</p>
                <a class="text-link" href="#quote">Передать задачу <span aria-hidden="true">→</span></a>
            </div>
        <?php endif; ?>
    </div>
</section>

<section class="section section--dark" aria-labelledby="process-heading">
    <div class="site-shell">
        <div class="section-heading section-heading--light">
            <div>
                <p class="eyebrow">Порядок работы</p>
                <h2 id="process-heading">От заявки до выдачи</h2>
            </div>
            <p>Фактические сроки и возможность выполнения подтверждаются после проверки документации.</p>
        </div>
        <ol class="process-grid">
            <li><span>01</span><strong>Заявка</strong><p>Контакты, описание, количество и файлы.</p></li>
            <li><span>02</span><strong>Проверка</strong><p>Анализ исходных данных и уточнение требований.</p></li>
            <li><span>03</span><strong>Расчёт</strong><p>Согласование состава работ, цены и срока.</p></li>
            <li><span>04</span><strong>Выполнение</strong><p>Работы по согласованной документации.</p></li>
            <li><span>05</span><strong>Контроль</strong><p>Проверка согласованных параметров результата.</p></li>
            <li><span>06</span><strong>Выдача</strong><p>Способ получения согласуется в заказе.</p></li>
        </ol>
    </div>
</section>

<section class="section section--mist" aria-labelledby="inputs-heading">
    <div class="site-shell split-layout">
        <div>
            <p class="eyebrow">Точный запрос</p>
            <h2 id="inputs-heading">Данные, которые ускоряют расчёт</h2>
            <p class="lead-copy">Чем точнее исходные данные, тем меньше уточнений потребуется перед коммерческим предложением.</p>
        </div>
        <dl class="definition-list">
            <div><dt>Геометрия</dt><dd>Чертёж, модель, эскиз или спецификация с размерами.</dd></div>
            <div><dt>Материал</dt><dd>Марка, вид заготовки и параметры, если они определены проектом.</dd></div>
            <div><dt>Объём</dt><dd>Количество единиц по каждой позиции и возможная повторяемость.</dd></div>
            <div><dt>Требования</dt><dd>Критичные размеры, контроль, покрытие, комплектность и упаковка.</dd></div>
        </dl>
    </div>
</section>

<section class="section" aria-labelledby="faq-heading">
    <div class="site-shell faq-layout">
        <div>
            <p class="eyebrow">Короткие ответы</p>
            <h2 id="faq-heading">Частые вопросы</h2>
            <a class="text-link" href="<?php echo esc_url( home_url( '/faq/' ) ); ?>">Все вопросы <span aria-hidden="true">→</span></a>
        </div>
        <div class="accordion-list">
            <?php if ( function_exists( 'wg_home_faq' ) ) : ?>
                <?php foreach ( wg_home_faq() as $item ) : ?>
                    <details>
                        <summary><?php echo esc_html( $item['question'] ); ?></summary>
                        <div><?php echo wp_kses_post( wpautop( $item['answer'] ) ); ?></div>
                    </details>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</section>

<section class="section section--dark quote-section" id="quote" aria-labelledby="quote-heading">
    <div class="site-shell quote-layout">
        <div>
            <p class="eyebrow">Заявка на расчёт</p>
            <h2 id="quote-heading">Передайте задачу и файлы</h2>
            <p>Укажите минимум один канал связи: телефон или e-mail. Ответ не содержит заранее обещанного SLA — срок обратной связи зависит от состава задачи.</p>
            <div class="contact-block">
                <a data-analytics-event="lead_phone_click" href="<?php echo esc_url( wolfagroles_phone_href() ); ?>"><?php echo esc_html( $phone ); ?></a>
                <a data-analytics-event="lead_email_click" href="mailto:<?php echo esc_attr( $email ); ?>"><?php echo esc_html( $email ); ?></a>
                <span><?php echo esc_html( $address ); ?></span>
            </div>
        </div>
        <div class="quote-card">
            <?php echo do_shortcode( '[wolfagroles_quote_form]' ); ?>
        </div>
    </div>
</section>
<?php get_footer(); ?>
