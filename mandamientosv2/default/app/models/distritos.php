<?php 
class Distritos extends ActiveRecord
{
	protected $database = 'catalogos';//base de datos
 
  public function initialize(){
    $this->has_many('ordenes');
	$this->has_many('municipios');
	$this->belongs_to('regiones');
  }  
   
  
  
  public function listar(){
    $sql = "SELECT id, distrito from distritos where id<31";
    $sql .= " ORDER by 1 asc, 2 asc";
    return $this->find_all_by_sql($sql);
   
  } 
 
   
}
?>
