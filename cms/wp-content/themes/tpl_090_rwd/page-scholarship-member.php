<?php
/*
Template Name: 奨学金用会員プロフィール画面
*/
get_header('scholarship');
?>

<section class="innerS">
    <header><h1>奨学金審査 会員ページ</h1></header>

    <?php
    if ( is_user_logged_in() ) {
        $user_id = get_current_user_id();
        $user_categories = get_user_meta($user_id, 'user_category', true);

        if ( is_array($user_categories) && in_array('scholarship', $user_categories) ) {
            // ✅ 奨学金担当者の場合
            echo '<div class="post">';
            // WP-Members プロフィール表示
            echo do_shortcode('[wpmem_profile]');
            // 案内リンク（例）
            echo '<p class="mt30"><a href="'. home_url('/scholarship/') . '" class="btn">奨学金審査トップページへ <i class="fa-solid fa-arrow-right" aria-hidden="true"></i></a></p>';
            echo '</div>';

        } else {
            // ❌ 担当外ユーザー
            echo '<p class="mb30">こちらは奨学金審査用サイトです。<br>お手数ですが、ユーザー情報に間違いがないか、運営に問い合わせください。</p>';
            echo '<p class="mb30 ta-c"><a href="' . home_url('/') . '" class="btn">コンクールおよびオーディションの<br class="pc-none">審査サイトはこちら <i class="fa-solid fa-arrow-right" aria-hidden="true"></i></a></p>';
            echo do_shortcode('[wpmem_form login]');
        }

    } else {
        // 未ログイン時：ログインフォーム表示
        echo do_shortcode('[wpmem_form login]');
    }
    ?>

</section>

<?php get_footer('scholarship'); ?>
