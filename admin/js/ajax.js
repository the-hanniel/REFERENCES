function getAjaxObject()
{
	var ajaxObj	= '';
	try
	{
		ajaxObj	= new XMLHttpRequest();	
	}
	catch(e)
	{
		try
		{
			ajaxObj		=	new ActiveXObject("Microsoft.XMLHTTP");
		}
		catch(e)
		{
			try
			{	
				ajaxObj		=	new ActiveXObject("Msxml2.XMLHTTP");
			}
			catch(e1)
			{
				ajaxObj = false;
			}	
		}	
		
	}	
	return ajaxObj;	
}	
function plotTeacherByDept(deptId,teacherId)
{
	if(teacherId)
	{	
		var urlLocation		=	"find_dept_based_teacher.php?id="+deptId+"&teacherId="+teacherId; 
	}
	else
	{
		var urlLocation		=	"find_dept_based_teacher.php?id="+deptId; 	
	}	
	var	ajaxObject		=	getAjaxObject();
	if(ajaxObject)
	{
		ajaxObject.onreadystatechange =	function()
		{	
			if(ajaxObject.readyState == 4)
			{
				if(ajaxObject.status == 200)
				{
					//alert(ajaxObject.responseText);	
					document.getElementById('teacher_name_div').innerHTML = ajaxObject.responseText;	
				}
				else
				{
					alert("There was a problem while using AJAX object");	
				}	
			}	
			
		}
		ajaxObject.open("GET" ,urlLocation,true);
		ajaxObject.send(null);
	}
}

function plotSubjectByDept(subjectId)
{
	var year 		= document.getElementById('year').value;
	var semester 	= document.getElementById('semester').value;
	var deptId 		= document.getElementById('department').value;
	if(subjectId !='')
	{
		var urlLocation		=	"find_dept_based_subject.php?year="+year+'&sem='+semester+'&deptId='+deptId+
								'&subjectId='+subjectId; 
	}
	else
	{
		var urlLocation		=	"find_dept_based_subject.php?year="+year+'&sem='+semester+'&deptId='+deptId; 	
	}
	//alert(urlLocation);
	var	ajaxObject		=	getAjaxObject();
	if(ajaxObject)
	{
		ajaxObject.onreadystatechange =	function()
		{	
			if(ajaxObject.readyState == 4)
			{
				if(ajaxObject.status == 200)
				{
					//alert(ajaxObject.responseText);	
					document.getElementById('subject_name_div').innerHTML = ajaxObject.responseText;	
				}
				else
				{
					alert("There was a problem while using AJAX object");	
				}	
			}	
			
		}
		ajaxObject.open("GET" ,urlLocation,true);
		ajaxObject.send(null);
	}
}
function plotSubjectByDeptForHistory(subjectId)
{
	var year 		= document.getElementById('year').value;
	var semester 	= document.getElementById('semester').value;
	var deptId 		= document.getElementById('dept_code').value;
	if(subjectId !='')
	{
		var urlLocation		=	"find_dept_based_subject_history.php?year="+year+'&sem='+semester+'&deptId='+
								deptId+'&subjectId='+subjectId; 
	}
	else
	{
		var urlLocation		=	"find_dept_based_subject_history.php?year="+year+'&sem='+semester+'&deptId='+deptId; 	
	}
	var	ajaxObject		=	getAjaxObject();
	if(ajaxObject)
	{
		ajaxObject.onreadystatechange =	function()
		{	
			if(ajaxObject.readyState == 4)
			{
				if(ajaxObject.status == 200)
				{
					//alert(ajaxObject.responseText);	
					document.getElementById('subject_name_div').innerHTML = ajaxObject.responseText;	
				}
				else
				{
					alert("There was a problem while using AJAX object");	
				}	
			}	
			
		}
		ajaxObject.open("GET" ,urlLocation,true);
		ajaxObject.send(null);
	}
	
}	