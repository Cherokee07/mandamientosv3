<?php
/**
 * Helpers
 * PHP version 5
 * LICENSE
 *
 * This source file is subject to the GNU/GPL that is bundled
 * with this package in the file docs/LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to fidel.oyarzo@gmail.com so we can send you a copy immediately.
 *
 * @author Fidel Oyarzo <fidel.oyarzo@gmail.com>
 */
require  APP_PATH.'libs/data_grid/arrays.php';
/**
 * Libreria para dimensionar imagenes
 */
require  APP_PATH.'libs/data_grid/images.php';

class dataGrid{
    /**
     * Almacena la versión actual del dataGrid
     *
     */	
    const DATAGRID_VERSION = '0.1 rc9';
    /**
    * cambia la hoja de estilo.
    * 
    * @var string
    */
    public $style = 'default';
	public $rutaimg = '';
	public $altoimg = 40;

    public $contador	=	array();
    /**
     * titulo del datagrid
    * @var string
    */
   public $title_caption		=	'';
   /**
    * Si desea que poder obtener un order by por campo
    * para esto debe recibir el field y el order
    * default = false
    * @var bool
    */
   public $order_fields	=	false;
   /**
    * Array de datos q se obtienen de un find
    * @var array
    */
   public $data_source	=	array();
   /**
    * url: url para la accion que efectua la paginacion,
    * por defecto "module/controller/page/" y se envia por parametro el numero de pagina. 
    * por defecto busca en el mismo controller y action
    * @var string
    */
   public $url		=	'';
   /**
    * show: número de paginas que se mostraran en el paginador, por defecto 10. 
    * @var integer
    */
   public $show		=	10;
   /**
    * Paginator a utilizar. por defecto usa el classic
    *   Classic -   Digg -   Extended -   Punbb -   Simple
    * @var unknown_type
    */
   public $paginator_name =	'default';
   /**
    *URL donde se guardaran registros
  */
   public $save	=	'';
   /**
    * Si existe un formulario para editar
    * el registro debe indicar el controller_name y action_name
    * por defecto envia el id
    * @var string
    */
   public $edit			=	'';
   /**
    * Si existe un metodo para eliminar
    * el registro debe indicar el controller_name y action_name
    * por defecto envia el id
    * @var string
    */
   public $delete			=	'';
   /**
    *crea una imagen con el link para ir al formulario create
  */
   public $create			=	'';
   /**
    *crea una imagen para el indice
  */
   public $index			=	'';   
   /**
    * NO FUNCIONAL 
    * @param $name
    public $auto_delete		=	false;
    
    /**
    * Crear un autofiltro
    *
    */
   public $auto_filter	=	false;
   
   public $btn_save	=	'Guardar';
   
   public $btn_caption	=	'Buscar';
    /**
     * Mensaje personalizado antes de elminar
     * alert javascript.
     * elimar el {campo}
     */
    public $delete_confirm	=	'';
    /**
     * 
     * Tipos de mimes para las opciones
     * img: action
     */
    public	$array_mimes	=	array();
    
     
    public $get_fields_and_headers	=	array();
    /**
     * Modelo actual
     * @var string
     */
    public $model		=	'';
    
