<?php

/**
 * ショートコード
 */

/**
 * お問い合わせセクションのショートコード
 */
/*お問い合わせセクションのショートコード化（多言語対応）*/
function custom_contact_section_shortcode()
{
    ob_start();

    // 現在の言語を取得
    $current_lang = function_exists('pll_current_language') ? pll_current_language() : 'ja';

    // 言語別のテキストとリンク
    if ($current_lang === 'en') {
        $section_title = 'Contact';
        $main_title = 'Contact Us';
        $catalog_text = 'Download Catalogs';
        $contact_text = 'Contact Form';
        $office_text = 'Office Information';
        $catalog_link = '/catalogs-en';
        $contact_link = '/contactus';
        $company_link = '/company-en/#locations';
    } else {
        $section_title = 'Contact';
        $main_title = 'お問い合わせ';
        $catalog_text = 'カタログのダウンロード';
        $contact_text = 'お問い合わせフォームへ';
        $office_text = '各事業所の連絡先へ';
        $catalog_link = '/catalogs';
        $contact_link = '/contact';
        $company_link = '/company/#locations';
    }
?>
    <section id="contact" class="contact text-white py-5 pl-10 back-gradient">
        <div class="px-5">
            <h2 class="company-english-title f20em">
                <span class="js-english-scroll f10em"><?php echo $section_title; ?></span>
            </h2>
            <h2 class="js-text-fade"><?php echo $main_title; ?></h2>
            <div class="grid grid-3 mt-3">
                <a href="<?php echo $catalog_link; ?>" class="contact-card">
                    <div class="contact-card-inner">
                        <img src="<?php echo get_stylesheet_directory_uri(); ?>/assets/img/download-blue.png" alt="<?php echo $catalog_text; ?>">
                        <span><?php echo $catalog_text; ?></span>
                        <i class="fa-solid fa-arrow-right"></i>
                    </div>
                </a>
                <a href="<?php echo $contact_link; ?>" class="contact-card">
                    <div class="contact-card-inner">
                        <img src="<?php echo get_stylesheet_directory_uri(); ?>/assets/img/mail-blue.png" alt="<?php echo $contact_text; ?>">
                        <span><?php echo $contact_text; ?></span>
                        <i class="fa-solid fa-arrow-right"></i>
                    </div>
                </a>
                <a href="<?php echo $company_link; ?>" class="contact-card">
                    <div class="contact-card-inner">
                        <img src="<?php echo get_stylesheet_directory_uri(); ?>/assets/img/phone-blue.png" alt="<?php echo $office_text; ?>">
                        <span><?php echo $office_text; ?></span>
                        <i class="fa-solid fa-arrow-right"></i>
                    </div>
                </a>
            </div>
        </div>
    </section>
<?php
    return ob_get_clean();
}
add_shortcode('custom_contact_section', 'custom_contact_section_shortcode');

/**
 * テーマディレクトリURLのショートコード
 */
function theme_url_shortcode()
{
    return get_stylesheet_directory_uri();
}
add_shortcode('theme_url', 'theme_url_shortcode');

/**
 * サイトURLのショートコード
 */
function site_url_shortcode()
{
    $url = home_url();
    // /en, /ja などの言語プレフィックスを除去
    $url = preg_replace('/\/(en|ja|fr|de|es|it|zh|ko)$/', '', $url);
    return esc_url($url);
}
add_shortcode('site_url', 'site_url_shortcode');

/**
 * 現在の年を表示するショートコード
 */
function current_year_shortcode()
{
    return date('Y');
}
add_shortcode('current_year', 'current_year_shortcode');

/**
 * 会社情報のショートコード
 */
function company_info_shortcode($atts)
{
    $atts = shortcode_atts(array(
        'info' => 'name' // name, address, phone, email など
    ), $atts);

    $company_info = array(
        'name' => '株式会社サンプル',
        'address' => '〒123-4567 東京都渋谷区サンプル1-2-3',
        'phone' => '03-1234-5678',
        'email' => 'info@example.com'
    );

    return isset($company_info[$atts['info']]) ? $company_info[$atts['info']] : '';
}
add_shortcode('company_info', 'company_info_shortcode');

/**
 * ボタンのショートコード
 */
function custom_button_shortcode($atts, $content = null)
{
    $atts = shortcode_atts(array(
        'url' => '#',
        'target' => '_self',
        'class' => 'btn-primary',
        'icon' => ''
    ), $atts);

    $icon_html = '';
    if (!empty($atts['icon'])) {
        $icon_html = '<i class="' . esc_attr($atts['icon']) . '"></i> ';
    }

    return sprintf(
        '<a href="%s" target="%s" class="btn %s">%s%s</a>',
        esc_url($atts['url']),
        esc_attr($atts['target']),
        esc_attr($atts['class']),
        $icon_html,
        do_shortcode($content)
    );
}
add_shortcode('button', 'custom_button_shortcode');

/**
 * 製品カテゴリー一覧のショートコード
 */
function product_categories_shortcode($atts)
{
    $atts = shortcode_atts(array(
        'parent' => 0,
        'hide_empty' => true,
        'number' => 0
    ), $atts);

    $args = array(
        'taxonomy' => 'product_category',
        'parent' => $atts['parent'],
        'hide_empty' => $atts['hide_empty'],
        'number' => $atts['number']
    );

    $categories = get_terms($args);

    if (empty($categories) || is_wp_error($categories)) {
        return '';
    }

    ob_start();
?>
    <div class="product-categories-grid">
        <?php foreach ($categories as $category) : ?>
            <div class="category-item">
                <a href="<?php echo get_term_link($category); ?>" class="category-link">
                    <h3 class="category-name"><?php echo esc_html($category->name); ?></h3>
                    <p class="category-count"><?php echo $category->count; ?>件</p>
                </a>
            </div>
        <?php endforeach; ?>
    </div>
<?php
    return ob_get_clean();
}
add_shortcode('product_categories', 'product_categories_shortcode');
