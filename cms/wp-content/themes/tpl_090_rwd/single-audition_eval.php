<?php
/**
 * Template Name: オーディション用single template
 * Description: 【csvインポート版】オーディション用のテンプレート
 * Created: 2026-02-10
 */
get_header();?>
<div id="documents" class="posts-wrap">
<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
    <main id="post-<?php the_ID(); ?>" class="innerS">

        <?php
            // 年度タクソノミー取得
            $years = get_the_terms(get_the_ID(), 'evaluation_year');
            $year_label = ($years) ? $years[0]->name . ' ' : '';

            // フィールド取得
            $No = SCF::get('entryno');
            $furigana = SCF::get('furigana');
            $youtube_url = SCF::get('youtube_url'); 

            // YouTube ID抽出
            $youtubeid = '';
            if (preg_match('%(?:youtube\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/ ]{11})%i', $youtube_url, $match)) {
                $youtubeid = $match[1];
            }

            $display_fields = [
                '性別' => SCF::get('gender'),
                '生年月日' => SCF::get('birth'),
                '現在の所属' => SCF::get('affiliation'),
                '音楽学歴' => SCF::get('education'),
                '経歴及び活動暦' => SCF::get('career'),
                '受賞歴' => SCF::get('awards'),
                '演奏曲名' => SCF::get('performed_pieces'),
                '作曲者名' => SCF::get('composer_names'),
            ];
        ?>

        <header>
            <span class="category-name"><?php echo esc_html($year_label); ?>年オーディション</span>
            <?php if ($No): ?><p class="entry-no">エントリーNo：<?php echo esc_html($No); ?></p><?php endif; ?>
            <h1><?php the_title(); ?></h1>
            <?php if ($furigana): ?><span class="furigana-display">（<?php echo esc_html($furigana); ?>）</span><?php endif; ?>
        </header>

        <div class="post">
            <?php if ($youtubeid): ?>
                <div class="youtube-video">
                    <iframe src="https://www.youtube.com/embed/<?php echo esc_attr($youtubeid); ?>" frameborder="0" allowfullscreen></iframe>
                </div>
            <?php endif; ?>

            <div class="profiles">
                <?php foreach ($display_fields as $label => $value): if ($value): ?>
                    <div class="profile-item">
                        <h3><?php echo esc_html($label); ?></h3>
                        <p><?php echo nl2br(esc_html($value)); ?></p>
                    </div>
                <?php endif; endforeach; ?>
            </div>

            <hr style="margin: 50px 0; border: 0; border-top: 2px double #ccc;">

            <div class="box" style="background: #fdfdfd; padding: 25px; border: 2px solid #efefef;">
                <h3>選考委員用：評価フォーム</h3>
                <?php
                $current_user_id = get_current_user_id();
                $current_post_id = get_the_ID();
                if (function_exists('has_user_already_evaluated_audition') && has_user_already_evaluated_audition($current_user_id, $current_post_id)) {
                    echo '<p style="color: red; font-weight: bold;">※この方の評価は送信済みです。</p>';
                } else {
                    echo '<p>演奏の評価をお願いいたします。</p>';
                }
                echo do_shortcode('[contact-form-7 id="c5a8410" title="オーディション評価フォーム_v2"]');
                ?>
            </div>
        </div>
    </main>
    <aside id="related-posts">
        <?php
        // 現在の投稿の年（evaluation_year）を取得
        $terms = get_the_terms(get_the_ID(), 'evaluation_year');
        $current_year_term_id = ($terms && !is_wp_error($terms)) ? $terms[0]->term_id : null;
        $aside_title = ($terms) ? $terms[0]->name . ' 応募者リスト' : '応募者リスト';
        ?>

        <h3><?php echo esc_html($aside_title); ?></h3>
        <ul>
            <?php
            $args = array(
                'post_type'      => 'audition_eval', // オーディションの箱
                'posts_per_page' => -1,
                'post_status'    => 'publish',
                'meta_key'       => 'entryno',
                'orderby'        => 'meta_value_num',
                'order'          => 'ASC'
            );
            if ($current_year_term_id) {
                $args['tax_query'] = array(array('taxonomy' => 'evaluation_year','field' => 'term_id','terms' => $current_year_term_id));
            }
            $related_query = new WP_Query($args);
            if ($related_query->have_posts()) : while ($related_query->have_posts()) : $related_query->the_post();
                $is_current = (get_the_ID() === get_queried_object_id()) ? ' class="current"' : '';
            ?>
                <li<?php echo $is_current; ?>>
                    <a href="<?php the_permalink(); ?>">
                        No.<?php echo esc_html(SCF::get('entryno')); ?> <?php the_title(); ?>
                    </a>
                </li>
            <?php endwhile; wp_reset_postdata(); endif; ?>
        </ul>

        <h3 class="mt30">
            <a href="<?php echo home_url('/audition-list/'); ?>">
                オーディション一覧に戻る <i class="fa-solid fa-arrow-right" aria-hidden="true"></i>
            </a>
        </h3>
    </aside>
<?php endwhile; endif; ?>
</div>
<?php get_header();?>