    /**
     * campos que se van a mostrar de la base de datos
     * @var array
     */
    public $fields		=	array();
    /**
     * titulos o cabezeras que se mostra en la table <th></th>
     * @var array
     */
    public $headers		=	array();
    /**
     * usa paginador true or false
     * @var bool
     */
    public $use_paginator	=	false;
    /**
     * campo relacionado a mostrar para los campos _id
     * 
     * @var unknown_type
     */
    public $values			=	array();
    /**
     * Tipos de datos para los campos
     * array($id => $type);
     * @var unknown_type
     */
    public $type			=	array();
    public $params			=	array();
    /**
     * Array de campos para su valor a mostrar
    */
    public $array_alias		=	array();
    /**
     *Array para asignar clases a cada campo de la grilla
   */
    public $array_class		=	array();
    /**
     * Array para mostrar una imagen segun el valor del campo
   */
    public $array_image		=	array();
    /**
     *Array para generar los select del auto filtro
     **/
    public $array_select		=	array("0" => "Seleccione ...");
    /**
     *Se crea un chek al principio, esta experimental
   */
    public $set_check		=	FALSE;
    /**
     *Contador para nombrar nuevas columnas
   */
    public $cont_cols		=	0;
	/**
	*Columna para poner una ventana emergente
	**/
	public $rel = '';
    /**
     *calculos para las columnas de las grillas
   */
    public $sum_column  = array();
    public function __construct($model, $model_alias=FALSE) {
	$this->data_source = $model;
	$this->url = Router::get('controller') . '/' . Router::get('action');
	if($this->existData()){
	    if(array_key_exists('per_page', $this->data_source)){
		$this->use_paginator = true;
		$this->fields 	= array_combine($this->data_source->items[0]->fields, $this->data_source->items[0]->fields);
		if($model_alias){
		    $this->headers 	= array_combine($this->data_source->items[0]->fields, $this->data_source->items[0]->alias);
		}else{
		    $this->headers 	= array_combine($this->data_source->items[0]->fields, array_change_value_case($this->data_source->items[0]->fields));
		}
		$this->model 	= $this->data_source->items[0]->get_source();
	    }else{
		$this->fields 	= array_combine($this->data_source[0]->fields, $this->data_source[0]->fields);
		if($model_alias){
		    $this->headers 	= array_combine($this->data_source[0]->fields, $this->data_source[0]->alias);
		}else{
                    $this->headers 	= array_combine($this->data_source[0]->fields, array_change_value_case($this->data_source[0]->fields));
		}
		$this->model 	= $this->data_source[0]->get_source();
	    }
	}
    }
    public function existData(){
    static $i = 0;
	if(count($this->data_source)==1){
	    if(array_key_exists('count', $this->data_source)){
		if($this->data_source->count > 0 or $this->data_source->count ==''){
		    return true;
		}else{
		    if($i==0){
			Flash::error('No existen registros');
			$i++;
		    }
		    return false;
		}
	    }else{
		return true;
	    }
	}if(count($this->data_source)>0){
	    return true;
	}
	if($i==0){
	    Flash::error('No existen registros');
	    $i++;
	}
	return false;
    }
    /**
     * No visualiza un campo en la Tabla
     * @param $field
     * @return unknown_type
     */
    public function ignore($field){
	if(!$this->existData()) return false;
        if(array_key_exists($field, $this->fields)){
	    unset($this->fields[$field]);
	    unset($this->headers[$field]);
        } else {
            throw new KumbiaException("No se pudo ignorar, porque el campo: \"$field\" no existe.");
        }		
    }       
    /**
     *Cambia el valor a mostrar según la $field del array $data.
     */
    public function setAlias($field, $data=array()){
	if(!$this->existData()) return false;
        if(array_key_exists($field, $this->fields)){
            array_push_associative($this->array_alias, array($field => $data));
        } else {
            throw new KumbiaException("No se pudo asignar el Alias, porque el campo: \"$field\" no existe.");
	}
    }
    /**
     *Cambia el titulo a mostrar según la key del array $data.
     */    
    public function setCaption($field, $caption){
        if(!$this->existData()) return false;
	if(array_key_exists($field, $this->headers)){
	    $replace = array($field => $caption);
	    $this->headers = array_replace($this->headers, $replace);
        } else {
            throw new KumbiaException("No se pudo cambiar el nombre de la cabecera, porque el campo: \"$field\" no existe.");
	}
    }
    /**
     * asigna un campo a mostrar de la tabla _id
     * @param $campo_id
     * @param $campo
     */
    public function setValue($field_id, $field){
	if(!$this->existData()) return false;
        if(array_key_exists($field_id, $this->fields)){
            array_push_associative($this->values, array($field_id => $field));
        } else {
            throw new KumbiaException("No se pudo mostrar la asosiación, porque el campo: \"$field_id\" no existe.");
	}
    }
    /**
     * Campo de tipo imagen
     * @param $field
     * @return unknown_type
     */
    public function setTypeImage($field){
	if(!$this->existData()) return false;
        if(array_key_exists($field, $this->fields)){
	    unset($this->type[$field]);
	    array_push_associative($this->type, array($field => 'dtg_image'));
        } else {
            throw new KumbiaException("No se pudo mostrar la imagen, porque el campo: \"$field\" no existe.");
        }
    }  
    /**
     * Campo de tipo numerico
     * @param $field
     * @return unknown_type
     */
    public function setTypeNumeric($field){
	if(!$this->existData()) return false;
        if(array_key_exists($field, $this->fields)){
	    unset($this->type[$field]);
	    array_push_associative($this->type, array($field => 'dtg_numeric'));
        } else {
            throw new KumbiaException("No se pudo dar formato, porque el campo: \"$field\" no existe.");
        }
    }
    public function setTypeMoney($field){
	if(!$this->existData()) return false;
        if(array_key_exists($field, $this->fields)){
	    unset($this->type[$field]);
	    array_push_associative($this->type, array($field => 'dtg_money'));
        } else {
            throw new KumbiaException("No se pudo dar formato, porque el campo: \"$field\" no existe.");
        }	
    }
    public function setTypePercent($field){
	if(!$this->existData()) return false;
        if(array_key_exists($field, $this->fields)){
	    unset($this->type[$field]);
	    array_push_associative($this->type, array($field => 'dtg_percent'));
        } else {
            throw new KumbiaException("No se pudo dar formato, porque el campo: \"$field\" no existe.");
        }	
    }
    public function setTypeDate($field){
	if(!$this->existData()) return false;
        if(array_key_exists($field, $this->fields)){
	    unset($this->type[$field]);
	    array_push_associative($this->type, array($field => 'dtg_date'));
        } else {
            throw new KumbiaException("No se pudo dar formato, porque el campo: \"$field\" no existe.");
        }
    }
    public function setTextUpper($field){
	if(!$this->existData()) return false;
        if(array_key_exists($field, $this->fields)){
	    unset($this->type[$field]);	
	    array_push_associative($this->type, array($field => 'strtoupper'));
        } else {
            throw new KumbiaException("No se pudo dar formato, porque el campo: \"$field\" no existe.");
        }
    }
    public function setTextLower($field){
	if(!$this->existData()) return false;
        if(array_key_exists($field, $this->fields)){
	    unset($this->type[$field]);	
	    array_push_associative($this->type, array($field => 'strtolower'));
        } else {
            throw new KumbiaException("No se pudo dar formato, porque el campo: \"$field\" no existe.");
        }
    }
    public function setTextUpperFirst($field){
	if(!$this->existData()) return false;
        if(array_key_exists($field, $this->fields)){
	    unset($this->type[$field]);	
	    array_push_associative($this->type, array($field => 'ucfirst'));
        } else {
            throw new KumbiaException("No se pudo dar formato, porque el campo: \"$field\" no existe.");
	}
    }	
    public function setTextUpperWords($field){
	if(!$this->existData()) return false;
        if(array_key_exists($field, $this->fields)){
	    unset($this->type[$field]);	
	    array_push_associative($this->type, array($field => 'ucwords'));
        } else {
            throw new KumbiaException("No se pudo dar formato, porque el campo: \"$field\" no existe.");
	}
    }
    /**
     *Coloca una class a toda la columna o segun su valor a mostrar
     */
    public function setClass($field, $data=array()){
	if(!$this->existData()) return false;
        if(array_key_exists($field, $this->fields)){
	    array_push_associative($this->array_class, array($field => $data));
        } else {
            throw new KumbiaException("No se pudo asignar clase, porque el campo: \"$field\" no existe.");
	}	
    }
    /**
     *Lo mismo que el setAlias, pero con imagenes
     */
    public function setImage($field, $data=array()){
	if(!$this->existData()) return false;
        if(array_key_exists($field, $this->fields)){
	    array_push_associative($this->array_image, array($field => $data));
        } else {
            throw new KumbiaException("No se pudo asignar una imagen, porque el campo: \"$field\" no existe.");
	}	
    }
    /**
     *Coloca imagenes para cada fila, ej: PDF; XLS, DOC ...
     */
    public function setMimes($name){
	$params = is_array($name) ? $name : Util::getParams(func_get_args());
	$this->array_mimes	=	$params;
    }    
    /**
     *Cambia el titulo a mostrar según la key del array $data.
     */    
    public function setSumColumn($field){
        if(!$this->existData()) return false;
	if(array_key_exists($field, $this->fields)){
	    array_push_associative($this->sum_column, array($field => 'sum'));
        } else {
            throw new KumbiaException("No se pudo crear el calculo, porque el campo: \"$field\" no existe.");
	}
    }    
    public function getSumColumn($field){
	if(array_key_exists($field, $this->sum_column)){
	    return true;
        }
        return false;
    }        
    
    
    
    
    
    
    
