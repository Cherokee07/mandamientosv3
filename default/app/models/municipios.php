<?php 
class Municipios extends ActiveRecord
{
	protected $database = 'catalogos';

  public function initialize(){
  $this->has_many('ordenes');
	$this->has_many('localidades');
	$this->belongs_to('distritos');
  }  
  

  public function listar($distritos_id){
    return $this->find("distritos_id=$distritos_id","order: 2 ASC");	
    }
  
}
?>
