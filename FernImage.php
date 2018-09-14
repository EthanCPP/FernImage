<?php
/*
- FernImage - Image Manipulation Script
- Created by Ethan Webster
*/

class FernImage {
	private $im_image, $im_width, $im_height, $im_type;
	private $im_layers = array();
	
	/*
	- Image Manipulation Begin
	- Loads the image into memory or allocates memory for it
	- If $path is left blank then memory will be allocated for it using the
	  dimensions provided.
	
	$path     - Optional, Where the background image is stored relative to this
	            script's location.
	$width    - Automatically set if left blank and path is provided, width of the
	            image.
	$height   - Automatically set if left blank and path is provided, height of the
	            image.
	*/
	
	public function __construct($path = '', $width = 0, $height = 0) {
		if ($path != '') {
			if (is_resource($path)) {
				$this->im_image = $path;
				$this->im_width = imagesx($path);
				$this->im_height = imagesy($path);
				$this->im_type = exif_imagetype($path);
			} else {
				list($w, $h, $type) = getimagesize($path);
				
				if ($width == 0)
					$this->im_width = $w;
				else
					$this->im_width = $width;
				
				if ($height == 0)
					$this->im_height = $h;
				else
					$this->im_height = $height;
				
				$this->im_type = $type;
				
				if ($type == IMG_JPG)
					$this->im_image = imagecreatefromjpeg($path);
				elseif ($type == IMG_GIF)
					$this->im_image = imagecreatefromgif($path);
				elseif ($type == IMG_PNG || $type == 3)
					$this->im_image = imagecreatefrompng($path);
			}
		} else {
			$this->im_image = imagecreatetruecolor($width, $height);
			imagealphablending($this->im_image, false);
			imagesavealpha($this->im_image, true);
			$col = imagecolorallocatealpha($this->im_image, 255, 255, 255, 127);
			imagefill($this->im_image, 0, 0, $col);
			
			$this->im_type = IMG_PNG;
		}
		
		if ($this->im_type == IMG_PNG || $this->im_type == 3)
			header('Content-type: image/png');
		elseif ($this->im_type == IMG_JPG)
			header('Content-type: image/jpeg');
		elseif ($this->im_type == IMG_GIF)
			header('Content-type: image/gif');
	}
	
	/*
	- Image Manipulation Crop Image
	- Crops the current image stack assuming all layers are already collapsed
	
	$x      - Pixels from the left to begin the crop
	$y      - Pixels from the top to begin the crop
	$width  - Width of the crop
	$height - Height of the crop
	*/
	
	public function crop($x = 0, $y = 0, $width = 0, $height = 0) {
		$temp = imagecreatetruecolor($width, $height);
		imagealphablending($temp, false);
		imagesavealpha($temp, true);
		$col = imagecolorallocatealpha($temp, 255, 255, 255, 127);
		imagefill($temp, 0, 0, $col);
		
		imagecopy($temp, $this->im_image, 0, 0, $x, $y, $width, $height);
		$this->im_image = $temp;
	}
	
	/*
	- Image Manipulation Add Layer
	- Adds another layer to the image stack
	
	$path     - Location of the image to add as the new layer relative to this script's
	            location, can be left blank for an empty layer.
	$x        - Number of pixels to the left to place the new layer.
	$y        - Number of pixels from the top to place the new layer.
	$width    - Width of the new layer, automatic if left blank.
	$height   - Height of the new layer, automatic if left blank.
	*/
	
	public function addLayer($path = '', $x = 0, $y = 0, $width = 0, $height = 0) {
		$temp;
		
		if ($path != '') {
			if (is_resource($path)) {
				$temp = $path;
				$width = imagesx($path);
				$height = imagesy($path);
			} else {
				list ($w, $h, $type, $attr) = getimagesize($path);
				
				if ($width == 0)
					$width = $w;
				
				if ($height == 0)
					$height = $h;
				
				if ($type == IMG_PNG || $type == 3)
					$temp = imagecreatefrompng($path);
				elseif ($type == IMG_JPG)
					$temp = imagecreatefromjpeg($path);
				elseif ($type == IMG_GIF)
					$temp = imagecreatefromgif($path);
			}
		} else {
			$temp = imagecreatetruecolor($width, $height);
			imagealphablending($temp, false);
			imagesavealpha($temp, true);
			$col = imagecolorallocatealpha($temp, 255, 255, 255, 127);
			imagefill($temp, 0, 0, $col);
		}
		
		array_push($this->im_layers, array($temp, $x, $y, $width, $height));
	}
	
	/*
	- Image Manipulation Get Image
	- Fetches the current image stack
	*/
	
	public function getImage() {
		return $this->im_image;
	}
	
	/*
	- Image Manipulation Collapse All Layers
	- Collapses all layers into a single image
	*/
	
	public function collapse() {
		if (count($this->im_layers) > 0) {
			foreach ($this->im_layers as $layer) {
				imagealphablending($layer[0], true);
				imagealphablending($this->im_image, true);
				imagesavealpha($layer[0], true);
				imagesavealpha($this->im_image, true);
				imagecopy($this->im_image, $layer[0], $layer[1], $layer[2], 0, 0, $layer[3], $layer[4]);
			}
		}
	}
	
	/*
	- Image Manipulation Render the Image
	- Finally renders the image so that it can be drawn with HTML's <img> tag
	*/
	
	public function draw() {
		if ($this->im_type == IMG_PNG || $this->im_type == 3)
			imagepng($this->im_image);
		elseif ($this->im_type == IMG_JPG)
			imagejpeg($this->im_image);
		elseif ($this->im_type == IMG_GIF)
			imagegif($this->im_image);
	}	
};