    public function getValue($field_id){
	if(array_key_exists($field_id, $this->values)){
	    $model	=	substr($field_id, 0, -3);
	    return array($model, $this->values[$field_id]);
	}else{
	    return array();
	}
    }


    /**
    * Campo de tipo texto
    */
    public function setTextField($params){
	$params = is_array($params) ? $params : Util::getParams(func_get_args());	
	unset($this->type[$params[0]]);
	unset($this->params[$params[0]]);
	array_push_associative($this->params, array($params[0] => $params));
	array_push_associative($this->type, array($params[0] => 'text_field_tag'));
	$this->contador[$params[0]][]=-1;	
    }
    public function setDateField($params){
	$params = is_array($params) ? $params : Util::getParams(func_get_args());
	unset($this->type[$params[0]]);
	unset($this->params[$params[0]]);
	array_push_associative($this->params, array($params[0] => $params));
	array_push_associative($this->type, array($params[0] => 'date_field_tag'));
	$this->contador[$params[0]][]=-1;
    }
    public function setComboBySql($params, $sql){
	$params = is_array($params) ? $params : Util::getParams(func_get_args());
	unset($this->type[$params[0]]);
	unset($this->params[$params[0]]);
	array_push_associative($this->params, array($params[0] => $params));
	array_push_associative($this->type, array($params[0] => 'select_tag'));
	$this->contador[$params[0]][]=-1;	
	$db = DbBase::raw_connect();
	$db->query($sql, true);
	while($item = $db->fetch_array()){
	    array_push_associative($this->array_select, array(
		$item[$db->field_name(0)]	=>	$item[$db->field_name(1)]));
	}
	$db->close();
    }

