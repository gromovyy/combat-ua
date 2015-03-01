
jQuery.fn.serializeJSON=function() {
var json = {};
jQuery.map($(this).serializeArray(), function(n, i){
json[n['name']] = n['value'];
});
return json;
};




$(document).ready(function() {
	// Запуск fancybox с параметрами.
 	if ($(".fancybox").fancybox){
		$(".fancybox").fancybox(
		{
			helpers		: {
			             title	: { type : 'inside' },
			}
		});
	} 
	// Подсветка синтаксиса в pre.code
	$.each( $('.code'), function( key, value ) {
		hljs.highlightBlock(value); 
	});
	// next
});



function isset () {
    // +   original by: Kevin van Zonneveld 
    // +   improved by: FremyCompany
    // +   improved by: Onno Marsman
    // *     example 1: isset( undefined, true);
    // *     returns 1: false
    // *     example 2: isset( 'Kevin van Zonneveld' );
    // *     returns 2: true
    
    var a=arguments, l=a.length, i=0;
    
    if (l===0) {
        throw new Error('Empty isset'); 
    }
    
    while (i!==l) {
        if (typeof(a[i])=='undefined' || a[i]===null) { 
            return false; 
        } else { 
            i++; 
        }
    }
    return true;
}
$(document).keydown( function(e){
		// f10 key pressed
		if (e.keyCode == 121) {
			window.location.href = jsData.baseUrl+"User/ChangeEditMode";
		}
	});
	
// ---- file upload
/* 
Для ajax отправки формы
 
onsubmit="return AIM.submit(this, {'onStart' : startCallback, 'onComplete' : completeCallback})"
 */
AIM = {
 
	frame : function(c) {
 
		var n = 'f' + Math.floor(Math.random() * 99999);
		var d = document.createElement('DIV');
		d.innerHTML = '<iframe style="display:none"  id="'+n+'" name="'+n+'" onload="AIM.loaded(\''+n+'\');"></iframe>';
		document.body.appendChild(d);
 
		var i = document.getElementById(n);
		if (c && typeof(c.onComplete) == 'function') {
			i.onComplete = c.onComplete;
		}
 
		return n;
	},
 
	form : function(f, name) {
		f.setAttribute('target', name);
	},
 
	submit : function(f, c) {
		AIM.form(f, AIM.frame(c));
		if (c && typeof(c.onStart) == 'function') {
			return c.onStart();
		} else {
			return true;
		}
	},
 
	loaded : function(id) {
		var i = document.getElementById(id);
		if (i.contentDocument) {
			var d = i.contentDocument;
		} else if (i.contentWindow) {
			var d = i.contentWindow.document;
		} else {
			var d = window.frames[id].document;
		}
		if (d.location.href == "about:blank") {
			return;
		}
 
		if (typeof(i.onComplete) == 'function') {
			i.onComplete(d.body.innerHTML);
		}
	}
 
};

var startDate = new Date();
var intervals;
function timeDiff() {
		//	console.log(interval);	
			currentDate = new Date();
			var datediff = currentDate.getTime() - startDate.getTime();
			var cd = 24 * 60 * 60 * 1000,
				ch = 60 * 60 * 1000,
				cm = 60 * 1000,
				cs = 1 * 1000;
				
			$('[id^=timer]').each( function () {
				interval = 1000*$(this).data('time'); // To miliseconds
				interval += datediff;
			
				d = Math.floor(interval / cd);
				h = Math.floor( (interval - d * cd) / ch);
				m = Math.floor( (interval - d * cd - h * ch) / cm);
				s = Math.round( (interval - d * cd - h * ch - m * cm) / cs);
				h = h  + 24*d;
				hs = (h <= 9)?'0' + h: h;
				ms = (m <= 9)?'0' + m: m;
				ss = (s <= 9)?'0' + s: s;
				//console.log($(this).attr('id'));
				$(this).html(  hs +':' + ms + ':' + ss);
			});
			
		    return;
		};
		
function exit() {
		document.cookie='PHPSESSID=; expires='+(new Date(0)).toGMTString()+'; path=/';
		document.cookie='cmsid=; expires='+(new Date(0)).toGMTString()+'; path=/';
		location.reload();
		return false;
	}
	
