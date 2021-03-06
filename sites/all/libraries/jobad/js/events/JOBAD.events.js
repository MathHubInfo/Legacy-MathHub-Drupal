/*
	JOBAD 3 Events
	JOBAD.events.js
		
	Copyright (C) 2013 KWARC Group <kwarc.info>
	
	This file is part of JOBAD.
	
	JOBAD is free software: you can redistribute it and/or modify
	it under the terms of the GNU General Public License as published by
	the Free Software Foundation, either version 3 of the License, or
	(at your option) any later version.
	
	JOBAD is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.
	
	You should have received a copy of the GNU General Public License
	along with JOBAD.  If not, see <http://www.gnu.org/licenses/>.
*/

/* left click */
JOBAD.events.leftClick = 
{
	'default': function(){
		return false;
	},
	'Setup': {
		'enable': function(root){
			var me = this;
			root.delegate("*", 'click.JOBAD.leftClick', function(event){
				var element = JOBAD.refs.$(event.target); //The base element.  
				switch (event.which) {
					case 1:
						/* left mouse button => left click */
						preEvent(me, "leftClick", [element]); 
						me.Event.leftClick.trigger(element);
						postEvent(me, "leftClick", [element]); 
						event.stopPropagation(); //Not for the parent. 
						break;
					default:
						/* nothing */
				}
			});
		},
		'disable': function(root){
			root.undelegate("*", 'click.JOBAD.leftClick');	
		}
	},
	'namespace': 
	{
		
		'getResult': function(target){
			return this.modules.iterateAnd(function(module){
				module.leftClick.call(module, target, module.getJOBAD());
				return true;
			});
		},
		'trigger': function(target){
			if(JOBAD.util.isHidden(target)){
				return true;
			}
			var evt = this.Event.leftClick.getResult(target);
			return evt;
		}
	}
};

/* keypress */
JOBAD.events.keyPress = 
{
	'default': function(key, JOBADInstance){
		return false;
	},
	'Setup': {
		'enable': function(root){
			var me = this;
			me.Event.keyPress.__handlerName = JOBAD.util.onKey(function(k){
				if(me.Instance.isFocused()){
					preEvent(me, "keyPress", [k]); 
					var res = me.Event.keyPress.trigger(k); 
					postEvent(me, "keyPress", [k]); 
					return res; 
				} else {
					return true; 
				}
			}); 
		},
		'disable': function(root){
			JOBAD.refs.$(document).off(this.Event.keyPress.__handlerName); 
		}
	},
	'namespace': 
	{
		
		'getResult': function(key){
			var res = this.modules.iterateAnd(function(module){
				return !module.keyPress.call(module, key, module.getJOBAD());
			});
 
			return res; 
		},
		'trigger': function(key){
			var evt = this.Event.keyPress.getResult(key);
			return evt;
		}
	}
};

/* double Click */
JOBAD.events.dblClick = 
{
	'default': function(){
		return false;
	},
	'Setup': {
		'enable': function(root){
			var me = this;
			root.delegate("*", 'dblclick.JOBAD.dblClick', function(event){
				var element = JOBAD.refs.$(event.target); //The base element.  
				preEvent(me, "dblClick", [element]); 
				var res = me.Event.dblClick.trigger(element);
				postEvent(me, "dblClick", [element]); 
				event.stopPropagation(); //Not for the parent. 
			});
		},
		'disable': function(root){
			root.undelegate("*", 'dblclick.JOBAD.dblClick');	
		}
	},
	'namespace': 
	{
		
		'getResult': function(target){
			return this.modules.iterateAnd(function(module){
				module.dblClick.call(module, target, module.getJOBAD());
				return true;
			});
		},
		'trigger': function(target){
			if(JOBAD.util.isHidden(target)){
				return true;
			}
			var evt = this.Event.dblClick.getResult(target);
			return evt;
		}
	}
};

