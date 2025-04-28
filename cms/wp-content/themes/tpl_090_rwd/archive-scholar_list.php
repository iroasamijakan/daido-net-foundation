<?php get_header('scholarship'); ?>

<style>
    .post .scholarship-list { margin-inline: auto; }
    .post .scholarship-list .ttl { font-size: 120%; font-family: sans-serif; margin: 0; padding: 0; text-align: left; }
    .post .scholarship-list .school-name { margin-bottom: 0;}
    .post .scholarship-list li { list-style: none; padding: 0; }
    .post .scholarship-list li a { background-color: #f0f0f1; display: flex; align-items: center; flex-wrap: wrap; gap: 20px; padding: 10px 10px 10px 30px; position: relative; transition: all .3s; }
    .post .scholarship-list li a::before { background-color: #2b86b5; content: ""; clip-path: polygon(0 0, 100% 50%, 0 100%); height: 10px; position: absolute; left: 14px; top: 50%; transform: translateY(-50%); width: 7px; }
    .post .scholarship-list li a:hover { background-color: #ccc; text-decoration: none; }
    @media screen and (min-width: 981px) {
        .post .scholarship-list { width: 60%; }
    }
    @media screen and (max-width: 980px) {
        .post .scholarship-list { width: 80%; }
    }
</style>

<!-- ユーザー情報を取得 -->
<?php
$current_user_id = get_current_user_id();

if ( is_user_logged_in() ) :
    // ユーザーメタから担当カテゴリを取得
    $user_categories = get_user_meta($current_user_id, 'user_category', true);
    // 配列かどうか確認（万が一の対策）
    if ( is_array($user_categories) && in_array('scholarship', $user_categories) ) : ?>

        <section id="toppage">
            <header class="header ta-c">
                <h1 class="title"><span>奨学金応募者一覧</span></h1>
            </header>
        </section>


        <section class="post innerS">
            <?php if (have_posts()) : ?>
                <ul class="scholarship-list">
                    <?php while (have_posts()) : the_post(); ?>
                        <li class="scholarship-item">
                            <a href="<?php the_permalink(); ?>">
                                <h2 class="ttl"><?php the_title(); ?></h2>
                                <?php
                                // 学校名を取得
                                $school = SCF::get('scholarship_school');
                                if (!empty($school)) :
                                ?>
                                    <p class="school-name"><?php echo esc_html($school); ?></p>
                                <?php endif; ?>
                            </a>
                        </li>
                    <?php endwhile; ?>
                </ul>

                <!-- ページネーション -->
                <div class="pagination">
                    <?php the_posts_pagination(); ?>
                </div>

            <?php else : ?>
                <p>応募者が見つかりませんでした。</p>
            <?php endif; ?>
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
                <li style="text-align: center"><a href="<?php echo home_url('/member'); ?>" class="btn">会員ページ <i class="fa-solid fa-arrow-right" aria-hidden="true"></i></a></li>
            </ul>
        </div>
    </section>
<?php endif; ?>

<?php get_footer('scholarship'); ?>
