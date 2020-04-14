$(function(){
	$('footer .container .div_footer .footer-section > li >a').click(function(){
		var url = $(this).attr('href');
		if(url!='#index'){
			var go_to = url+':last';
			$go_sroll_url = ($(go_to).offset().top -50);
			$('html, body').animate({scrollTop:$go_sroll_url},500);
		}else{
			$go_sroll_url = 0;
			$('html, body').animate({scrollTop:$go_sroll_url},500);
		}
		return false;
	});
});