/* onEvent */
JOBAD.events.onEvent = 
{
	'default': function(){},
	'Setup': {
		'enable': function(root){
			var me = this;

			me.Event.onEvent.id = 
			me.Event.on("event.handlable", function(event, args){
				me.Event.onEvent.trigger(event, args);
			});
		},
		'disable': function(root){
			var me = this;
			me.Event.off(me.Event.onEvent.id);
		}
	},
	'namespace': 
	{
		
		'getResult': function(event, element){
			return this.modules.iterateAnd(function(module){
				module.onEvent.call(module, event, element, module.getJOBAD());
				return true;
			});
		},
		'trigger': function(event, element){
			if(JOBAD.util.isHidden(element)){
				return true;
			}
			return this.Event.onEvent.getResult(event, element);
		}
	}
};

/* context menu entries */
JOBAD.events.contextMenuEntries = 
{
	'default': function(){
		return [];
	},
	'Setup': {
		'enable': function(root){
			var me = this;
			JOBAD.UI.ContextMenu.enable(root, function(target){
				preEvent(me, "contextMenuEntries", [target]);
				var res = me.Event.contextMenuEntries.getResult(target);
				postEvent(me, "contextMenuEntries", [target]);
				return res;
			}, {
				"type": function(target){
					return me.Config.get("cmenu_type");
				}, 
				"show": function(){
					me.Event.trigger("contextmenu.open", []); 
					me.Event.handle("contextMenuOpen");
				},
				"close": function(){
					me.Event.trigger("contextmenu.close", []); 
					me.Event.handle("contextMenuClose");
				},
				"stopPropagnate": true
			});
		},
		'disable': function(root){
			JOBAD.UI.ContextMenu.disable(root);
		}
	},
	'namespace': 
	{
		'getResult': function(target){
			var res = [];
			var mods = this.modules.iterate(function(module){
				var mtarget = target;
				var res = []; 
				while(true){
					if(mtarget.length == 0 
						|| res.length > 0){
						
						return res; 
					}
					res = module.contextMenuEntries.call(module, mtarget, module.getJOBAD());
					res = JOBAD.UI.ContextMenu.generateMenuList(res); 
					mtarget = mtarget.parent(); 
				}
			});
			for(var i=0;i<mods.length;i++){
				var mod = mods[i];
				for(var j=0;j<mod.length;j++){
					res.push(mod[j]);
				}
			}
			if(res.length == 0){
				return false;		
			} else {
				return res;		
			}
		}
	}
}

/* configUpdate */
JOBAD.events.configUpdate = 
{
	'default': function(setting, JOBADInstance){},
	'Setup': {
		'enable': function(root){
			var me = this;
			JOBAD.refs.$("body").on('JOBAD.ConfigUpdateEvent', function(jqe, setting, moduleId){
				preEvent(me, "configUpdate", [setting, moduleId]);
				me.Event.configUpdate.trigger(setting, moduleId);
				postEvent(me, "configUpdate", [setting, moduleId]);
			});
		},
		'disable': function(root){
			JOBAD.refs.$("body").off('JOBAD.ConfigUpdateEvent');
		}
	},
	'namespace': 
	{
		
		'getResult': function(setting, moduleId){
			return this.modules.iterateAnd(function(module){
				if(module.info().identifier == moduleId){ //only call events for own module. 
					module.configUpdate.call(module, setting, module.getJOBAD());
				}
				return true;
			});
		},
		'trigger': function(setting, moduleId){
			return this.Event.configUpdate.getResult(setting, moduleId);
		}
	}
};

