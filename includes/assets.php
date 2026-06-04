<?php

/**
 * CSS/JS読み込み関連
 */

function my_child_theme_scripts()
{
    wp_enqueue_script(
        'child-main-js', // ハンドル名
        get_stylesheet_directory_uri() . '/assets/js/main.js', // パス
        array(), // 依存スクリプト（jQueryが必要なら array('jquery') など）
        null, // バージョン（省略可）
        true // フッターで読み込む
    );

    wp_enqueue_script(
        'fv-scroll-effect',
        get_stylesheet_directory_uri() . '/assets/js/fv-scroll-effect.js',
        array(),
        '1.0.0',
        true
    );

    wp_enqueue_script(
        'nordson',
        get_stylesheet_directory_uri() . '/assets/js/nordson.js',
        array(),
        '1.0.0',
        true
    );
}
add_action('wp_enqueue_scripts', 'my_child_theme_scripts');

// ページ別専用CSSを読み込む
function enqueue_page_specific_styles()
{
    // GeneratePressのメインCSSを明示的に読み込み（既に読み込まれている場合は重複しない）
    wp_enqueue_style(
        'generate-style',
        get_template_directory_uri() . '/assets/css/main.min.css'
    );

    // GeneratePressのwidget-areas CSSを明示的に読み込み
    wp_enqueue_style(
        'generate-widget-areas',
        get_template_directory_uri() . '/assets/css/components/widget-areas.min.css',
        array('generate-style'), // メインCSSに依存
        wp_get_theme()->get('Version')
    );

    // 共通CSS（GeneratePressの後に読み込み）
    wp_enqueue_style(
        'common-style',
        get_stylesheet_directory_uri() . '/assets/css/common.css',
        array('generate-style', 'generate-widget-areas'), // 両方のGeneratePress CSSに依存
        filemtime(get_stylesheet_directory() . '/assets/css/common.css')
    );

    // ページ別CSS（さらに後に読み込み）
    if (is_singular('news') || is_post_type_archive('news') || is_tax() || (is_archive() && get_post_type() === 'news')) {
        wp_enqueue_style(
            'news-style',
            get_stylesheet_directory_uri() . '/assets/css/news.css',
            array('common-style'),
            filemtime(get_stylesheet_directory() . '/assets/css/news.css')
        );
    }

    // 製品・アプリケーションページ
    if (
        is_singular('product') ||
        is_post_type_archive('product') ||
        is_tax('product_category') ||
        is_tax('industry') ||
        (is_archive() && get_post_type() === 'product') ||
        is_singular('application') ||
        is_post_type_archive('application') ||
        (is_archive() && get_post_type() === 'application') ||
        is_page_template('template-product.php')
    ) {
        wp_enqueue_style(
            'product-style',
            get_stylesheet_directory_uri() . '/assets/css/product.css',
            array('common-style'),
            filemtime(get_stylesheet_directory() . '/assets/css/product.css')
        );
    }
}
add_action('wp_enqueue_scripts', 'enqueue_page_specific_styles');

function add_fontawesome_6()
{
    wp_enqueue_style(
        'fontawesome-6',
        'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css',
        array(),
        '6.5.1'
    );
}
add_action('wp_enqueue_scripts', 'add_fontawesome_6');

function add_theme_image_paths()
{
    $theme_uri = get_template_directory_uri();

    $custom_css = "
        :root {
            --theme-url: '{$theme_uri}';
            --img-path: '{$theme_uri}/assets/img';
        }
    ";

    wp_add_inline_style('your-theme-style', $custom_css);
}
add_action('wp_enqueue_scripts', 'add_theme_image_paths');

/*テンプレートページ用のCSSを読み込む */
function add_policy_template_css()
{
    if (is_page_template('template-policy.php')) {
        wp_enqueue_style(
            'policy-common',
            get_stylesheet_directory_uri() . '/assets/css/policy-common.css',
            array(),
            '1.0.0'
        );
    }
}
add_action('wp_enqueue_scripts', 'add_policy_template_css', 20);  // ← 優先度を20に

// 郵便番号自動入力（YubinBango）
function add_yubinbango_script()
{
    wp_enqueue_script(
        'yubinbango',
        'https://yubinbango.github.io/yubinbango/yubinbango.js',
        array(),
        null,
        true
    );
}
add_action('wp_enqueue_scripts', 'add_yubinbango_script');
