<?php


class DirScan {
	// Variable that contains the list of file dans directory
	var $TabFiles = array();

	// Count of the number of files
	var $FileCount = 0 ;

	// Total size of the files 
	var $FileSize = 0 ;

	// Count of subdirectory
	var $DirCount = 0 ;

	// Filter for files entensions
	var $FilterExt = array();

	// Filtering enable mode
	var $FilterEnable = false;

	// Enable SubDirectory scanning
	var $ScanSubDir = true;

	// enable Files Scanning
	var $ScanFiles = true; 

	// Array of all the extension find in files list
	var $FileExtList = array();

	// enable File Extension List
	var $FilesExt = false;

        // Store Full details of files
        var $FullDetails = false;

	/**
	 *	Setup the list of all the extension find in files list
	 * @author	jeanf.garcia71 AT gmail.com 
	 * 
	 * @param	bool	$b			Enable (true) / disable (false) the process to keep all the extension file
	 *							found during the scan of the directory
	*/
	function SetFileExtListEnable($b) {
		$this->FilesExt = $b ;
	}


	/**
	 *	Enable the scan of the sub directories
	 * @author	jeanf.garcia71 AT gmail.com
	 *
	 * @param	bool	$b			Enable (true) / disable (false) the scan of the sub directory
	*/
	function SetScanSubDirs($b) {
		$this->ScanSubDir = $b ;
	}
	/**
	 *	Enable the scan of the files
	 * @author	jeanf.garcia71 AT gmail.com
	 *
	 * @param	bool	$b			Enable (true) / disable (false) the scan of the files
	*/
	function SetScanFiles($b) {
		$this->ScanFiles = $b ;
	}

	/**
	 *	Enable the full details of files
	 * @author	jeanf.garcia71 AT gmail.com
	 *
	 * @param	bool	$b			Enable (true) / disable (false) the full details
	*/
	function SetFullDetails($b) {
		$this->FullDetails = $b ;
	}

        /**
	 * Private : Function to log the extension file list conditionned by the flag
	 * @author	jeanf.garcia71 AT gmail.com
	 * 
	 * @param	string	$ext			Add an extension file to the array. 
	 *							
	*/
	function PutExtInList($ext) {
		if ($this->FilesExt) {
			if ( (! in_array($ext, $this->FileExtList) ) && ($ext!="") ){
				$this->FileExtList[] = $ext ;
			}
		}
	}
	/*
		Filtering extension management
	*/	

	/**
	 *	Setup the Filtering table to select only defined extension files 
	 * @author	jeanf.garcia71 AT gmail.com
	 * 
	 * @param	array	$arr			give an array to the function to set the filtering extension list
	 *							
	 */
	function SetFilterExt($arr) {
		$this->FilterExt=array();
		foreach ($arr as $r) {
			$this->FilterExt[] = $r ;			
		}
	}
	/*
		Enable/disable the Filtering mode
	 * @author	jeanf.garcia71 AT gmail.com
	 * 
	 * @param	bool	$b			Enable (true) / disable (false) the filter Mode
	 *							
	*/
	function SetFilterEnable($b) {
		$this->FilterEnable = $b ;
	}
	/*
		Private function to check if the extension file  is in the filter definition
	 * @author	jeanf.garcia71 AT gmail.com
	 * 
	 * @param	str	$f			Check if the extension is present in the filter
	 *							also return true if no filter mode is active
	 * @return	bool	if present in filter return true, otherwise false.
	 *			if filtre mode is not active, return always true							
	 * 
	*/
	function IsFilter ($f) {
		$ret = true ;
		if ($this->FilterEnable) {		
			if (!in_array($f,$this->FilterExt)) {
				$ret = false;
			}
		} 
		return $ret ;
	}

