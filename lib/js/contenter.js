// Функция для редактирования текстового поля "на лету"
function text(elem, data) {
	id_cell = $(elem).attr('id');
	data = data || false;
	// Добавляем поле для ввода, прикрепляем его к телу странички,
	input_overlay = $('<input type="text" class="input-overlay text" id="T_' + id_cell + '"/>').appendTo('body');
	// устанавливаем события на потерю фокуса - сохраняем данные на сервер
	$(input_overlay).blur( function(e) { 
			ajax('Article/editCell/' + id_cell , {
				value: $(this).val().replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;')
			});
			$(this).remove();
			$('.twitter-typeahead').remove();
	});
	
	// устанавливаем событие на нажатие клавиши esc - отмена действия
	$(input_overlay).keydown( function(e){
		// ESCAPE key pressed
		if (e.keyCode == 27) {
			$(this).remove();
			$('.twitter-typeahead').remove();
		}
		if (e.keyCode == 13) {
			$(input_overlay).blur();
		}
	});

if(!data){
		// Отображаем элемент, устанавливая ему высоту, ширину, начальную координату, формат шрифта, значение поля,  даем свойство авторесайза,
	$(input_overlay).show()
	                .height($(elem).height()).width($(elem).width())
	                .offset({
	                	top: $(elem).offset().top-1,
	                	left: $(elem).offset().left-1
	                })
	                .css("font-size", $(elem).css('font-size'))
	                .css("z-index", 1001)
	                .val($(elem).html() == '&nbsp;' ? '' : $(elem).text())
	                .focus()
	                .trigger('keyup')
	                .autoResize({
	                	maxWidth: 1400
	                });	
	
		
	} else{
		// Используем typehead
		// В отличии от предыдущего варианта базовый инпут разбивается typehead и работаем уже с ним.
	$(input_overlay)
	.typeahead({name: id_cell,local: data})
	.parent()
	.show()
	.height($(elem).height())
	.width($(elem).width())
	.offset({
		top: $(elem).offset().top-1,
		left: $(elem).offset().left-1
	})
	.css("font-size", $(elem).css('font-size'))
	.css("z-index", 1001).
	find('.tt-query')
	.val($(elem).html() == '&nbsp;' ? '' : $(elem).text())
	.focus()
	.trigger('keyup')
	.autoResize({
		maxWidth: 1400
	});
	
	}
	return false;
}

// Функция для редактирования текстового поля "на лету"
function password(elem) {
	id_cell = $(elem).attr('id');
	// Добавляем поле для ввода, прикрепляем его к телу странички,
	input_overlay = $('<input type="password" class="input-overlay text" placeholder="*****" id="T_' + id_cell + '"/>').appendTo('body');
	// устанавливаем события на потерю фокуса - сохраняем данные на сервер
	$(input_overlay).blur( function(e) { 
			if ($(this).val().length > 0) {
				ajax('Article/editCell/' + id_cell , {
					value: $(this).val().replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;')
				});
			}
			$(this).remove();
	});
	
	// устанавливаем событие на нажатие клавиши esc - отмена действия
	$(input_overlay).keydown( function(e){
		// ESCAPE key pressed
		if (e.keyCode == 27) {
			$(this).remove();
		}
		
		if (e.keyCode == 13) {
			$(input_overlay).blur();
		}
	});

	// Отображаем элемент, устанавливая ему высоту, ширину, начальную координату, формат шрифта, значение поля,  даем свойство авторесайза,
	$(input_overlay).show()
	                .height($(elem).height()).width($(elem).width())
	                .offset({
	                	top: $(elem).offset().top-1,
	                	left: $(elem).offset().left-1
	                })
	                .css("font-size", $(elem).css('font-size'))
	                .css("z-index", 1001)
	                .val('')
	                .focus()
	                .trigger('keyup')
	                .autoResize({
	                	maxWidth: 1400
	                });	
	return false;
}


