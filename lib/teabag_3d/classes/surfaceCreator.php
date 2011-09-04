<?php

interface SurfaceCreator{

}


//class FlatSurfaceCreator extends SurfaceCreator{
class GenericSurfaceCreator {

	protected $model;
	protected $peak;

	public function GenericSurfaceCreator($imageMask){
		$this->createSurfare($imageMask);

	}

	protected function createSurfare($imageMask){
		/*  creating primary 3D object */
		// there will be two arrays - one with points and the second with links between them
		$this->model = new Model();

		//attention! This is important parameter, This defines how high will be the word on a plate
		//@TODO define a way to operate this magic number
		$this->peak = 4;

		$net_step = 3;

		//size of 3d object, I mean how much points for each dimention
		$dim_x = intval( $imageMask->getIw()/ $net_step );
		$dim_y = intval( $imageMask->getIh() / $net_step );

		//and now - scanning the b-w mask
		for ($i=0; $i<$dim_x; $i++)
		for ($j=0; $j<$dim_y; $j++){

			//dots:
			$this->model->dots[$dim_y * $i + $j]['x']=$i * $net_step;
			$this->model->dots[$dim_y * $i + $j]['y']=$j * $net_step;
			//I decided to add $rand parameter to make net not well-structured. This must help against regognizers.
			//it may be big if picture big enough

			//shifting up the "black" dots
			$this->model->dots[$dim_y * $i + $j]['z'] = $this->getZ($i * $net_step, $j * $net_step,
			imagecolorat($imageMask->getImage(), $i * $net_step, $imageMask->getIh() - $j * $net_step - 1) == 0);

		}

		// ¬≈–“» ¿À‹Õ€≈ À»Õ»»
		for ($i=0; $i<$dim_x; $i++)
		{

			$first = 0;
			for ($j=1; $j<$dim_y; $j++)
			{
				if ( $this->model->dots[$dim_y * $i + $j]['z'] != $this->model->dots[$dim_y * $i + $first]['z'] )
				{
					$this->model->links[$dim_y * $i + $first][] = $dim_y * $i + $j - 1;
					$first = $j - 1;
				}
			}
			$this->model->links[$dim_y * $i + $first][] = $dim_y * $i + $dim_y - 1;
		}

		// √Œ–»«ŒÕ“¿À‹Õ€≈ À»Õ»»
		for ($j=0; $j<$dim_y; $j++)
		{
			$first = 0;
			for ($i=1; $i<$dim_x; $i++)
			{
				if ( $this->model->dots[$dim_y * $i + $j]['z'] != $this->model->dots[$dim_y * $first + $j]['z'] )
				{
					$this->model->links[$dim_y * $first + $j][] = $dim_y * ($i - 1) + $j;
					$first = $i - 1;
				}
			}
			$this->model->links[$dim_y * $first + $j][] = $dim_y * ($dim_x - 1) + $j;
		}
	}

	protected function getZ($x, $y, $pinch){
		if ( $pinch){
			return $this->peak;
		}
		else{
			return 0;
		}
	}

	public function getModel(){
		return $this->model;
	}
}

class FlatSurfaceCreator extends GenericSurfaceCreator{






}

class SimpleWaveSurfaceCreator extends GenericSurfaceCreator{

	private $shiftX;
	private $shiftY;


	public function SimpleWaveSurfaceCreator($imageMask){
		$this->shiftX = rand(0, 100);
		$this->shiftY = rand(0, 100);
		$this->createSurfare($imageMask);
	}

	protected function getZ($x, $y, $pinch){
		$x +=$this->shiftX;
		$y +=$this->shiftY;
		
		if ( $pinch){
			return (sin($x/50)*cos($y/48)*7+cos($x/15+$y/15)+sin($y/17+$y/15))*$this->peak/2+$this->peak;
		}
		else{
			return (sin($x/50)*cos($y/48)*7+cos($x/15+$y/15)+sin($y/17+$y/15))*$this->peak/2;
		}
	}
}


