<?php
namespace Asgard\Files;

class File {
	protected $src;
	protected $name;

	public function __construct($src=null, $name=null) {
		$this->setSrc($src);
		$this->name = $name;
	}

	public function setSrc($src) {
		$this->src = realpath($src);
	}

	public function setName($name) {
		$this->name = $name;
	}

	public function getName() {
		if($this->name)
			return $this->name;
		else
			return basename($this->src);
	}

	public function isUploaded() {
		return is_uploaded_file($this->src);
	}

	public function size() {
		return filesize($this->src);
	}

	public function type() {
		$finfo = finfo_open(FILEINFO_MIME_TYPE);
		return finfo_file($finfo, $this->src);
	}

	public function extension() {
		if(!$this->getName())
			return;
		return pathinfo($this->getName(), PATHINFO_EXTENSION);
	}

	public function exists() {
		return file_exists($this->src);
	}

	public function src() {
		return $this->src;
	}

	public function relativeTo($path) {
		return \Asgard\Common\FileManager::relativeTo($this->src, $path);
	}

	protected function formatPath($path) {
		return preg_replace('/\/|\\\/', DIRECTORY_SEPARATOR, realpath($path));
	}

	public function moveToDir($dir, $rename=true) {
		if($this->isIn($dir))
			return;
		return $this->move($dir.'/'.$this->getName(), $rename);
	}

	public function isIn($dir) {
		if(!$this->formatPath($dir))
			return false;
		return strpos($this->formatPath($this->src()), $this->formatPath($dir)) === 0;
	}

	public function isAt($at) {
		return $this->formatPath($at) === $this->src;
	}

	public function move($dst, $rename=true) {
		if(!$this->src || $this->isAt($dst)) return;
		$filename = \Asgard\Common\FileManager::move($this->src, $dst, $rename);
		if(!$filename)
			return false;
		$this->src = realpath(dirname($dst).'/'.$filename);
		return $dst;
	}

	public function delete() {
		if($r = \Asgard\Common\FileManager::unlink($this->src))
			$this->src = null;
		return $r;
	}

	public function copy($dst, $rename=true) {
		$dst = \Asgard\Common\FileManager::copy($this->src, $dst, $rename);
		if($dst) {
			$copy = clone $this;
			$copy->setSrc($dst);
			return $copy;
		}
		return false;
	}
}