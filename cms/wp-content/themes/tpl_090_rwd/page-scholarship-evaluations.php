<?php
/*
Template Name: 奨学金評価一覧
*/
get_header();  // 専用ヘッダー → get_header('scholarship')
?>
<link rel="stylesheet" href="https://unpkg.com/scroll-hint@latest/css/scroll-hint.css">
<style>
    .header { margin-bottom: 40px; }
    .header h1 { font-size: 120%; text-align: center; }
</style>


<!-- ユーザー情報を取得 -->
<?php
$current_user_id = get_current_user_id();

if ( is_user_logged_in() ) :
    // ユーザーメタから担当カテゴリを取得
    $user_categories = get_user_meta($current_user_id, 'user_category', true);
    // 配列かどうか確認（万が一の対策）
    if ( is_array($user_categories) && in_array('scholarship', $user_categories) ) : ?>

    <section class="post">
        <header class="header">
            <h1 class="title"><span><?php the_title(); ?></span></h1>
        </header>

        <?php
        // 評価データ（scholar_eval）をすべて取得
        $args = array(
            'post_type'      => 'scholar_eval',
            'posts_per_page' => -1,
            'post_status'    => 'publish',
        );

        $raw_evals = get_posts($args);

        // 応募者×審査員ごとに最新のみ抽出
        $latest_evals = [];

        foreach ($raw_evals as $eval) {
            $post_id   = $eval->ID;
            $name      = get_the_title($post_id);
            $judge     = SCF::get('scholarship_user', $post_id);
            $unique_key = $name . '||' . $judge;

            if (!isset($latest_evals[$unique_key])) {
                $latest_evals[$unique_key] = $eval;
            } else {
                $current_time  = strtotime($eval->post_date);
                $existing_time = strtotime($latest_evals[$unique_key]->post_date);
                if ($current_time > $existing_time) {
                    $latest_evals[$unique_key] = $eval;
                }
            }
        }

        // 氏名とエントリーNoでグループ化
        $grouped = [];
        foreach ($latest_evals as $eval) {
            $post_id = $eval->ID;
            $entryno = SCF::get('scholarship_entryno', $post_id) ?: '-';
            $name = get_the_title($post_id);
            $key = $entryno . '||' . $name;

            $grouped[$key]['entryno'] = $entryno;
            $grouped[$key]['name'] = $name;
            $grouped[$key]['evaluations'][] = $eval;
        }

        // ▼ エントリーNo順にソート
        usort($latest_evals, function($a, $b) {
            $a_no = SCF::get('scholarship_entryno', $a->ID);
            $b_no = SCF::get('scholarship_entryno', $b->ID);

            // 数値として比較（数字＋文字でも自然順に並ぶ）
            return strnatcmp($a_no, $b_no);
        });
        ?>

        <div class="inner" style="width: 90%;">
            <?php if (!empty($latest_evals)): ?>
                <div class="scroll-wrap">
                    <table>
                        <thead>
                            <tr>
                                <th>エントリーNo</th>
                                <th>氏名</th>
                                <th>審査員名</th>
                                <th>学歴・<br>指導教官</th>
                                <th>成績</th>
                                <th>受賞歴</th>
                                <th>志望動機</th>
                                <th>合計点</th>
                                <!-- <th>判定</th> -->
                                <th style="width: 30%">備考</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($grouped as $key => $group): ?>
                                <?php foreach ($group['evaluations'] as $index => $eval): ?>
                                <?php
                                $post_id   = $eval->ID;
                                $entryno   = SCF::get('scholarship_entryno', $post_id) ?: '-';
                                $name      = get_the_title($post_id);                     // 応募者名
                                $judge     = SCF::get('scholarship_user', $post_id);      // 審査員名
                                // $result    = SCF::get('scholarship_judge', $post_id);  // 判定
                                $academic  = SCF::get('score_academic', $post_id);        // 学歴
                                $grades    = SCF::get('score_grades', $post_id);          // 成績
                                $awards    = SCF::get('score_awards', $post_id);          // 受賞歴
                                $reason    = SCF::get('score_reason', $post_id);         // 志望動機
                                $comment   = SCF::get('scholarship_comment', $post_id);  // 備考
                                $total = (int)$academic + (int)$grades + (int)$awards + (int)$reason;
                                ?>
                                <tr>
                                    <?php if ($index === 0): ?>
                                    <td rowspan="<?php echo count($group['evaluations']); ?>"><?php echo esc_html($group['entryno']); ?></td>
                                    <td rowspan="<?php echo count($group['evaluations']); ?>"><?php echo esc_html($group['name']); ?></td>
                                    <?php endif; ?>
                                    <td><?php echo esc_html($judge); ?></td>
                                    <td><?php echo esc_html($academic); ?></td>
                                    <td><?php echo esc_html($grades); ?></td>
                                    <td><?php echo esc_html($awards); ?></td>
                                    <td><?php echo esc_html($reason); ?></td>
                                    <td><?php echo esc_html($total); ?></td>
                                    <?php /* <td><?php echo esc_html($result); ?></td> */ ?>
                                    <td><?php echo esc_html($comment); ?></td>
                                </tr>
                                <?php endforeach; ?>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <p>評価データがありません。</p>
            <?php endif; ?>
        </div>
    </section>

    <?php else: ?>
        <section>
            <div class="innerS">
                <p>こちらは奨学金審査用サイトです。<br>お手数ですが、ユーザー情報に間違いがないか、運営に問い合わせください。</p>
            </div>
        </section>
    <?php endif; ?>

<?php else: ?>
    <section id="login-section">
        <div class="innerS">
            <p>閲覧するには会員登録またはログインが必要です。</p>
            <ul class="col2 mt30">
                <?php /* <li style="text-align: center"><a href="<?php echo home_url('/create-account'); ?>">新規会員登録</a></li> */ ?>
                <li style="text-align: center"><a href="<?php echo home_url('/scholarship/login'); ?>" class="btn">ログイン <i class="fa-solid fa-arrow-right" aria-hidden="true"></i></a></li>
                <li style="text-align: center"><a href="<?php echo home_url('/scholarship/member'); ?>" class="btn">会員ページ <i class="fa-solid fa-arrow-right" aria-hidden="true"></i></a></li>
            </ul>
        </div>
    </section>
<?php endif; ?>

<script src="https://unpkg.com/scroll-hint@latest/js/scroll-hint.min.js"></script>
<script>
  window.addEventListener('DOMContentLoaded', function () {
    new ScrollHint('.scroll-wrap');
  });
</script>
<?php get_footer('scholarship'); ?>