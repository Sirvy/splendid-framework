<?php

namespace Splendid;

/**
 * Image
 *
 * @author		Bobby Tran <bobby-tran@email.cz>
 * @copyright	Copyright (c) 2017, Bobby Tran
 */
class Image
{
	/**
	 * Filename 
	 * @var string 
	 */
	private $name;
	
	/** 
	 * File type
	 * @var string 
	 */
	private $type;
	
	/** 
	 * Temporary filename
	 * @var string 
	 */
	private $tmp_name;
	
	/** 
	 * File size
	 * @var int 
	 */
	private $size;
	
	/**
	 * Target directory path 
	 * @var string 
	 */
	private $target_dir;
	
	/** 
	 * Target file path
	 * @var string 
	 */
	private $target_file;

	/** 
	 * Allowed image extensions
	 * @var array 
	 */
	private $allowed_ext = array(
		'image/jpeg' => 'jpeg',
		'image/jpg' => 'jpg',
		'image/png' => 'png', 
		'image/bmp' => 'bmp', 
		'image/gif' => 'gif'
	);
	
	/** 
	 * Constructor
	 *
	 * @param src string source file
	 * @param target string target directory
	 */
	public function __construct($src, $target)
	{
		$this->name = $src["name"];
		$this->type = $src["type"];
		$this->tmp_name = $src["tmp_name"];
		$this->size = $src["size"];
		
		$this->target_dir = $target;
	}


	public function getTargetDir()
    {
        return $this->target_dir;
    }

	/*
	 * Checking the correct type of file
	 *
	 * @param arr defines array of allowed types or null if auto
	 * @return bool
	 */
	public function checkType($arr = null)
	{
		if ($arr === null) {
			$arr = $this->allowed_ext;
		}
		return in_array($this->type, $arr);
	}
	
	
	/*
	 * Checking valid image file
	 *
	 * @return bool
	 */
	public function checkImage()
	{
		return getimagesize($this->tmp_name);
	}
	
	
	/*
	 * Checking the file size
	 *
	 * @param max defines int of maximum size in kB
	 * @return bool
	 */
	public function checkSize($max)
	{
		return $this->size <= ($max/1024); //kB
	}
	
	
	/*
	 * Checking the file resolution
	 *
	 * @param maxWidth defines int of maximum width, skip if null
	 * @param maxHeight defines int of maximum height, skip if null
	 * @param minWidth defines int of minimum width, skip if null
	 * @param minHeight defines int of minimum height, skip if null
	 * @return bool
	 */
	public function checkResolution($maxWidth = null, $maxHeight = null, $minWidth = null, $minHeight = null)
	{
		list($w, $h) = getimagesize($this->tmp_name);
		if ($maxWidth !== null && $w > $maxWidth) {
			return false;
		}
		if ($maxHeight !== null && $h > $maxHeight) {
			return false;
		}
		if ($minWidth !== null && $w < $minWidth) {
			return false;
		}
		if ($minHeight !== null && $w < $minHeight) {
			return false;
		}
		return true;
	}

	
	/*
	 * Setting the name of target file
	 *
	 * @param name defines string of new filename
	 * @return string
	 */	
	public function setTargetName($name = null)
	{
		if ($name === null) {
			$name = $this->generateName();
		}
		$this->target_file = $this->target_dir . '/' . $name . '.' . $this->allowed_ext[$this->type];
		return $name . '.' . $this->allowed_ext[$this->type];
	}

	
	/*
	 * Generates filename
	 *
	 * @return string
	 */	
	private function generateName()
	{
		return md5(microtime());
	}
	
	
	/*
	 * Cropes, resizes and creates the image
	 *
	 * @param x1 start crop position X
	 * @param y1 start crop position Y
	 * @param x2 end crop position X
	 * @param y2 end crop position Y
	 * @param max defines the max image resolution
	 * @return string
	 */	
	public function applyCrop($x1, $y1, $x2, $y2, $max)
	{		
		try 
		{
			list($w, $h) = getimagesize($this->tmp_name);
			if ($w > $h) {
				$w_ratio = $w/500;
				$h_ratio = $w/500;
			} else {
				$w_ratio = $h/500;
				$h_ratio = $h/500;
			}
			
			$newWidth = ($x2 - $x1)*$w_ratio;
			$newHeight = ($y2 - $y1)*$h_ratio;
			
			$ratio = $newWidth / $newHeight;
			if ($ratio > 1) {
				$n_w = $max;
				$n_h = ($max/$ratio);
			} else {
				$n_w = ($max*$ratio);
				$n_h = $max;
			}
			
			$src = imagecreatefromstring(file_get_contents($this->tmp_name));
			$dst = imagecreatetruecolor($n_w, $n_h);
			
			imagecopyresampled($dst, $src, 0, 0, $x1*$w_ratio, $y1*$h_ratio, $n_w, $n_h, $newWidth, $newHeight);
			imagedestroy($src);	
			
			switch($this->type) {
				case 'image/jpeg':
					imagejpeg($dst, $this->target_file);
					break;
				case 'image/jpg':
					imagejpeg($dst, $this->target_file);
					break;
				case 'image/png':
					imagepng($dst, $this->target_file);
					break;
				case 'image/bmp':
					imagewbmp($dst, $this->target_file);
					break;
				case 'image/gif':
					imagegif ($dst, $this->target_file);
					break;
				default:
					return false;
			}
	
			imagedestroy($dst);
			
			return true;
			
		} catch(Exception $e) {
			return false;
		}
	}
	
	
	/*
	 * Uploads file
	 *
	 * @param force is true to keep generating new filename
	 * @return bool
	 */
	public function upload($force = true)
	{
		if ($force) {
			while(file_exists($this->target_file)) {
				$this->setTargetName();
			}
		}
		return move_uploaded_file($this->tmp_name, $this->target_file);
	}
	
	
	/*
	 * Returns the uploaded filename
	 *
	 * @return string
	 */
	public function getUploadedFile()
	{
		return $this->target_file;
	}
	
}