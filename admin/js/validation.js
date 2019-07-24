window.onload = function() 
{
	document.getElementsByTagName('input')[0].focus();
};
function DetailsData(stext){
	window.open ("../details.php?leaves=" + stext, "window1", "width = 600, height = 300, resizable = 0, status = 0, position=center");
	return false;
}
function deleteFunction(ms=""){
	var msg = "Dou realy want to delete '" + ms + "'?";
	var agree = confirm(msg);
	if(agree){
		return true;
	} else{
		return false;
	}
}
function confirmFunction(msg=""){
	var agree = confirm(msg);
	if(agree){
		return true;
	} else{
		return false;
	}
}
function studentMarksTeacher(deptid=2,classid=4,term=1,acronym=''){
	var xmlhttp;
	xmlhttp = new XMLHttpRequest();
	xmlhttp.onreadystatechange = function () {
		if (xmlhttp.readyState == 4) {
			document.getElementById('studentmarks').innerHTML = xmlhttp.responseText;
		}
	}
	xmlhttp.open ("GET", "studentmarks.php?dept=" + deptid + "&class=" + classid + "&term=" + term + "&acronym=" + acronym, true);
	xmlhttp.send (null);
}
function studentMarksTeacherSitting(deptid=2,classid=4,acronym=''){
	var xmlhttp;
	xmlhttp = new XMLHttpRequest();
	xmlhttp.onreadystatechange = function () {
		if (xmlhttp.readyState == 4) {
			document.getElementById('studentmarks').innerHTML = xmlhttp.responseText;
		}
	}
	xmlhttp.open ("GET", "studentmarkssitting.php?dept=" + deptid + "&class=" + classid + "&acronym=" + acronym, true);
	xmlhttp.send (null);
}
function studentMarks(year,deptid=2,classid=4,start=0,end=16,page='studentmarks.php',term=1){
	var xmlhttp;
	xmlhttp = new XMLHttpRequest();
	xmlhttp.onreadystatechange = function () {
		if (xmlhttp.readyState == 4) {
			document.getElementById('studentlist').innerHTML = xmlhttp.responseText;
		}
	}
	xmlhttp.open ("GET", page + "?dept=" + deptid + "&class=" + classid + "&start=" + start + "&end=" + end + "&term=" + term + "&year=" + year , true);
	xmlhttp.send (null);
}
function studentReport(year,deptid=2,classid=4,start=0,end=16,page='studentreport.php',term=1,student=1,studentno=1){
	var xmlhttp;
	xmlhttp = new XMLHttpRequest();
	xmlhttp.onreadystatechange = function () {
		if (xmlhttp.readyState == 4) {
			document.getElementById('studentlist').innerHTML = xmlhttp.responseText;
		}
	}
	xmlhttp.open ("GET", page + "?dept=" + deptid + "&class=" + classid + "&start=" + start + "&end=" + end + "&term=" + term + "&year=" + year + "&studentid=" + student + "&studentnumber=" + studentno , true);
	xmlhttp.send (null);
}
function reportStudentList(year,deptid=2,classid=4,start=0,end=16,page='reportstudentlist.php'){
	var xmlhttp;
	xmlhttp = new XMLHttpRequest();
	xmlhttp.onreadystatechange = function () {
		if (xmlhttp.readyState == 4) {
			document.getElementById('reportstudentlist').innerHTML = xmlhttp.responseText;
		}
	}
	xmlhttp.open ("GET", page + "?dept=" + deptid + "&class=" + classid + "&start=" + start + "&end=" + end + "&year=" + year , true);
	xmlhttp.send (null);
}
function studentListPromo(year,deptid=2,classid=4,move=0){
	var xmlhttp;
	xmlhttp = new XMLHttpRequest();
	xmlhttp.onreadystatechange = function () {
		if (xmlhttp.readyState == 4) {
			document.getElementById('studentlist').innerHTML = xmlhttp.responseText;
		}
	}
	xmlhttp.open ("GET", "studentlist.php?dept=" + deptid + "&class=" + classid + "&move=" + move + "&year=" + year , true);
	xmlhttp.send (null);
}
function studentList(deptid=2,classid=4,start=0,end=12,page='studentlist.php',move=0,year=0){
	var xmlhttp;
	xmlhttp = new XMLHttpRequest();
	xmlhttp.onreadystatechange = function () {
		if (xmlhttp.readyState == 4) {
			document.getElementById('studentlist').innerHTML = xmlhttp.responseText;
		}
	}
	xmlhttp.open ("GET", page + "?dept=" + deptid + "&class=" + classid + "&start=" + start + "&end=" + end + "&move=" + move + "&year=" + year , true);
	xmlhttp.send (null);
}
function studentImage(){
	document.getElementById('studentcards').innerHTL = "OK";
}
function saveMarks(studentid,field,mark,id='ouput',row=0){
	var xmlhttp;
	xmlhttp = new XMLHttpRequest();
	xmlhttp.onreadystatechange = function () {
		if (xmlhttp.readyState == 4) {
			document.getElementById(id).innerHTML = xmlhttp.responseText;
		}
	}
	xmlhttp.open ("GET", "savemarks.php?student=" + studentid + "&field=" + field + "&mark=" + mark + "&row=" + row , true);
	xmlhttp.send (null);
}
function saveMarksSitting(studentid,field,mark,id='ouput',row=0){
	var xmlhttp;
	xmlhttp = new XMLHttpRequest();
	xmlhttp.onreadystatechange = function () {
		if (xmlhttp.readyState == 4) {
			document.getElementById(id).innerHTML = xmlhttp.responseText;
		}
	}
	xmlhttp.open ("GET", "savemarkssitting.php?student=" + studentid + "&field=" + field + "&mark=" + mark + "&row=" + row , true);
	xmlhttp.send (null);
}
function classCourse(deptid=3,classid=4,year=''){
	var xmlhttp;
	xmlhttp = new XMLHttpRequest();
	xmlhttp.onreadystatechange = function () {
		if (xmlhttp.readyState == 4) {
			document.getElementById('classcourse').innerHTML = xmlhttp.responseText;
		}
	}
	xmlhttp.open ("GET", "classcourse.php?dept=" + deptid + "&class=" + classid + "&year=" + year , true);
	xmlhttp.send (null);
}
function navigation1(deptid=3,classid=4,type=1,id='classform'){
	var xmlhttp;
	xmlhttp = new XMLHttpRequest();
	xmlhttp.onreadystatechange = function () {
		if (xmlhttp.readyState == 4) {
			document.getElementById(id).innerHTML = xmlhttp.responseText;
		}
	}
	xmlhttp.open ("GET", "navigation1.php?dept=" + deptid + "&class=" + classid + "&type=" + type , true);
	xmlhttp.send (null);
}
function navigation(deptid=3,classid=4,type=1,id='classform'){
	var xmlhttp;
	xmlhttp = new XMLHttpRequest();
	xmlhttp.onreadystatechange = function () {
		if (xmlhttp.readyState == 4) {
			document.getElementById(id).innerHTML = xmlhttp.responseText;
		}
	}
	xmlhttp.open ("GET", "navigation.php?dept=" + deptid + "&class=" + classid + "&type=" + type , true);
	xmlhttp.send (null);
}
function SendRequest(page,id,days,userid=0){
	var xmlhttp;
	xmlhttp = new XMLHttpRequest();
	xmlhttp.onreadystatechange = function () {
		if (xmlhttp.readyState == 4) {
			var res = xmlhttp.responseText;
			if(res == 'OK'){
				alert(res);
				return true
			} else{
				alert(res);
				return true;
			}
		}
	}
	if(days == "" || id == "" || page == ""){
		alert("Days ID and Page are quired!!");
		return false;
	}
	xmlhttp.open ("GET", page + "?days=" + days + "&id="+ id + "&uid="+ userid , true);
	xmlhttp.send (null);
}
function ChangeDays(id,userid=0){
	var days = prompt("Enter Number of Requested Days");
	if(days > 0 && days < 31){
		sent = SendRequest("changedays.php",id,days,userid)
		if( sent){
			return true;
		} else{
			alert("Changes Aplyed!");
			return true;
		}
	} else{
		alert("Enter a Number!");
	}
	return false;
}
function isNumberKey(evt){
	var charCode = (evt.which) ? evt.which : event.keyCode
	if (charCode > 31 && (charCode < 48 || charCode > 57)){
		//document.getElementById('styles').innerHTML = charCode;
		if(charCode == 46)
			return true;
		return false;
	}
	//document.getElementById('styles').innerHTML = charCode;
	return true;
}