// Функция для редактирования списка "на лету"
function combobox(elem){	
	id_cell = $(elem).attr('id');
	comboboxInput = $('<select class="input-overlay combobox" id="C_' + id_cell + '"/>');
	// Устанавливаем события на изменение значения - отправка на сервер
	$(comboboxInput).change(function(){
		ajax('Article/editCell/' + id_cell, {
			value: $(this).val()
		});
		$(comboboxInput).remove();
	});
	
	// устанавливаем события на потерю фокуса - отмена действия
	$(comboboxInput).blur( function(e) { 
			$(this).remove();
	});
	
	// устанавливаем событие на нажатие клавиши esc - отмена действия
	$(comboboxInput).keydown( function(e){
		// ESCAPE key pressed
		if (e.keyCode == 27) {
			$(this).remove();
		}
	});
	
	//alert(id_cell);
	// устанавливаем событие на клик мышкой
	comboboxInput.empty();
	comboboxInput.append('<option value=""></option>');
	$.each(jsData.input[id_cell].valueList, function(){
			comboboxInput.append('<option value="'+this.id+'">'+this.v+'</option>');
	});
	comboboxInput.appendTo('body');
			
	// Отображаем элемент, устанавливая ему высоту, ширину, начальную координату, формат шрифта, значение поля	
	comboboxInput
		.show()
		.height($(elem).height()+4)
		.width($(elem).width()+4)
		.offset({
			top: $(elem).offset().top-1,
			left: $(elem).offset().left-1
		})
		.css({
			font: $(elem).css('font')
		})
		.css("z-index", 1001)
		.val(jsData.input[id_cell].selected)
		.data({
			id: id_cell
		})
		.focus();
}

//Функция для обработки WYSYWIG редактора CKEditor
var editor, html = '';
function textarea(id_cell){	
	if ( editor )		return;
	// Добавляем возможность редактирования
	element = document.getElementById(id_cell).contentEditable = "true";;

	var config = {startupFocus : true // Установка фокуса при создании обьекста.
	              ,//extraPlugins : 'autogrow,insertpre'
	             }; 
	editor = CKEDITOR.inline( id_cell, config);
	editor.on( 'blur', function() {
		// Сохраняем данные через ajax
		ajax('Article/editCell/'+ id_cell, {
							value: editor.getData()
							});
		removeEditor();
	} );
}

function removeEditor()
{
	if ( !editor )
		return;
	// Destroy the editor.
	editor.destroy();
	editor = null;
}

//Функция для редактирования поля выбора даты
function date(elem){
	id_cell = $(elem).attr('id');
	//Создаем объект для выбора даты
	dateInput=$('<input type="text" class="input-overlay date" id="D_' + id_cell + '"/>').datepicker({
		changeMonth: true,
		changeYear: true,
		dateFormat: 'yy-mm-dd',
		yearRange: '1970:2020'
	})
	.appendTo('body')
	.change(function(){
		ajax('Article/editCell/'+id_cell, {
			value: $(this).val()
		});
		$(this).remove();
	});
	

	// устанавливаем событие на нажатие клавиши esc - отмена действия
	$(dateInput).keydown( function(e){
		// ESCAPE key pressed
		if (e.keyCode == 27) {
			$(this).remove();
		}
	});
	
	// Отображаем элемент, устанавливая ему высоту, ширину, начальную координату, формат шрифта, значение поля
	$(dateInput)
		.show()
		.height($(elem).height())
		.width($(elem).width())
		.offset({
			top: $(elem).offset().top-1,
			left: $(elem).offset().left-1
		})
		.css("font-size", $(elem).css('font-size'))
		.css("z-index", 1001)
		.val($(elem).html() == '&nbsp;' ? '' : $(elem).text())
		.data({
			id: id_cell
		})
		.focus()
		.trigger('keyup');
}

