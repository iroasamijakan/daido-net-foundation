<?php
/*
Template Name: コンクール評価一覧
*/
if (!is_user_logged_in()) {
    wp_redirect(wp_login_url(get_permalink()));
    exit;
}
get_header();
?>

<link rel="stylesheet" href="https://unpkg.com/scroll-hint@latest/css/scroll-hint.css">
<style>
    .header { margin-bottom: 40px; }
    .header h1 { font-size: 120%; text-align: center; }
</style>

<?php
//$target_category = '2025 コンクール';  ← この部分を「オーディション」などに変えると別カテゴリに対応可能

// --- 1. 最新の年度（2026年度など）を自動取得 ---
$latest_year_term = get_terms(array(
    'taxonomy' => 'evaluation_year',
    'orderby'  => 'name',
    'order'    => 'DESC',
    'number'   => 1,
    'hide_empty' => true
));

$year_id = (!empty($latest_year_term)) ? $latest_year_term[0]->term_id : null;
$year_label = (!empty($latest_year_term)) ? $latest_year_term[0]->name : '';

// --- 2. データの取得条件を設定 ---
$args = array(
    'post_type'      => 'evaluations',
    'posts_per_page' => -1,
    'post_status'    => 'publish',
    'orderby'        => 'date',
    'order'          => 'DESC'
);

// 年度タームがあるものだけ、かつ最新年度のみに絞る
if ($year_id) {
    $args['tax_query'] = array(
        array(
            'taxonomy' => 'evaluation_year',
            'field'    => 'term_id',
            'terms'    => $year_id,
        ),
    );
} else {
    // 年度設定が一つもない場合は、何も表示しない（空の配列を返すためのダミーID）
    $args['post__in'] = array(0);
}

    // $raw_evaluations = get_posts(array(
    //     'post_type'      => 'evaluations',
    //     'posts_per_page' => -1,
    //     'post_status'    => 'publish',
    //     'orderby'        => 'date',
    //     'order'          => 'DESC'
    // ));

$raw_evaluations = get_posts($args);

$latest_evaluations = [];

foreach ($raw_evaluations as $evaluation) {
    $post_id   = $evaluation->ID;
    $entry_name = get_the_title($post_id);
    $judge      = SCF::get('data_judge', $post_id);

    // カテゴリを取得してフィルター
    // $category   = SCF::get('data_category', $post_id);
    // if ($category !== $target_category) {
    //     continue;
    // }

    $unique_key = $entry_name . '||' . $judge;

    $entryno = SCF::get('entryno', $post_id);
    $item    = SCF::get('item', $post_id);
    $score   = SCF::get('data_score', $post_id);
    $comment = SCF::get('data_comment', $post_id);
    $post_date = get_the_date('U', $post_id); // UNIXタイムで比較用

    $eval_data = array(
        'entry_name' => $entry_name,
        'judge'      => $judge,
        'score'      => $score,
        'comment'    => $comment,
        'entryno'    => $entryno,
        'item'       => $item,
        'post_date'  => $post_date
    );

    if (!isset($latest_evaluations[$unique_key])) {
        $latest_evaluations[$unique_key] = $eval_data;
    } else {
        $existing_date = $latest_evaluations[$unique_key]['post_date'];
        if ($post_date > $existing_date) {
            $latest_evaluations[$unique_key] = $eval_data;
        }
    }
}

$grouped = [];

foreach ($latest_evaluations as $eval) {
    $entry_key = $eval['entry_name'];

    if (!isset($grouped[$entry_key])) {
        $grouped[$entry_key] = array(
            'entryno'    => $eval['entryno'],
            'item'       => $eval['item'],
            'evaluations' => [],
        );
    } else {
        if (empty($grouped[$entry_key]['entryno']) && !empty($eval['entryno'])) {
            $grouped[$entry_key]['entryno'] = $eval['entryno'];
        }
        if (empty($grouped[$entry_key]['item']) && !empty($eval['item'])) {
            $grouped[$entry_key]['item'] = $eval['item'];
        }
    }

    $grouped[$entry_key]['evaluations'][] = array(
        'judge'   => $eval['judge'],
        'score'   => $eval['score'],
        'comment' => $eval['comment'],
    );
}

uasort($grouped, function($a, $b) {
    $a_no = mb_convert_kana(trim($a['entryno']), 'as');
    $b_no = mb_convert_kana(trim($b['entryno']), 'as');

    if ($a_no === '' && $b_no !== '') return 1;
    if ($b_no === '' && $a_no !== '') return -1;

    return strnatcmp($a_no, $b_no);
});
?>
<header class="header">
    <!-- <h1 class="title"><span><?php the_title(); ?></span></h1> -->
    <h1 class="title"><span><?php echo esc_html($year_label); ?> <?php the_title(); ?></span></h1>
</header>

<div class="post inner">
    <div class="scroll-wrap">
        <table>
            <thead>
                <tr>
                    <th>エントリーNo</th>
                    <th>氏名</th>
                    <th>審査員名</th>
                    <th>点数</th>
                    <th>コメント</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($grouped as $entry_name => $entry_data): ?>
                <?php $evaluations = $entry_data['evaluations']; ?>
                <?php foreach ($evaluations as $index => $e): ?>
                    <tr>
                        <?php if ($index === 0): ?>
                            <td rowspan="<?php echo count($evaluations); ?>"><?php echo esc_html($entry_data['entryno']); ?></td>
                            <td rowspan="<?php echo count($evaluations); ?>">
                                <?php echo esc_html($entry_name); ?><?php if ($entry_data['item']) echo '／' . esc_html($entry_data['item']); ?>
                            </td>
                        <?php endif; ?>
                        <td><?php echo esc_html($e['judge']); ?></td>
                        <td><?php echo esc_html($e['score']); ?></td>
                        <td><?php echo esc_html($e['comment']); ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<script src="https://unpkg.com/scroll-hint@latest/js/scroll-hint.min.js"></script>
<script>
  window.addEventListener('DOMContentLoaded', function () {
    new ScrollHint('.scroll-wrap');
  });
</script>

<?php get_footer(); ?>