// Обновление списка задач при изменении статистики	
function getNewStatistics(){
		$.getJSON(jsData.baseUrl+'Task/getNewStatistics', function(data){
			//alert(data);

			old_all = parseInt($('#tab-all .numberCircle').html());
			old_worker = parseInt($('#tab-worker .numberCircle').html());
			old_controller = parseInt($('#tab-controller .numberCircle').html());
			//$('#tab-all,#tab-worker,#tab-controller').find('div').removeClass('numberCircle').html('');
			if (isNaN(old_all)) old_all = 0;
			if (isNaN(old_worker)) old_worker = 0;
			if (isNaN(old_controller)) old_controller = 0;
			if (!data) {
				new_all = 0;
				new_worker = 0;
				new_controller = 0;
			} else {
				new_all = parseInt(data['all']);
				new_worker = parseInt(data['worker']);
				new_controller = parseInt(data['controller']);
				if (isNaN(new_all)) new_all = 0;
				if (isNaN(new_worker)) new_worker = 0;
				if (isNaN(new_controller)) new_controller = 0;
			}
			id_project = $('#id_project').val();
			if ( old_all != new_all) {
				update('Task/FullList/'+id_project);
				if (new_all > 0)
					$('#tab-all div').replaceWith('<div class="numberCircle">'+new_all+'</div>');
				else 
					$('#tab-all div').removeClass('numberCircle').html('');
			}
			if ( old_worker != new_worker ) {
				update('Task/UserList/'+id_project);
				if (new_worker > 0)
					$('#tab-worker div').replaceWith('<div class="numberCircle">'+new_worker+'</div>');
				else 
					$('#tab-worker div').removeClass('numberCircle').html('');
			}
			if ( old_controller != new_controller ) {
				update('Task/ControlList/'+id_project);
				if (new_controller > 0) 
					$('#tab-controller div').replaceWith('<div class="numberCircle">'+new_controller+'</div>');
				else 
					$('#tab-controller div').removeClass('numberCircle').html('');
			}
		});
}


