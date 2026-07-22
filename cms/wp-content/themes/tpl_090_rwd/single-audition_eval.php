<?php
/**
 * Template Name: オーディション用single template
 * Description: 【csvインポート版】オーディション用のテンプレート
 * Created: 2026-02-10
 */
if (!is_user_logged_in()) {
    wp_redirect(wp_login_url(get_permalink()));
    exit;
}
get_header();?>
<div id="documents" class="posts-wrap">
<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
    <main id="post-<?php the_ID(); ?>" class="innerS">

        <?php
            // 年度・基本情報
            $years = get_the_terms(get_the_ID(), 'evaluation_year');
            $year_label = ($years) ? $years[0]->name . ' ' : '';
            $No = SCF::get('entryno');
            $furigana = SCF::get('furigana');
            $birth = SCF::get('birth');
            $age = SCF::get('age'); // 自動計算フィールド

            // 【常時表示：上に出してほしい項目】
            $display_fields = [
                '生年月日・年齢' => $birth . ' （' . $age . '）',
                '現在の所属' => SCF::get('affiliation'),
                '音楽についての学歴・師事歴' => SCF::get('education'),
                '経歴及び活動歴' => SCF::get('career'),
                '指揮者オーディション参加歴' => SCF::get('history'),
            ];
        ?>
            <header class="entry-header">
                <p class="entry-no">No.<?php echo esc_html($No); ?></p>
                <h1><?php the_title(); ?> <span class="kana">（<?php echo esc_html($furigana); ?>）</span></h1>
            </header>
            <div id="sec_review" class="profiles"></div>
            <div class="post">
                <div id="sec_review" class="profiles">
                    <table class="profile-item">
                        <?php foreach ($display_fields as $label => $value) : if($value): ?>
                            <tr style="border-bottom: 1px solid #eee;">
                                <th style="width:30%; padding:10px; text-align:left; background:#f9f9f9; border:1px solid #ddd;"><?php echo esc_html($label); ?></th>
                                <td style="padding:10px; border:1px solid #ddd;"><?php echo nl2br(esc_html($value)); ?></td>
                            </tr>
                        <?php endif; endforeach; ?>
                    </table>
                </div>

                <hr style="margin: 50px 0; border: 0; border-top: 2px double #ccc;">

                <div id="sec_private">
                    <div id="private_data_container">
                        <p style="text-align: center; color: #666; font-size: 0.9em;">※個人情報・詳細項目は非表示設定です。</p>
                    </div>

                    <div style="text-align: center; margin-top: 15px;">
                        <button id="load_private_data" class="btn-load-more" data-post-id="<?php the_ID(); ?>" >
                            その他の詳細情報を見る（SNS・受賞歴・選択曲など）
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
                                $(this).empty().html('<p style="text-align: center; color: #666; font-size: 0.9em;">※個人情報・詳細項目は非表示設定です。</p>').show();
                            });
                            $btn.removeClass('is-opened').text('その他の詳細情報を見る（SNS・受賞歴・選択曲など）').css('background-color', '#666');
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

                <div id="sec_evaluation" class="box" style="background: #fdfdfd; padding: 25px; border: 2px solid #efefef;">
                    <h3>評価入力</h3>
                    <?php
                    $current_user_id = get_current_user_id();
                    $current_post_id = get_the_ID();
                    $existing_eval_post = null;
                    if ($current_user_id) {
                        $existing = get_posts(array(
                            'post_type'      => 'evaluations_audition',
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
                    <?php echo do_shortcode('[contact-form-7 id="cff77ba" title="オーディション評価フォーム_v2"]'); ?>
                    <?php if ($existing_eval_post): ?>
                    <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        var score   = <?php echo json_encode(get_post_meta($existing_eval_post->ID, 'data_score', true)); ?>;
                        var comment = <?php echo json_encode(get_post_meta($existing_eval_post->ID, 'data_comment', true)); ?>;
                        var scoreEl   = document.querySelector('.wpcf7 input[name="score"]');
                        var commentEl = document.querySelector('.wpcf7 textarea[name="comment"]');
                        if (scoreEl && score)     scoreEl.value   = score;
                        if (commentEl && comment) commentEl.value = comment;
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
                'post_type'      => 'audition_eval', // オーディション応募者の箱
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
            <a href="<?php echo home_url('/audition-list/'); ?>">
                オーディション一覧に戻る <i class="fa-solid fa-arrow-right" aria-hidden="true"></i>
            </a>
        </h3>
    </aside>

<?php endwhile; endif; ?>

</div>

<?php get_footer(); ?>