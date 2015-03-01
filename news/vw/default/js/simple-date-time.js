var count = 0;
var isTime = 0;
var strdate = "";
var strtime = "";
var today = new Date();
var day = today.getDate();
var year = today.getFullYear();
var month = today.getMonth();
var hours = today.getHours();
var minutes = today.getMinutes();
var object;
	
function Left(obj){
	var curleft = 0;
	if (obj.offsetParent)
	{
		while (obj.offsetParent)
		{
			curleft += obj.offsetLeft
			obj = obj.offsetParent;
		}
	}
	else if (obj.x)
		curleft += obj.x;
	return curleft;
}

function Top(obj){
	var curtop = 0;
	var objHeight = obj.style.height;
	//document.write(objHeight);
	if (obj.offsetParent){
		while (obj.offsetParent){
			curtop += obj.offsetTop;
			obj = obj.offsetParent;
		}
	}
	else if (obj.y)
		curtop += obj.y;
	curtop += objHeight;
	return curtop;
}

/*Рассчитывает положение календаря на странице*/
function Position(obj, isT) {
	isTime = isT;
	var ob = document.getElementById(obj);
	var left = Left(ob);
	var top = Top(ob);
	var fullScreen = document.createElement('div');
	fullScreen.id = "full_screen";

	var newDiv = document.createElement('div');
	newDiv.id = "calendar";
	newDiv.style.left = left + 'px';
	newDiv.style.top = top + 'px';
	//newDiv.style.zindex = "2000";
	/*newDiv.onkeydown = function() {
		var elem = document.getElementById("calendar");
		elem.parentNode.removeChild(elem);
		count--;
	}*/
		fullScreen.onclick = function() {
		var elem = document.getElementById("full_screen");
		elem.parentNode.removeChild(elem);
		var elem = document.getElementById("calendar");
		elem.parentNode.removeChild(elem);
		count--;
	}
	if (count > 0)
		return;
	else {
		count++;
		document.body.appendChild(fullScreen);
		document.body.appendChild(newDiv);
	}
	object = ob;
	setCal(year, month, day, hours, minutes, isTime);
}

/*Функция проверки на високосный год*/
function leapYear(year) {
	if (year % 4 == 0)
		return true;
	return false;
}

/*Функция, которая получает количество дней в конкретном месяце*/
function getDays(month, year) {
	var ar = new Array(12)
	ar[0] = 31
	ar[1] = (leapYear(year)) ? 29 : 28
	ar[2] = 31
	ar[3] = 30
	ar[4] = 31
	ar[5] = 30
	ar[6] = 31
	ar[7] = 31
	ar[8] = 30
	ar[9] = 31
	ar[10] = 30
	ar[11] = 31
	return ar[month]
}

/*Функция, которая получает название месяца*/
function getMonthName(month) {
	var ar = new Array(12)
	ar[0] = "Январь"
	ar[1] = "Февраль"
	ar[2] = "Март"
	ar[3] = "Апрель"
	ar[4] = "Май"
	ar[5] = "Июнь"
	ar[6] = "Июль"
	ar[7] = "Август"
	ar[8] = "Сентябрь"
	ar[9] = "Октябрь"
	ar[10] = "Ноябрь"
	ar[11] = "Декабрь"
	return ar[month]
}

/*Функция, вызывающая построение календаря*/
function setCal(year, month, day, hours, minutes, isTime) {
	var now = new Date();
	var date = now.getDate();
	var firstDayInstance = new Date(year, month, 1);
	var firstDay = firstDayInstance.getDay();
	firstDayInstance = null;
	var days = getDays(month, year);
	document.getElementById("calendar").innerHTML = drawCal(firstDay + 1, days, date, month, year, day, hours, minutes, isTime);
}

/*Присваевает значения дате и времени*/
function setDay(val) {
	day = val;
}

function setMonth(val) {
	month = val;
}

function setYear(val) {
	year = val;
}

function setHours(val) {
	hours = val;
}

function setMinutes(val) {
	minutes = val;
}
	
