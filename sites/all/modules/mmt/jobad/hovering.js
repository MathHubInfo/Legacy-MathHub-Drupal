(function($){

var hovering = {
	/* JOBAD Interface  */ 
	info: {
		'identifier' : 'kwarc.mmt.hovering',
		'title' : 'MMT hovering Service',
		'author': 'MMT developer team',
		'description' : 'The main service handling hovering for MMT documents',
		'version' : '1.0',
		'dependencies' : [],
		'hasCleanNamespace': true
	},


	hoverText: function(target, JOBADInstance) {
		//hover on OMS: show jobad:href and select the smallest proper superexpression
		if (target.hasAttribute(mmtattr.symref)) {			
			var mr = $(target).closest('mrow');
			var select = (mr.length == 0) ? target : mr[0];
			mmt.setSelected(select);
			return target.attr(mmtattr.symref);
		}
		// hover on bracketed expression: select expression
		if (mmt.getTagName(target) == 'mfenced') {
			mmt.setSelected(target);
			return true;
		}
		// hover on variable: select declaration
		if (target.hasAttribute(mmtattr.varref)) {
			var v = $(target).parents('mrow').children().filter(function() {
                return $(this).attr(mmtattr.position) == target.attr(mmtattr.varref);
			})
			mmt.setSelected(v[0]);
			return true;
		}
		return false;
	},

}

JOBAD.modules.register(hovering);
})(jQuery);