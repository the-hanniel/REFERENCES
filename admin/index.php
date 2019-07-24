<?php
ob_start();
session_start();
/* Include the database configuration file */
require_once("includes/config.php");
/* Include the default function file */
require_once("includes/functions.php");

/* If admin hits the backbutton in the department new page we dont allow him to redirect to index page */

if (isset($_POST['submit']) && $_POST['submit'] == 'Login') {
    //echo '<pre>';print_r($_POST);die;
    $userName = mysql_real_escape_string(trim($_POST['user_name']));
    $userPass = mysql_real_escape_string(trim($_POST['user_pass']));
    $userMD5Pass = md5($userPass);

    if ($userName == '' || $userPass == '') {
        $errorMsg = "Error! Required Fields Cannot Be Left Blank!";
    } else {
        $loginQuery = "SELECT *  FROM `tbl_users` WHERE `u_uname` LIKE '{$userName}' AND `u_pass` LIKE '{$userMD5Pass}' AND `u_utype` NOT LIKE 'student'";
        
        $existFlag = recordAlreadyExist($loginQuery);
        if (!$existFlag) {
            $errorMsg = "Error! Invalid Username or Password!";
        } else {
            $loginRes = mysql_query($loginQuery) or die(mysql_error());
            $loginArr = mysql_fetch_assoc($loginRes);

            $_SESSION['u_id'] = $loginArr['u_id'];
            $_SESSION['u_fname'] = $loginArr['u_fname'];
            $_SESSION['u_lname'] = $loginArr['u_lname'];
            $_SESSION['u_uname'] = $loginArr['u_uname'];
            $_SESSION['u_pass'] = $loginArr['u_pass'];
            $_SESSION['u_utype'] = $loginArr['u_utype'];
            $_SESSION['stud_year'] = $loginArr['stud_year'];
            $_SESSION['stud_dept'] = $loginArr['stud_dept'];
            $_SESSION['stud_sem'] = $loginArr['stud_sem'];
            $_SESSION['stud_regno'] = $loginArr['stud_regno'];
            /* If the user is valid , we need to re direct into department new page */
            header("Location:department_new.php");
            exit;
        }
    }
//} else {
//    if (isset($_SESSION['u_id'])) {
//        header('Location:department_new.php');
//        exit;
//    }
//    if (isset($_GET['msg']) && ($_GET['msg'] == 'sesexpired')) {
//        $errorMsg = "Error! Session has been expired!Login Agai!";
//    }
//    if (isset($_GET['msg']) && ($_GET['msg'] == 'invalid')) {
//        $errorMsg = "Error!Invalid User!";
//    }
//    if (isset($_GET['msg']) && ($_GET['msg'] == 'logout')) {
//        $successMsg = "Successfully Logged out!";
//    }
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <title>Welcome to Course Evaluation</title>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        <!--Link to the template css file-->
        <link rel="stylesheet" type="text/css" href="css/style.css" />
        <!--Link to Favicon -->
        <link rel="icon" href="images/favi_logo.gif"/>
        <!--Link to Validation JS source File -->
        <script type = 'text/javascript' language='javascript' src = 'js/validation.js'></script>
        <!-- Spry Stuff Starts here-->
        <link href="spry/textfieldvalidation/SpryValidationTextField.css" rel="stylesheet" type="text/css" />
        <script type="text/javascript" src="spry/textfieldvalidation/SpryValidationTextField.js"></script>
        <!-- Spry Stuff Ends Here-->
    </head>
    <body>
        <div class="main">
            <div class="header">
                <div class="header_text"></div><!--End of header text div-->
                <div class="menu">
                    <ul>
                        <li><a href="index.php" class="active">Admin Login</a></li>
                    </ul>
                </div><!--End of menu div-->
            </div><!--End of header div-->
            <div class="body">
                <div class="main_body">
                    <h2>Welcome to Course Evaluation System</h2>
<?php
/* Display the Messages */
if (isset($errorMsg)) {
    echo "<p><span class = 'error'>{$errorMsg}</span></b>";
} elseif (isset($successMsg)) {
    echo "<p><span class = 'success'>{$successMsg}</span></p>";
}
?>
                    <br/>
                    <form method = 'POST' action = "<?php echo $_SERVER['PHP_SELF']; ?>">
                        <div class='shadowEffect' style='width:320px;'>
                            <table  border = '0' cellspacing = '0' cellpadding = '0'>
                                <tr>
                                    <td width = '120' height = '30'>
                                        <strong>User Name  </strong>
                                        <span class = 'mandatory'>*</span>
                                    </td>
                                    <td width = '120' height = '30'>
                                        <span id="sprytextfield1">
                                            <input type = 'text' name = 'user_name' id = 'user_name'
                                                   value = "<?php echo (isset($userName)) ? $userName : '' ?>"  class = 'typeproforms' />
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <td width = '120' height = '30'>
                                        <strong>Password  </strong>
                                        <span class = 'mandatory'>*</span>
                                    </td>
                                    <td height = '30'>
                                        <span id="sprytextfield2">
                                            <input type = 'password' name = 'user_pass' id = 'user_pass' class = 'typeproforms' />
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <td>&nbsp;</td>
                                    <td height = '30'>
                                        <input type = 'submit' name = 'submit' class = 'button' value = 'Login' />
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </form><!-- End of form-->
                    <br/>
                </div><!-- End of main_body div(main white div)-->
<?php
/* This function will return the logo div string to the sidebody */
echo plotLogoDiv();
?>
            </div><!-- End of Body div-->
            <div class='clr'></div>
        </div><!--End of Main Div-->
<?php
/* This function will return the footer div information */
echo plotFooterDiv();
?>
        <script type="text/javascript">
            <!--
            var sprytextfield1 = new Spry.Widget.ValidationTextField("sprytextfield1", "custom",{isRequired:true,characterMasking:/[a-zA-Z ]/,
                useCharacterMasking:true, validateOn:["change"]});
            var sprytextfield2 = new Spry.Widget.ValidationTextField("sprytextfield2", "custom",{isRequired:true,characterMasking:/[a-zA-Z0-9 ]/,
                useCharacterMasking:true, validateOn:["change"]});
            -->
        </script>
    </body>
</html>