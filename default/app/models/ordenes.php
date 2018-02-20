<?php 
class Ordenes extends ActiveRecord
{
 
  public function initialize(){
	$this->has_many('indiciados');
	$this->has_many('ofendidos');
	$this->has_many('nomindis');
	$this->has_many('nomofens');
	$this->has_many('ofeindis');
	$this->belongs_to('juzgados');
	$this->belongs_to('localidades');
	$this->belongs_to('agencias');
	$this->belongs_to('tiposoa');
  }  

}
?>