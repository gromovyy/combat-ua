console.log('все системы запущены успешно');
function loadmore () {
	ajax($('[data-view]').data('view'),{ajaxOffset:$('[data-offset]').data('offset')})
	
	//busy = false;
}

$(document).ready(function(){
console.log('все системы запущены успешно load');
});

window.onscroll = function(){
	var	busy=false;
	// window.innerHeight - не работает в ие 8 и раньше.
	if (window.innerHeight*1.5+window.pageYOffset > document.height && !busy) {
		busy=true;
		loadmore();
	};
};