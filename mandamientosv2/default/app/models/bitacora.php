<?php 
class Bitacora extends ActiveRecord
{
  /**
  *Retorna un array de objetos cuyos campos son los mismos de la tabla Marca
  *$page : es el número o indice de la página
  *$$ppage : es el número de filas o registro de la página
  **/
  public function initialize(){
   }  
  
  public function listar(){
    return $this->find();
  }
  
  function fecEquipo($valor){
	if (substr($valor,2,1)=='-'){			
		$aux = explode('-',$valor);
		return $aux[2].'-'.$aux[1].'-'.$aux[0];
	}
	return $valor;
  }
  
  public function obtenRegistros($usuario=0,$fecini=0,$fecfin=0){
	$sql = "SELECT b.id,b.movimiento,DATE_FORMAT(b.fecha,'%d/%m/%Y') as fecha,b.hora,b.descripcion, b.oficio,DATE_FORMAT(b.fechaof,'%d/%m/%Y') as fechaof,b.solicita,";
	$sql .= "b.ip, s.subprocuraduria, CONCAT(ifnull(u.nombre,''),' ',ifnull(u.paterno,''),' ',ifnull(u.materno,'')) as usuario FROM ".$this->mandamientos.".bitacora b ";
	$sql .= "INNER JOIN ".$this->catalogos.".usuarios u ON u.id = b.usuarios_id".(($usuario==0)?" ":" AND u.id = ".$usuario." ");
	$sql .= "INNER JOIN ".$this->catalogos.".subprocuradurias s ON s.id = b.subprocuradurias_id ";
	$sql .= "WHERE b.fecha BETWEEN '".$this->fecEquipo($fecini)."' AND '".$this->fecEquipo($fecfin)."'";
	return $this->find_all_by_sql($sql);
  }
  
  public function obtenUna($exppenal=0,$anipenal=0,$juzgados_id=0,$tiposoa_id=0){
	$res = Load::model('tiposoa')->find(Input::post('tiposoa_id'));
	$sql = "SELECT b.id,b.movimiento,DATE_FORMAT(b.fecha,'%d/%m/%Y') as fecha,b.hora,b.descripcion, b.oficio,DATE_FORMAT(b.fechaof,'%d/%m/%Y') as fechaof,b.solicita,";
	$sql .= "b.ip, s.subprocuraduria, CONCAT(ifnull(u.nombre,''),' ',ifnull(u.paterno,''),' ',ifnull(u.materno,'')) as usuario FROM ".$this->mandamientos.".bitacora b ";
	$sql .= "INNER JOIN ".$this->catalogos.".usuarios u ON u.id = b.usuarios_id ";
	$sql .= "INNER JOIN ".$this->catalogos.".subprocuradurias s ON s.id = b.subprocuradurias_id ";
	$sql .= "WHERE b.descripcion LIKE '% ".$exppenal."/".$anipenal."/".$juzgados_id."/".$res->clave."%' ";
	$sql .= "OR b.orden IN (SELECT id FROM ".$this->mandamientos.".ordenes WHERE exppenal=$exppenal AND anipenal=$anipenal AND juzgados_id = $juzgados_id AND tiposoa_id = $tiposoa_id)";
	return $this->find_all_by_sql($sql);
  }
  
	public $before_save="aux_before_save";
	public function aux_before_save(){ 		
		if (@Session::get('mj_oficio')){
			$this->oficio = @Session::get('mj_oficio');			
			$this->fechaof = $this->fecEquipo(@Session::get('mj_fecha'));
			$this->solicita = @Session::get('mj_solicita');	
			$this->observaciones = @Session::get('mj_observa');	
		}
  }  
}
?>
