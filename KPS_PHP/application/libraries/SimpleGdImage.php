<?php

class SimpleGdImage {

	const BOUNDARY_INCREASE_ONLY = 'increase';
	const BOUNDARY_DECREASE_ONLY = 'decrease';
	
	protected $is_new = true;
	protected $is_loaded = false;
	protected $resource;
	protected $source;
	protected $image_type;
	protected $width = null;
	protected $height = null;
	
	function __construct($load_from_file = null) {
	  if(!is_null($load_from_file)) $this->loadFromFile($load_from_file);
	}
	
	function __destruct() {
	  if(is_resource($this->resource)) imagedestroy($this->resource);
	}
	
	function loadFromFile($file_path) {
	  if(!is_readable($file_path)) throw new Exception("File Error.");
	  $image_type = false;
	  if(function_exists('exif_imagetype')) {
		$image_type = exif_imagetype($file_path);
	  } else {
		$image_size = getimagesize($file_path);
		if(is_array($image_size) && isset($image_size[2])) $image_type = $image_size[2];
	  }
	  
	  if($image_type === false) throw new Exception("Failed not image.");
	  
	  switch($image_type) {
		case IMAGETYPE_PNG:
		  $this->resource = imagecreatefrompng($file_path);
		  break;
		case IMAGETYPE_JPEG:
		  $this->resource = imagecreatefromjpeg($file_path);
		  break;
		case IMAGETYPE_GIF:
		  $this->resource = imagecreatefromgif($file_path);
		  break;
		default:
		 throw new Exception("Image Type not supported.");
	  }
	  
	  if(!is_resource($this->resource)) {
		$this->resource = null;
		throw new Exception("Failed to load image.");
	  }
	  
	  $this->setIsLoaded();
	  $this->setSource($file_path);
	  $this->setImageType($image_type);
	}
	
	function save() {
	  if(!$this->isLoaded() || !is_file($this->getSource())) {
		throw new Error('This image was not loaded from the file. Use saveAs() function instead of save() - there you\'ll be able to specify output file and type');
	  }
	  if(!file_is_writable($this->getSource())) {
		throw new FileNotWriableError($this->getSource());
	  }
	  switch($this->getImageType()) {
		case IMAGETYPE_PNG:
		  imagepng($this->resource, $this->getSource());
		  break;
		case IMAGETYPE_JPG:
		  imagejpeg($this->resource, $this->getSource(), 80);
		  break;
		case IMAGETYPE_GIF:
		  imagegif($this->resource, $this->getSource());
		  break;
		default:
		  throw new Exception("Image Type not supported.");
	  }
	  return true;
	}
	
	function saveAs($file_path, $as_type = null) {
	  if(is_null($as_type)) $as_type = $this->getImageType();
	  
	  $as_type = (integer) $as_type;
	  if(($as_type < IMAGETYPE_GIF) || ($as_type > IMAGETYPE_PNG)) $as_type = IMAGETYPE_PNG;
	  
	  switch($as_type) {
		case IMAGETYPE_PNG:
		  $write = imagepng($this->resource, $file_path);
		  break;
		case IMAGETYPE_JPEG:
		  $write = imagejpeg($this->resource, $file_path, 80);
		  break;
		case IMAGETYPE_GIF:
		  $write = imagegif($this->resource, $file_path);
		  break;
		default:
		  throw new Exception("Image Type not supported.");
	  }
	  
	  if(!$write) throw new Exception("Failed to write file.");
	  return true;
	}
	
	function createFromResource($resource) {
	  if(is_resource($resource) && (get_resource_type($resource) == 'gd')) {
		$this->reset();
		$this->resource = $resource;
	  } else {
		throw new Exception("Resource Error.");
	  }
	}
	
	protected function reset() {
	  $this->is_new = true;
	  $this->is_loaded = false;
	  $this->resource = null;
	  $this->source = null;
	  $this->image_type = null;
	  $this->width = null;
	  $this->height = null;
	} 
	
	function getMimeType() {
	  return image_type_to_mime_type($this->getImageType());
	}
	
	function getExtension() {
	  if($this->isLoaded()) {
		return get_file_extension($this->getSource());
	  } else {
		return image_type_to_extension($this->getImageType());
	  }
	}
	
