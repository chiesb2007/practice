<?php

/**
 * 管理画面カスタマイズ
 */

/**
 * 特定の固定ページでビジュアルエディタを無効化
 */
add_filter('user_can_richedit', function ($default) {
    global $post;

    // 固定ページ以外は通常通り
    if (!$post || $post->post_type !== 'page') {
        return $default;
    }

    // スラッグで判定
    $disabled_pages = array('home', 'company', 'recruit');

    if (in_array($post->post_name, $disabled_pages)) {
        return false; // ビジュアルエディタ無効
    }

    return $default;
});

/**
 * 上記ページはコードエディタをデフォルトに
 */
add_filter('wp_default_editor', function () {
    global $post;

    if (!$post || $post->post_type !== 'page') {
        return 'tinymce';
    }

    $code_editor_pages = array('home', 'company', 'recruit');

    if (in_array($post->post_name, $code_editor_pages)) {
        return 'html';
    }

    return 'tinymce';
});

/**
 * 全ての固定ページでコードエディタをデフォルトに
 */
add_filter('wp_default_editor', function () {
    global $post;

    // 固定ページの場合はコードエディタをデフォルトに
    if ($post && $post->post_type === 'page') {
        return 'html';
    }

    return 'tinymce';
});

/**
 * 製品一覧のカラムをカスタマイズ
 */

// カラムを追加
add_filter('manage_product_posts_columns', 'add_product_columns');
function add_product_columns($columns)
{
    $new_columns = array();
    $new_columns['cb'] = $columns['cb'];
    $new_columns['thumbnail'] = 'アイキャッチ';
    $new_columns['title'] = $columns['title'];
    $new_columns['product_category'] = '製品カテゴリー';
    $new_columns['date'] = $columns['date'];
    return $new_columns;
}

// カラムの内容を表示
add_action('manage_product_posts_custom_column', 'display_product_columns', 10, 2);
function display_product_columns($column, $post_id)
{
    switch ($column) {
        case 'thumbnail':
            if (has_post_thumbnail($post_id)) {
                echo '<a href="' . get_edit_post_link($post_id) . '">';
                echo get_the_post_thumbnail($post_id, array(80, 200));
                echo '</a>';
            } else {
                echo '<span style="color:#ccc;">画像なし</span>';
            }
            break;

        case 'product_category':
            $terms = get_the_terms($post_id, 'product_category');
            if ($terms && !is_wp_error($terms)) {
                // 最も深い階層のカテゴリーを見つける
                $deepest_term = null;
                $max_depth = -1;

                foreach ($terms as $term) {
                    $depth = 0;
                    $parent_id = $term->parent;

                    while ($parent_id) {
                        $depth++;
                        $parent = get_term($parent_id, 'product_category');
                        $parent_id = $parent->parent;
                    }

                    if ($depth > $max_depth) {
                        $max_depth = $depth;
                        $deepest_term = $term;
                    }
                }

                // 階層を逆順で取得（親 → 子の順）
                $hierarchy = array();

                if ($deepest_term) {
                    // 最も深いカテゴリーから親を遡る
                    $current_term = $deepest_term;

                    while ($current_term) {
                        $term_link = '<a href="' . admin_url('edit.php?post_type=product&product_category=' . $current_term->slug) . '">' . esc_html($current_term->name) . '</a>';
                        array_unshift($hierarchy, $term_link); // 先頭に追加

                        if ($current_term->parent) {
                            $current_term = get_term($current_term->parent, 'product_category');
                        } else {
                            break;
                        }
                    }
                }

                echo !empty($hierarchy) ? implode(' > ', $hierarchy) : '—';
            } else {
                echo '—';
            }
            break;

        case 'product_code':
            $product_code = get_post_meta($post_id, 'product_code', true);
            echo $product_code ? esc_html($product_code) : '—';
            break;
    }
}

// カラムをソート可能にする
add_filter('manage_edit-product_sortable_columns', 'product_sortable_columns');
function product_sortable_columns($columns)
{
    $columns['product_category'] = 'product_category';
    return $columns;
}

/**
 * アプリケーション一覧のカラムをカスタマイズ
 */

// カラムを追加
add_filter('manage_application_posts_columns', 'add_application_columns');
function add_application_columns($columns)
{
    $new_columns = array();
    $new_columns['cb'] = $columns['cb'];
    $new_columns['application_image'] = '画像';
    $new_columns['title'] = $columns['title'];
    $new_columns['industry'] = '業種';
    $new_columns['date'] = $columns['date'];
    return $new_columns;
}

// カラムの内容を表示
add_action('manage_application_posts_custom_column', 'display_application_columns', 10, 2);
function display_application_columns($column, $post_id)
{
    switch ($column) {
        case 'application_image':
            $image = get_field('application_image', $post_id);
            if ($image) {
                echo '<a href="' . get_edit_post_link($post_id) . '">';
                echo '<img src="' . esc_url($image['url']) . '" alt="' . esc_attr($image['alt']) . '" style="width:80px;height:auto;">';
                echo '</a>';
            } else {
                echo '<span style="color:#ccc;">画像なし</span>';
            }
            break;

        case 'industry':
            $terms = get_the_terms($post_id, 'industry');
            if ($terms && !is_wp_error($terms)) {
                $term_links = array();
                foreach ($terms as $term) {
                    $term_links[] = '<a href="' . admin_url('edit.php?post_type=application&industry=' . $term->slug) . '">' . esc_html($term->name) . '</a>';
                }
                echo implode(', ', $term_links);
            } else {
                echo '—';
            }
            break;
    }
}

// カラムをソート可能にする
add_filter('manage_edit-application_sortable_columns', 'application_sortable_columns');
function application_sortable_columns($columns)
{
    $columns['industry'] = 'industry';
    return $columns;
}
