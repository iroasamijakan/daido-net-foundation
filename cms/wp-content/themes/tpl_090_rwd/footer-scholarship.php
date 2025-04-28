<?php /*<aside id="widget">
  <ul class="col3">
    <li><?php dynamic_sidebar('widgetLeft'); ?></li>
    <li><?php dynamic_sidebar('widgetCenter'); ?></li>
    <li><?php dynamic_sidebar('widgetRight'); ?></li>
  </ul> 
</aside>*/ ?>


<footer id="footer">
<?php if(is_user_logged_in()): ?>
  <p class="mb30">
    <a href="<?php echo home_url('/scholarship/login'); ?>">ログアウト</a>　<a href="<?php echo home_url('/scholarship/member'); ?>">会員プロフィール</a>
  </p>
<?php endif; ?>

<p><small>Copyright(c) <?php echo date('Y'); ?> <?php bloginfo('name'); ?>. All Rights Reserved.</small></p>
<p style="text-align: right; font-size: 75%;">Design by <a href="http://f-tpl.com" target="_blank">http://f-tpl.com</a><!-- ←「Design by http://f-tpl.com」を外す場合はシリアルキーが必要です http://f-tpl.com/credit/ --></p>
</footer>

<script src="<?php bloginfo('template_url'); ?>/js/script.js"></script>



<?php wp_footer(); ?>
</body>
</html>