//Функция для редактирования поля выбора даты и времени
function datetime(elem){
	id_cell = $(elem).attr('id');
	datetimeInput=$('<input type="text" class="input-overlay datetime" data-id="' + id_cell + '" id="DT_' + id_cell + '"/>').datetimepicker({
		showSecond: false,
		showMillisec: false,
		timeFormat: 'HH:mm:ss',
		changeMonth: true,
		changeYear: true,
		dateFormat: 'yy-mm-dd',
		yearRange: '1970:2020',
		// onSelect: function() { 
			// $(".ui-datepicker-close").click(function(){
			// alert ('select');
			// });
		 // },
		// onChange: function(evt,ui){
			// alert('change');
		// }	
		onClose: function(evt, ui){
		  ajax('Article/editCell/'+id_cell, {
			value: $(datetimeInput).val()
		  });
          $(this).remove();
     }
	}).appendTo('body');

	// устанавливаем событие на нажатие клавиши esc - отмена действия
	$(datetimeInput).keydown( function(e){
		// ESCAPE key pressed
		if (e.keyCode == 27) {
			$(this).remove();
		}
	});
	
	// Отображаем элемент, устанавливая ему высоту, ширину, начальную координату, формат шрифта, значение поля
	$(datetimeInput)
		.show()
		.height($(elem).height())
		.width($(elem).width())
		.offset({
			top: $(elem).offset().top-1,
			left: $(elem).offset().left-1
		})
		.css("font-size", $(elem).css('font-size'))
		.css("z-index", 1001)
		.val($(elem).html() == '&nbsp;' ? '' : $(elem).data('value'))
		.data({
			id: id_cell
		})
		.focus()
		.trigger('keyup');
	//$(".ui-datepicker-close").replaceWith('<div class="btn btn-default" id="submit-date">Подтвердить</div>');
	
	// На кнопку диалога Done добавляем функцию onclick, которая сохраняет выбранное время в БД и уничтожает элемент.
	// $(".ui-datepicker-close").bind('click',function(){
		// ajax('Article/editCell/'+id_cell, {
			// value: $(datetimeInput).val()
		// });
		// $(".ui-datepicker-close").unbind('click');
		// $(datetimeInput).datetimepicker('hide');
		// $(datetimeInput).remove();
	// });
	//$(datetimeInput).change(function(){
		
	//});
	
	
	// $(datetimeInput).blur( function(e) { 
			// ajax('Article/editCell/' + id_cell , {
				// value: $(datetimeInput).val()
			// });
			// $(this).remove();
			// $('.twitter-typeahead').remove();
	// });
	/*$(".ui-datepicker-close").on('click',function(){
			 // ajax('Article/editCell/'+id_cell, {
				 // value: $(datetimeInput).val()
			 // });
			 // $(datetimeInput).datetimepicker('hide');
			 // $(datetimeInput).remove();
			alert('hello');
		});*/
}

//$("input.datetime")

//Функция для редактирования поля выбора даты и времени
function time(elem){
	id_cell = $(elem).attr('id');
	timeInput=$('<input type="text" class="input-overlay time" id="TM_' + id_cell + '"/>').timepicker().appendTo('body');

	// устанавливаем событие на нажатие клавиши esc - отмена действия
	$(timeInput).keydown( function(e){
		// ESCAPE key pressed
		if (e.keyCode == 27) {
			$(this).remove();
		}
	});
	
	// Отображаем элемент, устанавливая ему высоту, ширину, начальную координату, формат шрифта, значение поля
	$(timeInput).show().height($(elem).height()).width($(elem).width()).offset({
		top: $(elem).offset().top-1,
		left: $(elem).offset().left-1
	})
	.css("font-size", $(elem).css('font-size')).css("z-index", 1001).val($(elem).html() == '&nbsp;' ? '' : $(elem).text()).data({
		id: id_cell
	}).focus().trigger('keyup');
	
	// На кнопку диалога Done добавляем функцию onclick, которая сохраняет выбранное время в БД и уничтожает элемент.
	$(".ui-datepicker-close").die("click").live("click",function(){
		ajax('Article/editCell/'+id_cell, {
			value: $(timeInput).val()
		});
		$(timeInput).remove();
	});
}

function datetimesimple(){
	id_cell = $(elem).attr('id');
	Position (id_cell, 1);
}

function datesimple(elem){
	id_cell = $(elem).attr('id');
	Position (id_cell, 0);
}


//Функция для редактирования поля выбора цвета
function color(elem){
	id_cell = $(elem).attr('id');
}
