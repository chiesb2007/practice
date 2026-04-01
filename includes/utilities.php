<?php

/**
 * ユーティリティ機
 */

/**
 * パンくずリストを生成する関数
 */
function custom_breadcrumb()
{
    // ホームページでは表示しない
    if (is_home() || is_front_page()) {
        return;
    }

    $breadcrumb = '<nav class="breadcrumb">';
    $breadcrumb .= '<a href="' . home_url() . '">ホーム</a>';

    if (is_category() || is_single()) {
        if (is_single()) {
            $category = get_the_category();
            if ($category) {
                $breadcrumb .= ' > <a href="' . get_category_link($category[0]->term_id) . '">' . $category[0]->name . '</a>';
                $breadcrumb .= ' > <span>' . get_the_title() . '</span>';
            }
        } else {
            $breadcrumb .= ' > <span>' . single_cat_title('', false) . '</span>';
        }
    } elseif (is_page()) {
        $breadcrumb .= ' > <span>' . get_the_title() . '</span>';
    } elseif (is_search()) {
        $breadcrumb .= ' > <span>検索結果</span>';
    } elseif (is_404()) {
        $breadcrumb .= ' > <span>ページが見つかりません</span>';
    }

    $breadcrumb .= '</nav>';
    return $breadcrumb;
}

/**
 * 抜粋文字数を制限する関数
 */
function custom_excerpt($text, $length = 100, $more = '...')
{
    if (mb_strlen($text) > $length) {
        return mb_substr($text, 0, $length) . $more;
    }
    return $text;
}

/**
 * 画像のalt属性を自動設定
 */
function auto_image_alt($attr, $attachment, $size)
{
    if (empty($attr['alt'])) {
        $attr['alt'] = get_the_title($attachment->ID);
    }
    return $attr;
}
add_filter('wp_get_attachment_image_attributes', 'auto_image_alt', 10, 3);

/**
 * 投稿の前後ナビゲーションをカスタマイズ
 */
function custom_post_navigation()
{
    $prev_post = get_previous_post();
    $next_post = get_next_post();

    if (!$prev_post && !$next_post) {
        return;
    }

    echo '<nav class="post-navigation">';

    if ($prev_post) {
        echo '<div class="nav-previous">';
        echo '<a href="' . get_permalink($prev_post) . '" rel="prev">';
        echo '<span class="nav-subtitle">前の記事</span>';
        echo '<span class="nav-title">' . get_the_title($prev_post) . '</span>';
        echo '</a>';
        echo '</div>';
    }

    if ($next_post) {
        echo '<div class="nav-next">';
        echo '<a href="' . get_permalink($next_post) . '" rel="next">';
        echo '<span class="nav-subtitle">次の記事</span>';
        echo '<span class="nav-title">' . get_the_title($next_post) . '</span>';
        echo '</a>';
        echo '</div>';
    }

    echo '</nav>';
}

/**
 * 関連記事を取得する関数
 */
function get_related_posts($post_id = null, $limit = 3)
{
    if (!$post_id) {
        $post_id = get_the_ID();
    }

    $categories = get_the_category($post_id);
    if (empty($categories)) {
        return array();
    }

    $category_ids = array();
    foreach ($categories as $category) {
        $category_ids[] = $category->term_id;
    }

    $args = array(
        'post_type' => get_post_type($post_id),
        'posts_per_page' => $limit,
        'post__not_in' => array($post_id),
        'category__in' => $category_ids,
        'orderby' => 'rand'
    );

    return get_posts($args);
}

/**
 * 電話番号をリンク形式に変換
 */
function format_phone_link($phone)
{
    $clean_phone = preg_replace('/[^0-9+]/', '', $phone);
    return '<a href="tel:' . $clean_phone . '">' . $phone . '</a>';
}

/**
 * メールアドレスをリンク形式に変換
 */
function format_email_link($email)
{
    return '<a href="mailto:' . $email . '">' . $email . '</a>';
}

/**
 * 日付を日本語形式でフォーマット
 */
function format_japanese_date($date = null, $format = 'Y年n月j日')
{
    if (!$date) {
        $date = get_the_date('Y-m-d');
    }
    return date_i18n($format, strtotime($date));
}

/**
 * ページネーションを生成
 */