       public function getFieldsAndHeaders(){
	    if($this->fields){
	       return 	$this->get_fields_and_headers	=	array_combine($this->fields, $this->headers);
	    }else{
		return array();
	    }
       }
       public function countOptions(){
	    $i=0;
	    if($this->edit!='') $i++;
	    if($this->delete!='') $i++;
	    return count($this->array_mimes) + $i;
       }

       public function getUrl(){
	       if($this->url==''){
		       $this->url	=	Router::get('controller') . '/' . Router::get('action');
	       }
	       return $this->url;
       }
       public function getUrlOrder(){
	       if($this->url==''){
		       $this->url	=	Router::get('controller') . '/' . Router::get('action');
	       }
	       if($this->use_paginator){
		       if(Router::get('id')==''){
			       $id	=	1;
		       }else{
			       $id	=	Router::get('id');
		       }
		       return $this->url . '/' . $id . '/';
	       }else{
		       return $this->url . '/';
	       }
       }
    public function getFormat($field, $value){
	if($value=='') return '&nbsp';
	if(array_key_exists($field, $this->type)){
	    $format		=	$this->type[$field];
	    if(strstr($format,'tag')){
		$format_aux	=	$format;
		$format		=	'tag';
	    }
	}else{
            return $value;
	}
	$ini			=	Config::read('datagrid');
	switch ($format) {
	    case 'dtg_image':
		//$array_img	=	explode(',', $ini['img']['size']);
		//$image		=	new SimpleImage();
		//$image->load('img/' . $value);
		//if(count($array_img)==2){
		//    $image->resize($array_img[0], $array_img[1]);
		//}else{
		//    $image->scale($array_img[0]);
		//}
		//$image->save($ini['img']['thumbs'] . $value, $ini['img']['image_type']);
		//$value		=	'thumbs/' . $value;
		//if(file_exists('img/' . $ini['img']['thumbs'] . $value))
		$value		=	Html::img($ini['img']['thumbs'].$this->rutaimg."/". $value, null, 'class=type_image style="height:'.$this->altoimg.'px;"');
	    break;
	case 'dtg_money':
	        $simbolo	=	$ini['money']['simbolo'];
	        $miles		=	$ini['money']['miles'];
	        $decimales	=	$ini['money']['decimales'];
	        $num_decimales	=	$ini['money']['num_decimales'];
	        $value		=	$format($value, $simbolo, $miles, $decimales, $num_decimales);	
	    break;	
	case 'dtg_numeric':
		$miles		=	$ini['number']['miles'];
		$decimales		=	$ini['number']['decimales'];
		$num_decimales	=	$ini['number']['num_decimales'];
		$value		=	$format($value, $miles, $decimales, $num_decimales);
	    break;
	case 'dtg_percent':
		$decimales	=	$ini['percent']['decimales'];
		$num_decimales	=	$ini['percent']['num_decimales'];
		$value		=	$format($value, $decimales, $num_decimales);						
	    break;
	case 'tag':
		$i		=	count($this->contador[$field]);
		$this->contador[$field][]	=	$i;
		if($format_aux=='select_tag'){
		    //array_push_associative($this->params[$field], array("datagrid[$field][$i]"));
		    $value		=	$format_aux("datagrid[$field][$i]", $this->array_select);
		}else{
		    if(array_key_exists("value", $this->params[$field])){
			array_push_associative($this->params[$field], array("datagrid[$field][$i]"));
		    }else{
			array_push_associative($this->params[$field], array("datagrid[$field][$i]", "value" => $value));
		    }
		    $value		=	$format_aux($this->params[$field]);						
		}
	    break;	
	default:
		if(isset($format)){
		    $value		=	$format($value);
		}
	    break;
	}
	return	$value;
    }


