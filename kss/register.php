<?php
session_start(); 
include("config.php")?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<script type="text/javascript" src="jquery-1.4.2.js"></script>
	<script type="text/javascript" src="ui/jquery.ui.core.js"></script>
	<script type="text/javascript" src="ui/jquery.ui.widget.js"></script>
	<script type="text/javascript" src="ui/jquery.ui.datepicker.js"></script>
	  <script type="text/javascript" src="files/mootools.js"></script>
  <script type="text/javascript" src="files/caption.js"></script>
  
  <!--StyleSheet included-->
<link rel="stylesheet" type="text/css" href="css/message.css" media="all">
<!--Stylesheet ends here-->

<!--Javascript included-->
<script type="text/javascript" src="javascript/validation.js"></script>
<style type="text/css">
a:hover {
	background-color:orange;
	font-size: 24px;
	}
a,a:link ,a:visited {
	text-decoration:none;
	color:#000000;
	}

</style>
<!--Javascript included-->
<script type="text/javascript" src="javascript/validation.js"></script>
<link rel="stylesheet" href="files/template.css" type="text/css">
<link rel="stylesheet" href="files/constant.css" type="text/css">

<script src="javascript/jquery-latest.pack.js" type="text/javascript"></script>
<script src="javascript/jcarousellite_1.0.1c4.js" type="text/javascript"></script>
<script type="text/javascript">
$(function() {
	$(".newsticker-jcarousellite").jCarouselLite({
		vertical: true,
		hoverPause:true,
		visible: 3,
		auto:500,
		speed:1000
	});
});
</script>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>WDA</title>
<link rel="WDA icon" href="wda.png" type="image/x-icon"/>
</head>

<body>
<p align="center"><img src="banner.jpg" alt="img1" width="923" height="162" /></p>
<table width="55%" height="41" border="0" align="center" bordercolor="#000000" bgcolor="#2F8E16">
  <tr>
    <td width="18%"><div align="center"><strong><a href="index.php">HOME</a></strong></div></td>
    <td width="32%"><div align="center"><strong><a href="register.php">REGISTRATION</a></strong></div></td>
    <td width="22%"><div align="center"><strong><a href="login.php">LOGIN</a></strong></div></td>
	 <td width="28%"><div align="center"><strong><a href="contactus.php">CONTACT US </a></strong></div></td>
  </tr>
</table>

<p align="center"><img src="images/stu.png" width="125" height="125" /></p>
<p align="center"><strong>PARENT INFORMATION REGISTRATION</strong></p>
<form action="chkre.php" method="post"  enctype="multipart/form-data" name="form1">
<table align="center" bgcolor="#FFFFFF">
<tr><td height="31" bgcolor="#2F8E16"><strong>Familly Name</strong></td>
<td bgcolor="#E1E1E1">  <strong>
  <input id="parentid"  name="parentid" type="text" size="50" maxlength="500" /> 
  <script type="text/javascript">
				    var f1 = new LiveValidation('parentid');
				    f1.add(Validate.Presence,{failureMessage: " Please enter familly name"});
				   f1.add(Validate.Format,{pattern: /^[a-zA-Z\s]+$/i ,failureMessage:
				   " It allows only characters"});
				    f1.add(Validate.Format,{pattern: /^[a-zA-Z][a-zA-Z\s]{0,}$/,failureMessage: 
					       " Invalid Name"});
				 </script>