	function resize($width, $height, $mutate = true) {
	  if(!is_resource($this->resource) || (get_resource_type($this->resource) <> 'gd')) return false;
	  
	  $width = (integer) $width > 0 ? (integer) $width : 1;
	  $height = (integer) $height > 0 ? (integer) $height : 1;
	  
	  if($this->getImageType() == IMAGETYPE_GIF) {
		$new_resource = imagecreate($width, $height);
	  } else {
		$new_resource = imagecreatetruecolor($width, $height);
	  }
	  
	  imagecopyresampled($new_resource, $this->resource, 0, 0, 0, 0, $width, $height, $this->getWidth(), $this->getHeight());
	  if($mutate) {
		imagedestroy($this->resource); 
		$this->resource = $new_resource;
		$this->width = $width;
		$this->height = $height;
		return true;
	  } else {
		$new_image = new SimpleGdImage();
		$new_image->createFromResource($new_resource);
		return $new_image;
	  }
	} 
	
	function scale($width, $height, $boundary = null, $mutate = true) {
	  if(!is_resource($this->resource) || (get_resource_type($this->resource) <> 'gd')) return false;
	  
	  $width = (integer) $width > 0 ? (integer) $width : 1;
	  $height = (integer) $height > 0 ? (integer) $height : 1;
	  
	  $scale = min($width / $this->getWidth(), $height / $this->getHeight());
	  
	  if($boundary == self::BOUNDARY_DECREASE_ONLY) {
		if($scale > 1) {
		  if($mutate) {
			return;
		  } else {
			$new_image = new SimpleGdImage();
			$new_image->createFromResource($this->resource);
			return $new_image;
		  }
		}
	  } elseif($boundary == self::BOUNDARY_INCREASE_ONLY) {
		if($scale < 1) {
		  if($mutate) {
			return;
		  } else {
			$new_image = new SimpleGdImage();
			$new_image->createFromResource($this->resource);
			return $new_image;
		  }
		}
	  }
	  
	  $new_width = floor($scale * $this->getWidth());
	  $new_height = floor($scale * $this->getHeight());
	  
	  if($this->getImageType() == IMAGETYPE_GIF) {
		$new_resource = imagecreate($new_width, $new_height);
	  } else {
		$new_resource = imagecreatetruecolor($new_width, $new_height);
	  }
	  
	  imagecopyresampled($new_resource, $this->resource, 0, 0, 0, 0, $new_width, $new_height, $this->getWidth(), $this->getHeight());
	  if($mutate) {
		imagedestroy($this->resource); 
		$this->resource = $new_resource;
		$this->width = $new_width;
		$this->height = $new_height;
		return true;
	  } else {
		$new_image = new SimpleGdImage();
		$new_image->createFromResource($new_resource);
		return $new_image;
	  }
	  
	}
	
	function convertType($to_type) {
	  if($this->getImageType() == $to_type) return;
	  if($to_type == IMAGETYPE_PNG || $to_type == IMAGETYPE_JPEG || $to_type == IMAGETYPE_GIF) {
		$this->setImageType($to_type);
	  } else {
		throw new Exception("Image Type not supported.");
	  }
	}
	
	function isNew() {
	  return $this->is_new;
	}
	function isLoaded() {
	  return $this->is_loaded;
	} 
	
	function setIsNew() {
	  $this->is_new = true;
	  $this->is_loaded = false;
	}
	
	function setIsLoaded() {
	  $this->is_new = false;
	  $this->is_loaded = true;
	}
	
	function getSource() {
	  return $this->source;
	}
	
	private function setSource($value) {
	  $this->source = $value;
	}
	
	function getImageType() {
	  return $this->image_type;
	}
	
	private function setImageType($value) {
	  $this->image_type = $value;
	}
	
	function getWidth() {
	  if(is_null($this->width)) $this->width = imagesx($this->resource);
	  return $this->width;
	} 
	
	protected function setWidth($value) {
	  $this->width = $value;
	}
	
	function getHeight() {
	  if(is_null($this->height)) $this->height = imagesy($this->resource);
	  return $this->height;
	}
	
	protected function setHeight($value) {
	  $this->height = $value;
	}

}
