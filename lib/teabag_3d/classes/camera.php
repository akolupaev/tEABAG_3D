<?php

class Camera {
	private $position;
	private $target;
	private $projection;
	private $zoom = 1.4;

	private $fgColor;
	private $bgColor;
	private $savePath;
	

	/**
	 * @param unknown_type $fgColor
	 */
	public function setFgColor ($fgColor) { $this->fgColor = $fgColor; }

	/**
	 * @param unknown_type $bgColor
	 */
	public function setBgColor ($bgColor) { $this->bgColor = $bgColor; }

	/**
	 * @param unknown_type $zoom
	 */
	public function setZoom ($zoom) { 
	$this->zoom = $zoom; 
	}
	public function setSavePath($path){
		 $this->savePath = $path;
	}

	public function setPosition($position){
		$this->position = $position;

	}

	public function setRandPosition($model){
		//-1 or 1 50/50
		$i = (rand(0,10)>5) ? 1 : -1;
		//good position is not at center and not far away
		$this->position->x =   $model->getIw()/2+ $i*$model->getIw()/2 + $i*rand(-30, 50); 
		$this->position->y = - 80 + rand(-30, 50);
		$this->position->z =  120 + rand(0, 50);

	}

	public function setTarget($target){
		$this->target = $target;

	}

	public function setTargetToCenter($mask){
	 $this->target->x = $mask->getIw()/2;
	 $this->target->z = 0;
	 $this->target->y = $mask->getIh()/2;
	}

	public function getProjection(){
		return $this->projection;
	}

	public function Camera ($target){
		$this->position = new Point(-1500, -2700, 2500);
		//$this->position = $position;
		$this->target = $target;
		
	}

	public function makeProjection($model){

		//print_r($model->dots);

		$render = array();

		$xt_ = $this->target->x - $this->position->x;
		$yt_ = $this->target->y - $this->position->y;
		$zt_ = $this->target->z - $this->position->z;
		$a = atan2($yt_, $xt_);
		$b = atan2($zt_, sqrt($xt_*$xt_ + $yt_*$yt_));

		$sa = sin(-$a);
		$ca = cos(-$a);
		$sb = sin(-$b);
		$cb = cos(-$b);

		$zoom = $this->zoom;

		
		// CALCULUS OF COEFFICIENT'S OF PARALLEL PROJECTION
		$a1 = - $sa * $zoom;
		$b1 = - $ca * $zoom;
		$c1 = 0;
		$d1 = ($sa * $this->position->x + $ca * $this->position->y) * $zoom;
		$a2 = + $ca * $sb * $zoom;
		$b2 = - $sa * $sb * $zoom;
		$c2 = + $cb * $zoom;
		$d2 = + (- $ca * $sb * $this->position->x +
		$sa * $sb * $this->position->y -
		$cb * $this->position->z) * $zoom;;

		
		// (<X, Y, Z>)  ->(<X, Y>).
		foreach ($model->dots as $n=>$dot){
			$render[$n]['x'] = $a1 * $dot['x'] + $b1 * $dot['y'] + $c1 * $dot['z'] + $d1;
			$render[$n]['y'] = $a2 * $dot['x'] + $b2 * $dot['y'] + $c2 * $dot['z'] + $d2;
		}

		$this->projection = $render;
	}

	public function makePicture($iw, $ih, $model, $method='file'){
		$image = imagecreatetruecolor ($iw, $ih);

		//background color.
		$bgColor = imagecolorallocate($image, $this->bgColor->r, $this->bgColor->g, $this->bgColor->b);
		imagefill ($image, 10 , 10, $bgColor );

		//antialiased lines are on now:
		imageantialias($image, true);

		/* render */
		if ($this->fgColor === '')
			$this->fgColor = imagecolorclosest ( $image, rand(0,120), rand(0,120), rand(0,120));
		else	
			$this->fgColor = imagecolorclosest ( $image, $this->fgColor->r, $this->fgColor->g, $this->fgColor->b);

		$i=0;
		$dx = $iw / 2;
		$dy = $ih / 2;

		foreach ($model->links as $from => $list)
		foreach ($list as $to)
		{
			$i++;

			imageline ($image,
			$dx + $this->projection[$from]['x'] ,
			$dy - $this->projection[$from]['y'],

			$dx + $this->projection[$to]['x'],
			$dy - $this->projection[$to]['y'],

			$this->fgColor);
		}
		
		
		//$method = 'file';

		if ($method == 'file') {
			$name = uniqid('tEABAG_3D_');
			imagepng  ($image, $this->savePath.$name.'.png');
			return $name.'.png';
		}
		elseif ($method == 'raw'){
			return $image;
		}
		elseif($method == 'stream'){
			// always modified
			header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");

			// HTTP/1.1
			header("Cache-Control: no-store, no-cache, must-revalidate");
			header("Cache-Control: post-check=0, pre-check=0", false);

			// HTTP/1.0
			header("Pragma: no-cache");

			header("Content-type: image/png");
			imagepng($image);
			return;
		}
	}
}


?>