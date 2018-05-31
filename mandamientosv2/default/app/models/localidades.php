<?php 
class Localidades extends ActiveRecord
{
	protected $database = 'catalogos';
  
  public function initialize(){
    $this->has_many('ordenes');
	$this->belongs_to('municipios');
  }  
  public function listar($municipios_id){
    return $this->find("municipios_id=$municipios_id","order: 4 ASC");
    }
}
?>
