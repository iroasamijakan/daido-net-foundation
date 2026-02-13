<?php
remove_action('wp_head', 'wp_generator');
function my_scripts(){
	wp_enqueue_script('jquery');
}
add_action( 'wp_enqueue_scripts', 'my_scripts' );


/*	Load up the theme options
/*---------------------------------------------------------*/
add_action('admin_menu', 'my_theme_option');
function my_theme_option() {
  add_theme_page('テーマの使い方','テーマの使い方','edit_themes','theme_option','theme_option_file');
}
function theme_option_file(){
  require_once (get_template_directory() . '/inc/theme-options.php');
}


/*	Register navigation
/*---------------------------------------------------------*/
register_nav_menus( array(
	'primary' => __('Main Navigation', 'tpl_090_rwd'),
));


/*	Register sidebars
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



/*	This is all for compatibility with versions of WordPress prior to 3.4.
/*---------------------------------------------------------*/
define('NO_HEADER_TEXT', true);
define('HEADER_TEXTCOLOR', true);
define('HEADER_IMAGE', '%s/images/mainImage.jpg');
define('HEADER_IMAGE_WIDTH', 2560);
define('HEADER_IMAGE_HEIGHT', 1056);
add_theme_support('custom-header');

add_theme_support('custom-logo');


/*	This theme uses post thumbnails
/*---------------------------------------------------------*/
add_theme_support('post-thumbnails');
add_image_size('size1',280,280,true);


/*	Custom Excerpt "more" Link
/*---------------------------------------------------------*/
function change_excerpt_more($post) {
  return ' ...';
}
add_filter('excerpt_more', 'change_excerpt_more');



/*	Add admin CSS
/*---------------------------------------------------------*/
function tpl_090_rwd_admin_css(){
	$adminCssPath = get_template_directory_uri().'/cloud9_admin.css';
	wp_enqueue_style( 'theme', $adminCssPath , false, '2016');
}
add_action('admin_head', 'tpl_090_rwd_admin_css', 11);



/*	Page navigation
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


/*	Post shortcode
/*---------------------------------------------------------*/
function home_url_shortcode() {
  return home_url();
}
add_shortcode('home_url', 'home_url_shortcode');



/*	Default Post label
/*---------------------------------------------------------*/
function change_post_menu_label() {
  global $menu, $submenu;
  $menu[5][0] = '応募者'; // サイドメニューの「投稿」を「応募者」に変更
  $submenu['edit.php'][5][0] = '応募者一覧'; // 「投稿一覧」を「応募者一覧」に変更
  $submenu['edit.php'][10][0] = '新規応募者'; // 「新規追加」も変更
}
add_action('admin_menu', 'change_post_menu_label');

function change_post_object_label() {
  global $wp_post_types;
  $labels = &$wp_post_types['post']->labels;
  $labels->name = '応募者';
  $labels->singular_name = '応募者';
  $labels->add_new = '新規応募者';
  $labels->add_new_item = '新規応募者を追加';
  $labels->edit_item = '応募者を編集';
  $labels->new_item = '新規応募者';
  $labels->view_item = '応募者を表示';
  $labels->search_items = '応募者を検索';
  $labels->not_found = '応募者が見つかりません';
  $labels->not_found_in_trash = 'ゴミ箱に応募者はありません';
  $labels->all_items = '応募者一覧';
  $labels->menu_name = '応募者';
  $labels->name_admin_bar = '応募者';
}
add_action('init', 'change_post_object_label');

// エディタを非表示
function remove_post_editor() {
  remove_post_type_support('post', 'editor');
}
add_action('init', 'remove_post_editor');



