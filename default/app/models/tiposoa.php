<?php 
class Tiposoa extends ActiveRecord
{
	protected $database = 'catalogos';

  public function initialize(){
    $this->has_many('ordenes');
  }  
  
   public function listar(){
    return $this->find();
  } 

}
?>
