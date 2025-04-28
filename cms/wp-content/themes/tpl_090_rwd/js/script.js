// jQuery(function($){
// 	$('.panel').hide();
// 	$('#menuWrap').toggle(function(){
// 		$(this).next().slideToggle();
// 		$('#menuBtn').toggleClass('close');
// 	},
// 	function(){
// 		$(this).next().slideToggle();
// 		$('#menuBtn').removeClass('close');
// 	});
// });

jQuery(function($) {
    $('.panel').hide(); // 初期状態でメニューを隠す

    $('#menuWrap').on('click', function() {
        $('.panel').slideToggle(); // メニューを開閉
        $('#menuBtn').toggleClass('close'); // ボタンのクラスを切り替え
    });
});
