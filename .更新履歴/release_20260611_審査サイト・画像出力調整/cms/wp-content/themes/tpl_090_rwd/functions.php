<?php
remove_action('wp_head', 'wp_generator');
function my_scripts(){
    wp_enqueue_script('jquery');
}
add_action( 'wp_enqueue_scripts', 'my_scripts' );


/* Load up the theme options
/*---------------------------------------------------------*/
add_action('admin_menu', 'my_theme_option');
function my_theme_option() {
  add_theme_page('テーマの使い方','テーマの使い方','edit_themes','theme_option','theme_option_file');
}
function theme_option_file(){
  require_once (get_template_directory() . '/inc/theme-options.php');
}


/* Register navigation
/*---------------------------------------------------------*/
register_nav_menus( array(
    'primary' => __('Main Navigation', 'tpl_090_rwd'),
));


/* Register sidebars
/*---------------------------------------------------------*/
register_sidebar(array(
  'id' => 'widgetLeft', // ← id を追加
  'name' => __('widgetLeft'),
  'before_widget' => '<div id="%1$s" class="widget %2$s">',
  'after_widget' => '</div>',
  'before_title' => '<h3>',
  'after_title' => '</h3>',
));
register_sidebar(array(
  'id' => 'widgetCenter', // ← id を追加
  'name' => __('widgetCenter'),
  'before_widget' => '<div id="%1$s" class="widget %2$s">',
  'after_widget' => '</div>',
  'before_title' => '<h3>',
  'after_title' => '</h3>',
));
register_sidebar(array(
  'id' => 'widgetRight', // ← id を追加 
  'name' => __('widgetRight'),
  'before_widget' => '<div id="%1$s" class="widget %2$s">',
  'after_widget' => '</div>',
  'before_title' => '<h3>',
  'after_title' => '</h3>',
));


add_filter( 'wp_list_categories', 'tpl_090_rwd_list_categories', 10, 2 );
function tpl_090_rwd_list_categories( $output, $args ) {
  $output = preg_replace('/<\/a>\s*\((\d+)\)/',' ($1)</a>',$output);
  return $output;
}

add_filter( 'get_archives_link', 'tpl_090_rwd_archives_link' );
function tpl_090_rwd_archives_link( $output ) {
  $output = preg_replace('/<\/a>\s*(&nbsp;)\((\d+)\)/',' ($2)</a>',$output);
  return $output;
}



/* This is all for compatibility with versions of WordPress prior to 3.4.
/*---------------------------------------------------------*/
define('NO_HEADER_TEXT', true);
define('HEADER_TEXTCOLOR', true);
define('HEADER_IMAGE', '%s/images/mainImage.jpg');
define('HEADER_IMAGE_WIDTH', 2560);
define('HEADER_IMAGE_HEIGHT', 1056);
add_theme_support('custom-header');

add_theme_support('custom-logo');


/* This theme uses post thumbnails
/*---------------------------------------------------------*/
add_theme_support('post-thumbnails');
add_image_size('size1',280,280,true);


/* Custom Excerpt "more" Link
/*---------------------------------------------------------*/
function change_excerpt_more($post) {
  return ' ...';
}
add_filter('excerpt_more', 'change_excerpt_more');



/* Add admin CSS
/*---------------------------------------------------------*/
function tpl_090_rwd_admin_css(){
    $adminCssPath = get_template_directory_uri().'/cloud9_admin.css';
    wp_enqueue_style( 'theme', $adminCssPath , false, '2016');
}
add_action('admin_head', 'tpl_090_rwd_admin_css', 11);



/* Page navigation
/*---------------------------------------------------------*/
function kriesi_pagination($pages = '', $range = 2){  
     $showitems = ($range * 2)+1;  

    global $paged;
    if(empty($paged)) $paged = 1;

    if($pages == ''){
        global $wp_query;
        $pages = $wp_query->max_num_pages;
        if(!$pages){
            $pages = 1;
        }
    }   

    if(1 != $pages){
        echo "<ul class='pagination'>";
        if($paged > 2 && $paged > $range+1 && $showitems < $pages) echo "<li><a href='".get_pagenum_link(1)."'>&laquo;</a></li>";
        if($paged > 1 && $showitems < $pages) echo "<li><a href='".get_pagenum_link($paged - 1)."'>&lsaquo;</a></li>";

        for ($i=1; $i <= $pages; $i++){
            if (1 != $pages &&( !($i >= $paged+$range+1 || $i <= $paged-$range-1) || $pages <= $showitems )){
                echo ($paged == $i)? "<li><span class='current'>".$i."</span></li>":"<li><a href='".get_pagenum_link($i)."#hoge' class='inactive' >".$i."</a></li>";
            }
        }

        if ($paged < $pages && $showitems < $pages) echo "<li><a href='".get_pagenum_link($paged + 1)."'>&rsaquo;</a></li>";  
        if ($paged < $pages-1 &&  $paged+$range-1 < $pages && $showitems < $pages) echo "<li><a href='".get_pagenum_link($pages)."'>&raquo;</a></li>";
        echo "</ul>\n";
    }
}


/* Post shortcode
/*---------------------------------------------------------*/
function home_url_shortcode() {
  return home_url();
}
add_shortcode('home_url', 'home_url_shortcode');



/* Default Post label
/*---------------------------------------------------------*/
// function change_post_menu_label() {
//   global $menu, $submenu;
//   $menu[5][0] = '__2025応募者'; // サイドメニューの「投稿」を「応募者」に変更
//   $submenu['edit.php'][5][0] = '__2025応募者一覧'; // 「投稿一覧」を「応募者一覧」に変更
//   $submenu['edit.php'][10][0] = '__2025新規応募者'; // 「新規追加」も変更
// }
// add_action('admin_menu', 'change_post_menu_label');

// function change_post_object_label() {
//     global $wp_post_types;
//     $labels = &$wp_post_types['post']->labels;
//     $labels->name = '応募者';
//     $labels->singular_name = '応募者';
//     $labels->add_new = '新規応募者';
//     $labels->add_new_item = '新規応募者を追加';
//     $labels->edit_item = '応募者を編集';
//     $labels->new_item = '新規応募者';
//     $labels->view_item = '応募者を表示';
//     $labels->search_items = '応募者を検索';
//     $labels->not_found = '応募者が見つかりません';
//     $labels->not_found_in_trash = 'ゴミ箱に応募者はありません';
//     $labels->all_items = '応募者一覧';
//     $labels->menu_name = '応募者';
//     $labels->name_admin_bar = '応募者';
// }
// add_action('init', 'change_post_object_label');

// // エディタを非表示
// function remove_post_editor() {
//     remove_post_type_support('post', 'editor');
// }
// add_action('init', 'remove_post_editor');

/* Default Post label
/*---------------------------------------------------------*/
function change_post_menu_label() {
  // 管理者以外は何もしない（Undefined array key 対策）
  if (!is_admin() || !current_user_can('manage_options')) {
      return;
  }
  
  global $menu, $submenu;
  
  if (isset($menu[5]) && is_array($menu[5])) {
      $menu[5][0] = '__2025応募者'; 
  }
  
  if (isset($submenu['edit.php']) && is_array($submenu['edit.php'])) {
      if (isset($submenu['edit.php'][5]))  $submenu['edit.php'][5][0] = '__2025応募者一覧';
      if (isset($submenu['edit.php'][10])) $submenu['edit.php'][10][0] = '__2025新規応募者';
  }
}

/* CF7 Member info
/*---------------------------------------------------------*/
// user_shimei を取得
function set_user_name_to_cf7_tag( $tag ) {
    if ( ! is_array( $tag ) || $tag['name'] !== 'user_shimei' ) {
        return $tag;
    }

    if ( is_user_logged_in() ) {
        $user_id = get_current_user_id();
        $user_shimei = get_user_meta( $user_id, 'user_shimei', true );

        if ( ! empty( $user_shimei ) ) {
            $tag['values'] = (array) $user_shimei;
        }
    }
    return $tag;
}
add_filter( 'wpcf7_form_tag', 'set_user_name_to_cf7_tag', 11 );

