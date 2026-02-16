<?php
/**
 * Template Name: 奨学金用single template
 * Description: 【csvインポート版】奨学金用のテンプレート
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

            // 基本情報の抽出
            $No = SCF::get('entryno'); 
            $instrument = SCF::get('instrument_played'); 
            $furigana = SCF::get('furigana');
            $youtube_url = SCF::get('youtube_url'); 
            $transcript = SCF::get('transcript');
            $recommendation = SCF::get('recommendation');
            
            // YouTube ID抽出
            $youtubeid = '';
            if (preg_match('%(?:youtube\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/ ]{11})%i', $youtube_url, $match)) {
                $youtubeid = $match[1];
            }

            // 奨学金専用の表示フィールド設定
            $display_fields = [
                '性別' => SCF::get('gender'),
                '生年月日' => SCF::get('birth'),
                '年齢' => SCF::get('age'),
                //'現住所' => SCF::get('address'),
                'SNS' => SCF::get('sns'),
                '現在の所属' => SCF::get('affiliation'),
                'その他の経歴' => SCF::get('career'),
                '受賞歴' => SCF::get('awards'),
                '演奏曲名' => SCF::get('performed_pieces'),
                '作曲者名' => SCF::get('composer_names'),
                '志望動機' => SCF::get('reasons'),
                '大学進学の目的' => SCF::get('purpose'),
                '特技' => SCF::get('skills'),
                //'認知経路' => SCF::get('how_did_you_know'),
                //'紹介者' => SCF::get('referrers_name'),
            ];
        ?>

        <header>
            <span class="category-name"><?php echo esc_html($year_label); ?>奨学金 申請者</span>
            <?php if ($No): ?><p class="entry-no">申請No：<?php echo esc_html($No); ?></p><?php endif; ?>
            <h1><?php the_title(); ?> <?php if ($instrument): ?> ／ <span style="font-size: 0.8em;"><?php echo esc_html($instrument); ?></span><?php endif; ?></h1>
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
            <?php if (!empty($transcript)): ?>
                <p><a href="<?php echo wp_get_attachment_url($transcript); ?>" style="background: #1e328b;color: #fff;padding: 1rem; margin-bottom:1rem;">成績表を見る ></a></p>
            <?php endif; ?>
            <?php if (!empty($recommendation)): ?>
                <p><a href="<?php echo wp_get_attachment_url($recommendation); ?>" style="background: #1e328b;color: #fff;padding: 1rem;">推薦書を見る ></a></p>
            <?php endif; ?>

            <hr style="margin: 50px 0; border: 0; border-top: 2px double #ccc;">

            <div class="box" style="background: #fdfdfd; padding: 25px; border: 2px solid #efefef;">
                <h3>選考委員用：奨学金評価フォーム</h3>
                <?php
                if (function_exists('has_user_already_scholar_evaluated') && has_user_already_scholar_evaluated(get_current_user_id(), get_the_ID())) {
                    echo '<p style="color: red; font-weight: bold;">※この方の評価は送信済みです。</p>';
                }
                ?>
                <p>各項目の評価（点数・コメント）をお願いいたします。</p>
                <?php echo do_shortcode('[contact-form-7 id="0e2c1ac" title="奨学金評価評価フォーム"]'); ?>
            </div>
        </div>
    </main>

    <aside id="related-posts">
        <h3><?php echo esc_html($year_label); ?> 申請者一覧</h3>
        <ul>
            <?php
            $args = array(
                'post_type'      => 'scholarship_eval_v2',
                'posts_per_page' => -1,
                'post_status'    => 'publish',
                'meta_key'       => 'entryno',
                'orderby'        => 'meta_value_num',
                'order'          => 'ASC'
            );
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
            <a href="<?php echo home_url('/scholarship-list/'); ?>">
                奨学金申請者一覧に戻る <i class="fa-solid fa-arrow-right" aria-hidden="true"></i>
            </a>
        </h3>
    </aside>
<?php endwhile; endif; ?>
</div>
<?php get_footer(); ?>