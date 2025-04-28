<?php
/*
Template Name: 奨学金用ログイン画面
*/
get_header('scholarship');  // 専用ヘッダー
?>

<section class="innerS">
    <header><h1>奨学金審査 会員ログイン</h1><header>

<?php
if ( is_user_logged_in() ) {
    $user_id = get_current_user_id();
    $categories = get_user_meta($user_id, 'user_category', true);

    if ( is_array($categories) && in_array('scholarship', $categories) ) {
        // ✅ 奨学金担当ユーザーの場合
        echo '<div class="post">';
        echo do_shortcode('[wpmem_form login]');
        echo '<a href="' . home_url('/scholarship/list') . '" class="btn">奨学金応募者一覧はこちら <i class="fa-solid fa-arrow-right" aria-hidden="true"></i></a>';
        echo '</div>';

    } else {
        // ❌ ログイン済みだが対象外
        echo '<p class="mb30">こちらは奨学金審査用サイトです。<br>お手数ですが、ユーザー情報に間違いがないか、運営に問い合わせください。</p>';
        echo '<p class="mb30 ta-c"><a href="' . home_url('/') . '" class="btn">コンクール・オーディションの<br class="pc-none">審査サイトはこちら <i class="fa-solid fa-arrow-right" aria-hidden="true"></i></a></p>';
        echo do_shortcode('[wpmem_form login]');
    }

} else {
    // 未ログインならログインフォームを表示
    echo do_shortcode('[wpmem_form login]');
}
?>
</section>

<?php get_footer('scholarship'); ?>