// user_id を取得
function set_user_id_to_cf7_tag( $tag ) {
    if ( ! is_array( $tag ) || $tag['name'] !== 'user_id' ) {
        return $tag;
    }

    if ( is_user_logged_in() ) {
        $user_id = get_current_user_id();
        $tag['values'] = (array) $user_id;
    }
    return $tag;
}
add_filter( 'wpcf7_form_tag', 'set_user_id_to_cf7_tag', 11 );


/* CF7 Post info
/*---------------------------------------------------------*/
// 投稿タイトル
function set_post_title_to_cf7_tag( $tag ){
    if ( ! is_array( $tag ) || $tag['name'] !== 'post_title' ) {
        return $tag;
    }
    $post_title = get_the_title();
    if ( empty( $post_title ) ) {
        return $tag;
    }
    $tag['values'] = (array)$post_title;
    return $tag;
    }
add_filter( 'wpcf7_form_tag', 'set_post_title_to_cf7_tag', 11 );

// 投稿カテゴリ
function set_post_category_to_cf7_tag( $tag ) {
    if ( ! is_array( $tag ) || $tag['name'] !== 'post_category' ) {
        return $tag;
    }

    $categories = get_the_category();
    $category_name = !empty($categories) ? $categories[0]->name : '未分類';

    if ( empty( $category_name ) ) {
        return $tag;
    }

    $tag['values'] = (array)$category_name;
    return $tag;
    }
add_filter( 'wpcf7_form_tag', 'set_post_category_to_cf7_tag', 11 );

// evaluated_post_id + entryno + item
function set_cf7_hidden_fields( $tag ) {
if ( ! is_array( $tag ) ) return $tag;

$post_id = get_the_ID();

switch ( $tag['name'] ) {
    case 'evaluated_post_id':
        $tag['values'] = array($post_id);
        break;

    case 'entryno':
        $entryno = get_post_meta($post_id, 'entryno', true);
        if (!empty($entryno)) {
            $tag['values'] = array($entryno);
        }
        break;

    case 'item':
        $item = get_post_meta($post_id, 'item', true);
        if (!empty($item)) {
            $tag['values'] = array($item);
        }
        break;
}

return $tag;
}
add_filter('wpcf7_form_tag', 'set_cf7_hidden_fields', 11);



/* CF7 → Post
/*---------------------------------------------------------*/
// ------------------------------
// 評価データのカスタム投稿タイプを登録
// ------------------------------
function create_evaluation_post_type() {
  $labels = array(
      'name'               => '評価データ',
      'singular_name'      => '評価データ',
      'menu_name'          => '評価データ',
      'name_admin_bar'     => '評価データを追加',
      'add_new'            => '新規追加',
      'add_new_item'       => '新しい評価データを追加',
      'new_item'           => '新しい評価データ',
      'edit_item'          => '評価データを編集',
      'view_item'          => '評価データを表示',
      'all_items'          => 'すべての評価データ',
      'search_items'       => '評価データを検索',
      'not_found'          => '評価データが見つかりません',
      'not_found_in_trash' => 'ゴミ箱に評価データはありません'
  );

  $args = array(
      'labels'             => $labels,
      'public'             => true,
      'publicly_queryable' => true,
      'show_ui'            => true,
      'show_in_menu'       => true,
      'query_var'          => true,
      'rewrite'            => array('slug' => 'evaluations'),
      'capability_type'    => 'post',
      'has_archive'        => true,
      'hierarchical'       => false,
      'menu_position'      => 5,
      'menu_icon'          => 'dashicons-id-alt',
      'supports'           => array('title', 'custom-fields'), // 'editor' を削除
      'show_in_rest'       => true, // Gutenberg対応
  );

  register_post_type('evaluations', $args);
}
add_action('init', 'create_evaluation_post_type');

// 評価データ カテゴリ分け
function create_evaluation_taxonomy() {
  $labels = array(
      'name'              => '評価カテゴリ',
      'singular_name'     => '評価カテゴリ',
      'search_items'      => '評価カテゴリを検索',
      'all_items'         => 'すべての評価カテゴリ',
      'parent_item'       => '親カテゴリ',
      'parent_item_colon' => '親カテゴリ:',
      'edit_item'         => '評価カテゴリを編集',
      'update_item'       => '評価カテゴリを更新',
      'add_new_item'      => '新しい評価カテゴリを追加',
      'new_item_name'     => '新しい評価カテゴリ名',
      'menu_name'         => '評価カテゴリ',
  );

  $args = array(
      'hierarchical'      => true,
      'labels'            => $labels,
      'show_ui'           => true,
      'show_admin_column' => true,
      'query_var'         => true,
      'rewrite'           => array('slug' => 'evaluation-category'),
  );

  register_taxonomy('evaluation_category', array('evaluations'), $args);
}
add_action('init', 'create_evaluation_taxonomy');



/*	評価データ CSVエクスポート
/*---------------------------------------------------------*/
// メニュー登録（オーディション／コンクール）
// function register_evaluations_csv_export() {
//   add_submenu_page(
//       'edit.php?post_type=evaluations',
//       'CSVエクスポート（オーディション）',
//       'CSVエクスポート（オーディション）',
//       'manage_options',
//       'export-evaluations-audition',
//       'export_evaluations_csv_page_audition'
//   );

//   add_submenu_page(
//       'edit.php?post_type=evaluations',
//       'CSVエクスポート（コンクール）',
//       'CSVエクスポート（コンクール）',
//       'manage_options',
//       'export-evaluations-competition',
//       'export_evaluations_csv_page_competition'
//   );
// }
// add_action('admin_menu', 'register_evaluations_csv_export');

// オーディション用ページ
// function export_evaluations_csv_page_audition() {
//   echo '<div class="wrap"><h1>CSVエクスポート（オーディション）</h1>';
//   echo '<p><a href="' . admin_url('admin-post.php?action=export_evaluations_csv_audition') . '" class="button button-primary">CSVダウンロード</a></p></div>';
// }

// コンクール用ページ
// function export_evaluations_csv_page_competition() {
//   echo '<div class="wrap"><h1>CSVエクスポート（コンクール）</h1>';
//   echo '<p><a href="' . admin_url('admin-post.php?action=export_evaluations_csv_competition') . '" class="button button-primary">CSVダウンロード</a></p></div>';
// }

// メイン処理（共通ロジックを再利用）
// function export_evaluations_csv_by_category($target_category) {
//   if (!current_user_can('manage_options')) wp_die('権限がありません');

//   header('Content-Type: application/octet-stream');
//   header('Content-Disposition: attachment; filename="evaluation_list_' . $target_category . '.csv"');
//   header('Pragma: no-cache');
//   header('Expires: 0');

//   $output = fopen('php://output', 'w');
//   if ($output === false) wp_die('CSVファイルを開けませんでした');

//   if (!class_exists('SCF')) wp_die('SCF プラグインが有効化されていません。');

//   // CSVヘッダー（カテゴリに応じて）
//   if (str_contains($target_category, 'コンクール')) {
//       $header = array('応募者', 'カテゴリ', '審査員名', '点数', 'コメント', '評価日');
//   } else {
//       $header = array('応募者', 'カテゴリ', '審査員名', 'コメント', '評価日');
//   }

//   $header = array_map(fn($v) => mb_convert_encoding($v, 'SJIS-win', 'UTF-8'), $header);
//   fputcsv($output, $header);