	/*
		Check files in the directory and create the array with all the attended files (filtering or not)
	 * @author	jeanf.garcia71 AT gmail.com
	 * 
	 * @param	string	$rootstr			string of the directory path, without the slash terminal
	 *							
	 * @param	int	$full				if 1, the filename is the full version (path + filename)
	 *							if 0, the filename is the short version (filename without path) (default)
	 *
	 * @return	array	array of all the files and subdirs scanned. each element is an array defined like this :
	 *   			'filename' 	=> filename
				'dirname'	=> directory of the file
				'basename'	=> filename without extension
				'extension'	=> filename extension
				'size'		=> Size of the file in byte
				'perms'		=> Permission of the file, in octal mode
				'datechange'	=> Date of the last changes fo the file
				'dateaccess'	=> Date of the last access to the file
				'datemodify'	=> Date of the last modification of the file
				'type'		=> type of the file (file always "file", otherwise "dir" for directory)
	 * 
	*/
	function GetFilesDirectory($rootstr, $full=false) {
	    $tab=array();
	    $dr=opendir($rootstr);
	    while (false !== ($f=readdir($dr))) {
		if ( (is_file($rootstr."/".$f)) && ($f!=".") && ($f!="..") ) {
			$path_parts = pathinfo($rootstr."/".$f);
			if (!isset($path_parts["extension"])) { $path_parts["extension"] = ""; }
			if ( $this->IsFilter($path_parts["extension"]) ) {
			    if ($full) {
				$filename = $rootstr."/".$f;
			    } else {
				$filename = $f;
			    }
                                if ($this->FullDetails) {
                                    $tab[]=array (	'filename' 	=> $filename ,
                                                    'dirname'	=> $path_parts['dirname'],
                                                    'basename'	=> $path_parts['basename'],
                                                    'extension'	=> $path_parts['extension'],
                                                    'size'		=> filesize($rootstr."/".$f),
                                                    'perms'		=> fileperms($rootstr."/".$f),
                                                    'datechange'	=> filectime($rootstr."/".$f),
                                                    //'datechange_str'=> date("F d Y H:i:s.", filectime($rootstr."/".$f)),
                                                    'dateaccess'	=> fileatime($rootstr."/".$f),
                                                    //'dateaccess_str'=> date("F d Y H:i:s.", fileatime($rootstr."/".$f)),
                                                    'datemodify'	=> filemtime($rootstr."/".$f),
                                                    //'datemodify_str'=> date("F d Y H:i:s.", filemtime($rootstr."/".$f)),
                                                    'type'		=> filetype($rootstr."/".$f),		);
                                    $this->PutExtInList($path_parts["extension"]);
                                } else {
                                    $tab[]=array (  'filename' 	=> $filename ,
                                                    'dirname'	=> $path_parts['dirname'],
                                                    'size'		=> filesize($rootstr."/".$f));
                                    $this->PutExtInList($path_parts["extension"]);
                                }

			}
		}
	    }
	    sort($tab);
	    return $tab;
	}

	/*
		Check subdirectory in the directory and create the array with all the attended Directory
	 * @author	jeanf.garcia71 AT gmail.com
	 * 
	 * @param	string	$rootstr			string of the directory path, without the slash terminal
	 *							
	 * @param	int	$full				if 1, the filename is the full version (path + filename)
	 *							if 0, the filename is the short version (filename without path) (default)
	 *
	 * @return	array	array of all the files and subdirs scanned. each element is an array defined like this :
	 *   			'filename' 	=> filename
				'dirname'	=> directory of the file
				'basename'	=> filename without extension
				'extension'	=> filename extension
				'size'		=> Size of the file in byte
				'perms'		=> Permission of the file, in octal mode
				'datechange'	=> Date of the last changes fo the file
				'dateaccess'	=> Date of the last access to the file
				'datemodify'	=> Date of the last modification of the file
				'type'		=> type of the file (file always "file", otherwise "dir" for directory)

	 * 
	*/
	function GetRootDirectories($rootstr, $full=false) {
	    $tab=array();
	    $d=opendir($rootstr);
	    while (false !== ($f=readdir($d))) {
		if ( (is_dir($rootstr."/".$f)) && ($f!=".") && ($f!="..") && ($f!="") ) { // directory de la recine, donc on memorise ce directory
                    $path_parts = pathinfo($rootstr."/".$f);
		    if ($full) {
		        $dirname=$rootstr."/".$f;
		    } else {
		        $dirname=$f;
		    }
                        if ($this->FullDetails) {
                            $tab[]=array (	'filename' 	=> $dirname ,
                                            'dirname'	=> $rootstr."/".$f,
                                            'basename'	=> "",
                                            'extension'	=> "",
                                            'size'		=> 0,
                                            'perms'		=> 0,
                                            'datechange'	=> null,
                                            //'datechange_str'=> "",
                                            'dateaccess'	=> null,
                                            //'dateaccess_str'=> "",
                                            'datemodify'	=> null,
                                            //'datemodify_str'=> "",
                                            'type'		=> "dir");
                            } else {
                                    $tab[]=array (  'filename' 	=> $dirname ,
                                                    'dirname'	=> $path_parts['dirname'],
                                                    'size'		=> filesize($rootstr."/".$f));
                           }

		}
	    }
	    sort($tab);
	    return $tab;
	}

	/*
		Recurse Function that retreive all the files and all the directory names (including subdirectory if not specified)
	 * @author	jeanf.garcia71 AT gmail.com
	 * 
	 * @param	string	$rootstr			string of the directory path, without the slash terminal
	 *							
	 * @param	int	$full				if 1, the filename is the full version (path + filename)
	 *							if 0, the filename is the short version (filename without path) (default)
	 *
	*/
	function ScanDir($rootfs, $full=false) {
                $sizedir = 0 ;
		$s = 0 ;
                
		if ($this->ScanFiles) {
			$fil = $this->GetFilesDirectory($rootfs,$full);
			foreach($fil as $f) {
				$this->TabFiles[] = $f ;
				$this->FileSize += $f["size"];
				$this->FileCount ++; 
				$sizedir += $f["size"];
			}

		}
		if ($this->ScanSubDir) {
			$dir = $this->GetRootDirectories($rootfs,true);
			foreach($dir as $d) {
				$s = $this->ScanDir($d["dirname"],true);

				$this->FileSize += $s ;
				$sizedir += $s ;
				$d["size"] = $sizedir ;

				$this->TabFiles[] = $d ;
				$this->DirCount ++ ;
			}
		}
	}

}



?>