/*Отрисовка календаря*/
function drawCal(firstDay, lastDate, date, month, year, day, hours, minutes, isTime) {
	var monthName = getMonthName(month);
	var text = "";
	text += '<table id = "date_table">';
	text += '<tr class = "day_row"><td colspan = 7>';
	text += '<select id = "monthSelect" onchange = "setMonth(value); setCal(year, month, day, hours, minutes, isTime)">';
	for (var m = 0; m <= 11; m++) {
		if (m == month)
			text += '<option selected value = '+ m +'>' + getMonthName(m);
		else
			text += '<option value = '+ m +'>' + getMonthName(m);
		text += '</option>';
	}
	text += '</select>';
	text += '<select id = "yearSelect" onchange = "setYear(value); setCal(year, month, day, hours, minutes, isTime)">';
	for (var y = 2000; y <= 2100; y++) {
		if (y == year)
			text += '<option selected value = '+ y +'>' + y;
		else
			text += '<option value = '+ y +'>' + y;
		text += '</option>';
	}
	text += '</select>';
	text += '</font>';
	text += '</td></tr>';
	
	var weekDay = new Array(7);
	weekDay[0] = "ВС";
	weekDay[1] = "ПН";
	weekDay[2] = "ВТ";
	weekDay[3] = "СР";
	weekDay[4] = "ЧТ";
	weekDay[5] = "ПТ";
	weekDay[6] = "СБ";

	text += '<tr class = "day_row">';
    for (var dayNum = 0; dayNum < 7; ++dayNum) {
		text += '<td class = "week_days">' + weekDay[dayNum] + '</td>';
	}
	text += '</tr>';
	var digit = 1;
	var curCell = 1;
	for (var row = 1; row <= Math.ceil((lastDate + firstDay - 1) / 7); ++row) {
		text += '<tr>';
		for (var col = 1; col <= 7; ++col) {
			if (digit > lastDate)
				break;
			if (curCell < firstDay) {
				text += '<td></td>';
				curCell++;
			}
			else if (digit == day) {
				text += '<td class = "day_cell"><button class = "day_button" id = "chosen_day" value = '+digit+'">' + digit + '</button></td>';
				digit++;
			}
			else {
				text += '<td class = "day_cell"><button class = "day_button" value = '+digit+' onclick = "setDay(value);setCal(year, month, day, hours, minutes, isTime);">' + digit + '</button></td>';
				digit++;
			}
		}
		text += '</tr>';
	}
	month ++;
	if (day < 10) 
		day = '0' + day;
	if (month < 10) 
		month = '0' + month;
	
	strdate = year + '-' + month + '-' + day;
	if (isTime == 1) {
		text += '<tr class = "day_row"><td colspan = "7"><select onChange = "setHours(value); setCal(year, month, day, hours, minutes, isTime)">';
		for (var hr = 0; hr <= 23; hr++) {
		if (hr == hours)
			text += '<option selected value = '+hr+'>';
		else
			text += '<option value = '+hr+'>';
			if (hr < 10)
				text += '0'+hr;
			else
				text += hr;
			text += '</option>';
		}
		text += '</select><select onChange = "setMinutes(value); setCal(year, month, day, hours, minutes, isTime)">';
		for (var min = 0; min <= 59; min++) {
		if (min == minutes)
			text += '<option selected value = '+min+'>';
		else
			text += '<option value = '+min+'>';
		if (min < 10)
			text += '0'+min;
		else
			text += min;
			text += '</option>';
		}
		text += '</select></td></tr>';
		if (hours < 10)
			hours = '0' + hours;
		if (minutes < 10)
			minutes = '0' + minutes;
		strtime = hours + ':' + minutes;
		strdate += ' ' + strtime;
	}
	text += '<tr class = "day_row"><td colspan = 7>'+strdate+'</td></tr>';
	text += '<tr class = "day_row"><td colspan = 7><button class = "OK_button" id = "changeTime" onclick = "changeInput()" value = '+strdate+'>OK</button></td></tr>';
	text += '</table>';
	return text;
}

/*Функция, меняющая значение Input'а*/
function changeInput() {
	ajax('Article/editCell/'+object.id, {
			value: strdate})
	var elem = document.getElementById("calendar");
		elem.parentNode.removeChild(elem);
	var elem = document.getElementById("full_screen");
		elem.parentNode.removeChild(elem);
		count--;
}