//   $args = array(
//       'post_type'      => 'evaluations',
//       'posts_per_page' => -1,
//       'post_status'    => 'publish',
//   );
//   $evaluations = get_posts($args);
//   if (empty($evaluations)) wp_die('評価データがありません。');

//   $latest_evaluations = [];

//   foreach ($evaluations as $evaluation) {
//       $post_id   = $evaluation->ID;
//       $title     = get_the_title($post_id);
//       $evaluator = SCF::get('data_judge', $post_id);
//       $category  = SCF::get('data_category', $post_id);

//       if ($category !== $target_category) continue;

//       $unique_key = $title . '||' . $evaluator;

//       if (!isset($latest_evaluations[$unique_key]) || strtotime($evaluation->post_date) > strtotime($latest_evaluations[$unique_key]->post_date)) {
//           $latest_evaluations[$unique_key] = $evaluation;
//       }
//   }

//   foreach ($latest_evaluations as $evaluation) {
//       $post_id   = $evaluation->ID;
//       $title     = get_the_title($post_id);
//       $category  = SCF::get('data_category', $post_id) ?: '未設定';
//       $evaluator = SCF::get('data_judge', $post_id) ?: '未設定';
//       $score     = SCF::get('data_score', $post_id) ?: '';
//       $comment   = SCF::get('data_comment', $post_id) ?: '未設定';
//       $post_date = get_the_date('Y-m-d H:i:s', $post_id);

//       if (str_contains($target_category, 'コンクール')) {
//           $row = array($title, $category, $evaluator, $score, $comment, $post_date);
//       } else {
//           $row = array($title, $category, $evaluator, $comment, $post_date);
//       }

//       $row = array_map(fn($v) => mb_convert_encoding($v, 'SJIS-win', 'UTF-8'), $row);
//       fputcsv($output, $row);
//   }

//   fclose($output);
//   exit;
// }

// アクション登録（オーディション／コンクール）
// add_action('admin_post_export_evaluations_csv_audition', function() {
//   export_evaluations_csv_by_category('2025 オーディション');    // 年ごとにここを修正
// });

// add_action('admin_post_export_evaluations_csv_competition', function() {
//   export_evaluations_csv_by_category('2025 コンクール');    // 年ごとにここを修正
// });



/* CF7 評価履歴
/*---------------------------------------------------------*/
function has_user_already_evaluated($user_id, $post_id) {
  // error_log("評価チェック: ユーザーID = $user_id, 投稿ID = $post_id");
  $args = array(
      'post_type'      => 'evaluations',
      'posts_per_page' => -1,
      'post_status'    => 'publish',
  );
  $evaluations = get_posts($args);

  foreach ($evaluations as $evaluation) {
      $evaluation_id = $evaluation->ID;
      $stored_user_id = SCF::get('user_id', $evaluation_id);
      $stored_post_id = SCF::get('evaluated_post_id', $evaluation_id);

      // error_log("取得データ: 評価ID = $evaluation_id, SCFの user_id = $stored_user_id, SCFの post_id = $stored_post_id");

      if ((int)$stored_user_id === (int)$user_id && (int)$stored_post_id === (int)$post_id) {
          // error_log("評価済みと判定！");
          return true;
      }
  }
  // error_log("評価データが見つかりませんでした。");
  return false;
}



/*	CF7 評価データのステータス
/*---------------------------------------------------------*/
// add_filter('cf7_2_post_status_evaluations', 'publish_new_evaluations', 10, 3);
// /**
//  * Function to change the post status of saved/submitted posts.
//  * @param string $status the post status, default is 'draft'.
//  * @param string $ckf7_key unique key to identify your form.
//  * @param array $submitted_data complete set of data submitted in the form as an array of field-name=>value pairs.
//  * @return string a valid post status ('publish'|'draft'|'pending'|'trash')
//  */
// function publish_new_evaluations($status, $ckf7_key, $submitted_data) {
//     // フォームキーを指定する（フォームが複数ある場合に特定のフォームのみ適用する）
//     if ($ckf7_key === 'contact-form-1') { // Contact Form 7 のキーを指定
//         return 'publish'; // 自動で「公開」にする
//     }

//     return $status; // 他のフォームはデフォルトのステータスを適用
// }

add_filter( 'cf7_2_post_status_scholar_eval', 'publish_new_scholar_eval',10,3);
	/**
	* Function to change the post status of saved/submitted posts.
	* @param string $status the post status, default is 'draft'.
	* @param string $ckf7_key unique key to identify your form.
	* @param array $submitted_data complete set of data submitted in the form as an array of field-name=>value pairs.
	* @return string a valid post status ('publish'|'draft'|'pending'|'trash')
	*/
	function publish_new_scholar_eval($status, $ckf7_key, $submitted_data){
	/*The default behaviour is to save post to 'draft' status.  If you wish to change this, you can use this filter and return a valid post status: 'publish'|'draft'|'pending'|'trash'*/
	return 'publish';
}

add_filter( 'cf7_2_post_status_evaluations', 'publish_new_evaluations',10,3);
/**
* Function to change the post status of saved/submitted posts.
* @param string $status the post status, default is 'draft'.
* @param string $ckf7_key unique key to identify your form.
* @param array $submitted_data complete set of data submitted in the form as an array of field-name=>value pairs.
* @return string a valid post status ('publish'|'draft'|'pending'|'trash')
*/
function publish_new_evaluations($status, $ckf7_key, $submitted_data){
/*The default behaviour is to save post to 'draft' status.  If you wish to change this, you can use this filter and return a valid post status: 'publish'|'draft'|'pending'|'trash'*/
return 'publish';
}



/*	CF7 → Post Mapping
/*---------------------------------------------------------*/
add_filter('cf7_2_post_meta_fields_evaluations', 'map_fields_for_evaluations', 10, 3);

function map_fields_for_evaluations($mapped_fields, $cf7_key, $submitted_data) {
    return array(
        'data_category'      => $submitted_data['post_category'] ?? '',
        'data_comment'       => $submitted_data['comment'] ?? '',
        'user_id'            => $submitted_data['user_id'] ?? '',
        'evaluated_post_id'  => $submitted_data['evaluated_post_id'] ?? '',
        'entryno'            => $submitted_data['entryno'] ?? '',
        'item'               => $submitted_data['item'] ?? '',
        'data_judge'         => $submitted_data['user_shimei'] ?? '',
        'data_score'         => $submitted_data['score'] ?? '',
    );
}



/*	Video Thumbnail
/*---------------------------------------------------------*/
function get_best_youtube_thumbnail($raw_id, $size = 'maxresdefault') {
  $clean_id = explode('?', $raw_id)[0]; // クエリを除去

  // WebPサムネイル
  $webp_url = 'https://i.ytimg.com/vi_webp/' . $clean_id . '/' . $size . '.webp';

  // JPGサムネイル（フォールバック）
  $jpg_url = 'https://img.youtube.com/vi/' . $clean_id . '/' . $size . '.jpg';

  // WebP が存在すればそれを使う
  if (@getimagesize($webp_url)) {
      return $webp_url;
  }

  // なければ JPG を使う
  return $jpg_url;
}


/* ユーザー編集画面に「担当カテゴリ」追加
/*---------------------------------------------------------*/
function add_user_category_field($user) {
  $categories = get_user_meta($user->ID, 'user_category', true);
  if (!is_array($categories)) $categories = array();
  ?>
  <h3>担当カテゴリ設定</h3>
  <table class="form-table">
      <tr>
          <th><label for="user_category">担当カテゴリ</label></th>
          <td>
              <label><input type="checkbox" name="user_category[]" value="audition" <?php checked(in_array('audition', $categories)); ?>> オーディション</label><br>
              <label><input type="checkbox" name="user_category[]" value="scholarship" <?php checked(in_array('scholarship', $categories)); ?>> 奨学金</label>
          </td>
      </tr>
  </table>
  <?php
}
add_action('show_user_profile', 'add_user_category_field');
add_action('edit_user_profile', 'add_user_category_field');

