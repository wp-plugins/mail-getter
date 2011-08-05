function mg_ajax_magic(url) {
	jQuery("#maillist").text('Loading...');
	jQuery.post(url, {
			action:"mail_getter_database_stuff",
			mg_post_id: jQuery("#mg_post_id").val(),
			mg_option: jQuery("#mg_option").val()
			}, function (data) {
					if(data){
					jQuery("#maillist").text(data);
					} else {
						data='No results, check the post ID';
						jQuery("#maillist").text(data);	
						}
					
				}
		);
}