    /**
     *Recupera el o los valores ingresados por el setAlias
  */
    public function getAlias($field, $value){
	if(array_key_exists($field, $this->array_alias)){
	    $alias	=	$this->array_alias[$field];
	    if(array_key_exists($value, $alias)){
		return $alias[$value];
	    }
	}
	return $value;
    }
    /**
     *Recupera el o los valores ingresador por el setClass
  */
    public function getClass($field, $value){
	if(array_key_exists($field, $this->array_class)){
	    $class	=	$this->array_class[$field];
	    if(array_key_exists($value, $class)){
		return $class[$value];
	    }
	}
	return '';
    }    
    /**
     *Recupera el o los valores ingresador por el setImage
  */
    public function getImage($field, $value){
	if(array_key_exists($field, $this->array_image)){
	    $imagen	=	$this->array_image[$field];
	    if(array_key_exists($value, $imagen)){
		return $imagen[$value];
	    }
	}
	return $value;
    }
    /**
     *Adiciona una columna la cabezera y a los fields
   */
    public function createCol($header, $html){
	array_push_associative($this->fields,  array('new_col_' . $this->cont_cols => $html));
	array_push_associative($this->headers, array('new_col_' . $this->cont_cols => $header));
	$this->cont_cols++;
    }
    /**
     *Obtiene las nuevas columnas si existe la key
   */
    public function getNewCol($key, $rows){
	$str		=	preg_match_all('/{[\s\w\/<>=\\\"]*}/', $key, $items);
	$values		=	'';
	$msg		=	$key;
	foreach ($items[0] as $item){
		$value	=	str_replace(array('{','}'), '', $item);
		$msg	=	str_replace($item, $rows->$value, $msg);
	}
	return $msg;
    }
    public function printSelect($field, $sel_dtg=null){
	$campo		=	$this->getRelation($field);
	$code		=	"<select name='sel_dtg[$field]' id='sel_dtg[$field]'>";
	$code		.=	"<option value='0'>Seleccione ...</option>";
	foreach(Load::model($this->model)->find_all_by_sql('SELECT DISTINCT ' . $field . ' FROM ' . $this->model . ' ORDER BY ' . $field) as $item):
	    $selected	=	'';
	    if(count($sel_dtg)>0){
		if($sel_dtg[$field]==$item->$field){
		    $selected = 'SELECTED';
		}
	    }
	    $code	.=	"<option value=\"". $item->$field ."\"  $selected>";
	    $array_field		=	$this->getValue($field);
	    if(strstr($field,'_') and count($array_field) == 2){
		$code	.=	$this->getFormat($field, $item->$campo()->$array_field[1]);
	    }else{
		$code	.=	$this->getFormat($field,$item->$field);
	    }
	    $code	.=	"</option>";
	endforeach;
	$code		.=	"</select>";
	return $code;
    }
    public function getRelation($field=''){
	if(strstr($field,'_')){
	    $array_field	=	explode('_', $field);
	    return	'get' . ucfirst($array_field[0]);
	}
	return $field;
    }    
    public function AutoFilter($items){
        $fields='';
        $conditions='';
        foreach($items as $val => $key):
	    $fields         .=   $val .', ';
	    if($key!=0){
		$conditions .=  "$val = '$key' AND ";
	    }
	endforeach;
	$fields     =   substr($fields, 0, strlen($fields)-2);
	$this->use_paginator	=	false;
	    if($conditions!=''){
		$conditions =   substr($conditions, 0, strlen($conditions)-5);
		Load::model($this->model)->find($conditions);
		$this->data_source	=	Load::model($this->model)->find($conditions);
		//$sql		=	"SELECT $fields FROM $this->model WHERE $conditions";
		if($this->existData()){
		    if(array_key_exists('per_page', $this->data_source)){
			$this->use_paginator	=	true;
		    }
	    }else{
	        Flash::warning("No existen datos para mostrar");
	    }
	}
    }
}
?>