/*	CF7 Member info
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


/*	CF7 Post info
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


/*	CF7 → Post
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
function register_evaluations_csv_export() {
  add_submenu_page(
      'edit.php?post_type=evaluations',
      'CSVエクスポート（オーディション）',
      'CSVエクスポート（オーディション）',
      'manage_options',
      'export-evaluations-audition',
      'export_evaluations_csv_page_audition'
  );

  add_submenu_page(
      'edit.php?post_type=evaluations',
      'CSVエクスポート（コンクール）',
      'CSVエクスポート（コンクール）',
      'manage_options',
      'export-evaluations-competition',
      'export_evaluations_csv_page_competition'
  );
}
add_action('admin_menu', 'register_evaluations_csv_export');

// オーディション用ページ
function export_evaluations_csv_page_audition() {
  echo '<div class="wrap"><h1>CSVエクスポート（オーディション）</h1>';
  echo '<p><a href="' . admin_url('admin-post.php?action=export_evaluations_csv_audition') . '" class="button button-primary">CSVダウンロード</a></p></div>';
}

// コンクール用ページ
function export_evaluations_csv_page_competition() {
  echo '<div class="wrap"><h1>CSVエクスポート（コンクール）</h1>';
  echo '<p><a href="' . admin_url('admin-post.php?action=export_evaluations_csv_competition') . '" class="button button-primary">CSVダウンロード</a></p></div>';
}

// メイン処理（共通ロジックを再利用）
function export_evaluations_csv_by_category($target_category) {
  if (!current_user_can('manage_options')) wp_die('権限がありません');

  header('Content-Type: application/octet-stream');
  header('Content-Disposition: attachment; filename="evaluation_list_' . $target_category . '.csv"');
  header('Pragma: no-cache');
  header('Expires: 0');

  $output = fopen('php://output', 'w');
  if ($output === false) wp_die('CSVファイルを開けませんでした');

  if (!class_exists('SCF')) wp_die('SCF プラグインが有効化されていません。');

  // CSVヘッダー（カテゴリに応じて）
  if (str_contains($target_category, 'コンクール')) {
      $header = array('応募者', 'カテゴリ', '審査員名', '点数', 'コメント', '評価日');
  } else {
      $header = array('応募者', 'カテゴリ', '審査員名', 'コメント', '評価日');
  }

  $header = array_map(fn($v) => mb_convert_encoding($v, 'SJIS-win', 'UTF-8'), $header);
  fputcsv($output, $header);

  $args = array(
      'post_type'      => 'evaluations',
      'posts_per_page' => -1,
      'post_status'    => 'publish',
  );
  $evaluations = get_posts($args);
  if (empty($evaluations)) wp_die('評価データがありません。');

  $latest_evaluations = [];

  foreach ($evaluations as $evaluation) {
      $post_id   = $evaluation->ID;
      $title     = get_the_title($post_id);
      $evaluator = SCF::get('data_judge', $post_id);
      $category  = SCF::get('data_category', $post_id);

      if ($category !== $target_category) continue;

      $unique_key = $title . '||' . $evaluator;

      if (!isset($latest_evaluations[$unique_key]) || strtotime($evaluation->post_date) > strtotime($latest_evaluations[$unique_key]->post_date)) {
          $latest_evaluations[$unique_key] = $evaluation;
      }
  }

  foreach ($latest_evaluations as $evaluation) {
      $post_id   = $evaluation->ID;
      $title     = get_the_title($post_id);
      $category  = SCF::get('data_category', $post_id) ?: '未設定';
      $evaluator = SCF::get('data_judge', $post_id) ?: '未設定';
      $score     = SCF::get('data_score', $post_id) ?: '';
      $comment   = SCF::get('data_comment', $post_id) ?: '未設定';
      $post_date = get_the_date('Y-m-d H:i:s', $post_id);

      if (str_contains($target_category, 'コンクール')) {
          $row = array($title, $category, $evaluator, $score, $comment, $post_date);
      } else {
          $row = array($title, $category, $evaluator, $comment, $post_date);
      }

      $row = array_map(fn($v) => mb_convert_encoding($v, 'SJIS-win', 'UTF-8'), $row);
      fputcsv($output, $row);
  }

  fclose($output);
  exit;
}

// アクション登録（オーディション／コンクール）
add_action('admin_post_export_evaluations_csv_audition', function() {
  export_evaluations_csv_by_category('2025 オーディション');    // 年ごとにここを修正
});

add_action('admin_post_export_evaluations_csv_competition', function() {
  export_evaluations_csv_by_category('2025 コンクール');    // 年ごとにここを修正
});



/*	CF7 評価履歴
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


/*	Scholarship post
/*---------------------------------------------------------*/
function create_scholarship_post_type() {
  $labels = array(
      'name'               => '奨学金応募者',
      'singular_name'      => '奨学金応募者',
      'menu_name'          => '奨学金応募者',
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


/*	CF7 → Post（scholarship）
/*---------------------------------------------------------*/
/* 奨学金評価データ：カスタム投稿タイプ */
function create_scholar_eval_post_type() {
  $labels = array(
      'name'          => '奨学金評価データ',
      'singular_name' => '奨学金評価データ',
      'menu_name'     => '奨学金評価データ',
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
      'post_type'      => 'scholar_eval',  // ←奨学金評価データの投稿タイプ名
      'posts_per_page' => -1,
      'post_status'    => 'publish',
  );

  $evaluations = get_posts($args);

  foreach ($evaluations as $evaluation) {
      $evaluation_id = $evaluation->ID;
      $stored_user_id = SCF::get('user_id', $evaluation_id);
      $stored_post_id = SCF::get('evaluated_post_id', $evaluation_id);

      if ((int)$stored_user_id === (int)$user_id && (int)$stored_post_id === (int)$post_id) {
          return true;  // 評価済み
      }
  }
  return false;  // 未評価
}


/* 奨学金評価データ：CSVエクスポートメニュー追加 */
function register_scholar_eval_csv_export() {
  add_submenu_page(
      'edit.php?post_type=scholar_eval',     // 親メニュー（奨学金評価データ）
      'CSVエクスポート',                     // ページタイトル
      'CSVエクスポート',                     // サブメニュー名
      'manage_options',                      // 権限
      'export-scholar-eval-csv',             // スラッグ
      'export_scholar_eval_csv_page'         // コールバック関数
  );
}
add_action('admin_menu', 'register_scholar_eval_csv_export');


// エクスポートページの表示
function export_scholar_eval_csv_page() {
  ?>
  <div class="wrap">
      <h1>奨学金評価データのCSVエクスポート</h1>
      <p><a href="<?php echo admin_url('admin-post.php?action=export_scholar_eval_csv'); ?>" class="button button-primary">CSVダウンロード</a></p>
  </div>
  <?php
}


// エクスポート処理本体
function export_scholar_eval_csv_action() {
  if (!current_user_can('manage_options')) {
      wp_die('権限がありません');
  }

  header('Content-Type: application/octet-stream');
  header('Content-Disposition: attachment; filename="scholar_eval_list.csv"');
  header('Pragma: no-cache');
  header('Expires: 0');

  $output = fopen('php://output', 'w');
  if ($output === false) {
      wp_die('CSVファイルを開けませんでした');
  }

  // CSVヘッダー行（Shift-JIS）
  $header = array('エントリーNo', '応募者名', '審査員名', '学歴・指導教官', '成績', '受賞歴', '志望動機', '合計点', '備考', '評価日');
  $header = array_map(function($val) {
      return mb_convert_encoding($val, 'SJIS-win', 'UTF-8');
  }, $header);
  fputcsv($output, $header);

  if (!class_exists('SCF')) {
      wp_die('SCF プラグインが有効化されていません。');
  }

  $args = array(
      'post_type'      => 'scholar_eval',
      'posts_per_page' => -1,
      'post_status'    => 'publish',
  );
  $evaluations = get_posts($args);

  if (empty($evaluations)) {
      wp_die('奨学金評価データがありません。');
  }

  // 応募者×審査員ごとの最新のみを取得
  $latest_evals = [];
  foreach ($evaluations as $eval) {
      $post_id   = $eval->ID;
      $name      = get_the_title($post_id);
      $judge     = SCF::get('scholarship_user', $post_id);
      $key       = $name . '||' . $judge;

      if (!isset($latest_evals[$key]) || strtotime($eval->post_date) > strtotime($latest_evals[$key]->post_date)) {
          $latest_evals[$key] = $eval;
      }
  }

  // エントリーNo順にソート
  usort($latest_evals, function($a, $b) {
      $a_no = SCF::get('scholarship_entryno', $a->ID);
      $b_no = SCF::get('scholarship_entryno', $b->ID);
      return strnatcmp($a_no, $b_no);
  });

  foreach ($latest_evals as $eval) {
      $post_id   = $eval->ID;
      $entryno   = SCF::get('scholarship_entryno', $post_id) ?: '未設定';
      $name      = get_the_title($post_id);
      $judge     = SCF::get('scholarship_user', $post_id) ?: '未設定';
      $academic  = (int) SCF::get('score_academic', $post_id);
      $grades    = (int) SCF::get('score_grades', $post_id);
      $awards    = (int) SCF::get('score_awards', $post_id);
      $reason    = (int) SCF::get('score_reason', $post_id);
      $total     = $academic + $grades + $awards + $reason;
      $comment   = SCF::get('scholarship_comment', $post_id);
      $date      = get_the_date('Y-m-d', $post_id);

      $row = array($entryno, $name, $judge, $academic, $grades, $awards, $reason, $total, $comment, $date);
      $row = array_map(function($val) {
          return mb_convert_encoding($val, 'SJIS-win', 'UTF-8');
      }, $row);

      fputcsv($output, $row);
  }

  fclose($output);
  exit;
}
add_action('admin_post_export_scholar_eval_csv', 'export_scholar_eval_csv_action');

?>