// 保存処理
function save_user_category_field($user_id) {
  if (current_user_can('edit_user', $user_id)) {
      $cats = isset($_POST['user_category']) ? (array)$_POST['user_category'] : array();
      update_user_meta($user_id, 'user_category', $cats);
  }
}
add_action('personal_options_update', 'save_user_category_field');
add_action('edit_user_profile_update', 'save_user_category_field');


/* Scholarship post
/*---------------------------------------------------------*/
function create_scholarship_post_type() {
  $labels = array(
     'name'               => '奨学金応募者',
      'singular_name'      => '奨学金応募者',
      'menu_name'          => '__2005奨学金応募者',
      'name_admin_bar'     => '応募者を追加',
      'add_new'            => '新規追加',
      'add_new_item'       => '新しい応募者を追加',
      'edit_item'          => '応募者情報を編集',
      'new_item'           => '新しい応募者',
      'view_item'          => '応募者を見る',
      'all_items'          => '全応募者一覧',
      'search_items'       => '応募者を検索',
      'not_found'          => '応募者が見つかりません',
      'not_found_in_trash' => 'ゴミ箱に応募者はいません'
  );

  $args = array(
      'labels'             => $labels,
      'public'             => true,
      'show_ui'            => true,
      'show_in_menu'       => true,
      'rewrite'            => array('slug' => 'scholar_list'),
      'capability_type'    => 'post',
      'has_archive'        => true,
      'hierarchical'       => false,
      'menu_position'      => 7,
      'menu_icon'          => 'dashicons-welcome-learn-more',
      'supports'           => array('title', 'custom-fields'),
      'show_in_rest'       => true,
  );

  register_post_type('scholar_list', $args);
}
add_action('init', 'create_scholarship_post_type');


/* CF7 → Post（scholarship）
/*---------------------------------------------------------*/
/* 奨学金評価データ：カスタム投稿タイプ */
function create_scholar_eval_post_type() {
  $labels = array(
      'name'          => '奨学金評価データ',
      'singular_name' => '奨学金評価データ',
      'menu_name'     => '__2005奨学金評価データ',
  );

  $args = array(
      'labels'        => $labels,
      'public'        => false,
      'show_ui'       => true,
      'show_in_menu'  => true,
      'menu_position' => 8,
      'menu_icon'     => 'dashicons-awards',
      'supports'      => array('title', 'custom-fields'),
      'has_archive'   => false,
      'rewrite'       => false,
  );

  register_post_type('scholar_eval', $args);
}
add_action('init', 'create_scholar_eval_post_type');

/* 奨学金評価データ：hidden項目の自動セット */
function set_cf7_hidden_fields_scholarship( $tag ) {
  if ( ! is_array( $tag ) ) return $tag;
  $post_id = get_the_ID();
  switch ( $tag['name'] ) {
    case 'evaluated_post_id':
        $tag['values'] = array($post_id);
        break;
    case 'entryno':
        $entryno = get_post_meta($post_id, 'scholarship_entryno', true);
        if (!empty($entryno)) {
            $tag['values'] = array($entryno);
        }
        break;
    case 'post_title':
        $post_title = get_the_title($post_id);
        $tag['values'] = array($post_title);
        break;
  }
  return $tag;
}
add_filter('wpcf7_form_tag', 'set_cf7_hidden_fields_scholarship', 11);

/* 奨学金評価データ：評価済みチェック用の関数 */
function has_user_already_scholar_evaluated($user_id, $post_id) {
  $args = array(
      'post_type'      => 'scholar_eval', // ←奨学金評価データの投稿タイプ名
      'posts_per_page' => -1,
      'post_status'    => 'publish',
  );
  $evaluations = get_posts($args);
  foreach ($evaluations as $evaluation) {
      $evaluation_id = $evaluation->ID;
      $stored_user_id = SCF::get('user_id', $evaluation_id);
      $stored_post_id = SCF::get('evaluated_post_id', $evaluation_id);
      if ((int)$stored_user_id === (int)$user_id && (int)$stored_post_id === (int)$post_id) {
          return true; // 評価済み
      }
  }
  return false; // 未評価
}
/* 奨学金評価データ：CSVエクスポートメニュー追加 */
function register_scholar_eval_csv_export() {
  add_submenu_page(
      'edit.php?post_type=scholar_eval',  // 親メニュー（奨学金評価データ）
      'CSVエクスポート', // ページタイトル
      'CSVエクスポート',// サブメニュー名
      'manage_options',// 権限
      'export-scholar-eval-csv',// スラッグ
      'export_scholar_eval_csv_page'// コールバック関数
  );
}
add_action('admin_menu', 'register_scholar_eval_csv_export');

// エクスポートページの表示
function export_scholar_eval_csv_page() {
    $terms = get_terms(array('taxonomy' => 'evaluation_year', 'hide_empty' => true, 'orderby' => 'name', 'order' => 'DESC'));
    ?>
    <div class="wrap">
        <h1>奨学金評価データのCSVエクスポート</h1>
        <p>抽出したい年度を選択してダウンロードしてください。</p>
        <form method="get" action="<?php echo admin_url('admin-post.php'); ?>">
            <input type="hidden" name="action" value="export_scholar_eval_csv">
            <table class="form-table">
                <tr>
                    <th scope="row">対象の年度</th>
                    <td>
                        <select name="target_year" style="width: 300px;">
                            <option value="">-- 全てエクスポート --</option>
                            <?php foreach ($terms as $term): ?>
                                <option value="<?php echo esc_attr($term->name); ?>"><?php echo esc_html($term->name); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </td>
                </tr>
            </table>
            <?php submit_button('CSVをダウンロード'); ?>
        </form>
    </div>
    <?php
}

// エクスポート処理本体
function export_scholar_eval_csv_action() {
    if (!current_user_can('manage_options')) wp_die('権限がありません');
    if (!class_exists('SCF')) wp_die('SCF プラグインが有効化されていません。');

    $target_year = isset($_GET['target_year']) ? $_GET['target_year'] : '';
    $filename = $target_year ? "scholar_eval_{$target_year}.csv" : "scholar_eval_list.csv";

    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    header('Pragma: no-cache');
    header('Expires: 0');
    $output = fopen('php://output', 'w');
    if ($output === false) wp_die('CSVファイルを開けませんでした');

    $header = array('年度', 'エントリーNo', '応募者名', '審査員名', '学歴・指導教官', '成績', '受賞歴', '志望動機', '合計点', '備考', '評価日');
    $header = array_map(function($val) {
        return mb_convert_encoding($val, 'SJIS-win', 'UTF-8');
    }, $header);
    fputcsv($output, $header);

    $args = array(
        'post_type'      => 'scholar_eval',
        'posts_per_page' => -1,
        'post_status'    => 'publish',
    );
    if ($target_year) {
        $args['tax_query'] = array(array(
            'taxonomy' => 'evaluation_year',
            'field'    => 'name',
            'terms'    => $target_year,
        ));
    }
    $evaluations = get_posts($args);
    if (empty($evaluations)) wp_die('奨学金評価データがありません。');

    // 応募者×審査員ごとの最新のみを取得
    $latest_evals = [];
    foreach ($evaluations as $eval) {
        $judge = SCF::get('scholarship_user', $eval->ID);
        $key   = get_the_title($eval->ID) . '||' . $judge;
        if (!isset($latest_evals[$key]) || strtotime($eval->post_date) > strtotime($latest_evals[$key]->post_date)) {
            $latest_evals[$key] = $eval;
        }
    }
    // エントリーNo順にソート
    usort($latest_evals, function($a, $b) {
        return strnatcmp(SCF::get('scholarship_entryno', $a->ID), SCF::get('scholarship_entryno', $b->ID));
    });

    foreach ($latest_evals as $eval) {
        $post_id    = $eval->ID;
        $year_terms = get_the_terms($post_id, 'evaluation_year');
        $year_label = ($year_terms && !is_wp_error($year_terms)) ? $year_terms[0]->name : ($target_year ?: '未設定');
        $academic   = (int) SCF::get('score_academic', $post_id);
        $grades     = (int) SCF::get('score_grades', $post_id);
        $awards     = (int) SCF::get('score_awards', $post_id);
        $reason     = (int) SCF::get('score_reason', $post_id);
        $row = array(
            $year_label,
            SCF::get('scholarship_entryno', $post_id) ?: '未設定',
            get_the_title($post_id),
            SCF::get('scholarship_user', $post_id) ?: '未設定',
            $academic, $grades, $awards, $reason,
            $academic + $grades + $awards + $reason,
            SCF::get('scholarship_comment', $post_id),
            get_the_date('Y-m-d', $post_id),
        );
        $row = array_map(function($val) {
            return mb_convert_encoding($val ?: '', 'SJIS-win', 'UTF-8');
        }, $row);
        fputcsv($output, $row);
    }
    fclose($output);
    exit;
}
add_action('admin_post_export_scholar_eval_csv', 'export_scholar_eval_csv_action');


