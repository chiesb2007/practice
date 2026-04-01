<?php

/**
 * フロントエンド機能
 */

// カスタムハンバーガーメニューをヘッダーに追加
function add_custom_hamburger_menu()
{
?>
    <!-- 言語スイッチャー（スマホ用） -->
    <div class="lang-switcher-mobile">
        <?php
        if (function_exists('pll_the_languages')) {
            $languages = pll_the_languages(array('raw' => 1));
            if (!empty($languages)) {
                foreach ($languages as $lang) {
                    $current_class = $lang['current_lang'] ? ' current-lang' : '';
                    printf(
                        '<a href="%s" class="lang-link%s">%s</a>',
                        esc_url($lang['url']),
                        $current_class,
                        esc_html(strtoupper($lang['slug']))
                    );
                }
            }
        }
        ?>
    </div>

    <!-- ハンバーガーボタン -->
    <div class="hamb animation" id="hamb">
        <span class="line animation"></span>
        <span class="line animation"></span>
        <span class="line animation"></span>
    </div>

    <!-- オーバーレイメニュー -->
    <div class="black-bg animation" id="black-bg">
        <nav class="overlay">
            <?php
            wp_nav_menu(array(
                'theme_location' => 'primary',
                'menu_class' => '',
                'container' => false,
                'fallback_cb' => false
            ));
            ?>
        </nav>
    </div>
<?php
}
add_action('generate_after_header', 'add_custom_hamburger_menu');

// お知らせ専用サイドバーを登録
function register_news_sidebar()
{
    register_sidebar(array(
        'name'          => 'お知らせサイドバー',
        'id'            => 'news-sidebar',
        'description'   => 'お知らせページ用のサイドバー',
        'before_widget' => '<div class="widget %2$s">',
        'after_widget'  => '</div>',
        'before_title'  => '<h3 class="widget-title">',
        'after_title'   => '</h3>',
    ));
}
add_action('widgets_init', 'register_news_sidebar');

/*ローディング */
function add_loading_screen()
{
?>
    <div class="loading-screen">
        <div class="loading-container">
            <div class="loading-blur-circle"></div>
            <div class="loading-grade-circle"></div>
            <div class="loading-coating"></div>
            <div class="loading-content">
                <div class="loading-logo">IEI</div>
                <div class="loading-text">
                    <span class="loading-dots"><span>.</span><span>.</span><span>.</span></span>
                </div>
            </div>
        </div>
    </div>
<?php
}
add_action('wp_body_open', 'add_loading_screen');