</strong></td>
</tr>

 <tr>
   <td height="32" bgcolor="#2F8E16"><strong>Father's Name</strong></td>
   <td bgcolor="#E1E1E1">  <strong>
     <input id="fathername" name="fathername" type="text" size="50" maxlength="500" />
     <script type="text/javascript">
				    var f1 = new LiveValidation('fathername');
				    f1.add(Validate.Presence,{failureMessage: " Please enter father's name"});
				   f1.add(Validate.Format,{pattern: /^[a-zA-Z\s]+$/i ,failureMessage:
				   " It allows only characters"});
				    f1.add(Validate.Format,{pattern: /^[a-zA-Z][a-zA-Z\s]{0,}$/,failureMessage: 
					       " Invalid Name"});
				 </script>
      </strong></td>
 </tr>

 <tr>
   <td height="28" bgcolor="#2F8E16"><strong>Mother's Name </strong></td>
   <td bgcolor="#E1E1E1">  <strong>
     <input id="mothername" name="mothername" type="text" size="50" maxlength="500" />
     <script type="text/javascript">
				    var f1 = new LiveValidation('mothername');
				    f1.add(Validate.Presence,{failureMessage: " Please enter Mother's name"});
				   f1.add(Validate.Format,{pattern: /^[a-zA-Z\s]+$/i ,failureMessage:
				   " It allows only characters"});
				    f1.add(Validate.Format,{pattern: /^[a-zA-Z][a-zA-Z\s]{0,}$/,failureMessage: 
					       " Invalid Name"});
				 </script>
   </strong></td>
 </tr>
    <tr>
        <td height="29" bgcolor="#2F8E16"><strong>Guardian Name </strong></td>
      <td bgcolor="#E1E1E1">   <strong>
        <input id="guardianname" name="guardianname" type="text" size="50" maxlength="500" />
        <script type="text/javascript">
				    var f1 = new LiveValidation('guardianname');
				    f1.add(Validate.Presence,{failureMessage: " Please enter Guardian name"});
				   f1.add(Validate.Format,{pattern: /^[a-zA-Z\s]+$/i ,failureMessage:
				   " It allows only characters"});
				    f1.add(Validate.Format,{pattern: /^[a-zA-Z][a-zA-Z\s]{0,}$/,failureMessage: 
					       " Invalid Name"});
				 </script>
      </strong></td>
    </tr>
  <tr bgcolor="#2F8E16"></p>

<p>
 <tr>
     <td height="27" bgcolor="#2F8E16"><strong>Tel</strong></td>
    <td bgcolor="#E1E1E1">  <strong>
      <input id="tel" name="tel" type="text" size="50" /> 
      <script type="text/javascript">
				    var f1 = new LiveValidation('tel');
				   f1.add(Validate.Presence,{failureMessage: " It cannot be empty"});
				   f1.add(Validate.Format,{pattern: /^[0-9]+$/ ,failureMessage: " It allows only numbers"});
				   f1.add( Validate.Length, { minimum: 10, maximum: 10 } );
				 </script>
        </strong></td>
 </tr>
<tr bgcolor="#2F8E16"></p>
<p>
  <tr>
      <td height="30" bgcolor="#2F8E16"><strong>Address</strong></td>
      <td bgcolor="#E1E1E1"> <strong>
        <input id="address" name="address" type="text" size="50" maxlength="500" />
        <script type="text/javascript">
				    var f1 = new LiveValidation('address');
				    f1.add(Validate.Presence,{failureMessage: " Please enter Address"});
				   f1.add(Validate.Format,{pattern: /^[a-zA-Z\s]+$/i ,failureMessage:
				   " It allows only characters"});
				    f1.add(Validate.Format,{pattern: /^[a-zA-Z][a-zA-Z\s]{0,}$/,failureMessage: 
					       " Invalid Name"});
				 </script>
      </strong></td>
  </tr>
  <tr> <td height="33" bgcolor="#FF6600">
    <strong>
    <label>
    <input type="reset" name="Submit2" value="Clear" />
      </label>
    </strong></td><td bgcolor="#FF6600"><strong>
   </label>
   <input type="submit" name="Submit" value="Save" />
    </strong></td>
  <tr bgcolor="#FFFFFF"><td height="32" bordercolor="#00CC33">&nbsp;</td>
  </tr>
 </table>     
</form>

<p>&nbsp;</p>
<p>&nbsp;</p>
<div>
  <div align="center"> Copyright &copy; 2018 Workfoce development Authority </div>
  <!-- end of tooplate_footer -->
</div>
<p>&nbsp;</p>
</body>
</html>
