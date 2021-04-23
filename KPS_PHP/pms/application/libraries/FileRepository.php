<?php
class FileRepository {

    public $repository_dir;

	function __construct() {
		$this->repository_dir = FCPATH . 'public/files';
	}

	private function idToPath($file_id) {
	
		if(strlen($file_id) == 40) {
			
			$parts = array();
	
			for($i = 0; $i < 2; $i++) {
			  $parts[] = substr($file_id, $i * 5, 5);
			}
	
			$parts[] = substr($file_id, 10, 30);
	
			return implode('/', $parts);
			
		}else{
			return null;
		}
	
	}
	
	private function getFilePath($file_id) {
		return with_slash($this->repository_dir) . $this->idToPath($file_id);
	}
	
	private function cleanUpDir($file_id) {
    
		$path = $this->idToPath($file_id);
		
		if(!$path) return;
		
		$path_parts = explode('/', $path);
		$repository_path = with_slash($this->repository_dir);
		
		$for_cleaning = array(
		$repository_path . $path_parts[0] . '/' . $path_parts[1],
		$repository_path . $path_parts[0],
		);
		
		foreach($for_cleaning as $dir) {

			if(is_dir_empty($dir)) {
				delete_dir($dir);
			} else {
				return;
			}

		}
    
	}
		
	private function getUniqueId() {
	
		do {

			$id = sha1(uniqid(rand(), true));
			$file_path = $this->getFilePath($id);

		} while(is_file($file_path));
		
		return $id;

	}


    function updateFileContent($source) {

		if(!is_readable($source)) {
			throw new Exception ("Source file is not readable.");
		}

		$file_id = $this->getUniqueId();
		$file_path = $this->getFilePath($file_id);

		$destination_dir = dirname($file_path);
      
      	if(!is_dir($destination_dir)) {
        
			if(!force_mkdir($destination_dir, 0777)) {
          		throw new Exception ("Failed to create folder.");
        	}
      	
		}
	  		
		if(!copy($source, $file_path)) {
		
			$this->cleanUpDir($file_id);
			throw new Exception ("Failed to upload file.");
		
		}

		return $file_id;

    }

    function getFileContent($file_id) {

		$file_path = $this->getFilePath($file_id);
	
		if(!is_file($file_path) || !is_readable($file_path)) {
			throw new Exception ("File Not found.");
		}
		
		return file_get_contents($file_path);
	  
    }
    
    function deleteFile($file_id) {

		$file_path = $this->getFilePath($file_id);

		if(is_file($file_path) && !@unlink($file_path)) {
			throw new Exception ("Failed to delete file.");
		}
				
		$this->cleanUpDir($file_id);

		return true;

    }

	
}
