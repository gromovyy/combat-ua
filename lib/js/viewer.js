function ajax(url, data, onSuccess, onError, thisArg) {
	$.post(jsData.baseUrl+url, $.extend({
		isAjax: true
	}, data), function(response)
	{
//		console.log(response);
		if(response.error)
		{
			alert(response.error);
			if(onError)
				onError.call(thisArg, response);
		}
		else
		{
			for(i in response.updatedViews)
			{
				var data_id = $(response.updatedViews[i]).data('id');
				if (data_id)
					$('[data-id="'+data_id+'"]').replaceWith(response.updatedViews[i]);
				else {
					var id = $(response.updatedViews[i]).attr('id');
					$('#'+id).replaceWith(response.updatedViews[i]);
				}
			}
//			if(response.updatedViews && updateHandlers)
//				updateHandlers();
			jsData = $.extend(true, jsData, response.jsData);
			eval(response.script);
			$(thisArg || document).trigger('ajaxSuccess', response)
			if(onSuccess)
				onSuccess.call(thisArg, response);
		}
	}, 'json');
}

function reload(url, data, onSuccess, onError, thisArg)
{
	$.post(jsData.baseUrl+url, $.extend({
		isAjax: true
	}, data), function(response)
	{
//		console.log(response);
		if(response.error)
		{
			alert(response.error);
			if(onError)
				onError.call(thisArg, response);
		}
		else
		{
			window.location.reload(true);
		}
	}, 'json');
}

function update(url,data,onSuccess)
{
	ajax(url, data, onSuccess);
}

function exec(url, url_update, data, is_reload)
{
	if (typeof(url)=='undefined') return;
	$.post(jsData.baseUrl+url, $.extend({
		isAjax: true
	}, data), function(response)
	{
//		.log(response);
		if(response.error)
		{
			alert(response.error);
		}
		else
		{
			
			if ( is_reload &&(typeof(url_update)=='undefined' || url_update==''))
				window.location.reload(true);
			else if (is_reload) {
				window.location.href = jsData.baseUrl+url_update;
			}
			
			if (typeof(url_update)=='undefined' || url_update=='') return;
			
			ajax(url_update, data);
		}
	}, 'json');
}

// Загружает и отображает диалог с заданого url
function loadDlg(url, init) {

	$.post(jsData.baseUrl+url, {
		isAjax: true
	}, function(response) {
		//var id = $(response).attr('id');
		
		//console.log(response);
		for(i in response.updatedViews)
			{
				var id = $(response.updatedViews[i]).attr('id');
				$(response.updatedViews[i]).hide().appendTo('body');
				ShowDlg(id, init);
			}
		jsData = $.extend(true, jsData, response.jsData);
		
	}, 'json');
}

// Отображает диалог с идентификатором id по центру страницы
	function ShowDlg(id, init){
		/*if (!init)	init = {'width':'400px', 'height': '400px', 'background-color':'#FFF'};
		if (!init['width']) init['width'] = "400px";
		if (!init['height']) init['height'] = "400px";
		if (!init['background-color']) init['background-color'] = "#FFF";
		
		//Set heigth and width to mask to fill up the whole screen
		mask=$('<div id="mask"></div>').appendTo('body').click(function(){RemoveDlg(id)});
		
		$('#mask').css({'width': $(document).width(), 'height': $(document).height(),'position':'absolute','top':'0px', 'left':'0px','z-index':'9','background-color':'#000000','opacity':0.6});
        
		console.log($(window).height());
		
		//Initialize css of popup
		$("#" + id).css('width', init['width']);
		$("#" + id).css('height', init['height']);
		$("#" + id).css('background-color', init['background-color']);
		
		//Set the popup window to center
		$("#" + id).css('top',  $(window).height() / 2 - $("#" + id ).height()/2);
		$("#" + id).css('position', 'fixed');
		$("#" + id).css('overflow', 'auto');
		$("#" + id).css('z-index', '10');
		$("#" + id).css('margin', '0');
		$("#" + id).css('left', $(window).width() / 2 - $("#" + id ).width()/2);
	
		//transition effect
		$("#" + id).slideDown(400);  */
		$("#" + id).modal('show');
	}
	
	// Удаляет диалог с идентификатором id из кода страницы
	function RemoveDlg(id){
		$("#" + id).remove();  
		$('#mask').remove();
	}
	
	// Загрузка json в форму и отображение модального диалога
	function loadFormJSON(url,modal_id){
	clearForm(modal_id);
	$.getJSON(jsData.baseUrl+url, 	function(json){ 
			$('#'+modal_id).loadJSON(json);
			$('#'+modal_id).modal('show');
		});
	}
	
	function addFormJSON(modal_id){
		clearForm(modal_id);
		json  = jsData.form[modal_id];
		if (json)
			$('#'+modal_id).loadJSON(json);
		$('#'+modal_id).modal('show');
	}
	
	function clearForm(modal_id) {	
		$('#'+modal_id).find('form').trigger("reset");
		$('#'+modal_id).find('textarea').html('');
		$('#'+modal_id).find('.list-group .list-group-item').remove();
		$('#'+modal_id).find('textarea').each(function() {
			if (CKEDITOR.instances[$(this).attr('id')])
				CKEDITOR.instances[$(this).attr('id')].setData('');
		});
		$('#'+modal_id).find('input[type=hidden]').val('');
	}