/******************************************************
 * 1. 2026年版：新投稿タイプ（3種）と共通年度タクソノミーの登録
 *****************************************************/
add_action('init', 'register_2026_evaluation_post_types');
function register_2026_evaluation_post_types() {
    register_taxonomy('evaluation_year',
    array(
        'evaluations',
        'evaluations_audition',
        'scholarship_eval_v2',
        'competition_eval',
        'audition_eval',
        'scholar_eval'
        ),
            array(
            'label' => '年度',
            'hierarchical' => true,
            'show_ui' => true,
            'show_admin_column' => true, 
            'show_in_rest' => true,
            'query_var' => true,
            'rewrite' => array('slug' => 'evaluation_year'),
    ));

    register_post_type('competition_eval', array(
        'label' => '01_コンクール',
        'public' => true,
        'menu_position' => 10, 
        'menu_icon' => 'dashicons-awards',
        'supports' => array('title', 'custom-fields'),
        'has_archive' => true,
        'show_in_rest' => true,
    ));

    register_post_type('audition_eval', array(
        'label' => '02_オーディション',
        'public' => true,
        'menu_position' => 11,
        'menu_icon' => 'dashicons-groups',
        'supports' => array('title', 'custom-fields'),
        'has_archive' => true,
        'show_in_rest' => true,
    ));

    register_post_type('scholarship_eval_v2', array(
        'label' => '03_奨学金',
        'public' => true,
        'menu_position' => 12,
        'menu_icon' => 'dashicons-welcome-learn-more',
        'supports' => array('title', 'custom-fields'),
        'has_archive' => true,
        'show_in_rest' => true,
    ));
}

$my_post_types = array('competition_eval', 'audition_eval', 'scholarship_eval_v2');
foreach ($my_post_types as $pt) {
    add_filter("manage_{$pt}_posts_columns", 'add_custom_evaluation_columns');
    add_action("manage_{$pt}_posts_custom_column", 'display_custom_evaluation_columns', 10, 2);
    add_filter("manage_edit-{$pt}_sortable_columns", 'sortable_custom_evaluation_columns');
}

function add_custom_evaluation_columns($columns) {
    $new_columns = array();
    foreach ($columns as $key => $value) {
        $new_columns[$key] = $value;
        if ($key == 'title') {
            $new_columns['entryno'] = 'No';
            $new_columns['instrument'] = '楽器';
        }
    }
    return $new_columns;
}

function display_custom_evaluation_columns($column, $post_id) {
    switch ($column) {
        case 'entryno':
            echo get_post_meta($post_id, 'entryno', true);
            break;
        case 'instrument':
            echo get_post_meta($post_id, 'instrument_played', true); 
            break;
    }
}

function sortable_custom_evaluation_columns($columns) {
    $columns['entryno'] = 'entryno';
    return $columns;
}

// evaluations 管理画面：評価カテゴリ列を削除し、審査員名列を追加
add_filter('manage_evaluations_posts_columns', 'customize_evaluations_columns');
function customize_evaluations_columns($columns) {
    unset($columns['taxonomy-evaluation_category']);
    $new = array();
    foreach ($columns as $key => $value) {
        $new[$key] = $value;
        if ($key === 'title') {
            $new['judge_name'] = '審査員名';
        }
    }
    return $new;
}

add_action('manage_evaluations_posts_custom_column', 'display_evaluations_judge_column', 10, 2);
function display_evaluations_judge_column($column, $post_id) {
    if ($column !== 'judge_name') return;
    $user_id = get_post_meta($post_id, 'user_id', true);
    if ($user_id) {
        $user = get_user_by('ID', $user_id);
        echo $user ? esc_html($user->display_name) : esc_html(get_post_meta($post_id, 'data_judge', true));
    } else {
        echo esc_html(get_post_meta($post_id, 'data_judge', true));
    }
}

// evaluations_audition 管理画面：審査員名列を追加
add_filter('manage_evaluations_audition_posts_columns', 'customize_evaluations_audition_columns');
function customize_evaluations_audition_columns($columns) {
    $new = array();
    foreach ($columns as $key => $value) {
        $new[$key] = $value;
        if ($key === 'title') {
            $new['judge_name'] = '審査員名';
        }
    }
    return $new;
}

add_action('manage_evaluations_audition_posts_custom_column', 'display_evaluations_audition_judge_column', 10, 2);
function display_evaluations_audition_judge_column($column, $post_id) {
    if ($column !== 'judge_name') return;
    $user_id = get_post_meta($post_id, 'user_id', true);
    if ($user_id) {
        $user = get_user_by('ID', $user_id);
        echo $user ? esc_html($user->display_name) : esc_html(get_post_meta($post_id, 'data_judge', true));
    } else {
        echo esc_html(get_post_meta($post_id, 'data_judge', true));
    }
}

// scholar_eval 管理画面：審査員名列を追加
add_filter('manage_scholar_eval_posts_columns', 'customize_scholar_eval_columns');
function customize_scholar_eval_columns($columns) {
    $new = array();
    foreach ($columns as $key => $value) {
        $new[$key] = $value;
        if ($key === 'title') {
            $new['judge_name'] = '審査員名';
        }
    }
    return $new;
}

add_action('manage_scholar_eval_posts_custom_column', 'display_scholar_eval_judge_column', 10, 2);
function display_scholar_eval_judge_column($column, $post_id) {
    if ($column !== 'judge_name') return;
    $user_id = get_post_meta($post_id, 'user_id', true);
    if ($user_id) {
        $user = get_user_by('ID', $user_id);
        echo $user ? esc_html($user->display_name) : esc_html(get_post_meta($post_id, 'scholarship_user', true));
    } else {
        echo esc_html(get_post_meta($post_id, 'scholarship_user', true));
    }
}

add_action('init', 'my_remove_post_editor_support');
function my_remove_post_editor_support() {
    remove_post_type_support('competition_eval', 'editor');
    remove_post_type_support('audition_eval', 'editor');
    remove_post_type_support('scholarship_eval_v2', 'editor');
}

/******************************************************
 * 2. 管理画面メニューの再構成（共通設定への集約）
 *****************************************************/
