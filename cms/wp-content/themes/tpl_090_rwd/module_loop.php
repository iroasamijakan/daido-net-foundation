<style>
	.header h1 { text-align: center; font-size: 120%; }
</style>

<!-- ユーザー情報を取得 -->
<?php $user = wp_get_current_user(); ?>

<!-- ログインチェック -->
<?php if ($user->ID === 0): ?>

	<section id="login-section">
		<div class="innerS">
			<p>閲覧するには会員登録またはログインが必要です。</p>
			<ul class="col2 mt30">
				<?php /* <li style="text-align: center"><a href="<?php echo home_url('/create-account'); ?>">新規会員登録</a></li> */ ?>
				<li style="text-align: center"><a href="<?php echo home_url('/login'); ?>" class="btn">ログイン <i class="fa-solid fa-arrow-right" aria-hidden="true"></i></a></li>
				<li style="text-align: center"><a href="<?php echo home_url('/member'); ?>" class="btn">会員ページ <i class="fa-solid fa-arrow-right" aria-hidden="true"></i></a></li>
			</ul>
		</div>
	</section>

<?php else: ?>

	<?php if (have_posts()) :?>
	<ul class="col3">
		<?php while (have_posts()) : the_post(); ?>
			<li>
				<a href="<?php the_permalink() ?>">
					<div>
						<div class="thumbnail">
						<?php
							// YouTube ID を取得
							$youtube_id = SCF::get('youtubeid', get_the_ID());
							// サムネイル URL を取得
							$thumb_url = !empty($youtube_id) ? get_best_youtube_thumbnail($youtube_id) : '';

								if (!empty($thumb_url)) :
							?>
								<img src="<?php echo esc_url($thumb_url); ?>" alt="YouTubeサムネイル" style="width:100%; height:auto;">
							<?php else : ?>
								<img src="<?php echo esc_url(get_theme_file_uri('/images/thumbnail.webp')); ?>" alt="<?php the_title(); ?>" style="width:100%; height:auto;">
							<?php endif; ?>
							<?php
							// 投稿の ID を取得
							$post_id = get_the_ID();
							// カスタムフィールド "item" の値を取得
							$item = SCF::get('item', $post_id);
							?>
						</div>
						<h3><span><?php the_title(); ?></span><?php if (!empty($item)) : ?>／<span><?php echo esc_html($item); ?></span><?php endif; ?></h3>
						<?php /* the_excerpt(); */ ?>
						<p class="date"><?php the_time('Y/m/d');?></p>
					</div>
				</a>
			</li>
		<?php endwhile; ?>
	</ul>
	<?php else: ?>
	<div class="inner">
		<p class="ta-c">応募者は現在準備中です。</p>
	</div>
	<?php endif; ?>

<?php endif; ?>



