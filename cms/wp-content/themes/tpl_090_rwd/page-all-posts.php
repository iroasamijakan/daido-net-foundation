<?php
/*
Template Name: 全投稿一覧
*/
get_header();
?>
<?php if (is_user_logged_in()): ?>
<style>
    @media screen and (max-width: 790px) {
        .category-section {
            margin-bottom: 80px;
        }
    }
</style>

<section>
    <header class="header">
        <h1 class="title ta-c"><span><?php the_title(); ?></span></h1>
    </header>

    <!-- ▼ コンクール応募者一覧 -->
    <?php
    // 対象の親カテゴリスラッグ（またはID）を指定
    $parent_slugs = array('audition', 'concours'); // 例: 親カテゴリのスラッグが "audition", "concours"

    foreach ($parent_slugs as $slug) {
        // 親カテゴリを取得
        $parent_cat = get_category_by_slug($slug);
        if (!$parent_cat) continue;

        // 子カテゴリを取得
        $child_cats = get_categories(array(
        'child_of'   => $parent_cat->term_id,
        'hide_empty' => true
        ));

        foreach ($child_cats as $child_cat) {
        // 投稿を取得
        $posts = get_posts(array(
            'post_type'      => 'post',
            'posts_per_page' => -1,
            'category'       => $child_cat->term_id,
            'orderby'        => 'date',
            'order'          => 'DESC',
        ));

        if ($posts) :
            ?>
            <div class="category-section">
                <h2 class="category-title ta-c mb30"><?php echo esc_html($child_cat->name); ?> 応募者一覧</h2>
                <ul class="col3">
                    <?php foreach ($posts as $post) : setup_postdata($post); ?>
                    <li>
                        <a href="<?php the_permalink(); ?>">
                            <div>
                                <div class="thumbnail">
                                <?php
                                    // YouTube ID を取得
                                    $youtube_id = SCF::get('youtubeid', get_the_ID());
                                    // サムネイル URL を取得
                                    $thumb_url = !empty($youtube_id) ? get_best_youtube_thumbnail($youtube_id) : '';

                                    if (!empty($thumb_url)) :
                                    ?>
                                        <img src="<?php echo esc_url($thumb_url); ?>" alt="YouTubeサムネイル" style="width:100%; height:auto;">
                                    <?php else : ?>
                                        <img src="<?php echo esc_url(get_theme_file_uri('/images/thumbnail.webp')); ?>" alt="<?php the_title(); ?>" style="width:100%; height:auto;">
                                    <?php endif; ?>

                                    <?php
                                    $post_id = get_the_ID();
                                    $item = SCF::get('item', $post_id);  // カスタムフィールド "item" の値を取得
                                    ?>
                                </div>
                                <h3><span><?php the_title(); ?></span><?php if (!empty($item)) : ?>／<span><?php echo esc_html($item); ?></span><?php endif; ?></h3>
                                <?php /* the_excerpt(); */ ?>
                            </div>
                        </a>
                    </li>
                    <?php endforeach; wp_reset_postdata(); ?>
                </ul>
            </div>
        <?php
        endif;
        }
    }
    ?>

    <!-- ▼ 奨学金応募者一覧 -->
    <?php
        $scholar_args = array(
            'post_type'      => 'scholar_list',
            'posts_per_page' => -1,
            'post_status'    => 'publish',
            'orderby'        => 'date',
            'order'          => 'DESC',
        );
        $scholar_query = new WP_Query($scholar_args);
    ?>
    <?php if ($scholar_query->have_posts()) : ?>
    <div class="category-section inner">
        <h2 class="category-title ta-c mb30">奨学金 応募者一覧</h2>
        <div class="post">
            <ul class="scholarship-list" style="width: auto;">
                <?php while ($scholar_query->have_posts()) : $scholar_query->the_post(); ?>
                    <li class="scholarship-item">
                        <a href="<?php the_permalink(); ?>">
                            <h3 class="ttl"><?php the_title(); ?></h3>
                            <?php
                            $school = get_post_meta(get_the_ID(), 'scholarship_school', true);
                            if (!empty($school)) {
                                echo '<p class="school-name">' . esc_html($school) . '</p>';
                            }
                            ?>
                        </a>
                    </li>
                <?php endwhile; ?>
            </ul>
        </div>
        <?php wp_reset_postdata(); ?>
    </div>
    <?php endif; ?>

</section>

<?php else: ?>
<section id="login-section">
    <div class="innerS">
        <p>閲覧するにはログインが必要です。</p>
        <div style="text-align:center; margin-top:30px;">
            <a href="<?php echo esc_url(wp_login_url(get_permalink())); ?>" class="btn">ログイン <i class="fa-solid fa-arrow-right" aria-hidden="true"></i></a>
        </div>
    </div>
</section>
<?php endif; ?>

<?php get_footer(); ?>
