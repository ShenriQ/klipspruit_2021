<?php

function folder_is_writable($path) {

	if(!is_dir($path))  return false;

	do {
		$test_file = with_slash($path) . sha1(uniqid(rand(), true));
	} while(is_file($test_file));

	$put = @file_put_contents($test_file, 'test');
	if($put === false)  return false;

	@unlink($test_file);
	
	return true;

}
  
function file_is_writable($path) {

	if(!is_file($path)) return false;

	$open = @fopen($path, 'a+');
	if($open === false) return false;

	@fclose($open);

	return true;

}
  
function get_file_extension($path, $leading_dot = false) {

	$filename = basename($path);
	$dot_offset = (boolean) $leading_dot ? 0 : 1;
	
	if( ($pos = strrpos($filename, '.')) !== false ) {
		return substr($filename, $pos + $dot_offset, strlen($filename));
	}
	
	return '';

}

function is_dir_empty($dir_path) {

	$d = dir($dir_path);
	
	if($d) {

		while(false !== ($entry = $d->read())) {

			if(($entry == '.') || ($entry == '..')) continue;
			return false;

		}

	}

	return true;
	
}

function delete_dir($dir) {

	$dh = opendir($dir);

	while($file = readdir($dh)) {

		if(($file != ".") && ($file != "..")) {

			$fullpath = $dir . "/" . $file;
			
			if(!is_dir($fullpath)) {
				unlink($fullpath);
			} else {
				delete_dir($fullpath);
			}

		}

	}
	
	closedir($dh);
	return rmdir($dir) ? true : false;

}

function force_mkdir($path, $chmod = null) {

	if(is_dir($path)) return true;

	$real_path = str_replace('\\', '/', $path);
	$parts = explode('/', $real_path);
	
	$forced_path = '';
	foreach($parts as $part) {
	
		if($forced_path == '') {
			$start = substr(__FILE__, 0, 1) == '/' ? '/' : '';
			$forced_path = $start . $part;
		} else {
			$forced_path .= '/' . $part;
		}
		
		if(!is_dir($forced_path)) {

			if(!is_null($chmod)) {
				if(!mkdir($forced_path)) return false;
			} else {
				if(!mkdir($forced_path, $chmod)) return false;
			}
		
		}
	
	}

	return true;

}

function add_avatar($source, $extension = null) {

	if(!is_readable($source)) return false;
	
	$attach_extension = trim($extension) == '' ? '' : '.' . trim($extension);
	do {
		$destination = get_avatar_file_path(sha1(uniqid(rand(), true)) . $attach_extension);
	} while(is_file($destination));
	
	return copy($source, $destination) ? basename($destination) : false;

}

function get_avatar_file_path($filename) {
	return FCPATH.'/public/avatars/'.$filename;
}

function delete_avatar($delete_file) {
	
	$destination = get_avatar_file_path($delete_file);
	
	if(is_file($destination)) {
		return @unlink($destination);
	}
	
	return false;
	
}
    
// upload photos
function upload_file($source, $extension = null) {

	if(!is_readable($source)) return false;
	
	$attach_extension = trim($extension) == '' ? '' : '.' . trim($extension);
	do {
		$destination = get_upload_path(sha1(uniqid(rand(), true)) . $attach_extension);
	} while(is_file($destination));
	
	return copy($source, $destination) ? basename($destination) : false;

}

function get_upload_path($filename) {
	return FCPATH.'/public/uploads/'.$filename;
}

function delete_file($delete_file) {
	
	$destination = get_upload_path($delete_file);
	
	if(is_file($destination)) {
		return @unlink($destination);
	}
	
	return false;
}
// 

function download_contents($content, $type, $name, $size, $force_download = false){

	if(connection_status() != 0) return false;
	
	if($force_download) {
		header("Cache-Control: public");
	} else {

		header("Cache-Control: no-store, no-cache, must-revalidate");
		header("Cache-Control: post-check=0, pre-check=0", false);
		header("Pragma: no-cache");

	}

	header("Expires: " . gmdate("D, d M Y H:i:s", mktime(date("H") + 2, date("i"), date("s"), date("m"), date("d"), date("Y"))) . " GMT");
	header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
	header("Content-Type: $type");
	header("Content-Length: " . (string) $size);
	
	$disposition = $force_download ? 'attachment' : 'inline';
	header("Content-Disposition: $disposition; filename=\"" . $name . "\"");
	header("Content-Transfer-Encoding: binary");
	print $content;
	
	return((connection_status() == 0) && !connection_aborted());   

}

function download_file($file){
	
	try {

		$file_repository = new FileRepository();
		download_contents($file_repository->getFileContent($file->getFileRepositoryId()), $file->getFileTypeString(), $file->getFileName(), $file->getFileSize(), true);
		die();

	} catch(Exception $e) {
		return false;
	}
		
}
