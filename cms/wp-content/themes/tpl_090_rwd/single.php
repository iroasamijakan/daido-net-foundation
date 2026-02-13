<?php get_header();?>
<style>
	.category-name {
		border: solid 1px #767676; color: #767676; display: inline-block; font-size: 12px; margin-bottom: 10px; padding: 5px 10px;
	}
</style>


<div id="documents" class="posts-wrap">
<?php if (have_posts()) : ?>
	<?php while (have_posts()) : the_post(); ?>
		<main id="post-<?php the_ID(); ?>" class="innerS">
			<?php /*
			if(has_post_thumbnail()):?>
				<div id="singleImg">
					<?php the_post_thumbnail( array( 300, 300 ) );?>
				</div>
			<?php endif;
			*/ ?>

			<?php
				// 投稿のカテゴリを取得
				$categories = get_the_category();
				$child_category = '';

				if (!empty($categories)) {
					foreach ($categories as $category) {
						if ($category->parent != 0) { // 子カテゴリを取得
							$child_category = $category->name;
							break; // 最初の子カテゴリだけを取得
						}
					}
				}

				// 記事のカスタムフィールドの値を取得
				$No = SCF::get('entryno'); 
				$item = SCF::get('item'); 
				$youtubeid = SCF::get('youtubeid'); 
				$profiles = SCF::get('profile'); // 繰り返しフィールド
				$app = SCF::get('application');
			?>

			<header>
				<?php if ($child_category) {
					echo '<span class="category-name">' . esc_html($child_category) . '</span>';
				} ?>
				<?php if (!empty($No)): ?>
					<p>エントリーNo：<?php echo esc_attr($No); ?></p>
				<?php endif; ?>
				<h1><?php the_title(); ?> <?php if (!empty($item)): ?> ／ <span><?php echo esc_html($item); endif; ?></span></h1>
			</header>
	
			<div class="post">
				<!-- youtubeid の埋め込み表示 -->
				<?php if (!empty($youtubeid)): ?>
					<div class="youtube-video">
						<iframe src="https://www.youtube.com/embed/<?php echo esc_attr($youtubeid); ?>" frameborder="0" allowfullscreen>
						</iframe>
					</div>
				<?php endif; ?>

				<!-- 繰り返しフィールド profile の表示 -->
				<?php if (!empty($profiles) && is_array($profiles)): ?>
					<div class="profiles">
						<?php foreach ($profiles as $profile): ?>
							<?php if (!empty($profile['profile_cont'])): // profile_cont が空でない場合のみ出力 ?>
								<div class="profile-item">
									<h3><?php echo esc_html($profile['profile_ttl']); ?></h3>
									<p><?php echo nl2br(esc_html($profile['profile_cont'])); ?></p>
								</div>
							<?php endif; ?>
						<?php endforeach; ?>
					</div>
				<?php endif; ?>
				<?php if (!empty($app)): ?>
					<p><a href="<?php echo wp_get_attachment_url($app); ?>" style="background: #1e328b;color: #fff;padding: 1rem;">応募申請書を見る ></a></p>
				<?php endif; ?>



				<?php
					$current_user_id = get_current_user_id();
					$current_post_id = get_the_ID();

					// デバッグ: 取得したユーザーIDと投稿IDを確認
					// error_log("single.php: チェック対象 - ユーザーID: $current_user_id, 投稿ID: $current_post_id");

					if (has_user_already_evaluated($current_user_id, $current_post_id)) {
						// error_log("single.php: 評価済みのためメッセージを表示");
						echo '<p style="color: red; font-weight: bold;">※評価済みです。</p>';
					}
				?>
				<!-- 評価フォームの表示 -->
				<div class="box">
					<?php
					$slugs = wp_list_pluck(get_the_category(), 'slug');
					if (in_array('concours-2025', $slugs)) : ?>
						<!-- コンクール用フォーム -->
						<p>演奏の評価をお願いいたします。</p>
						<?php echo do_shortcode('[contact-form-7 id="702a10d" title="コンクール評価フォーム"]'); ?>
					<?php elseif (in_array('audition-2025', $slugs)) : ?>
						<!-- オーディション用フォーム -->
						<p>コメントあればご記入ください。</p>
						<?php echo do_shortcode('[contact-form-7 id="a4f2a3e" title="オーディション評価フォーム"]'); ?>
					<?php endif; ?>
				</div>
			</div>

			<?php wp_link_pages( array(
				'before'      => '<p id="pageLinks">ページ :',
				'after'       => '</p>',
				'link_before' => '<span>',
				'link_after'  => '</span>',
			));?>

			<div class="entry-footer">
				<span>CATEGORY：<?php echo get_the_category_list(' / '); ?></span>
				<?php echo get_the_tag_list( '<span>TAGS：', ' / ', '</span>'); ?>
				<span><time datetime="<?php the_time('Y-m-d')?>"><?php the_time('Y/m/d')?></time></span>
				<?php edit_post_link(); ?>
			</div>

		</main>
	<?php endwhile; ?>

	<aside id="related-posts">
		<?php
		$categories = get_the_category();
		$category_name = '同じカテゴリの記事'; // デフォルトの見出し
		$current_parent_category_id = null; // 現在の親カテゴリID
		$current_child_category_id = null; // 現在の子カテゴリID

		if (!empty($categories)) {
			foreach ($categories as $category) {
				if ($category->parent != 0) { 
					// 子カテゴリがある場合、その親カテゴリIDを取得
					$current_parent_category_id = $category->parent;
					$current_child_category_id = $category->term_id;
					$category_name = $category->name;
					break;
				}
			}
			if ($category_name === '同じカテゴリの記事') {
				// 子カテゴリがなかった場合、最初の親カテゴリを取得
				$current_parent_category_id = $categories[0]->term_id;
				$category_name = $categories[0]->name;
			}
		}
		?>
		<h3><?php echo esc_html($category_name); ?></h3>
		<ul>
			<?php
			if ($categories) {
				$category_ids = array();
				foreach ($categories as $category) {
					$category_ids[] = $category->term_id;
				}

				$args = array(
					'category__in'   => $category_ids, // 同じカテゴリの記事
					'posts_per_page' => -1, // 表示する記事数
					'orderby'        => 'date', // 日付順
					'order'          => 'DESC' // 新しい順
				);

				$related_posts = new WP_Query($args);
				if ($related_posts->have_posts()) {
					while ($related_posts->have_posts()) {
						$related_posts->the_post();

						// 現在の投稿かどうかを判定
						$current_class = (get_the_ID() === get_queried_object_id()) ? ' class="current"' : '';

						// 記事ごとの item カスタムフィールドを取得
						$item_value = SCF::get('item', get_the_ID());
						?>
						<li<?php echo $current_class; ?>>
							<a href="<?php the_permalink(); ?>">
								<?php the_title(); ?>
								<?php if (!empty($item_value)) : ?>
									／ <span><?php echo esc_html($item_value); ?></span>
								<?php endif; ?>
							</a>
						</li>
						<?php
					}
					wp_reset_postdata();
				} else {
					echo '<li>関連する記事はありません。</li>';
				}
			}
			?>
		</ul>

		<?php
		// 他の親カテゴリの記事一覧（カテゴリページ）へのリンクを追加
		$other_parent_category = null;
		$other_child_category = null;

		// すべての親カテゴリを取得
		$all_categories = get_categories(array(
			'parent' => 0, // 親カテゴリのみ取得
			'hide_empty' => false
		));

		foreach ($all_categories as $parent_category) {
			if ($parent_category->term_id !== $current_parent_category_id) {
				$other_parent_category = $parent_category;
				break;
			}
		}

		if ($other_parent_category) {
			// その親カテゴリに属する子カテゴリを取得
			$child_categories = get_categories(array(
				'parent' => $other_parent_category->term_id,
				'hide_empty' => false
			));

			// 最初の子カテゴリを取得（存在する場合）
			if (!empty($child_categories)) {
				$other_child_category = $child_categories[0];
			}

			if ($other_child_category) {
				?>
				<h3 class="mt30"><a href="<?php echo get_category_link($other_child_category->term_id); ?>">
					<?php echo esc_html($other_child_category->name); ?>一覧はこちら <i class="fa-solid fa-arrow-right" aria-hidden="true"></i>
				</a></h3>
				<?php
			}
		}
		?>
	</aside>

<?php endif; ?>
</div> <!-- /#document -->


<?php get_footer(); ?>