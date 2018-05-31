<?php
/**
 * Esta clase permite extender o modificar la clase ViewBase de Kumbiaphp.
 *
 * @category KumbiaPHP
 * @package View
 **/

// @see KumbiaView
require_once CORE_PATH . 'kumbia/kumbia_view.php';

class View extends KumbiaView {
	public static function fecDef($campo){
		$aux = Form::getFieldValue($campo);
		if (@$aux  && (strtotime($aux)>0)){
			$temp = explode('-',$aux);
			return $temp[2].'-'.$temp[1].'-'.$temp[0]; 
			
		}
		return date('d-m-Y');
	}
	
	public static function fecHumana($valor=NULL){
		if (@$valor && (strtotime(@$valor)>0)){
			if (substr($valor,4,1)=='-'){
				$temp = explode('-',$valor);
				return $temp[2].'-'.$temp[1].'-'.$temp[0];
			}
			return $valor;			
		}
		return NULL;
	}	
}
