// Set the initial date.

// Get Element By Id
function ds_getel(id) {
	return document.getElementById(id);
}

// Get the left and the top of the element.
function ds_getleft(el) {
	var tmp = el.offsetLeft;
	el = el.offsetParent
	while (el) {
		tmp += el.offsetLeft;
		el = el.offsetParent;
	}
	return tmp;
}
function ds_gettop(el) {
	var tmp = el.offsetTop;
	el = el.offsetParent
	while (el) {
		tmp += el.offsetTop;
		el = el.offsetParent;
	}
	return tmp;
}
// Output Element
var ds_oe = ds_getel('ds_calclass');
// Container
var ds_ce = ds_getel('ds_conclass');

// Output Buffering
var ds_ob = '';
function ds_ob_clean() {
	ds_ob = '';
}
function ds_ob_flush() {
	ds_oe.innerHTML = ds_ob;
	ds_ob_clean();
}
function ds_echo(t) {
	ds_ob += t;
}

var ds_element; // Text Element...
// Calendar template
function ds_template_main_above(t) {
	return '<table cellpadding="3" cellspacing="1" class="ds_tbl">'
		+ '<tr>'
		+ '</tr>' + '<tr>' + '<td colspan="7" class="ds_headd" align="center"><img src="../1/ajaximage/uploads/' + t + '" alt="no found" />'
		+ '</td>' + '</tr><tr>';
}
function ds_template_main_below() {
	return '</tr>' + '</table>';
}
function ds_draw_photo(p){
	// First clean the output buffer.
	ds_ob_clean();
	// Here we go, do the header
	ds_echo(ds_template_main_above(p));
	
	// Do the footer
	ds_echo(ds_template_main_below());
	// And let's display..
	ds_ob_flush();
	// Scroll it into view.
	//ds_ce.scrollIntoView();
}
function DisplayPhoto(t,photo){
	// Set the element to set...
	ds_element = t;
	
	ds_draw_photo(photo);
	
	// To change the position properly, we must show it first.
	ds_ce.style.display = '';
	// Move the calendar container!
	the_left = ds_getleft(t);
	the_top = ds_gettop(t) + t.offsetHeight;
	ds_ce.style.left = the_left + 'px';
	ds_ce.style.top = the_top + 'px';
}