<?php
session_start();
$_SESSION['manager'] = NULL;
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>&nbsp;MIOLS:&nbsp;Customer Register Page.</title>
<link rel="stylesheet" media="all" href="css/newcss.css" />
<link rel="icon shortcut" href="./images/tools/money.png"/>
<script type="text/javascript" src="jquery-1.4.2.js"></script>
	<script type="text/javascript" src="ui/jquery.ui.core.js"></script>
	<script type="text/javascript" src="ui/jquery.ui.widget.js"></script>
	<script type="text/javascript" src="ui/jquery.ui.datepicker.js"></script>
	  <script type="text/javascript" src="files/mootools.js"></script>
  <script type="text/javascript" src="files/caption.js"></script>
<link rel="stylesheet" type="text/css" href="css/message.css" media="all">
<script type="text/javascript" src="kss/javascript/validation.js"></script>
<link rel="stylesheet" href="files/template.css" type="text/css">
<link rel="stylesheet" href="files/constant.css" type="text/css">

<script src="kss/javascript/jquery-latest.pack.js" type="text/javascript"></script>
<script src="kss/javascript/jcarousellite_1.0.1c4.js" type="text/javascript"></script>
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
<script>
function startTime()
{
var today=new Date();
var h=today.getHours();
var m=today.getMinutes();
var s=today.getSeconds();
// add a zero in front of numbers<10
m=checkTime(m);
s=checkTime(s);
document.getElementById('txt').innerHTML=h+":"+m+":"+s;
t=setTimeout(function(){startTime()},500);
}

