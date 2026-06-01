<?php
/*
Template Name: 結果一覧
*/
if (!is_user_logged_in()) {
    wp_redirect(wp_login_url(get_permalink()));
    exit;
}
get_header(); ?>
<link rel="stylesheet" href="https://unpkg.com/scroll-hint@latest/css/scroll-hint.css">
<style>
    .header { margin-bottom: 40px; }
    .header h1 { font-size: 120%; text-align: center; }
</style>

<header class="header">
    <h1 class="title"><span><?php the_title(); ?></span></h1>
</header>

<?php
// 評価データ（evaluations）をすべて取得
$raw_evaluations = get_posts(array(
    'post_type'      => 'evaluations',
    'posts_per_page' => -1,
    'post_status'    => 'publish',
    'orderby'        => 'date',
    'order'          => 'DESC'
));

// entry_name × judge ごとに最新を抽出（entryno がある方を優先）
$latest_evaluations = [];

foreach ($raw_evaluations as $evaluation) {
    $entry_name = get_the_title($evaluation->ID); // 応募者の氏名
    $judge      = SCF::get('data_judge', $evaluation->ID);
    $unique_key = $entry_name . '||' . $judge;

    $entryno = SCF::get('entryno', $evaluation->ID);
    $item    = SCF::get('item', $evaluation->ID);
    $score   = SCF::get('data_score', $evaluation->ID);
    $comment = SCF::get('data_comment', $evaluation->ID);

    // すでにデータがある場合は「entryno がある方を優先」
    if (isset($latest_evaluations[$unique_key])) {
        if (empty($entryno) && !empty($latest_evaluations[$unique_key]['entryno'])) {
            continue;
        }
    }

    // 上書き or 初回
    $latest_evaluations[$unique_key] = array(
        'entry_name' => $entry_name,
        'judge'      => $judge,
        'score'      => $score,
        'comment'    => $comment,
        'entryno'    => $entryno,
        'item'       => $item,
    );
}

// 氏名でグループ化しながら、entryno / item を応募者ごとに1つ選定（空じゃないもの優先）
$grouped = [];

foreach ($latest_evaluations as $eval) {
    $entry_key = $eval['entry_name'];

    // 初回
    if (!isset($grouped[$entry_key])) {
        $grouped[$entry_key] = array(
            'entryno'    => $eval['entryno'],
            'item'       => $eval['item'],
            'evaluations' => [],
        );
    } else {
        // entryno がまだ空で、今回のがあれば更新
        if (empty($grouped[$entry_key]['entryno']) && !empty($eval['entryno'])) {
            $grouped[$entry_key]['entryno'] = $eval['entryno'];
        }
        if (empty($grouped[$entry_key]['item']) && !empty($eval['item'])) {
            $grouped[$entry_key]['item'] = $eval['item'];
        }
    }

    // 評価データを追加
    $grouped[$entry_key]['evaluations'][] = array(
        'judge'   => $eval['judge'],
        'score'   => $eval['score'],
        'comment' => $eval['comment'],
    );
}

// entryno で並び替え（空は最後に）
uasort($grouped, function($a, $b) {
    $a_no = mb_convert_kana(trim($a['entryno']), 'as');
    $b_no = mb_convert_kana(trim($b['entryno']), 'as');

    if ($a_no === '' && $b_no !== '') return 1;
    if ($b_no === '' && $a_no !== '') return -1;

    return strnatcmp($a_no, $b_no);
});
?>

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
