<?php
/*
Template Name: 【分岐版】最新年度応募者一覧
*/
get_header();

// 1. 固定ページのスラッグを取得して、表示対象を判定
$current_slug = get_post_field('post_name', get_the_ID());

$target_post_type = '';
$label = '';

// --- スラッグによる条件分岐の修正 ---
if (strpos($current_slug, 'competition-list') !== false) {
    $target_post_type = 'competition_eval'; // コンクールの箱
    $label = 'コンクール';
} elseif (strpos($current_slug, 'audition-list') !== false) {
    $target_post_type = 'audition_eval';    // オーディションの箱
    $label = 'オーディション';
} elseif (strpos($current_slug, 'scholarship-list') !== false) {
    $target_post_type = 'scholarship_eval_v2'; // 奨学金の箱
    $label = '奨学金';
}

// 2. 最新の年度（2026年度など）を取得
$latest_year_term = get_terms(array(
    'taxonomy' => 'evaluation_year',
    'orderby'  => 'name',
    'order'    => 'DESC',
    'number'   => 1,
    'hide_empty' => true
));
$year_label = (!empty($latest_year_term)) ? $latest_year_term[0]->name : '';
$year_id = (!empty($latest_year_term)) ? $latest_year_term[0]->term_id : null;
?>

<section id="all-posts-list">
    <header class="header">
        <h1 class="title ta-c">
            <span><?php echo esc_html($year_label); ?> <?php echo esc_html($label); ?>応募者一覧</span>
        </h1>
    </header>

    <?php 
    if ($target_post_type) :
        $args = array(
            'post_type'      => $target_post_type,
            'posts_per_page' => -1,
            'post_status'    => 'publish',
            'meta_key'       => 'entryno',
            'orderby'        => 'meta_value_num',
            'order'          => 'ASC',
        );

        // 最新年度に絞り込み
        if ($year_id) {
            $args['tax_query'] = array(array(
                'taxonomy' => 'evaluation_year',
                'field'    => 'term_id',
                'terms'    => $year_id,
            ));
        }

        $query = new WP_Query($args);
        if ($query->have_posts()) : ?>
            <ul class="col3">
            <?php while ($query->have_posts()) : $query->the_post(); 
                $p_id = get_the_ID();
                $youtube_url = SCF::get('youtube_url', $p_id);
                
                // YouTubeサムネイルID抽出
                $thumb_url = '';
                if (preg_match('%(?:youtube\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/ ]{11})%i', $youtube_url, $match)) {
                    $thumb_url = "https://img.youtube.com/vi/{$match[1]}/mqdefault.jpg";
                } else {
                    $thumb_url = get_theme_file_uri('/images/thumbnail.webp');
                }
            ?>
                <li>
                    <a href="<?php the_permalink(); ?>">
                        <div class="thumbnail">
                            <img src="<?php echo esc_url($thumb_url); ?>" alt="" style="width:100%; aspect-ratio:16/9; object-fit:cover;">
                        </div>
                        <h3>
                            <span class="no">No.<?php echo esc_html(SCF::get('entryno', $p_id)); ?></span>
                            <span class="name"><?php the_title(); ?></span>
                            <br><small class="inst"><?php echo esc_html(SCF::get('instrument_played', $p_id)); ?></small>
                        </h3>
                    </a>
                </li>
            <?php endwhile; ?>
            </ul>
        <?php else: ?>
            <p class="ta-c">現在、表示できるデータがありません。</p>
        <?php endif; wp_reset_postdata(); ?>
    <?php endif; ?>
</section>

<?php get_footer(); ?>