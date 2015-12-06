function toggle(elem,s) {
   jQuery(elem).closest('.constant').find('.' + s).toggle()
}
function toggleClick(elem,label){
  var cls = label != null ? label : 'toggleTarget'
  jQuery(elem).parent().closest('div').find('.' + cls).toggle()
}

/*
var JOBAD1;
jQuery(function(){	
  JOBAD1 = new JOBAD(jQuery("math"));
  JOBAD1.modules.load("kwarc.mmt.navigation", []);
  JOBAD1.modules.load("kwarc.mmt.hovering", []);
  JOBAD1.modules.load("kwarc.mmt.intvw", []);
  JOBAD1.Setup();
});		
*/