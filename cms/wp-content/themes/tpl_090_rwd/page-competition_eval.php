<?php
/*
Template Name: 評価一覧（年度自動判別版）
*/
get_header();
?>

<link rel="stylesheet" href="https://unpkg.com/scroll-hint@latest/css/scroll-hint.css">
<style>
    .header { margin-bottom: 40px; }
    .header h1 { font-size: 120%; text-align: center; }
    /* テーブルの見た目を少し調整 */
    table { width: 100%; border-collapse: collapse; margin-top: 20px; }
    th, td { border: 1px solid #ccc; padding: 10px; text-align: left; font-size: 14px; }
    th { background: #f4f4f4; }
    tr:nth-child(even) { background: #fafafa; }
</style>

<header class="header">
    <h1 class="title"><span><?php the_title(); ?> 評価一覧</span></h1>
</header>

<?php
// 現在の西暦を自動取得してカテゴリ名にする（例：2026年度）
// または、固定ページのタイトルから年度を判別するようにしてもOK
$target_category = date('Y') . '年度'; 

$raw_evaluations = get_posts(array(
    'post_type'      => 'evaluations', // コンクールは既存の箱を使う
    'posts_per_page' => -1,
    'post_status'    => 'publish',
    'orderby'        => 'date',
    'order'          => 'DESC',
    // 年度で絞り込み
    'tax_query' => array(
        array(
            'taxonomy' => 'evaluation_category',
            'field'    => 'name',
            'terms'    => $target_category,
        ),
    ),
));

$latest_evaluations = [];

foreach ($raw_evaluations as $evaluation) {
    $post_id    = $evaluation->ID;
    $entry_name = get_the_title($post_id);
    $judge      = SCF::get('data_judge', $post_id);

    // 重複キー（同じ応募者＋同じ審査員）
    $unique_key = $entry_name . '||' . $judge;

    $eval_data = array(
        'entry_name' => $entry_name,
        'judge'      => $judge,
        'score'      => SCF::get('data_score', $post_id),
        'comment'    => SCF::get('data_comment', $post_id),
        'entryno'    => get_post_meta($post_id, 'entryno', true), // 保存したmetaから取得
        'item'       => SCF::get('instrument_played', $post_id), // 楽器
        'post_date'  => get_the_date('U', $post_id)
    );

    if (!isset($latest_evaluations[$unique_key]) || $eval_data['post_date'] > $latest_evaluations[$unique_key]['post_date']) {
        $latest_evaluations[$unique_key] = $eval_data;
    }
}

// 応募者ごとにグルーピング
$grouped = [];
foreach ($latest_evaluations as $eval) {
    $entry_key = $eval['entry_name'];
    if (!isset($grouped[$entry_key])) {
        $grouped[$entry_key] = array(
            'entryno'     => $eval['entryno'],
            'item'        => $eval['item'],
            'evaluations' => [],
        );
    }
    $grouped[$entry_key]['evaluations'][] = $eval;
}

// エントリーNo順にソート
uasort($grouped, function($a, $b) {
    return strnatcmp($a['entryno'], $b['entryno']);
});
?>

<div class="post inner">
    <div class="scroll-wrap js-scrollable">
        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>氏名／楽器</th>
                    <th>審査員名</th>
                    <th>点数</th>
                    <th>コメント</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($grouped as $entry_name => $entry_data): ?>
                <?php $evals = $entry_data['evaluations']; ?>
                <?php foreach ($evals as $index => $e): ?>
                    <tr>
                        <?php if ($index === 0): ?>
                            <td rowspan="<?php echo count($evals); ?>"><?php echo esc_html($entry_data['entryno']); ?></td>
                            <td rowspan="<?php echo count($evals); ?>">
                                <strong><?php echo esc_html($entry_name); ?></strong><br>
                                <small><?php echo esc_html($entry_data['item']); ?></small>
                            </td>
                        <?php endif; ?>
                        <td><?php echo esc_html($e['judge']); ?></td>
                        <td><?php echo esc_html($e['score']); ?></td>
                        <td><?php echo nl2br(esc_html($e['comment'])); ?></td>
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
    new ScrollHint('.js-scrollable');
  });
</script>

<?php get_footer(); ?>