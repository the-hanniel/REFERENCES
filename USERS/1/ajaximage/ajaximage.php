<?php
session_start();
include('../../../LIB/config.php');
$db = new DBConnector("school_report_db".$_SESSION['year']);
$session_id='1'; //$session id
$path = "uploads/";

	$valid_formats = array("JPG", "jpg", "PNG", "png", "GIF", "gif", "BMP", "bmp");
	if(isset($_POST) and $_SERVER['REQUEST_METHOD'] == "POST")
		{
			if($_POST['id'] == ''){
				#var_dump($_POST);
				echo "Select A Student Please!";
				die;
			}
			
			//view student profile
			$student = $db->selectFields($tbl="tbl_student",$field=array("StudentID","StudentPhoto"),$condition=array("StudentID"=>$_POST['id']),$limit=null,$order="",$indexed=true,$sign='=',$multiplereference=false,$distinct=false);
			
			#var_dump($_POST); die;
			#var_dump($student); die;
			if($student[0]['StudentPhoto'] != '' && $student[0]['StudentPhoto'] != null ){
				echo "<img src='ajaximage/uploads/".$student[0]['StudentPhoto']."' id=imgs  class='preview'>";
				echo "<script>document.getElementById('upload').style.display='none';</script>";
				die;
			}
			$name = $_FILES['photoimg']['name'];
			$size = $_FILES['photoimg']['size'];
			
			if(strlen($name))
				{
					list($txt, $ext) = explode(".", $name);
					if(in_array($ext,$valid_formats))
					{
					if($size<(1024*1024))
						{
							$actual_image_name = time().substr(str_replace(" ", "_", $txt), 5).".".$ext;
							
							$tmp = $_FILES['photoimg']['tmp_name'];
							if(move_uploaded_file($tmp, $path.$actual_image_name))
								{
								mysql_query("UPDATE tbl_student SET `StudentPhoto`='".$actual_image_name."' WHERE `StudentID`='".$student[0]['StudentID']."'");
									echo "<img src='ajaximage/uploads/".$actual_image_name."' id=imgs  class='preview'>";
									echo "<script>document.getElementById('upload').style.display='none';</script>";
								}
							else
								echo "failed";
						}
						else
						echo "Image file size max 1 MB";					
						}
						else
						echo "Invalid file format..";	
				}
				
			else
				echo "Please select image..!";
				
			exit;
		}
?>