function custom_pagination($query = null)
{
    global $wp_query;

    if (!$query) {
        $query = $wp_query;
    }

    $total_pages = $query->max_num_pages;

    if ($total_pages <= 1) {
        return;
    }

    $current_page = max(1, get_query_var('paged'));

    echo '<nav class="pagination">';

    // 前のページ
    if ($current_page > 1) {
        echo '<a href="' . get_pagenum_link($current_page - 1) . '" class="page-link prev">前へ</a>';
    }

    // ページ番号
    for ($i = 1; $i <= $total_pages; $i++) {
        if ($i == $current_page) {
            echo '<span class="page-link current">' . $i . '</span>';
        } else {
            echo '<a href="' . get_pagenum_link($i) . '" class="page-link">' . $i . '</a>';
        }
    }

    // 次のページ
    if ($current_page < $total_pages) {
        echo '<a href="' . get_pagenum_link($current_page + 1) . '" class="page-link next">次へ</a>';
    }

    echo '</nav>';
}

/**
 * 投稿タイプのアーカイブURLを取得
 */
function get_post_type_archive_url($post_type)
{
    if (get_option('permalink_structure')) {
        return home_url('/' . $post_type . '/');
    } else {
        return add_query_arg('post_type', $post_type, home_url());
    }
}
/**
 * 現在のページがカスタム投稿タイプかチェック
 */
function is_custom_post_type($post_type = null)
{
    if ($post_type) {
        return get_post_type() === $post_type;
    }

    $post_type = get_post_type();
    return $post_type && !in_array($post_type, array('post', 'page', 'attachment'));
}

/**
 * 文字列をスラッグ化
 */
function sanitize_slug($string)
{
    return sanitize_title($string);
}

/**
 * 配列から特定のキーの値を抽出
 */
function pluck($array, $key)
{
    return array_map(function ($item) use ($key) {
        return is_object($item) ? $item->$key : $item[$key];
    }, $array);
}

/**
 * 配列をグループ化
 */
function group_by($array, $key)
{
    $result = array();
    foreach ($array as $item) {
        $group_key = is_object($item) ? $item->$key : $item[$key];
        $result[$group_key][] = $item;
    }
    return $result;
}

/**
 * 安全にHTMLを出力
 */
function safe_html($html, $allowed_tags = null)
{
    if ($allowed_tags === null) {
        $allowed_tags = array(
            'a' => array('href' => array(), 'title' => array(), 'target' => array()),
            'br' => array(),
            'em' => array(),
            'strong' => array(),
            'p' => array(),
            'span' => array('class' => array()),
            'div' => array('class' => array()),
        );
    }
    return wp_kses($html, $allowed_tags);
}

/**
 * URLが外部リンクかチェック
 */
function is_external_url($url)
{
    $home_url = home_url();
    return strpos($url, $home_url) !== 0 && filter_var($url, FILTER_VALIDATE_URL);
}

/**
 * 外部リンクに属性を追加
 */
function add_external_link_attributes($content)
{
    $content = preg_replace_callback(
        '/<a[^>]+href=["\']([^"\']+)["\'][^>]*>/i',
        function ($matches) {
            $url = $matches[1];
            if (is_external_url($url)) {
                // target="_blank" と rel="noopener noreferrer" を追加
                if (strpos($matches[0], 'target=') === false) {
                    $matches[0] = str_replace('<a ', '<a target="_blank" ', $matches[0]);
                }
                if (strpos($matches[0], 'rel=') === false) {
                    $matches[0] = str_replace('<a ', '<a rel="noopener noreferrer" ', $matches[0]);
                }
            }
            return $matches[0];
        },
        $content
    );
    return $content;
}

/**
 * 投稿の読了時間を計算
 */
function calculate_reading_time($post_id = null)
{
    if (!$post_id) {
        $post_id = get_the_ID();
    }

    $content = get_post_field('post_content', $post_id);
    $word_count = str_word_count(strip_tags($content));
    $reading_time = ceil($word_count / 200); // 1分間に200語として計算

    return max(1, $reading_time); // 最低1分
}

/**
 * 投稿の文字数を取得
 */
function get_post_character_count($post_id = null)
{
    if (!$post_id) {
        $post_id = get_the_ID();
    }

    $content = get_post_field('post_content', $post_id);
    $content = strip_tags($content);
    return mb_strlen($content);
}

/**
 * カスタムフィールドの値を安全に取得
 */
function get_safe_meta($post_id, $key, $default = '')
{
    $value = get_post_meta($post_id, $key, true);
    return !empty($value) ? $value : $default;
}

/**
 * 画像URLからalt属性を生成
 */
function generate_alt_from_filename($image_url)
{
    $filename = basename($image_url);
    $filename = pathinfo($filename, PATHINFO_FILENAME);
    $alt = str_replace(array('-', '_'), ' ', $filename);
    return ucwords($alt);
}


// 自動整形を無効化
remove_filter('the_content', 'wpautop');
function enable_wpautop_for_news($content)
{
    if (is_singular('news') || (is_home() && get_post_type() == 'news')) {
        return wpautop($content);
    }
    return $content;
}
add_filter('the_content', 'enable_wpautop_for_news');
