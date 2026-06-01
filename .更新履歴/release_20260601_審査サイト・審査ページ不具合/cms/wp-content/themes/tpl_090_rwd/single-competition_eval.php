<?php
/**
 * Template Name: コンクール用single template
 * Description: 【csvインポート版】コンクール用のテンプレート
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
            // 年度タクソノミー取得
            $years = get_the_terms(get_the_ID(), 'evaluation_year');
            $year_label = ($years) ? $years[0]->name . ' ' : '';

            // フィールド取得
            $No = SCF::get('entryno'); // エントリーNo
            $instrument = SCF::get('instrument_played'); // 演奏楽器
            $furigana = SCF::get('furigana'); // フリガナ
            $youtube_url = SCF::get('youtube_url'); // YouTube URL
            
            // YouTube ID抽出
            $youtubeid = '';
            if (preg_match('%(?:youtube\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/ ]{11})%i', $youtube_url, $match)) {
                $youtubeid = $match[1];
            }

            $display_fields = [
                '最終学歴' => SCF::get('performance_time'),
                '現在の所属' => SCF::get('affiliation'),
                '⾳楽についての学歴・師事歴' => SCF::get('education'),                
                '経歴及び活動暦' => SCF::get('career'),
                '受賞歴' => SCF::get('awards'),
                '演奏曲名' => SCF::get('performed_pieces'),
                '作曲者名' => SCF::get('composer_names'),
                '演奏時間' => SCF::get('additional_info'),
            ];
        ?>

        <header>
            <p class="entry-no">
                <span>[<?php echo esc_html($year_label); ?>年コンクール] </span>
                <?php if ($No): ?>エントリーNo：<?php echo esc_html($No); ?><?php endif; ?>
            </p>
            <h1>
                <?php the_title(); ?>
                <?php if ($furigana): ?><span class="furigana-display">（<?php echo esc_html($furigana); ?>）</span><?php endif; ?>
                ／
                <?php if ($instrument): ?><span style="font-size: 0.8em;"><?php echo esc_html($instrument); ?></span><?php endif; ?>
            </h1>
        </header>

        <div class="post">
            <?php if ($youtubeid): ?>
                <div class="youtube-video">
                    <iframe src="https://www.youtube.com/embed/<?php echo esc_attr($youtubeid); ?>" frameborder="0" allowfullscreen></iframe>
                </div>
            <?php endif; ?>

            <div id="sec_review" class="profiles">
                <table class="profile-item">
                <?php foreach ($display_fields as $label => $value): if ($value): ?>
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
                    <p style="text-align: center; color: #666;">※個人情報（住所・連絡先等）は保護のため初期状態では非表示です。</p>
                </div>
                <div style="text-align: center; margin-top: 15px;">
                    <button id="load_private_data" class="btn-load-more" data-post-id="<?php the_ID(); ?>">
                        個人詳細情報を読み込む（住所・連絡先等）
                    </button>
                </div>
            </div>
            <script>
            jQuery(function($) {
                $('#load_private_data').on('click', function() {
                    const $btn = $(this);
                    const post_id = $btn.data('post-id');
                    const $container = $('#private_data_container');

                    // すでにデータが表示されている場合は「閉じる」挙動
                    if ($btn.hasClass('is-opened')) {
                        $container.fadeOut(function() {
                            $(this).empty().show(); // HTMLを空にして表示状態をリセット
                            $container.html('<p style="text-align: center; color: #666;">※個人情報（住所等）は保護のため初期状態では非表示です。</p>');
                        });
                        $btn.removeClass('is-opened').text('個人詳細情報を読み込む（住所・連絡先等）');
                        $btn.css('background-color', ''); // 元の色に戻す
                        return;
                    }

                    // 読み込み中の表示
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
                                $btn.prop('disabled', false)
                                    .addClass('is-opened')
                                    .text('詳細情報を閉じる');
                                $btn.css('background-color', '#999'); // 閉じるボタンっぽく色を変える（任意）
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
                if (function_exists('has_user_already_evaluated') && has_user_already_evaluated(get_current_user_id(), get_the_ID())) {
                    echo '<p style="color: red; font-weight: bold;">※この方の評価は送信済みです。</p>';
                }
                ?>
                <p>演奏の評価をお願いいたします。</p>
                <?php //テスト用コード echo do_shortcode('[contact-form-7 id="0117d3b" title="コンクール評価フォーム"]'); ?>
                <?php echo do_shortcode('[contact-form-7 id="702a10d" title="コンクール評価フォーム"]'); ?>
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
                'post_type'      => 'competition_eval', // コンクール応募者の箱
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
            <a href="<?php echo home_url('/competition-list/'); ?>">
                コンクール一覧に戻る <i class="fa-solid fa-arrow-right" aria-hidden="true"></i>
            </a>
        </h3>
    </aside>

<?php endwhile; endif; ?>
</div>

<?php get_footer(); ?>