<?php
ob_start();
session_start();
/* Include the database configuration file */
require_once("../admin/includes/config.php");
/* Include the default function file */
require_once("../admin/includes/functions.php");


/* If student hits the backbutton in the feedback page we dont allow him to redirect to index page */

if (isset($_POST['submit'])) {
    //echo '<pre>';print_r($_POST);die;
    $userName = mysql_real_escape_string(trim($_POST['user_name']));
    //$userPass		=	mysql_real_escape_string(trim($_POST['user_pass']));
    //$userMD5Pass	=	md5($userPass);

    if ($userName == '') {
        $errorMsg = "Error! Required Fields Cannot Be Left Blank!";
    } else {
        $loginQuery = "SELECT *  FROM `tbl_codes` WHERE `code` = '{$userName}' ";


        $loginRes = mysql_query($loginQuery) or die(mysql_error());
        $loginArr = mysql_fetch_assoc($loginRes);

        if ($loginArr) {
            if ($loginArr['used'] == 0) {
                //echo '<pre>';print_r($loginArr);die;
                $_SESSION['u_id'] = $loginArr['id'];
                $_SESSION['u_fname'] = 'student';
                $_SESSION['u_lname'] = 'student';
                $_SESSION['u_uname'] = $loginArr['code'];
                //$_SESSION['u_pass']	 	= $loginArr['u_pass'];
                $_SESSION['u_utype'] = 'student';
                $_stinfo = getInfoFromCode($loginArr['code']);
                $_SESSION['stud_year'] = $_stinfo['year'];
                $_SESSION['stud_dept'] = $_stinfo['department'];
                $_SESSION['stud_sem'] = $_stinfo['semester'];

                header("Location: feedback.php");
                exit;
            } else {
                $errorMsg = "This Code Has Been Used.";
            }
        } else {
            $errorMsg = "Code Invalid";
        }
    }
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <title>Welcome to Course Evaluation</title>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        <!--Link to the template css file-->
        <link rel="stylesheet" type="text/css" href="../admin/CSS/style.css" />
        <!--Link to Favicon -->
        <link rel="icon" href="../admin/images/favi_logo.gif"/>
        <!--Link to Validation JS source File -->
        <script type = 'text/javascript' language='javascript' src = '../admin/js/validation.js'></script>

        <link href="../admin/spry/textfieldvalidation/SpryValidationTextField.css" rel="stylesheet" type="text/css" />

        <script type="text/javascript" src="../admin/spry/textfieldvalidation/SpryValidationTextField.js"></script>
        <!-- Spry Stuff Ends Here-->


    </head>
    <body>

        <div class="main">
            <div class="header">
                <div class="header_text"></div><!--End of header text div-->
                <div class="menu">
                    <ul>
                        <li><a href="index.php" class="active">Student Login</a></li>
                    </ul>
                </div><!--End of menu div-->
            </div><!--End of header div-->
            <div class="body">
                <div class="main_body">
                    <h2>Welcome to Course Evaluation System</h2>
                    <?php
                    /* Display the Messages */
                    if (isset($errorMsg)) {
                        echo "<p><span class = 'error'>{$errorMsg}</span>";
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
                                        <strong>Code  </strong>
                                        <span class = 'mandatory'>*</span> 
                                        &nbsp;&nbsp;<span id="show_char"></span>
                                    </td>
                                    <td width = '120' height = '30'>

                                        <span id="sprytextfield1xxx">
                                            <input type = 'password' name = 'user_name' id = 'user_name' maxlength="15" class = 'typeproforms'  />
                                            <span class="textfieldInvalidFormatMsg">Invalid code!</span>
                                        </span>
                                    </td>
                                </tr>

                                <?php /*
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

                                 */
                                ?>
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
                                echo plotLogoDiv($imgPath = "../admin/images/logo.png");
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
            var sprytextfield1 = new Spry.Widget.ValidationTextField("sprytextfield1", "email", {isRequired:true,validateOn:["change"]});
            var sprytextfield2 = new Spry.Widget.ValidationTextField("sprytextfield2", "custom",{isRequired:true,characterMasking:/[a-zA-Z0-9 ]/,
                useCharacterMasking:true, validateOn:["change"]});
            -->
        </script>
    </body>
</html>