add_action('admin_menu', 'reorganize_admin_menu_with_shortcuts', 999);
function reorganize_admin_menu_with_shortcuts() {
    // 【最優先】管理者以外はメニュー操作を一切行わない（エラー防止の決定版）
    if (!current_user_can('manage_options')) {
        return;
    }

    global $menu, $submenu;

    // --- メニューの表示順序を入れ替える ---
    if (isset($menu[5]) && is_array($menu[5])) {
        $menu[100] = $menu[5];
        unset($menu[5]);
    }
    
    foreach ($menu as $key => $item) {
        if (!is_array($item) || !isset($item[2])) {
            continue;
        }

        if ($item[2] === 'edit.php?post_type=evaluations') {
            $menu[101] = $menu[$key];
            unset($menu[$key]);
        } elseif ($item[2] === 'edit.php?post_type=scholar_list') {
            $menu[102] = $menu[$key];
            unset($menu[$key]);
        } elseif ($item[2] === 'edit.php?post_type=scholar_eval') {
            $menu[103] = $menu[$key];
            unset($menu[$key]);
        }
    }

    // --- 01_コンクール ---
    $parent_comp = 'edit.php?post_type=competition_eval';
    if (isset($submenu[$parent_comp]) && is_array($submenu[$parent_comp])) {
        foreach ($submenu[$parent_comp] as $key => $sub_item) {
            if (isset($sub_item[2]) && strpos($sub_item[2], 'taxonomy=evaluation_year') !== false) {
                unset($submenu[$parent_comp][$key]);
            }
        }
        if(isset($submenu[$parent_comp][5]))  $submenu[$parent_comp][5][0]  = '1_応募者一覧';
        if(isset($submenu[$parent_comp][10])) $submenu[$parent_comp][10][0] = '2_応募者新規登録';
        
        // 親メニューが存在する場合のみ追加
        add_submenu_page($parent_comp, '評価一覧', '3_評価一覧', 'manage_options', 'edit.php?post_type=evaluations');
        add_submenu_page($parent_comp, '評価ダウンロード', '4_評価ダウンロード', 'manage_options', 'edit.php?post_type=evaluations&page=export-evaluations-main');
    }

    // --- 02_オーディション ---
    $parent_audi = 'edit.php?post_type=audition_eval';
    if (isset($submenu[$parent_audi]) && is_array($submenu[$parent_audi])) {
        foreach ($submenu[$parent_audi] as $key => $sub_item) {
            if (isset($sub_item[2]) && strpos($sub_item[2], 'taxonomy=evaluation_year') !== false) {
                unset($submenu[$parent_audi][$key]);
            }
        }
        if(isset($submenu[$parent_audi][5]))  $submenu[$parent_audi][5][0]  = '1_応募者一覧';
        if(isset($submenu[$parent_audi][10])) $submenu[$parent_audi][10][0] = '2_応募者新規登録';

        add_submenu_page($parent_audi, '評価一覧', '3_評価一覧', 'manage_options', 'edit.php?post_type=evaluations_audition');
        add_submenu_page($parent_audi, '評価ダウンロード', '4_評価ダウンロード', 'manage_options', 'export-audition-eval-csv', 'export_audition_eval_csv_page');
    }
    
    // --- 03_奨学金 ---
    $parent_scholar = 'edit.php?post_type=scholarship_eval_v2';
    if (isset($submenu[$parent_scholar]) && is_array($submenu[$parent_scholar])) {
        foreach ($submenu[$parent_scholar] as $key => $sub_item) {
            if (isset($sub_item[2]) && strpos($sub_item[2], 'taxonomy=evaluation_year') !== false) {
                unset($submenu[$parent_scholar][$key]);
            }
        }
        if(isset($submenu[$parent_scholar][5]))  $submenu[$parent_scholar][5][0]  = '1_申請者一覧';
        if(isset($submenu[$parent_scholar][10])) $submenu[$parent_scholar][10][0] = '2_申請者新規登録';

        add_submenu_page($parent_scholar, '評価一覧', '3_評価一覧', 'manage_options', 'edit.php?post_type=scholar_eval');
        add_submenu_page($parent_scholar, '評価ダウンロード', '4_評価ダウンロード', 'manage_options', 'edit.php?post_type=scholar_eval&page=export-scholar-eval-csv');
    }

    // --- 04_設定・管理（共通メニュー） ---
    add_menu_page('04_設定・管理', '04_設定・管理', 'manage_options', 'common_settings', '', 'dashicons-admin-generic', 13);
    add_submenu_page('common_settings', '1_インポート', '1_インポート', 'manage_options', 'admin.php?import=csv');
    add_submenu_page('common_settings', '2_年度カテゴリ', '2_年度カテゴリ', 'manage_options', 'edit-tags.php?taxonomy=evaluation_year');
    
    if (isset($submenu['common_settings']) && is_array($submenu['common_settings'])) {
        unset($submenu['common_settings'][0]);
    }
}

/******************************************************
 * 3. CSVインポート時に SCFの数字を元に年度タームを自動作成・セット
 *****************************************************/
// ★ここに1行追加：インポート成功時に実行されるようにする
add_action('really_simple_csv_importer_post_saved', 'sync_evaluation_year_on_import', 10, 1);

function sync_evaluation_year_on_import($post_id) {
    // 1. インポートされたばかりの投稿から SCF「tax_evaluation_year」を取得
    $year_num = SCF::get('tax_evaluation_year', $post_id);

    if ($year_num) {
        // 数字を「2027年度」という表記に整える
        $year_label = $year_num . '年度';

        // 2. そのタームがまだ存在しなければ自動作成
        if (!term_exists($year_label, 'evaluation_year')) {
            wp_insert_term($year_label, 'evaluation_year');
        }

        // 3. 投稿（受験生・申請者データ）にその年度を紐付ける
        wp_set_object_terms($post_id, $year_label, 'evaluation_year');
    }
}

/******************************************************
 * 4. 評価データに「年度」をコピーしてセット
 * 送信直後に親の年度を強制コピー（オーディション・奨学金・コンクール共通）
 *****************************************************/
add_action('wpcf7_mail_sent', 'ultra_force_sync_year', 10, 1);

function ultra_force_sync_year($contact_form) {
    // 1. 送信されたデータを取得
    $submission = WPCF7_Submission::get_instance();
    if (!$submission) return;
    $posted_data = $submission->get_posted_data();

    // 2. 親ID（応募者ID）と作成された投稿IDを取得
    $parent_id = $posted_data['evaluated_post_id'] ?? '';
    if (!$parent_id) return;

    // 3. 親の年度を取得
    $terms = get_the_terms($parent_id, 'evaluation_year');
    if (!$terms || is_wp_error($terms)) return;
    $year_names = array();
    foreach ($terms as $term) {
        $year_names[] = $term->name;
    }

    // 4. 数秒前に作られた「評価データ」を探して年度を叩き込む
    $user_id = get_current_user_id();
    $args = array(
        'post_type'      => array('evaluations', 'evaluations_audition', 'scholar_eval'),
        'posts_per_page' => -1,
        'meta_query'     => array(
            'relation' => 'AND',
            array('key' => 'evaluated_post_id', 'value' => $parent_id),
            array('key' => 'user_id', 'value' => $user_id),
        ),
        'orderby' => 'date',
        'order'   => 'DESC',
    );

    $all_evals = get_posts($args);
    if ($all_evals) {
        $new_post_id = $all_evals[0]->ID;
        wp_set_object_terms($new_post_id, $year_names, 'evaluation_year', false);
        // 再送信時：古い重複評価をゴミ箱へ
        for ($i = 1; $i < count($all_evals); $i++) {
            wp_trash_post($all_evals[$i]->ID);
        }
    }
}

/******************************************************
 * 5. Really Simple CSV Importer で post_name が一致する場合に既存投稿を更新する
 *****************************************************/
add_filter( 'really_simple_csv_importer_save_post', function( $post, $is_update ) {
    if ( ! $is_update && ! empty( $post['post_name'] ) ) {
        global $wpdb;
        // 既存の post_name を検索して ID を取得
        $post_id = $wpdb->get_var( $wpdb->prepare(
            "SELECT ID FROM $wpdb->posts WHERE post_name = %s AND post_type = %s AND post_status != 'trash' LIMIT 1",
            $post['post_name'],
            $post['post_type']
        ) );

        if ( $post_id ) {
            $post['ID'] = $post_id; // IDをセットすることで「更新」扱いにする
        }
    }
    return $post;
}, 10, 2 );

