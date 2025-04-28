<?php
/*
Template Name: 全投稿一覧
*/
get_header();
$header_image = get_header_image();
/* if ($header_image): ?>
    <div id="mainImg">
        <img src="<?php header_image(); ?>" width="<?php echo HEADER_IMAGE_WIDTH; ?>" height="<?php echo HEADER_IMAGE_HEIGHT; ?>" alt="<?php bloginfo('description'); ?>">
    </div>
<?php endif; */ ?>

<section id="toppage">
    <header class="header">
        <h1 class="title"><span><?php the_title(); ?></span></h1>
    </header>
</section>

<!-- 全投稿一覧を表示 -->
<?php
$paged = (get_query_var('paged')) ? get_query_var('paged') : 1; // ページネーション対応
$args = array(
    'post_type'      => 'post', // 「投稿」を取得
    'posts_per_page' => 10, // 1ページに表示する投稿数
    'paged'          => $paged // ページネーション対応
);
$query = new WP_Query($args);

if ($query->have_posts()) : ?>
    <div class="post-list">
        <?php while ($query->have_posts()) : $query->the_post(); ?>
            <?php get_template_part('module_loop'); // ループ部分をモジュール化 ?>
        <?php endwhile; ?>
    </div>

    <!-- ページネーション -->
    <div class="pagination">
        <?php
        echo paginate_links(array(
            'total'     => $query->max_num_pages,
            'current'   => max(1, get_query_var('paged')),
            'prev_text' => '« 前へ',
            'next_text' => '次へ »',
        ));
        ?>
    </div>

<?php else : ?>
    <p>投稿が見つかりませんでした。</p>
<?php endif; ?>

<?php wp_reset_postdata(); ?>

<?php get_footer(); ?>
