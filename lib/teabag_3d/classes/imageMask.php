<?php

class MaskImage{

	private $iw;
	private $ih;
	private $image;
	
	private $fontSize = 70;//you can play with this
	
	private $wHalfBorder = 30;
	private $hHalfBorder = 30;
	private $letterSpacing = 5;//pixels

	public function MaskImage($code, $font){
		$maskcolor= 0; //black
		
		//60 - is a magic fontsize, 0 - means no rotation
		$bbox=imagettfbbox ($this->fontSize, 0, $font, $code);

		$this->image = imagecreatetruecolor ($bbox[2]+2*$this->hHalfBorder, -$bbox[5]+2*$this->wHalfBorder);
		
		//bg is white....
		imagefill ($this->image, 10 , 10, 16777215 );
		
		//write it down per letter
		$lastLeft = $this->hHalfBorder;
		$l = strlen($code);
		for ($i = 0 ; $i<$l; $i++){
			$let = $code[$i];
			$rndSize = $this->fontSize-rand(0, 10);
			imagettftext ($this->image, $rndSize, 0, $lastLeft, -$bbox[5]+$this->wHalfBorder*rand(0,150)/100, 0, $font, $let);
			$nbbox=imagettfbbox ($rndSize, 0, $font, $let);
			$lastLeft += $nbbox[2]+$this->letterSpacing;
		}	

		//borders arounf the image
		$this->iw = $bbox[2]+2*$this->hHalfBorder;
		$this->ih = -$bbox[5]+2*$this->wHalfBorder;
		
	}
	
	public function getIw(){
		return $this->iw;
	}
	
	public function getIh(){
		return $this->ih;
	}

	public function getImage(){
		return $this->image;
	}
}



?>