/******************************************************
 * 6. 評価データの保存先を「オーディション」用に分離
 *****************************************************/
add_action('init', 'register_evaluation_split_post_types', 10);
function register_evaluation_split_post_types() {
    register_post_type('evaluations_audition', array(
        'label' => 'オーディション評価データ',
        'public' => true,
        'show_ui' => true,
        'show_in_menu' => true, 
        'supports' => array('title', 'custom-fields'),
        'has_archive' => true,
        'show_in_rest' => true,
        'menu_icon' => 'dashicons-edit',
    ));
}
/* オーディション用の重複チェック関数 */
function has_user_already_evaluated_audition($user_id, $post_id) {
    if (!$user_id || !$post_id) return false;

    $args = array(
        'post_type'      => 'evaluations_audition',
        'posts_per_page' => -1,
        'post_status'    => array('publish', 'pending', 'draft', 'future'), // 全ステータス対象
    );
    $evals = get_posts($args);

    foreach ($evals as $eval) {
        // SCFから値を取得（第2引数にIDを忘れずに）
        $stored_user_id = get_post_meta($eval->ID, 'user_id', true);
        $stored_post_id = get_post_meta($eval->ID, 'evaluated_post_id', true);

        // 型を揃えて比較
        if ( (int)$stored_user_id === (int)$user_id && (int)$stored_post_id === (int)$post_id ) {
            return true;
        }
    }
    return false;
}
/* CF7投稿時のステータスを「公開」にする（オーディション用） */
add_filter( 'cf7_2_post_status_evaluations_audition', function($status, $ckf7_key, $submitted_data){
    return 'publish';
}, 10, 3);

/******************************************************
 * 7. オーディション評価データのCSVエクスポート（追加分）
 *****************************************************/
function register_audition_eval_csv_export() {
add_submenu_page(
    'edit.php?post_type=evaluations_audition', // オーディションのメニューの中
    'CSVエクスポート',                     
    'CSVエクスポート',                     
    'manage_options',                      
    'export-audition-eval-csv',             
    'export_audition_eval_csv_page'         
);
}
add_action('admin_menu', 'register_audition_eval_csv_export');

function export_audition_eval_csv_page() {
    $terms = get_terms(array('taxonomy' => 'evaluation_year', 'hide_empty' => true, 'orderby' => 'name', 'order' => 'DESC'));
?>
<div class="wrap">
    <h1>オーディション評価データのCSVエクスポート</h1>
    <p>抽出したい年度を選択してダウンロードしてください。</p>
    <form method="get" action="<?php echo admin_url('admin-post.php'); ?>">
        <input type="hidden" name="action" value="export_audition_eval_csv">
        <table class="form-table">
            <tr>
                <th scope="row">対象の年度</th>
                <td>
                    <select name="target_year" style="width: 300px;">
                        <option value="">-- 全てエクスポート --</option>
                        <?php foreach ($terms as $term): ?>
                            <option value="<?php echo esc_attr($term->name); ?>"><?php echo esc_html($term->name); ?></option>
                        <?php endforeach; ?>
                    </select>
                </td>
            </tr>
        </table>
        <?php submit_button('CSVをダウンロード'); ?>
    </form>
</div>
<?php
}

function export_audition_eval_csv_action() {
if (!current_user_can('manage_options')) wp_die('権限がありません');

$target_year = isset($_GET['target_year']) ? $_GET['target_year'] : '';
$filename = $target_year ? "audition_eval_{$target_year}.csv" : "audition_eval_list.csv";

header('Content-Type: application/octet-stream');
header('Content-Disposition: attachment; filename="' . $filename . '"');
header('Pragma: no-cache');
header('Expires: 0');
$output = fopen('php://output', 'w');

$header = array('年度', 'エントリーNo', '応募者名', '審査員名', '点数', 'コメント', '評価日');
$header = array_map(function($val) {
    return mb_convert_encoding($val, 'SJIS-win', 'UTF-8');
}, $header);
fputcsv($output, $header);

$args = array(
    'post_type'      => 'evaluations_audition',
    'posts_per_page' => -1,
    'post_status'    => 'publish',
);
if ($target_year) {
    $args['tax_query'] = array(array(
        'taxonomy' => 'evaluation_year',
        'field'    => 'name',
        'terms'    => $target_year,
    ));
}
$evaluations = get_posts($args);
if (empty($evaluations)) wp_die('オーディション評価データがありません。');

// 応募者×審査員ごとに最新のみ取得
$latest_evals = [];
foreach ($evaluations as $eval) {
    $judge = SCF::get('data_judge', $eval->ID);
    $key   = get_the_title($eval->ID) . '||' . $judge;
    if (!isset($latest_evals[$key]) || strtotime($eval->post_date) > strtotime($latest_evals[$key]->post_date)) {
        $latest_evals[$key] = $eval;
    }
}

foreach ($latest_evals as $eval) {
    $post_id    = $eval->ID;
    $year_terms = get_the_terms($post_id, 'evaluation_year');
    $year_label = ($year_terms && !is_wp_error($year_terms)) ? $year_terms[0]->name : ($target_year ?: '未設定');
    $row = array(
        $year_label,
        SCF::get('entryno', $post_id),
        get_the_title($post_id),
        SCF::get('data_judge', $post_id),
        SCF::get('data_score', $post_id),
        SCF::get('data_comment', $post_id),
        get_the_date('Y-m-d', $post_id),
    );
    $row = array_map(function($val) {
        return mb_convert_encoding($val ?: '', 'SJIS-win', 'UTF-8');
    }, $row);
    fputcsv($output, $row);
}
fclose($output);
exit;
}
add_action('admin_post_export_audition_eval_csv', 'export_audition_eval_csv_action');

/******************************************************
 * 8. 個人情報セクション（sec_private）をAjaxで取得する
 *****************************************************/
add_action('wp_ajax_load_sec_private_data', 'load_sec_private_data_callback');
add_action('wp_ajax_nopriv_load_sec_private_data', 'load_sec_private_data_callback');

