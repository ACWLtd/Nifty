jQuery(document).ready(function($) {
	var mainHeight = $('.chs-main').outerHeight();
	var sideHeight = $('.chs-sidebar').outerHeight();

	if ( sideHeight > mainHeight ) {
		$('.chs-main').css('minHeight', sideHeight + 'px');
	}

	$('.editor-content img').each(function() {
		$(this).addClass('img-thumbnail').wrap("<a href='" + $(this).attr('src') + "' class='lightbox'></a>");
	});

	$('#french').click(function(event) {
		event.preventDefault();
		var $this = $(this);
		
		$.post($this.data('url'), function(data) {
			if ( data.page != undefined ) {
				$('.editor-content').html(data.page);
				$this.addClass('visibility');
				$('#english').parent().removeClass('visibility');

				$('#spanish').click(function() {
					$this.removeClass('visibility');
				});
			}
			if ( data.error != undefined )
				alert(data.error);
		}, 'json');		

	});

	$('#spanish').click(function(event) {
		event.preventDefault();
		var $this = $(this);
		
		$.post($this.data('url'), function(data) {
			if ( data.page != undefined ) {
				$('.editor-content').html(data.page);
				$this.addClass('visibility');
				$('#english').parent().removeClass('visibility');

				$('#french').click(function() {
					$this.removeClass('visibility');
				});
			}
			if ( data.error != undefined )
				alert(data.error);
		}, 'json');
	});

	$('#english').click(function(event) {
		event.preventDefault();
		location.reload(false);
	});

});