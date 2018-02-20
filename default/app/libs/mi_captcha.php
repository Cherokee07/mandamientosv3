<?php
class MiCaptcha{
	public function texto() {
		$length = rand(6,8);
		$consonantes = "-abcdefghijlmnopqrstvwyz0987654321ABCDEFGHIJKLMNOPQRSTUVWXYZ_";
		$vocales = "aeiouAEIOU?";
		$text = "";
		$vocal = rand(0,1);
		for ($i=0; $i<$length; $i++) {
			if ($vocal) {
				$text .= substr($vocales, mt_rand(0, strlen($vocales)), 1);
			} else {
			$text .= substr($consonantes, mt_rand(0, strlen($consonantes)), 1);
			}
		$vocal = !$vocal;
		}
		return $text;
	}
// Define el ancho del texto usando la funcion creada anteriormente.
	public function generar(){
		//ob_end_clean();
		$texto = $this->texto();
		Session::set('mj_rtr',sha1($texto.'manjud'));

		// Crea una imagen gif en memoria.
		$captcha = imagecreatefromgif(str_replace("//","/",$_SERVER['DOCUMENT_ROOT'].PUBLIC_PATH."default/public/img/default/imagen.gif"));
	
		// La localizacion de la imagen.
		$letras = imagecolorallocate($captcha,120,0,0);

		// Unir el texto en la imagen gif creada.
		imagestring($captcha,5,16,7,$texto,$letras);

		// Pone la imagen en cabezera.
		header("Content-type: image/gif");

		// Muestra la imagen.
		imagegif($captcha);
	}
	
	public function isValid(){
		if (Input::hasPost('captcha')){
			return (Session::get('mj_rtr')==sha1(Input::post('captcha').'manjud'));
		}
		return false;
	}
}
?>