/* hover Text */
JOBAD.events.hoverText = 
{
	'default': function(){
		return false;	
	},
	'Setup': {
		'init': function(){
			this.Event.hoverText.activeHoverElement = undefined; //the currently active element. 
		},
		'enable': function(root){
			
			var me = this;
			var trigger = function(event){
				var $element = JOBAD.refs.$(this);
				var res = me.Event.hoverText.trigger($element);
				if(res){//something happened here: dont trigger on parent
					event.stopPropagation();
				} else if(!$element.is(root)){ //I have nothing => trigger the parent
					JOBAD.refs.$(this).parent().trigger('mouseenter.JOBAD.hoverText', event); //Trigger parent if i'm not root. 	
				}
				root.trigger('JOBAD.Event', ['hoverText', $element]);
				return false;
			};


			var untrigger = function(event){
				return me.Event.hoverText.untrigger(JOBAD.refs.$(this));	
			};

			root
			.delegate("*", 'mouseenter.JOBAD.hoverText', trigger)
			.delegate("*", 'mouseleave.JOBAD.hoverText', untrigger);

		},
		'disable': function(root){
			if(typeof this.Event.hoverText.activeHoverElement != 'undefined')
			{
				me.Event.hoverText.untrigger(); //remove active Hover menu
			}
		
			
			root
			.undelegate("*", 'mouseenter.JOBAD.hoverText')
			.undelegate("*", 'mouseleave.JOBAD.hoverText');
		}
	},
	'namespace': {
		'getResult': function(target){
			if(JOBAD.util.isHidden(target)){
				return true;
			}
			var res = false;
			this.modules.iterate(function(module){
				var hoverText = module.hoverText.call(module, target, module.getJOBAD()); //call apply and stuff here
				if(typeof hoverText != 'undefined' && typeof res == "boolean"){//trigger all hover handlers ; display only the first one. 
					if(typeof hoverText == "string"){
						res = JOBAD.refs.$("<p>").text(hoverText)			
					} else if(typeof hoverText != "boolean"){
						try{
							res = JOBAD.refs.$(hoverText);
						} catch(e){
							JOBAD.error("Module '"+module.info().identifier+"' returned invalid HOVER result. ");
						}
					} else if(hoverText === true){
						res = true;
					}
				}
				return true;
			});
			return res;
		},
		'trigger': function(source){
			if(source.data('JOBAD.hover.Active')){
				return false;		
			}

			preEvent(this, "hoverText", [source]);
			var EventResult = this.Event.hoverText.getResult(source); //try to do the event
		
			if(typeof EventResult == 'boolean'){
				postEvent(this, "hoverText", [source]);
				return EventResult;		
			}

			if(this.Event.hoverText.activeHoverElement instanceof JOBAD.refs.$)//something already active
			{
				if(this.Event.hoverText.activeHoverElement.is(source)){
					return true; //done and die			
				}
				this.Event.hoverText.untrigger(this.Event.hoverText.activeHoverElement);	
			}

			this.Event.hoverText.activeHoverElement = source;

			source.data('JOBAD.hover.Active', true);
			var tid = window.setTimeout(function(){
				source.removeData('JOBAD.hover.timerId');
				JOBAD.UI.hover.enable(EventResult.html(), "JOBAD_Hover");
			}, JOBAD.UI.hover.config.hoverDelay);

			source.data('JOBAD.hover.timerId', tid);//save timeout id
			return true;
						
		},
		'untrigger': function(source){
			if(typeof source == 'undefined'){
				if(this.Event.hoverText.activeHoverElement instanceof JOBAD.refs.$){
					source = this.Event.hoverText.activeHoverElement;
				} else {
					return false;			
				}
			}		

			if(!source.data('JOBAD.hover.Active')){
				return false;		
			}

		

			if(typeof source.data('JOBAD.hover.timerId') == 'number'){
				window.clearTimeout(source.data('JOBAD.hover.timerId'));
				source.removeData('JOBAD.hover.timerId');		
			}

			source.removeData('JOBAD.hover.Active');

			this.Event.hoverText.activeHoverElement = undefined;

			JOBAD.UI.hover.disable();

			postEvent(this, "hoverText", [source]);

			if(!source.is(this.element)){
				this.Event.hoverText.trigger(source.parent());//we are in the parent now
				return false;
			}
		}
	}
}