(function($) {
	var CurrentSelection = null;
	var SelTable = function(table, options){
		this.init(table, options);
	}
	SelTable.prototype = {
		table: null,
		options: null,
		areas: null,
		init: function(table, options){
			this.table = $(table).clone();
			this.options = options;
			$('<div class="selectionable"/>').append(this.table).replaceAll(table);
			this.selectionHighlight=$('<div class="selection-highlight"/>');
			this.selectionSelected=$('<div class="selection-selected"/>')
			this.table.after(this.selectionHighlight).after(this.selectionSelected);
			var classvar = this;
			this.table.mousedown(function(e){
				var c = classvar.coords(e.srcElement);
				if(c===false)
				{
					classvar.disselect();
					return;
				}
				CurrentSelection = classvar;
				var area = {
					begin:{
						x: c.x,
						y: c.y
					},
					end:{
						x: c.x,
						y: c.y
					}
				};
				classvar.selectionHighlight.css('display', 'block');
				if(e.shiftKey && classvar.areas !== null && classvar.areas.length != 0)
				{
					classvar.selectionSelected.children().filter(':last').remove();
					area = classvar.areas.pop();
					area.end = {
						x: c.x,
						y: c.y
					};
				}
				classvar.highlight(area);
				if(e.ctrlKey && classvar.options.multiselect)
					classvar.areas.push(area);
				else
				{
					classvar.areas = [area];
					classvar.selectionSelected.empty();
				}
			}).mousemove(function(e){
				if(CurrentSelection==classvar && e.eventPhase===3)
				{
					var area = classvar.areas.pop();
					c = classvar.coords(e.srcElement);
					if(c !== false){
						//						console.log(area.begin.y);
						c.x = classvar.options.horisontal ? c.x : area.begin.x;
						c.y = classvar.options.vertical ? c.y : area.begin.y;
						area.end={
							x: c.x,
							y: c.y
						};
						classvar.highlight(area);
					}
					classvar.areas.push(area);
				}
			});
			$(document).mouseup(function(){
				if(CurrentSelection !== null)
				{
					CurrentSelection.selectionHighlight.css('display','').clone().removeClass('selection-highlight').appendTo(CurrentSelection.selectionSelected);
					CurrentSelection.options.selectionChange.call(CurrentSelection.table, CurrentSelection.areas);
					CurrentSelection = null;
				}
			});
		},
		highlight: function(area){
			var begin = this.table.find('tr:has(td):eq(' + area.begin.y + ') td:eq(' + area.begin.x + ')');
			var end = this.table.find('tr:has(td):eq(' + area.end.y + ') td:eq(' + area.end.x + ')');
			var x, y, w, h;
			x = Math.min(begin.offset().left, end.offset().left);
			if(begin.offset().left == x)
				w = end.offset().left + end.outerWidth() - x;
			else
				w = begin.offset().left + begin.outerWidth() - x;
			y = Math.min(begin.offset().top, end.offset().top);
			if(begin.offset().top == y)
				h = end.offset().top + end.outerHeight() - y;
			else
				h = begin.offset().top + begin.outerHeight() - y;
			this.selectionHighlight.css({
				left: x,
				top: y,
				width: w,
				height: h
			});
			this.options.highlightChange.call(this.table, area);
		},
		coords: function (cell)
		{
			var index = this.table.find('td').index(cell);
			if(index === -1)
				return false;
			var colls = this.table.find('tr:has(td):first td').length;
			return {
				x: index % colls,
				y: Math.floor(index / colls)
			};
		},
		selected: function(){
			var cells=$();
			var classvar = this;
			$.each(this.areas, function(){
				ymin=Math.min(this.begin.y, this.end.y);
				ymax=Math.max(this.begin.y, this.end.y);
				xmin=Math.min(this.begin.x, this.end.x);
				xmax=Math.max(this.begin.x, this.end.x);
				var colls = classvar.table.find('tr:has(td):first td').length;
				cells=cells.add(classvar.table.find('tr:has(td)').filter(function(i){
					return ymin<=i && i<=ymax;
				}).find('td').filter(function(i){
					return xmin<=i%colls && i%colls<=xmax;
				}));
			});
			return cells;
		},
		getAreas: function(){
			return this.areas;
		},
		setAreas: function(areas){
			this.areas=areas;
		}
		,
		disselect: function(){
			this.areas=[];
			this.selectionHighlight.css('display','');
			this.selectionSelected.empty();
		}
	}

	$(function(){
		var myStylesheet = new CSSStyleSheet();
		myStylesheet.addRule(".selectionable .selection-highlight", "opacity: 0.2; background: #888; display: none; position: absolute; pointer-events: none;");
		myStylesheet.addRule(".selectionable .selection-selected div", "opacity: 0.5; background: #888; /*border: solid 2px black; margin: -2px;*/ position: absolute; pointer-events: none;");

	});

	$.fn.selectionable = function(options){
		if(this.filter('table').length==0)
			return this;
		options=$.extend({
			multiselect: true,
			highlightChange: function(area){},
			selectionChange: function(areas){},
			vertical: true,
			horisontal: true
		}, options);
		this.filter('table').each(function(){
			$(this).data('selectionable_class', new SelTable(this, options));
			$('#'+$(this).attr('id')).data($(this).data());
		});
		return this;
	};
	$.fn.selectedCells=function()
	{
		if(this.data('selectionable_class')!==undefined)
			return this.data('selectionable_class').selected();
		else
			return $();
	};
	$.fn.disselect=function()
	{
		if(this.data('selectionable_class')!==undefined)
			this.data('selectionable_class').disselect();
		return this;
	};
	$.fn.setSelection=function(value)
	{
		if(this.data('selectionable_class')!==undefined)
		{
			if(value===undefined)
				return this.data('selectionable_class').getAreas();
			else
			{
				this.data('selectionable_class').setAreas(value);
				return this;
			}
		}
		else
			return this;
	}
})(jQuery);

