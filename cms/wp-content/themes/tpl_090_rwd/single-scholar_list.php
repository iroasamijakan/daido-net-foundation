<?php get_header(); ?>
<style>
	.category-name { border: solid 1px #767676; color: #767676; display: inline-block; font-size: 12px; margin-bottom: 10px; padding: 5px 10px; }
	.post-wrap header { border-bottom: solid 1px #767676; }
	.post h2 { font-size: 160%; }
	.posts-wrap header { border-bottom: solid 1px #c3c4c7; padding-bottom: 30px; }
</style>

<!-- ユーザー情報を取得 -->
<?php
$current_user_id = get_current_user_id();

if ( is_user_logged_in() ) :
    // ユーザーメタから担当カテゴリを取得
    $user_categories = get_user_meta($current_user_id, 'user_category', true);
    // 配列かどうか確認（万が一の対策）
    if ( is_array($user_categories) && in_array('scholarship', $user_categories) ) : ?>

		<div id="documents" class="posts-wrap">
		<?php if (have_posts()) : while (have_posts()) : the_post(); ?>

			<main id="post-<?php the_ID(); ?>" class="innerS">

				<header class="mb30">
					<span class="category-name">奨学金応募者</span>
					<h1><?php the_title(); ?></h1>
				</header>

				<div class="post">
					<!-- エントリーNoの表示 -->
					<?php
					/*
					$entry_no = SCF::get('scholarship_entryno');
					if (!empty($entry_no)) :
					?>
						<p class="ta-r"><strong>エントリーNo：</strong> <?php echo esc_html($entry_no); ?></p>
					<?php endif; 
					*/ ?>

					<!-- 学校名 -->
					<?php
					$school = SCF::get('scholarship_school');
					if (!empty($school)) :
					?>
						<h2 class="ta-l">学校名</h2>
						<p><?php echo esc_html($school); ?></p>
					<?php endif; ?>

					<!-- 受賞歴 -->
					<?php
					$awards = SCF::get('scholarship_awards');
					if (!empty($awards)) :
					?>
						<h2 class="ta-l">受賞歴</h3>
						<p><?php echo nl2br(esc_html($awards)); ?></p>
					<?php endif; ?>

					<!-- 応募理由・目的 -->
					<?php
					$purpose = SCF::get('scholarship_purpose');
					if (!empty($purpose)) :
					?>
						<h2 class="ta-l">応募理由・目的</h3>
						<p><?php echo nl2br(esc_html($purpose)); ?></p>
					<?php endif; ?>

					<!-- 繰り返しフィールドの表示 -->
					<?php
					$documents = SCF::get('scholarship_documents');
					if (!empty($documents)) :
					?>
						<h2 class="ta-l">提出書類一覧</h2>
						<ul>
							<?php foreach ($documents as $doc) : 
								$doc_title = $doc['scholarship_ttl'];
								$doc_file_url = wp_get_attachment_url($doc['scholarship_file']);
								?>
								<li>
									<?php if ($doc_file_url): ?>
										<a href="<?php echo esc_url($doc_file_url); ?>" target="_blank">
											<?php echo esc_html($doc_title); ?>
										</a>
									<?php else: ?>
										<?php echo esc_html($doc_title); ?>（ファイルなし）
									<?php endif; ?>
								</li>
							<?php endforeach; ?>
						</ul>
					<?php endif; ?>

					<div class="mt30">
						<?php
							$current_user_id = get_current_user_id();
							$current_post_id = get_the_ID();

							// 奨学金用の評価済みチェック関数（専用に分けると安全）
							if (has_user_already_scholar_evaluated($current_user_id, $current_post_id)) {
								echo '<p style="color: red; font-weight: bold;">※この応募者は評価済みです。</p>';
							}
							// 評価フォームの表示
						?>
						<div class="box">
							<p>奨学金応募者の評価をお願いいたします。</p>
							<?php echo do_shortcode('[contact-form-7 id="0e2c1ac" title="奨学金評価評価フォーム"]'); ?>
						</div>
					</div>

				</div>

			</main>

		<?php endwhile; endif; ?>

			<aside id="related-posts">
				<h3>奨学金応募者一覧</h3>
				<ul>
					<?php
					// 現在の投稿IDを取得
					$current_id = get_the_ID();

					// 同じ投稿タイプ（scholar_list）の全記事を取得
					$args = array(
						'post_type'      => 'scholar_list',
						'posts_per_page' => -1,
						'orderby'        => 'menu_order',
						'order'          => 'ASC'
					);

					$scholar_posts = get_posts($args);

					if ($scholar_posts) :
						foreach ($scholar_posts as $post) :
							setup_postdata($post);

							// 現在の記事なら class="current" を付与
							$class = ($current_id == get_the_ID()) ? 'current' : '';
							?>
							<li class="<?php echo $class; ?>">
								<a href="<?php the_permalink(); ?>">
									<?php the_title(); ?>
								</a>
							</li>
						<?php
						endforeach;
						wp_reset_postdata();
					else :
						echo '<li>応募者がありません。</li>';
					endif;
					?>
				</ul>
			</aside>
		</div>

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


<script>
// ラジオボタンの選択肢を取得
document.addEventListener('DOMContentLoaded', function() {
    const radios = document.querySelectorAll('input[name="result_radio"]');
    const hiddenField = document.getElementById('result_hidden');

    radios.forEach(function(radio) {
        radio.addEventListener('change', function() {
            if (this.checked) {
                hiddenField.value = this.value;
            }
        });
    });
});
</script>

<?php get_footer('scholarship'); ?>