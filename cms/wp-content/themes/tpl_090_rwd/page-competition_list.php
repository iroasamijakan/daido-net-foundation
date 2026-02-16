<?php
/*
Template Name: 応募者一覧（コンクール）
*/
get_header(); ?>

<section id="toppage">
  <header class="header">
    <h1 class="title"><span><?php the_title(); ?></span></h1>
  </header>
  
  <div class="post innerS">
    <?php if ( have_posts()) : the_post(); the_content(); endif; ?>
  </div>

  <?php
  // コンクール応募者(competition_eval)を全件取得
  $args = array(
      'post_type'      => 'competition_eval',
      'posts_per_page' => -1,
      'meta_key'       => 'entryno',
      'orderby'        => 'meta_value_num',
      'order'          => 'ASC',
  );
  $query = new WP_Query($args);
  ?>




  <div class="inner">
    ああああああ
    <?php if ($query->have_posts()) : ?>
      <ul class="col3">
        <?php while ($query->have_posts()) : $query->the_post(); 
          $no = SCF::get('entryno');
          $inst = SCF::get('instrument_played');
          $youtube_url = SCF::get('youtube_url');
          
          // YouTube ID抽出
          $youtube_id = '';
          if (preg_match('%(?:youtube\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/ ]{11})%i', $youtube_url, $match)) {
              $youtube_id = $match[1];
          }
          
          // サムネイルURL取得（関数がない場合はデフォルト画像）
          $thumb_url = '';
          if ($youtube_id && function_exists('get_best_youtube_thumbnail')) {
              $thumb_url = get_best_youtube_thumbnail($youtube_id);
          }
        ?>
          <li>
            <a href="<?php the_permalink(); ?>">
              <div>
                <?php if ($thumb_url): ?>
                  <img src="<?php echo esc_url($thumb_url); ?>" alt="YouTubeサムネイル" style="width:100%; height:auto;">
                <?php else: ?>
                  <div style="width:100%; aspect-ratio: 16/9; background:#eee; display:flex; align-items:center; justify-content:center;">No Video</div>
                <?php endif; ?>

                <h3>
                  <?php if($no): ?><span>No.<?php echo esc_html($no); ?> </span><?php endif; ?>
                  <span><?php the_title(); ?></span>
                  <?php if($inst): ?>／<span><?php echo esc_html($inst); ?></span><?php endif; ?>
                </h3>
                <p class="date"><?php echo get_the_date('Y/m/d'); ?></p>
              </div>
            </a>
          </li>
        <?php endwhile; ?>
      </ul>
    <?php else : ?>
      <p style="text-align:center;">応募者データが登録されていません。</p>
    <?php endif; wp_reset_postdata(); ?>
  </div>
</section>

<?php get_footer(); ?>