$(document).ready( function () { 

		// Инициализация диалога даты и времени
		if (typeof $.timepicker != 'undefined') {
		$.timepicker.regional['ru'] = {
			timeOnlyTitle: 'Выберите время',
			timeText: 'Время',
			hourText: 'Часы',
			minuteText: 'Минуты',
			secondText: 'Секунды',
			millisecText: 'Миллисекунды',
			microsecText: 'Микросекунды',
			timezoneText: 'Часовой пояс',
			dayNames: ['воскресенье','понедельник','вторник','среда','четверг','пятница','суббота'],
            dayNamesShort: ['вск','пнд','втр','срд','чтв','птн','сбт'],
			firstDay: 1,
            dayNamesMin: ['Вс','Пн','Вт','Ср','Чт','Пт','Сб'],
			currentText: 'Сейчас',
			closeText: 'Закрыть',
			timeFormat: 'HH:mm',
			amNames: ['AM', 'A'],
			pmNames: ['PM', 'P'],
			isRTL: false
		};
		$.timepicker.setDefaults($.timepicker.regional['ru']);
		}

		// Счетчик для обновления таймера
		setInterval(timeDiff, 1000);
		// Счетчик для обновления статистики новых задач
		setInterval(getNewStatistics, 10000);
		
		// Восстановление состояния таба после перезагрузки
		$('.restore-active > li').click(function(){
			id_ul = $(this).parent('ul').attr('id');
			$.cookie(id_ul, $(this).index(), { expires: 1, path: '/' });
		});
		$('.restore-active').each(function(){
				id_ul = $(this).attr('id');
				index = $.cookie(id_ul);
				if ( id_ul && index ) {
					$(this).find('li').each( function() {
						if ($(this).index()== index)
							$(this).find('a').click();
					});
				}
			});
			
			
			
			// Открываем описанию по клику на задачу
			$( document ).on('click','tr.task',function(e) {
				//alert(e.target);
				id = $(this).data('id');
				if ($(this).hasClass('new')) {
					tr = this;
					$.post(jsData.baseUrl+'Task/NotNewTask/'+id, function(data){
					//	alert(data);
						if (data == '1') {
							$(tr).removeClass('new');
							/*$(tr).addClass('open');*/
						}
					});
					
				}
			    if (e.target.className.indexOf("glyphicon")<0  
				 && e.target.className.indexOf("checkmark")<0 ) {
					// tr = this;
					// update('Task/getWorks/'+id);
					$(this).next().children().children().slideToggle('fast');
				}
			});

			$('input[type=datetime]').datetimepicker({
				showSecond: false,
				showMillisec: false,
				timeFormat: 'HH:mm:ss',
				changeMonth: true,
				changeYear: true, 
				dateFormat: 'yy-mm-dd',
				yearRange: '1970:2020'
			});
			
			// Инициализируем CKEditors
			CKEDITOR.replace( 'work',  {
				resize_enabled: false,
				toolbar: [
				{ name: 'document', items: [ 'Source','-', 'Undo', 'Redo'  ] },	
				{ name: 'basicstyles', items: [ 'Bold', 'Italic', 'RemoveFormat','-','NumberedList','BulletedList','-','Outdent','Indent','-','JustifyLeft','JustifyCenter' ] },
				{ name: 'styles', items : [ 'FontSize', 'TextColor'  ] }
			]} 
			);
				
			CKEDITOR.replace( 'desc',  {
				startupFocus : true,
				removePlugins: 'elementspath',
				resize_enabled: false,
				toolbar: [
				/*{ name: 'document', items: [ 'Source','-', 'Undo', 'Redo'  ] },	*/
				{ name: 'basicstyles', items: [ 'Bold', 'Italic', 'RemoveFormat','-','NumberedList','BulletedList','-','Outdent','Indent','-','JustifyLeft','JustifyCenter' ] },
				{ name: 'styles', items : [ 'FontSize', 'TextColor'  ] }
			]});

			
			// Правка, чтобы модальное окно  не закрывалось при диалоге выбора даты
			$.fn.modal.Constructor.prototype.enforceFocus = function () {};
			//getNewStatistics();
			
			// Подключаем валидацию бутстрап ко всем модальным окнам в момент отображения
			
			$('.modal').on('shown.bs.modal', function() {
				form = $(this).find('form');
				$(form).bootstrapValidator();
				$(form).bootstrapValidator('resetForm', false);
				lastfocus = $(this);
				$(this).find('input:text:visible:first').focus();
				if ($(this).attr('id') == 'modal-add-comment')
					CKEDITOR.instances.work.focus();
			});
			
			// Картинка загрузки
			$('<div id="loading-block" style="display:none"><img src="lib/img/loading.gif" id="loading-indicator"/></div>').appendTo('body');
			$('.modal').on('success.form.bv',function(){$('#loading-block').show()});
			// Устанавливаем фокус в первый элемент при загрузке страницы
			// $('#task-modal-form').on('shown.bs.modal', function() {
				
			// });
		
			//$('#left-menu').collapse();
			/*$('tr.task').click(function(e) {
				alert(e.target);
				var $link = $(this).find("a");

				if (e.target === $link[0]) {
					$(this).next().slideToggle('slow');
					return false;
				}
				$link.trigger('click');
				return false;
			});*/
		});
	

// Конец 
// -----

function addFile(obj){
	list_group = $(obj).parent('.files').find('.list-group');
	$('<span style="display:none" class="list-group-item"><i class="glyphicon glyphicon-file"></i><input type="file" name="attachment-new[]"  onchange="ShowFile(this)" /></span>').appendTo(list_group).find('input').click();
}

function addAttachment(element, id_attachment, file_name){
	list_group = $(element).find('.files .list-group');
	$('<span class="list-group-item"><a href="Attachment/Download/'+id_attachment+'"><i class="glyphicon glyphicon-file"></i>'+file_name+'<input type="hidden" name="attachments['+id_attachment+']" value="'+id_attachment+'"/></a><i class="glyphicon icon-remove" onclick="removeFile(this)"></i></span>').appendTo(list_group);
}

function ShowFile(obj){
	if (!$(obj).val() || $(obj).val().length < 1) 
		return;
	//var file_path = $(obj).val().replace(/\//g,'/');
	var file_name = $(obj).val().replace(/^.*[\\\/]/, '');
	//var file_name = file_path.substring(file_path.lastIndexOf('/'));
	$(obj).parent('span').append(file_name + '<i class="glyphicon icon-remove" onclick="removeFile(this)"></i>').show();
}

function removeFile(obj){
	$(obj).parent('span').remove();
}