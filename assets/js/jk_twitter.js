jQuery( document ).ready(function() {
	load_feeds();
});

function load_feeds()
{
	var data = {
			action: 'get_jk_twitter_feeds'
		};
	var page, id, sf_block;

	sf_block = '';
	post_url = '';
	html = '';

	jQuery.post(jkTwitterAjax.ajaxurl, data, function(response) {
		if (response != '')
		{
			response = jQuery.parseJSON(response);

			jQuery.each(response, function(i, item) {
//				page = response[i].page;
//				type = response[i].type;
//				id   = response[i].id;

				if(typeof response[i].html != 'undefined')
				{
					html = response[i].html;
					sf_block += html;
				}
			});

			jQuery('#social-media-container').html(sf_block);

			jQuery('#social-loader').hide();
		}
		else
		{
			console.log("Empty Response");
		}

	});
}