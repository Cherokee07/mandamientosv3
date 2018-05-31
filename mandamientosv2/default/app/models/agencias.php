<?php 
class Agencias extends ActiveRecord
{
	protected $database = 'catalogos';
  
  public function initialize(){
    $this->has_many('ordenes');
  }  
  
  
 
  
  public function listar(){
     $sql = "SELECT id, concat(codagen,'.- ',agencia)  as agencia FROM agencias";
    return $this->find_all_by_sql($sql);
  } 
 
  


}
?>
