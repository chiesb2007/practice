<?php

/**
 * カスタム投稿タイプ・タクソノミー関連
 */

/**
 * 製品カテゴリー保存時に親カテゴリも自動追加
 */
function auto_add_parent_product_category($post_id)
{
    // 自動保存時はスキップ
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }

    // 製品投稿タイプのみ
    if (get_post_type($post_id) !== 'product') {
        return;
    }

    // 現在のカテゴリー取得
    $terms = wp_get_post_terms($post_id, 'product_category', array('fields' => 'ids'));

    if (empty($terms) || is_wp_error($terms)) {
        return;
    }

    $all_terms = array();

    // 各タームの親を遡って追加
    foreach ($terms as $term_id) {
        $all_terms[] = $term_id;

        // 親カテゴリーを取得
        $parent_id = wp_get_term_taxonomy_parent_id($term_id, 'product_category');

        while ($parent_id) {
            if (!in_array($parent_id, $all_terms)) {
                $all_terms[] = $parent_id;
            }
            $parent_id = wp_get_term_taxonomy_parent_id($parent_id, 'product_category');
        }
    }

    // 重複削除して設定
    $all_terms = array_unique($all_terms);
    wp_set_post_terms($post_id, $all_terms, 'product_category');
}
add_action('save_post_product', 'auto_add_parent_product_category', 20);

/**
 * product_category タクソノミーアーカイブで post_type を設定
 */
function set_product_category_post_type($query)
{
    // 管理画面とメインクエリ以外は除外
    if (is_admin() || !$query->is_main_query()) {
        return;
    }

    // product_category タクソノミーアーカイブの場合
    if (is_tax('product_category')) {
        $query->set('post_type', 'product');
        $query->set('orderby', 'menu_order');
        $query->set('order', 'ASC');
    }
}
add_action('pre_get_posts', 'set_product_category_post_type');

/**
 * 製品アーカイブページの並び順を設定
 * 投稿日の新しい順で表示
 */
function modify_product_archive_query($query)
{
    if (!is_admin() && $query->is_main_query()) {
        if (is_tax('product_category')) {
            $query->set('orderby', 'date');
            $query->set('order', 'ASC');
        }
    }
}
add_action('pre_get_posts', 'modify_product_archive_query');
