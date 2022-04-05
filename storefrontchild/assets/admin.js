jQuery(function($){

	$('body').on( 'click', '._thumbnail', function(e){

		e.preventDefault();

		var button = $(this),
		custom_uploader = wp.media({
			title: 'Insert image',
			library : {				
				type : 'image'
			},
			button: {
				text: 'Использовать это изображение' 
			},
			multiple: false
		}).on('select', function() { 
			var attachment = custom_uploader.state().get('selection').first().toJSON();
			button.html('<img src="' + attachment.url + '" style="max-width:150px;">').next().show();
			$(".class_hidden").val(attachment.id);
			$('._thumbnail_del').css( "display", "block" );
		}).open();
		
	});

	$('body').on('click', '._thumbnail_del', function(e){

		e.preventDefault();

		var button = $(this);
		button.next().val(''); 
		$(".class_hidden").val("");
		button.hide().prev().html('<a href="#" class="_thumbnail"><input type="button" class="button" value="Выбрать изображение" /></a>');
	});	
	
	$('body').on( 'click', '#save_post', function(e){
		$("#post").submit();
	});
	
	$('body').on( 'click', '#cf_clear', function(e){
		$("._thumbnail_del").click();
		$("#_select_type option:first").prop("selected", true);	
	});
	
});