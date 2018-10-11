// trimmed down from http://www.silverscripting.com/moduletabs/
var moduletabs = new Class({
	
	initialize: function(element, options) {
		this.options = Object.extend({
			mouseOverClass:	'active',
			activateOnLoad:		'tab0', 
			url : '',
			tabstyle:0
		}, options || {});
		this.options.url = options.url;
		this.options.tabstyle = options.tabstyle;
		
		this.el = $(element);
		this.elid = element;
		
		if (tabsOrselect == 1) {
			this.titles = $$('#' + this.elid + ' select.tabbed_events option');
		} else {
			this.titles = $$('#' + this.elid + ' ul.moduletabs_title li');
		}
		this.panelHeight = this.el.getSize().y - (this.titles[0].getSize().y + 4);
		if (this.panelHeight<0) {
		// This causes problems in MSIE7
		//this.panelHeight = "50";
		}

		this.titles.each(function(item) {
			item.addEvent('click', function(){
				item.removeClass(this.options.mouseOverClass);
				this.activate(item);
			}.bind(this));
			
			item.addEvent('mouseover', function() {
				if(item != this.activeTitle)
				{
					item.addClass(this.options.mouseOverClass);
				}
			}.bind(this));
			
			item.addEvent('mouseout', function() {
				if(item != this.activeTitle)
				{
					item.removeClass(this.options.mouseOverClass);
				}
			}.bind(this));
		}.bind(this));		

		if(this.options.activateOnLoad != 'none')
		{
			if(this.options.activateOnLoad == 'tab0')
			{
				this.activate(this.titles[0], 0);
			}
			else
			{
				this.activate(this.options.activateOnLoad, 0);
			}
		}
	},
	
	activate: function(tab){
		if(this.type(tab) == 'string') 
		{
			if (tabsOrselect == 1) {
				myTab = $$('#' + this.elid + ' select.tabbed_events option').filterByAttribute('title', '=', tab)[0];
			} else {
				myTab = $$('#' + this.elid + ' ul li').filterByAttribute('title', '=', tab)[0];
			}
			tab = myTab;
		}
		
		if(this.type(tab) == 'element')
		{
			var activetab = 0;
			var t=0;
			this.titles.each(function(item) {
				if (item.title == tab.title){
					activetab=t;
				}
				t++;
			});
			this.titles.removeClass('active');
			if (this.options.tabstyle==0){
				tab.addClass('active');
			}
			else {
				tab.addClass('onscreen');
			}
			this.activeTitle = tab;
			this.activeTab = activetab ;

			var pane = $(this.elid);
			var tabs = pane.getElements("div.moduletabs_panel");
			if (activetab<tabs.length){
				if (this.options.tabstyle==0){
					tabs.removeClass('active');
					tabs.addClass('inactive');
					tabs[activetab].addClass('active');
					tabs[activetab].removeClass('inactive');
				}
				else {
					tabs.removeClass('onscreen');
					tabs.addClass('offscreen');
					tabs[activetab].addClass('onscreen');
					tabs[activetab].removeClass('offscreen');
				}
				var tabcontent = $(tab.id+"_content");
				if (tabcontent && tabcontent.innerHTML.trim()==""){
					var requestObject = new Object();
					requestObject.error = false;
					var tabid = tab.id.split("_");
					if(tabid.length==2){
						requestObject.modid = tabid[1]
					}
					else {
						alert('Invalid modid');
						return;
					}

					var jSonRequest = new Request.JSON({
						'url':this.options.url,
						onSuccess: function(json, responsetext){
							if (!json){
								alert('Update Failed');
							}
							if (json.error){
								try {
									eval(json.error);
								}
								catch (e){
									alert('could not process error handler');
								}
							}
							else {
								if ( json.modvalue){
									tabcontent.innerHTML = json.modvalue;
								}
								else {
									alert('Module update failed');
								}
							}
						},
						onFailure: function(x){
							alert('Something went wrong... '+x )
						}
						// use post not get because otherwise browser cache the result!
					}).post({
						'json':JSON.encode(requestObject)
					});
					
				}
			}

		}
	},
	
	setTitle: function (title, label) {
		if (tabsOrselect == 1) {
			$$('#' + this.elid + ' select.tabbed_events option').filterByAttribute('title', '=', title)[0].innerHTML = label;
		} else {
			$$('#' + this.elid + ' ul li').filterByAttribute('title', '=', title)[0].innerHTML = label;
		}
	}, 
	previous: function(){
		var previousTab = this.activeTitle.getPrevious();
		if(!previousTab) {
			previousTab = this.titles[this.titles.length - 1];
		}
		this.activate(previousTab);
	},
	type: function(object){
		var type = typeOf(object);
		if (type == 'elements') return 'array';
		return (type == 'null') ? false : type;
	}
});