class PoppedUpLettersWaveSurfaceCreator extends SimpleWaveSurfaceCreator{

	
	protected function getZ($x, $y, $pinch){
	
		$addPinch = $this->peak;
		$add = - $addPinch * (150 / ($y+70));
		
		$x +=$this->shiftX;
		$y +=$this->shiftY;
		
		if ( $pinch){
			return (sin($x/50)*cos($y/48)*4 + cos($x/15+$y/15)+sin($y/17+$y/15))-$add;
		}
		else{
			return (sin($x/50)*cos($y/48)*4 + cos($x/15+$y/15)+sin($y/17+$y/15))*$this->peak/2;
		}
	}
}

class PoppedDownLettersWaveSurfaceCreator extends SimpleWaveSurfaceCreator{

	
	protected function getZ($x, $y, $pinch){
	
		$addPinch = $this->peak;
		$add = $addPinch * ($y / 100);
		
		$x +=$this->shiftX;
		$y +=$this->shiftY;
		
		if ( $pinch){
			return (sin($x/40)*cos($y/68)*3+ cos($x/14+$y/16)+sin($y/18+$y/13))*$this->peak+$add;
		}
		else{
			return (sin($x/50)*cos($y/48)*3 + cos($x/15+$y/15)+sin($y/17+$y/15))*$this->peak/2;
		}
	}
}

class PoppedVaryLettersWaveSurfaceCreator extends SimpleWaveSurfaceCreator{

	
	protected function getZ($x, $y, $pinch){
	
		$x +=$this->shiftX;
		$y +=$this->shiftY;
		
		if ( $pinch){
			return (sin($x/20)*sin($x/50)*cos($y/48)*2 + cos($x/15+$y/15)+sin($y/17+$y/15)) > $this->peak*0.9 ? 0 : $this->peak;
		}
		else{
			return (sin($x/20)*sin($x/50)*cos($y/48)*2 + cos($x/15+$y/15)+sin($y/17+$y/15))*$this->peak/2;
		}
	}
}

class StandardShapeSurfaceCreator extends GenericSurfaceCreator {

	private $imageMask;

	public function StandardShapeSurfaceCreator($imageMask){
		$this->imageMask = $imageMask;
		$this->createSurfare($imageMask);

	}

	protected function getZ($x, $y, $pinch){
		if ( $pinch){
			return $this->Arc($x, $y)+$this->peak;
		}
		else{
			return $this->Arc($x, $y);
		}
	}

	protected function Arc($x, $y){
		/*return -sin(pi()/2-pi()*$x/($this->imageMask->getIw())+
					pi()/2-pi()*$y/($this->imageMask->getIh())
		            )*$this->peak*3;*/
		$dcX = abs($x - $this->imageMask->getIw()/2);
		$dcY = abs($y - $this->imageMask->getIh()/2);
		$cz = 500;
		return $cz - sqrt(1000*1000 - $dcX*$dcX - $dcY*$dcY);
	}
}







/*class ComplexWaveSurfaceCreator extends GenericSurfaceCreator{


protected function Oscillator($x, $y){
$params = array(0=> array('freq'=>0.03, 'amp'=>2),
1=> array('freq'=>0.26, 'amp'=>0.7),
2=> array('freq'=>0.7, 'amp'=>1.5),
3=> array('freq'=>0.01	, 'amp'=>0.3),
4=> array('freq'=>0.01, 'amp'=>0.2),
5=> array('freq'=>0.99, 'amp'=>1),
);

$result=0;

foreach ($params as $k=>$p){
if ($k%2){
$result+=sin($x*$p['freq'])*$p['amp'];
}
else{
$result+=cos($y*$p['freq'])*$p['amp'];
}
}
return $result;
}
protected function getZ($x, $y, $pinch){

if ( $pinch){
return $this->Oscillator($x, $y)+$this->peak;
}
else{
return $this->Oscillator($x, $y);
}
}
}*/


?>