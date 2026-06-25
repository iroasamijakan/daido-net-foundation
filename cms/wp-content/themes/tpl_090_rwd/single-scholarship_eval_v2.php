<?php
/**
 * Template Name: 奨学金用single template
 * Description: 【csvインポート版】奨学金用のテンプレート
 * Created: 2026-02-10
 */
if (!is_user_logged_in()) {
    wp_redirect(home_url('/scholarship/login'));
    exit;
}
get_header();?>
<div id="documents" class="posts-wrap">
<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
    <main id="post-<?php the_ID(); ?>" class="innerS">

        <?php
            // 年度タクソノミー取得
            $years = get_the_terms(get_the_ID(), 'evaluation_year');
            $year_label = ($years) ? $years[0]->name . ' ' : '';
            $No = SCF::get('entryno');
            $furigana = SCF::get('furigana');

            // 提出書類（郵送物）の取得用（後で判定に使用）
            $transcript = SCF::get('transcript');
            $transcript_url = is_numeric($transcript) ? wp_get_attachment_url($transcript) : $transcript;
            $recommendation = SCF::get('recommendation');
            $recommendation_url = is_numeric($recommendation) ? wp_get_attachment_url($recommendation) : $recommendation;

            // 【常時表示：上に出してほしい項目】
            $display_fields = [
                '現在の所属' => SCF::get('affiliation'),
                '現在の所属の入学・卒業見込み年月' => SCF::get('current_school_period'),
                '受賞歴' => SCF::get('awards'),
            ];
        ?>


        <header class="entry-header">
            <p class="entry-no">No.<?php echo esc_html($No); ?></p>
            <h1><?php the_title(); ?> <span class="kana">（<?php echo esc_html($furigana); ?>）</span></h1>
        </header>

    <div class="post">
        <div id="sec_review" class="profiles">
            <table class="profile-item">
                <?php foreach ($display_fields as $label => $value) : if($value): ?>
                    <tr>
                        <th><?php echo esc_html($label); ?></th>
                        <td><?php echo nl2br(esc_html($value)); ?></td>
                    </tr>
                <?php endif; endforeach; ?>
            </table>
        </div>

        <hr style="margin: 50px 0; border: 0; border-top: 2px double #ccc;">

        <div id="sec_private">
            <div id="private_data_container">
                <p style="text-align: center; color: #666; font-size: 0.9em;">※個人情報・志望動機等は非表示設定です。</p>
            </div>
            <div style="text-align: center; margin-top: 15px;">
                <button id="load_private_data" class="btn-load-more" data-post-id="<?php the_ID(); ?>">
                    詳細情報（顔写真・志望動機など）を見る
                </button>
            </div>
        </div>
        <script>
        jQuery(function($) {
            $('#load_private_data').on('click', function() {
                const $btn = $(this);
                const post_id = $btn.data('post-id');
                const $container = $('#private_data_container');

                if ($btn.hasClass('is-opened')) {
                    $container.fadeOut(function() {
                        $(this).empty().html('<p style="text-align: center; color: #666; font-size: 0.9em;">※個人情報・志望動機等は非表示設定です。</p>').show();
                    });
                    $btn.removeClass('is-opened').text('詳細情報（顔写真・志望動機など）を見る').css('background-color', '#666');
                    return;
                }

                $btn.prop('disabled', true).text('読み込み中...');

                $.ajax({
                    url: '<?php echo admin_url('admin-ajax.php'); ?>',
                    type: 'POST',
                    data: {
                        action: 'load_sec_private_data',
                        post_id: post_id
                    },
                    success: function(response) {
                        if (response.success) {
                            $container.hide().html(response.data).fadeIn();
                            $btn.prop('disabled', false).addClass('is-opened').text('情報を閉じる').css('background-color', '#999');
                        } else {
                            alert('データの取得に失敗しました。');
                            $btn.prop('disabled', false).text('再試行する');
                        }
                    }
                });
            });
        });
        </script>

        <hr style="margin: 50px 0; border: 0; border-top: 2px double #ccc;">

        <div class="document-check" style="margin: 30px 0; display: flex; gap: 10px; justify-content: center;">
            <?php if ($transcript_url): ?>
                <a href="<?php echo esc_url($transcript_url); ?>" target="_blank" style="background: #1e328b; color: #fff; padding: 10px 20px; border-radius: 3px; text-decoration: none;">
                    成績表
                </a>
            <?php else: ?>
                <span style="background: #ccc; color: #666; padding: 10px 20px; border-radius: 3px; cursor: not-allowed;">
                    成績表
                </span>
            <?php endif; ?>

            <?php if ($recommendation_url): ?>
                <a href="<?php echo esc_url($recommendation_url); ?>" target="_blank" style="background: #1e328b; color: #fff; padding: 10px 20px; border-radius: 3px; text-decoration: none;">
                    推薦書
                </a>
            <?php else: ?>
                <span style="background: #ccc; color: #666; padding: 10px 20px; border-radius: 3px; cursor: not-allowed;">
                    推薦書
                </span>
            <?php endif; ?>
        </div>

        <hr style="margin: 50px 0; border: 0; border-top: 2px double #ccc;">

        <div id="sec_evaluation" class="box" style="background: #fdfdfd; padding: 25px; border: 2px solid #efefef;">
            <h3>評価入力</h3>
            <?php
            $current_user_id = get_current_user_id();
            $current_post_id = get_the_ID();
            $existing_eval_post = null;
            if ($current_user_id) {
                $existing = get_posts(array(
                    'post_type'      => 'scholar_eval',
                    'posts_per_page' => 1,
                    'post_status'    => 'publish',
                    'meta_query'     => array(
                        'relation' => 'AND',
                        array('key' => 'user_id', 'value' => $current_user_id),
                        array('key' => 'evaluated_post_id', 'value' => $current_post_id),
                    ),
                    'orderby' => 'date',
                    'order'   => 'DESC',
                ));
                if ($existing) $existing_eval_post = $existing[0];
            }
            if ($existing_eval_post) {
                echo '<p style="color: #1a6e1a; font-weight: bold;">※この方の評価は入力済みです。内容を確認・修正して再送信できます。</p>';
            }
            ?>
            <p>評価をお願いいたします。</p>
            <?php echo do_shortcode('[contact-form-7 id="f95d9f2" title="奨学金評価評価フォーム"]'); ?>
            <?php if ($existing_eval_post): ?>
            <script>
            document.addEventListener('DOMContentLoaded', function() {
                var fields = {
                    'score_academic':    <?php echo json_encode(get_post_meta($existing_eval_post->ID, 'score_academic', true)); ?>,
                    'score_grades':      <?php echo json_encode(get_post_meta($existing_eval_post->ID, 'score_grades', true)); ?>,
                    'score_awards':      <?php echo json_encode(get_post_meta($existing_eval_post->ID, 'score_awards', true)); ?>,
                    'score_reason':      <?php echo json_encode(get_post_meta($existing_eval_post->ID, 'score_reason', true)); ?>,
                    'scholarship_comment': <?php echo json_encode(get_post_meta($existing_eval_post->ID, 'scholarship_comment', true)); ?>
                };
                Object.keys(fields).forEach(function(name) {
                    if (!fields[name]) return;
                    var el = document.querySelector('.wpcf7 input[name="' + name + '"], .wpcf7 textarea[name="' + name + '"]');
                    if (el) el.value = fields[name];
                });
            });
            </script>
            <?php endif; ?>
        </div>
    </div>
    </main>

    <aside id="related-posts">
        <?php
        // 現在の投稿の年（evaluation_year）を取得
        $terms = get_the_terms(get_the_ID(), 'evaluation_year');
        $current_year_term_id = ($terms && !is_wp_error($terms)) ? $terms[0]->term_id : null;
        $aside_title = ($terms) ? $terms[0]->name . ' 応募者リスト' : '応募者一覧';
        ?>
        
        <h3><?php echo esc_html($aside_title); ?></h3>
        <ul>
            <?php
            $args = array(
                'post_type'      => 'scholarship_eval_v2', // 奨学金応募者の箱
                'posts_per_page' => -1,                 // 全員表示
                'post_status'    => 'publish',
                'meta_key'       => 'entryno',          // エントリーNo順に並べる
                'orderby'        => 'meta_value_num',
                'order'          => 'ASC'
            );

            // 年度タグがある場合は、その年度の受験生だけに絞り込む
            if ($current_year_term_id) {
                $args['tax_query'] = array(
                    array(
                        'taxonomy' => 'evaluation_year',
                        'field'    => 'term_id',
                        'terms'    => $current_year_term_id,
                    ),
                );
            }

            $related_query = new WP_Query($args);

            if ($related_query->have_posts()) :
                while ($related_query->have_posts()) : $related_query->the_post();
                    // 現在表示している人のページには「current」クラスを付与
                    $is_current = (get_the_ID() === get_queried_object_id()) ? ' class="current"' : '';
                    $list_no = SCF::get('entryno');
                    $list_inst = SCF::get('instrument_played');
            ?>
                <li<?php echo $is_current; ?>>
                    <a href="<?php the_permalink(); ?>">
                        <?php if($list_no): ?>No.<?php echo esc_html($list_no); ?> <?php endif; ?>
                        <?php the_title(); ?>
                        <?php if($list_inst): ?> ／ <span><?php echo esc_html($list_inst); ?></span><?php endif; ?>
                    </a>
                </li>
            <?php 
                endwhile;
                wp_reset_postdata();
            else :
                echo '<li>データが見つかりません。</li>';
            endif; 
            ?>
        </ul>

        <h3 class="mt30">
            <a href="<?php echo home_url('/scholarship-list/'); ?>">
                奨学金応募者一覧に戻る <i class="fa-solid fa-arrow-right" aria-hidden="true"></i>
            </a>
        </h3>
    </aside>
<?php endwhile; endif; ?>
</div>

<?php get_footer(); ?>