function load_sec_private_data_callback() {
    $post_id = isset($_POST['post_id']) ? intval($_POST['post_id']) : 0;
    if (!$post_id) {
        wp_send_json_error('Invalid Post ID');
    }

    $post_type = get_post_type($post_id);

    // 【共通項目リスト】
    $private_items = [
        'gender'               => '性別',
        'birth'                => '⽣年⽉⽇',
        'age'                  => '年齢',
        'sns'                  => 'SNSアカウント',
        'photo'                => '顔写真',
        'performance_time'     => '最終学歴',
        'awards'               => '受賞歴',
        'aspiration'           => '志望動機',
        'purpose'              => '大学進学の目的',
        'awards_punishments'   => '特技・賞罰・職歴など',
        'connection'           => '愛知県とのつながり',
        'future_direction'     => '将来の方向性',
        'secondary_exam_piece' => '本選選択曲・課題曲',
        'how_did_you_know'     => '山田貞夫音楽賞について何でお知りになりましたか？',
        'referrers_name'       => '紹介者名',
    ];

    $html = '<table class="private-table">'; // 余計なstyleを全削除
    
    $has_data = false;
    foreach ($private_items as $field_name => $label) {

        // コンクール(competition_eval)の場合のAjaxリストから除外する
        if ($post_type === 'competition_eval') {
            // 除外したい Name
            $exclude_fields = ['career', 'awards', 'performance_time']; 
            if (in_array($field_name, $exclude_fields)) {
                continue; // リストに含まれていたらスキップ
            }
        }

        // オーディション(audition_eval)の場合のAjaxリストから除外する
        if ($post_type === 'audition_eval') {
            // 除外したい Name
            $exclude_fields = ['birth', 'age', 'career']; 
            if (in_array($field_name, $exclude_fields)) {
                continue; // リストに含まれていたらスキップ
            }
        }

        $value = SCF::get($field_name, $post_id);
        if (is_array($value)) {
            $value = !empty($value) ? reset($value) : '';
        }
        if ($value) {
            $has_data = true;
            $html .= '<tr>';
            $html .= '<th>' . esc_html($label) . '</th>';
            $html .= '<td>';

            // ★顔写真項目のときだけ画像タグを生成する
            if ($field_name === 'photo') {
                if (filter_var($value, FILTER_VALIDATE_URL) && strpos($value, 'drive.google.com') === false) {
                    // 直接URL（WordPressメディア等）はそのままimgタグに
                    $html .= '<img src="' . esc_url($value) . '" alt="顔写真" style="max-width:200px; height:auto; border: 1px solid #ccc; padding:2px; border-radius:4px;">';
                } elseif (preg_match('/([a-zA-Z0-9_-]{25,})/', $value, $matches)) {
                    // GoogleドライブURL or ファイルIDからサムネイルURLを構築
                    $file_id = $matches[1];
                    $direct_url = "https://drive.google.com/thumbnail?id=" . $file_id . "&sz=w1000";
                    $html .= '<img src="' . esc_url($direct_url) . '" alt="顔写真" style="max-width:200px; height:auto; border: 1px solid #ccc; padding:2px; border-radius:4px;">';
                } else {
                    $html .= esc_html($value);
                }
            } 
            // ★それ以外の項目（性別、学歴、志望動機など）はテキストとして出力する
            else {
                $html .= nl2br(esc_html($value));
            }

            $html .= '</td>';
            $html .= '</tr>';
        }
    }
    
    $html .= '</table>';

    if (!$has_data) {
        $html = '<p>表示できる詳細情報はありません。</p>';
    }

    wp_send_json_success($html);
}

/******************************************************
/* 評価データ CSVエクスポート（年度選択式にアップグレード）
/*****************************************************/

// 1. メニュー登録：個別のボタン形式から「エクスポート画面」1つに統合
function register_evaluations_csv_export() {
    add_submenu_page(
        'edit.php?post_type=evaluations',
        'CSVエクスポート',
        'CSVエクスポート',
        'manage_options',
        'export-evaluations-main', // スラッグを統合
        'export_evaluations_csv_page_integrated'
    );
}
add_action('admin_menu', 'register_evaluations_csv_export');

// 2. エクスポート画面の表示（年度を選ばせる）
function export_evaluations_csv_page_integrated() {
    // 存在する「年度」を取得
    $terms = get_terms(array('taxonomy' => 'evaluation_year', 'hide_empty' => true, 'orderby' => 'name', 'order' => 'DESC'));
    ?>
    <div class="wrap">
        <h1>評価データ CSVエクスポート</h1>
        <p>抽出したい年度（カテゴリ）を選択してダウンロードしてください。</p>
        
        <form method="get" action="<?php echo admin_url('admin-post.php'); ?>">
            <input type="hidden" name="action" value="export_evaluations_csv_final">
            
            <table class="form-table">
                <tr>
                    <th scope="row">対象の年度（カテゴリ）</th>
                    <td>
                        <select name="target_cat_name" id="target_cat_name" style="width: 300px;">
                            <option value="">-- 全てエクスポート --</option>
                            <?php foreach ($terms as $term): ?>
                                <option value="<?php echo esc_attr($term->name); ?>"><?php echo esc_html($term->name); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </td>
                </tr>
            </table>
            
            <?php submit_button('CSVをダウンロード'); ?>
        </form>
    </div>
    <?php
}

// 3. ダウンロード実行処理
add_action('admin_post_export_evaluations_csv_final', 'handle_integrated_csv_export');
function handle_integrated_csv_export() {
    if (!current_user_can('manage_options')) wp_die('権限がありません');

    // 選択されたカテゴリ名を取得
    $target_category = isset($_GET['target_cat_name']) ? $_GET['target_cat_name'] : '';
    
    // ファイル名（年度があれば名前に含める）
    $filename = $target_category ? "evaluation_list_{$target_category}.csv" : "evaluation_list_all.csv";

    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    header('Pragma: no-cache');
    header('Expires: 0');

    $output = fopen('php://output', 'w');
    if (!class_exists('SCF')) wp_die('SCF プラグインが必要です');

    // ヘッダー（一旦、点数ありのコンクール形式に統一）
    $header = array('応募者', 'カテゴリ', '審査員名', '点数', 'コメント', '評価日');
    $header = array_map(fn($v) => mb_convert_encoding($v, 'SJIS-win', 'UTF-8'), $header);
    fputcsv($output, $header);

    $args = array(
        'post_type'      => 'evaluations',
        'posts_per_page' => -1,
        'post_status'    => 'publish',
    );

    // 特定の年度が選ばれていれば絞り込む
    if ($target_category) {
        $args['tax_query'] = array(
            array(
                'taxonomy' => 'evaluation_year',
                'field'    => 'name',
                'terms'    => $target_category,
            ),
        );
    }

    $evaluations = get_posts($args);
    if (empty($evaluations)) wp_die('対象のデータが見つかりませんでした。');

    // 重複を排除して最新のみ取得（タイトル×審査員）
    $latest_evaluations = [];
    foreach ($evaluations as $evaluation) {
        $evaluator = SCF::get('data_judge', $evaluation->ID);
        $title = get_the_title($evaluation->ID);
        $unique_key = $title . '||' . $evaluator;

        if (!isset($latest_evaluations[$unique_key]) || strtotime($evaluation->post_date) > strtotime($latest_evaluations[$unique_key]->post_date)) {
            $latest_evaluations[$unique_key] = $evaluation;
        }
    }

    // CSV書き出し
    foreach ($latest_evaluations as $evaluation) {
        $p_id = $evaluation->ID;
        $year_terms = get_the_terms($p_id, 'evaluation_year');
        $year_label = ($year_terms && !is_wp_error($year_terms)) ? $year_terms[0]->name : ($target_category ?: '未設定');
        $row = array(
            get_the_title($p_id),
            $year_label,
            SCF::get('data_judge', $p_id),
            SCF::get('data_score', $p_id),
            SCF::get('data_comment', $p_id),
            get_the_date('Y-m-d H:i:s', $p_id)
        );
        $row = array_map(fn($v) => mb_convert_encoding($v ?: '', 'SJIS-win', 'UTF-8'), $row);
        fputcsv($output, $row);
    }

    fclose($output);
    exit;
}

/******************************************************
/* オーディション評価用CF7のマッピング 
/******************************************************/
/*	CF7 → Post Mapping　evaluations_audition
/*---------------------------------------------------------*/
add_filter('cf7_2_post_meta_fields_evaluations_audition', 'map_fields_for_evaluations_audition', 10, 3);
function map_fields_for_evaluations_audition($mapped_fields, $cf7_key, $submitted_data) {
    return array(
        'post_title'         => $submitted_data['post_title'] ?? '無題の評価',
        'data_category'      => $submitted_data['post_category'] ?? '',
        'data_comment'       => $submitted_data['comment'] ?? '',
        'user_id'            => $submitted_data['user_id'] ?? '',
        'evaluated_post_id'  => $submitted_data['evaluated_post_id'] ?? '',
        'entryno'            => $submitted_data['entryno'] ?? '',
        'item'               => $submitted_data['item'] ?? '',
        'data_judge'         => $submitted_data['user_shimei'] ?? '',
        'data_score'         => $submitted_data['score'] ?? '',
    );
}
?>