function checkTime(i)
{
if (i<10)
  {
  i="0" + i;
  }
return i;
}
</script>
</head>
<body id="body" onload="startTime()">
  <div align="center">
    <table width="850" height="600" border="0" cellspacing="0" background="images/index_06.jpg">
      <tr>
      <td width="60" rowspan="3" background="images/index_06.jpg">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
        <td height="114" colspan="2" id="logo"><img src="images/new_logo.jpg" width="800" height="100" alt="logo" /></td>
        <td width="26" rowspan="3" background="images/index_06.jpg">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
      </tr>
      <tr>
        <td height="50" colspan="2" ><center><div class="top_menu">
      <ul>
      <li><a href="index.php">HOME</a></li>
      <li><a href="admin.php">MANAGER</a></li>
      <li><a href="logincustomer.php">CUSTOMER</a></li>
      <li><a href="about us.php">ABOUT US</a></li>
      </ul>
      </div></center></td>
      </tr>
      <tr>
        <td  style="border-radius:40px 40px 40px 40px;"height="447" width="100" background="images/index_08.jpg" colspan="2">
		<br><table cellspacing="0px;" width="800" align="center">
		 <tr>
			<td colspan="3" align="center" style="font-size:20px;color:blue;">&nbsp;<img src="./images/tools/key.png"  />&nbsp;<b>GATE OF CUSTOMER REGISTER</b></td>
		 </tr>
		 <tr bgcolor="skyblue" align="center">
			<td><img src="images/de.jpg" width="216" height="189" alt="mon" /></td>
			<td rowspan="2">
            <div class="customer">
            <form id="register" method="post" action="insert.php" >
            
                
                    Name:<br> 
                    <input  type="text" name="name" id="name" placeholder="your name here"  required/>
                  <script type="text/javascript">
				    var f1 = new LiveValidation('name');
				    f1.add(Validate.Presence,{failureMessage: "Please enter your name"});
				   f1.add(Validate.Format,{pattern: /^[a-zA-Z\s]+$/i ,failureMessage:
				   " It allows only characters"});
				    f1.add(Validate.Format,{pattern: /^[a-zA-Z][a-zA-Z\s]{0,}$/,failureMessage: 
					       " Invalid Name"});
				 </script>
                <br>
                
                
                    Visa card number:<br> 
                    <input class="text" name="visa" id="visa" pattern="([0-9]{4,4})" maxlength="4" type="number" required="required" placeholder="your visa number here"  />
                 <script type="text/javascript">
				    var f1 = new LiveValidation('visa');
				   f1.add(Validate.Presence,{failureMessage: "It cannot be empty"});
				   f1.add(Validate.Format,{pattern: /^[0-9]+$/ ,failureMessage: "It allows only Visa card numbe"});
                    f1.add(Validate.Format,{pattern: /^(?:(\w)(?!\1\1))+$/,failureMessage: 
					       "no repeat number"});
				 </script>
                <br>
                  
                
                
                    phone number:<br> 
                    <input class="text" name="phone" id="phone" type="number" required="required" maxlength="13"  placeholder="your phone number here" required/>
                <script type="text/javascript">
				    var f1 = new LiveValidation('phone');
				   f1.add(Validate.Presence,{failureMessage: "It cannot be empty"});
				   f1.add(Validate.Format,{pattern: /^[0-9]+$/ ,failureMessage: "It allows only number"});
                    f1.add(Validate.Format,{pattern: /^(?:(\w)(?!\1\1))+$/,failureMessage: 
					       "no repeat number"});
				 </script>
                <br>
                
                    Email:<br> 
                   <input id="email" name="email" type="text" size="50" maxlength="500" placeholder="enter your email" required="required"/> 
  <script type="text/javascript">
				    var f1 = new LiveValidation('email');
				    f1.add(Validate.Presence,{failureMessage: "Please enter email"});
				   f1.add(Validate.Format,{pattern: /[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}$/i ,failureMessage:
				   " It allows only email :@/co/."});
				    f1.add(Validate.Format,{pattern: /[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}$/,failureMessage: 
					       " Invalid email"});
				 </script>
                <br>
               
                    Country/Zip code:<br> 
                    <input class="textLong" name="address" id="address" type="text" placeholder="enter your country/zip code"  required="required" />
                 <script type="text/javascript">
				    var f1 = new LiveValidation('address');
				   f1.add(Validate.Presence,{failureMessage: "It cannot be empty"});
				   f1.add(Validate.Format,{pattern: /^[0-9]+$/ ,failureMessage: "It allows only Country/Zip code"});
                    f1.add(Validate.Format,{pattern: /^(?:(\w)(?!\1\1))+$/,failureMessage: 
					       "no repeat number"});
				 </script>
                <br>
                
                    Password:<br> 
                    <input class="text" name="password" id="password" type="password" placeholder="enter your password" required="required" />
                 <script type="text/javascript">
				    var f1 = new LiveValidation('password');
				    f1.add(Validate.Presence,{failureMessage: "Please enter password"});
				   f1.add(Validate.Format,{pattern: /^(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{6,20}$/i ,failureMessage:
				   "It allows only Password Must contain at least one number and one uppercase and lowercase letter, and at least 8 or more characters!"});
				    f1.add(Validate.Format,{pattern: /^(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{6,20}$/,failureMessage: 
					       "Invalid Password"});
				 </script>
                <br>
                
                 
                    <input class="btn" type="submit" value="Submit" />
                    <input class="btn" type="reset" value="Reset" />
                <br>


            </ul>

        </form>
    <!----        <?php
			if(isset($_POST['submit']))
			{
			
include ('connection.php');
$a=$_POST['name'];
$b=$_POST['phone'];
$c=$_POST['password'];
$d=$_POST['email'];
$e=$_POST['address'];
$sql="insert into customer(c_name,c_phone,c_password,email,address)values('$a','$b','$c','$d','$e')";
$query=mysql_query($sql);
if( $query ){
	header('location:index.php');
} 
else{
	echo "enable to query";
}

				
				}
			
			?>--->
          </div>  
          
            <br></td>
			<td><img src="./images/rice.jpg" width="150" height="150" alt="logo" /></td>
		 </tr>
		  <tr bgcolor="white">
			<td><img src="./images/pack.jpg" width="150" height="150" alt="logo" /></td>
	
			<td><img src="./images/carton.jpg" width="150" height="150" alt="logo" /></td>
		 </tr>
		</table>
        <strong style="font-weight:bold;font-size:20px;color:grey;text-align:center;"><p id="txt"></p><strong></td>
			</td>
			</tr>
			<tr>
        <td height="20" colspan="3"><strong><center>
              &copy; All Rights Reserved;2019
        </center></strong></td>
      </tr>
	  </table>
  </div>
</body>
</html>
