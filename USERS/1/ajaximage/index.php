<?php
#include('db.php');
#session_start();
#$session_id='1'; //$session id
?>

<script type="text/javascript" src="ajaximage/scripts/jquery.min.js"></script>
<script type="text/javascript" src="ajaximage/scripts/jquery.form.js"></script>

<script type="text/javascript" >
 $(document).ready(function() { 
		
            $('#photoimg').live('change', function()			{ 
			           $("#photo").html('');
			    $("#photo").html('<img src="ajaximage/loader.gif" width="90" alt="Uploading...."/>');
			$("#imageform").ajaxForm({
						target: '#photo'
		}).submit();
		
			});
        }); 
</script>

<style>

body
{
font-family:arial;
}
.preview
{
width:200px;
border:solid 1px #dedede;
padding:10px;
}
#photo
{
color:#cc0000;
font-size:12px
}
#preview
{
color:#cc0000;
font-size:12px
}

</style>
<!-- <a href='http://9lessons.info'>9lessons.info</a> -->


<form id="imageform" name=imageform method="post" enctype="multipart/form-data" action='ajaximage/ajaximage.php'>
	<div id=studentcard2 style="width:350px; border:1px solid grey; display:block">
		<table border=0>
			<tr valign=top>
				<td rowspan=3><img src='../../admin/images/lgo.jpg' width='50' height='60' alt='Logo' /></td>
				<td colspan=2>ECOLE TECHNIQUE SAINT KIZITO-SAVE</td>
			</tr>
			<tr>
				<td colspan=2>Rwanda - Southern Province - Gisagara District</td>
			</tr>
			<tr>
				<style>
					#imgs{
						width:90px; height:98px; border:1px solid grey;
					}
					.ids{
						font-weight:bold;
					}
				</style>
				<td align=center><font color=green style='font-size:18px;font-weight:bold; text-decoration:underline;' >STUDENT ID</font></td>
				<td rowspan=2 valign=top width=90 style="border:0px solid #003;">
					<input type=hidden name=id />
					<label id=photo><img src='./ajaximage/uploads/photo.jpg' style='width:90px; height:98px; border:1px solid grey;' alt='Photo' /></label>
					<label id=upload></label>
					<div id='preview'></div>
				</td>
			</tr>
			<tr valign=top>
				<td colspan=2>
					Name:<label id=name class=ids></label><br />
					Class:<label id=class class=ids></label><br />
					Ac.Year:<label id=acyear class=ids></label><br />
					Exp.Date:<label id=expdate class=ids></label>
				</td>
			</tr>
			
		</table>
	</div>

	<div id=studentcard1 style="width:350px; border:1px solid grey; display:none;"> <!--
			<table>
				<tr>
					<td>
						Name
					</td>
					<td>
						<input type=text style='width:180px' readonly name=studentname class=typeproform /> 
					</td> 
				</tr>
				<tr>
					<td>
						Select Image
					</td>
					<td>
						<input class=typeproform style='width:180px' type="file" name="photoimg" id="photoimg" />
					</td>
				</tr>
			</table>
		<div id='preview'>
		</div> -->
	</div>
</form>