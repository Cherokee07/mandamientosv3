<?php 
class Ordenes extends ActiveRecord
{
  /**
  *Retorna un array de objetos cuyos campos son los mismos de la tabla Marca
  *$page : es el número o indice de la página
  *$$ppage : es el número de filas o registro de la página
  **/
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
  
  function fecEquipo($valor){
	if (substr($valor,2,1)=='-'){			
		$aux = explode('-',$valor);
		return $aux[2].'-'.$aux[1].'-'.$aux[0];
	}
	return ((($valor!='0000-00-00') && ($valor!=''))?$valor:NULL);
  }
   
  public function buscaOrden($exp=0,$ani=0,$juz=0,$tipo=0){
	return $this->exists("exppenal = ".$exp." and anipenal = ".$ani." and juzgados_id = ".$juz." and tiposoa_id = ".$tipo);
  }
  
  public function obtenOrdenes($exp=0,$ani=0,$juz=0,$tipo=0){
	return $this->find("conditions: exppenal = ".$exp." and anipenal = ".$ani." and juzgados_id = ".$juz." and tiposoa_id = ".$tipo);
  }  
  
  public function obtenOcasion($exp=0,$ani=0,$juz=0,$tipo=0){
	return $this->maximum("ocasion","conditions: exppenal = ".$exp." and anipenal = ".$ani." and juzgados_id = ".$juz." and tiposoa_id = ".$tipo)+1;
  }  
  
  public function listaOrdenes($exp=0,$ani=0,$juz=0,$tipo=0){	
	$sql = "SELECT g.id,concat(g.exppenal,'/',g.anipenal,'/',juzgados_id,'/',t.clave,'/',g.ocasion) as expediente,";
	$sql .= "DATE_FORMAT(g.feclib,'%d-%m-%Y') as feclib, DATE_FORMAT(g.fecrec,'%d-%m-%Y') as fecrec,";
	$sql .= "GROUP_CONCAT(CONCAT(ifnull(i.nombre,''),' ',ifnull(i.paterno,''),' ',ifnull(i.materno,''))) as indiciado,";
	$sql .= "GROUP_CONCAT(CONCAT(ifnull(o.nombre,''),' ',ifnull(o.paterno,''),' ',ifnull(o.materno,''))) as ofendido ";
	$sql .= "FROM ordenes g ";
	$sql .= "LEFT JOIN ".$this->catalogos.".tiposoa t ON t.id = g.tiposoa_id ";
	$sql .= "LEFT JOIN indiciados i ON i.ordenes_id = g.id ";
	$sql .= "LEFT JOIN ofendidos o ON o.ordenes_id = g.id ";
	$sql .= "WHERE g.exppenal = ".$exp." and g.anipenal = ".$ani." and g.juzgados_id = ".$juz." and g.tiposoa_id = ".$tipo." ";
	$sql .= "GROUP BY g.id ";
	return $this->find_all_by_sql($sql); 
  }
  
  public function listaDiversas($busquedas){	
	$sql = "SELECT distinct g.id,concat(g.exppenal,'/',g.anipenal,'/',juzgados_id,'/',t.clave,'/',g.ocasion) as expediente,t.descripcion,";
	$sql .= "DATE_FORMAT(g.feclib,'%d-%m-%Y') as feclib, DATE_FORMAT(g.fecrec,'%d-%m-%Y') as fecrec,g.sintesis ";	
	$sql .= "FROM ordenes g ";
	$sql .= "INNER JOIN ".$this->catalogos.".tiposoa t ON t.id = g.tiposoa_id ";
	$sql .= "INNER JOIN ofeindis oi ON oi.ordenes_id = g.id ";
	if ((@$busquedas['delitos_id']) || (@$busquedas['particularidades']) || (@$busquedas['grave']) || (@$busquedas['tentativa']) || (@$busquedas['culposo'])){
			if (@$busquedas['delitos_id']){
				$sql .= " AND oi.delitos_id = ".$busquedas['delitos_id'];
			}
			if (@$busquedas['particularidades']){
				$sql .= " AND oi.particularidades like '%".$busquedas['particularidades']."%'";
			}
			if (@$busquedas['grave']){
				$sql .= " AND oi.grave = '".$busquedas['grave']."'";
			}
			if (@$busquedas['tentativa']){
				$sql .= " AND oi.tentativa = '".$busquedas['tentativa']."'";
			}
			if (@$busquedas['culposo']){
				$sql .= " AND oi.culposo = '".$busquedas['culposo']."'";
			}			
		}		
	if ((@$busquedas['estadosoa_id']) || (@$busquedas['motivosoa_id']) || (@$busquedas['sexoin_id']) || (@$busquedas['edadin'])
		|| (@$busquedas['autoridadin']) || (@$busquedas['fecejecini']) ||(@$busquedas['fecejecfin'])
		|| (@$busquedas['feccmbini']) ||(@$busquedas['feccmbfin'])){
		$sql .= " INNER JOIN indiciados i ON i.id = oi.indiciados_id ";
		$sql .= ((@$busquedas['estadosoa_id'])?" AND i.estadosoa_id = ".$busquedas['estadosoa_id']:"");
		$sql .= ((@$busquedas['motivosoa_id'])?" AND i.motivosoa_id = ".$busquedas['motivosoa_id']:"");
		$sql .= ((@$busquedas['sexoin_id'])?" AND i.sexo = '".$busquedas['sexoin_id']."'":"");
		$sql .= ((@$busquedas['edadin'])?" AND i.edad = ".$busquedas['edadin']:"");
		$sql .= ((@$busquedas['autoridadin'])?" AND i.autoridad = '".$busquedas['autoridadin']."'":"");
		$sql .= ((@$busquedas['fecejecini'])?" AND i.fecejec >= '".$this->fecEquipo($busquedas['fecejecini'])."'":"");
		$sql .= ((@$busquedas['fecejecfin'])?" AND i.fecejec <= '".$this->fecEquipo($busquedas['fecejecfin'])."'":"");
		$sql .= ((@$busquedas['feccmbini'])?" AND i.fecedo >= '".$this->fecEquipo($busquedas['feccmbini'])."'":"");
		$sql .= ((@$busquedas['feccmbfin'])?" AND i.fecedo <= '".$this->fecEquipo($busquedas['feccmbfin'])."'":"");
	}
	
	if ((@$busquedas['sexoof_id']) || (@$busquedas['edadof']) || (@$busquedas['autoridadof']) ||(@$busquedas['occiso'])){
		$sql .= " INNER JOIN ofendidos o ON o.id = oi.ofendidos_id ";
		$sql .= ((@$busquedas['sexoof_id'])?" AND o.sexo = '".$busquedas['sexoof_id']."'":"");
		$sql .= ((@$busquedas['edadof'])?" AND o.edad = ".$busquedas['edadof']:"");
		$sql .= ((@$busquedas['autoridadof'])?" AND o.autoridad = '".$busquedas['autoridadof']."'":"");
		$sql .= ((@$busquedas['occiso'])?" AND o.occiso = '".$busquedas['occiso']."'":"");
	}	
	if ((@$busquedas['juzgados_id']) || (@$busquedas['anipenal']) || (@$busquedas['tiposoa_id']) || (@$busquedas['regiones_id'])	|| (@$busquedas['distritos_id'])
	|| (@$busquedas['municipios_id']) || (@$busquedas['localidades_id']) || (@$busquedas['feclibini']) || (@$busquedas['feclibfin'])){
		$art = " WHERE ";	
		if (Session::get('todas')!='S'){
			$sql .= $art." g.subprocuradurias_id = ".Session::get('subprocuradurias_id');
			$art = " AND ";
		}			
		if (@$busquedas['juzgados_id']){
			if($busquedas['juzgados_id']==0){
				
			}
			else{
				$sql .= $art." g.juzgados_id = ".$busquedas['juzgados_id'];
				$art = " AND ";
			}
			
		}
		if (@$busquedas['anipenal']){
			$sql .= $art." g.anipenal = ".$busquedas['anipenal'];
			$art = " AND ";
		}
		if (@$busquedas['tiposoa_id']){
			$sql .= $art." g.tiposoa_id = ".$busquedas['tiposoa_id'];
			$art = " AND ";
		}
		if (@$busquedas['feclibini']){
			$sql .= $art." g.feclib >= '".$this->fecEquipo($busquedas['feclibini'])."'";
			$art = " AND ";
		}
		if (@$busquedas['feclibfin']){
			$sql .= $art." g.feclib <= '".$this->fecEquipo($busquedas['feclibfin'])."'";
			$art = " AND ";
		}
		if (@$busquedas['localidades_id']){
			$sql .= $art." g.localidades_id = ".$busquedas['localidades_id'];
			$art = " AND ";
		}elseif (@$busquedas['municipios_id']){
			$sql .= $art." g.localidades_id in (SELECT id FROM ".$this->catalogos.".localidades WHERE municipios_id = ".$busquedas['municipios_id'].")";
			$art = " AND ";
		}elseif (@$busquedas['distritos_id']){
			$sql .= $art." g.localidades_id in (SELECT l.id FROM ".$this->catalogos.".localidades l, ".$this->catalogos.".municipios m WHERE m.id = l.municipios_id AND m.distritos_id = ".$busquedas['distritos_id'].")";
			$art = " AND ";
		}elseif (@$busquedas['regiones_id']){
			$sql .= $art." g.localidades_id in (SELECT l.id FROM ".$this->catalogos.".localidades l, ".$this->catalogos.".municipios m, ".$this->catalogos.".distritos d WHERE l.municipios_id = m.id AND d.id = m.distritos_id AND d.regiones_id = ".$busquedas['regiones_id'].")";
			$art = " AND ";
		}
	}
		echo $sql;
	return $this->find_all_by_sql($sql); 
  }
  
  public function porvictima(){
	  $vacio = 1;
	  if (Input::hasPost('busquedas')){
		  $vacio = 0;
		  $busquedas = Input::post('busquedas');		  
	  }
	  if (Input::hasPost('delitos')){
		  $vacio = 0;
		  $delitos = Input::post('delitos');
		  $numdel = "";
		  foreach($delitos as $delito){
			  $numdel .= (($numdel=="")?"":",").$delito;
		  }
	  }
	  else{
		  $numdel = "-1";
	  }
		$sql = "SELECT CONCAT(g.exppenal,'/',g.anipenal,'/',j.juzgado) as expediente,";
		$sql.= "e.descripcion,DATE_FORMAT(g.feclib,'%d-%m-%Y') AS feclib,d.delito,CONCAT(IFNULL(o.nombre,''),' ',IFNULL(o.paterno,''),' ',IFNULL(o.materno,'')) AS ofendido,";
		$sql.= "o.sexo,o.edad,";
		$sql.= "GROUP_CONCAT(CONCAT(IFNULL(i.nombre,''),' ',IFNULL(i.paterno,''),' ',IFNULL(i.materno,'')) SEPARATOR ', ') AS indiciado,";
		$sql.= "l.localidad,m.municipio,dis.distrito,r.region ";
		$sql.= "FROM ordenes g INNER JOIN ofeindis oi ON oi.ordenes_id = g.id AND oi.delitos_id in ($numdel) ";
		$sql.= "INNER JOIN ofendidos o ON o.id = oi.ofendidos_id ".(($busquedas['sexoof_id']<>'T')?" AND o.sexo ='".$busquedas['sexoof_id']."' ":"");
		$sql.= ((@$busquedas['edadini'])?" AND o.edad >=".$busquedas['edadini']." ":"");
		$sql.= ((@$busquedas['edadfin'])?" AND o.edad <=".$busquedas['edadfin']." ":"");
		$sql .= "INNER JOIN indiciados i ON i.id = oi.indiciados_id ".((@$busquedas['estadosoa_id'])?" AND i.estadosoa_id = ".$busquedas['estadosoa_id']." ":"");
		$sql .= "INNER JOIN catalogosgral.delitos d ON d.id = oi.delitos_id ";
		$sql .= "INNER JOIN catalogosgral.juzgados j ON j.id = g.juzgados_id ";
		$sql .= "INNER JOIN catalogosgral.tiposoa t ON t.id = g.tiposoa_id ";
		$sql .= "INNER JOIN catalogosgral.estadosoa e ON e.id =i.estadosoa_id ";
		$sql .= "INNER JOIN catalogosgral.localidades l ON l.id = g.localidades_id ";
		$sql .= "INNER JOIN catalogosgral.municipios m ON m.id = l.municipios_id ";
		$sql .= "INNER JOIN catalogosgral.distritos dis ON dis.id = m.distritos_id ";
		$sql .= "INNER JOIN catalogosgral.regiones r ON r.id = dis.regiones_id ";
		$art = "WHERE ";
		if (@$busquedas['feclibini']){
			$sql .= $art."feclib >= '".$this->fecEquipo($busquedas['feclibini'])."' ";
			$art = " AND ";
		}
		if (@$busquedas['feclibini']){
			$sql .= $art."feclib <= '".$this->fecEquipo($busquedas['feclibfin'])."' ";
			$art = " AND ";
		}
		if ($vacio==1){
			$sql .= $art." o.id = -100";
			$art = " AND ";
		}
		$sql .= "GROUP BY o.id ORDER BY g.feclib";
		return $this->find_all_by_sql($sql);
  }
  
  public function porregionydel(){
	  $vacio = 1;
	  if (Input::hasPost('busquedas')){
		  $vacio = 0;
		  $busquedas = Input::post('busquedas');		  
	  }
	  if (Input::hasPost('delitos')){
		  $vacio = 0;
		  $delitos = Input::post('delitos');
		  $numdel = "";
		  foreach($delitos as $delito){
			  $numdel .= (($numdel=="")?"":",").$delito;
		  }
	  }
	  else{
		  $numdel = "-1";
	  }
		$sql = "SELECT CONCAT(g.exppenal,'/',g.anipenal,'/',j.juzgado) as expediente,";
		$sql.= "e.descripcion,DATE_FORMAT(g.feclib,'%d-%m-%Y') AS feclib,d.delito,CONCAT(IFNULL(o.nombre,''),' ',IFNULL(o.paterno,''),' ',IFNULL(o.materno,'')) AS ofendido,";
		$sql.= "o.sexo,o.edad,";
		$sql.= "GROUP_CONCAT(CONCAT(IFNULL(i.nombre,''),' ',IFNULL(i.paterno,''),' ',IFNULL(i.materno,'')) SEPARATOR ', ') AS indiciado,";
		$sql.= "l.localidad,m.municipio,dis.distrito,r.region ";
		$sql.= "FROM ordenes g INNER JOIN ofeindis oi ON oi.ordenes_id = g.id AND oi.delitos_id in ($numdel) ";
		$sql.= "INNER JOIN ofendidos o ON o.id = oi.ofendidos_id ".(($busquedas['sexoof_id']<>'T')?" AND o.sexo ='".$busquedas['sexoof_id']."' ":"");
		$sql.= ((@$busquedas['edadini'])?" AND o.edad >=".$busquedas['edadini']." ":"");
		$sql.= ((@$busquedas['edadfin'])?" AND o.edad <=".$busquedas['edadfin']." ":"");
		$sql .= "INNER JOIN indiciados i ON i.id = oi.indiciados_id ".((@$busquedas['estadosoa_id'])?" AND i.estadosoa_id = ".$busquedas['estadosoa_id']." ":"");
		$sql .= "INNER JOIN catalogosgral.delitos d ON d.id = oi.delitos_id ";
		$sql .= "INNER JOIN catalogosgral.juzgados j ON j.id = g.juzgados_id ";
		$sql .= "INNER JOIN catalogosgral.tiposoa t ON t.id = g.tiposoa_id ";
		$sql .= "INNER JOIN catalogosgral.estadosoa e ON e.id =i.estadosoa_id ";
		$sql .= "INNER JOIN catalogosgral.localidades l ON l.id = j.localidades_id ";
		$sql .= "INNER JOIN catalogosgral.municipios m ON m.id = l.municipios_id ";
		$sql .= "INNER JOIN catalogosgral.distritos dis ON dis.id = m.distritos_id ";
		$sql .= "INNER JOIN catalogosgral.regiones r ON r.id = dis.regiones_id ";
		$art = "WHERE ";
		if (@$busquedas['feclibini']){
			$sql .= $art."feclib >= '".$this->fecEquipo($busquedas['feclibini'])."' ";
			$art = " AND ";
		}
		if (@$busquedas['feclibini']){
			$sql .= $art."feclib <= '".$this->fecEquipo($busquedas['feclibfin'])."' ";
			$art = " AND ";
		}
		if ($vacio==1){
			$sql .= $art." o.id = -100";
			$art = " AND ";
		}
		$sql .= "GROUP BY o.id ORDER BY r.region,d.delito,g.feclib";
		return $this->find_all_by_sql($sql);
  }  
  
 /* function reportes($tipo=1,$fecini=0,$fecfin=0){
	$fecini = $this->fecEquipo($fecini);
	$fecfin = $this->fecEquipo($fecfin);
	
	$sql = "SELECT DISTINCT G.id,G.exppenal,G.anipenal,J.id as juzgados_id,T.clave as tipoorden,G.ocasion,J.juzgado,DATE_FORMAT(G.feclib,'%d/%m/%Y') as feclib,";
	$sql .= "REPLACE(I.nombre,'Ã‘','Ñ') as nombre,REPLACE(I.paterno,'Ã‘','Ñ') as paterno,REPLACE(I.materno,'Ã‘','Ñ') as materno,";
	$sql .= "REPLACE(I.sobren,'Ã‘','Ñ') as sobren,I.edad,CASE  WHEN I.sexo IN ('F','M') THEN I.sexo ELSE 'D' END AS sexo,REPLACE(D.delito,'Ã‘','Ñ') as delito,";
	$sql .= "CASE OI.culposo WHEN 'S' THEN 'SI' ELSE 'NO' END culposo,";
	$sql .= "CASE OI.tentativa WHEN 'S' THEN 'SI' ELSE 'NO' END tentativa,";
	$sql .= "CASE I.estadosoa_id ";
  	$sql .= "  WHEN 6 THEN 'VIGENTE' ";
	$sql .= "  WHEN 9 THEN 'TRAMITE' ";
	$sql .= "  WHEN 3 THEN 'RESERVA' ";
	$sql .= "  WHEN 2 THEN 'CUMPLIDA' ";
	$sql .= "  WHEN 1 THEN 'SIN EFECTO' ";
	$sql .= "END AS edoorden, ";
	$sql .= "CASE I.motivosoa_id ";
  	$sql .= "  WHEN 1 THEN 'PREESCRIPCION' ";
	$sql .= "  WHEN 2 THEN 'AMPARO CONCEDIDO' ";
	$sql .= "  WHEN 3 THEN 'DEFUNSION' ";
	$sql .= "  WHEN 4 THEN 'COMPARECENCIA VOLUNTARIA' ";
	$sql .= "  WHEN 5 THEN 'REVOCADA' ";
	$sql .= "  WHEN 6 THEN 'PERDON' ";
	$sql .= "  ELSE '' ";
	$sql .= "END AS motivo,DATE_FORMAT(I.fecedo,'%d/%m/%Y') AS fecedo,DATE_FORMAT(I.fecejec,'%d/%m/%Y') as fecejec ";
	$sql .= "FROM ordenes.ordenes G ";
	$sql .= "LEFT JOIN ordenes.indiciados I ON G.id = I.ordenes_id ";
	$sql .= "LEFT JOIN catalogosgral.juzgados J ON G.juzgados_id = J.id ";
	$sql .= "LEFT JOIN ordenes.ofeindis OI ON G.id = OI.ordenes_id ";
	$sql .= "LEFT JOIN catalogosgral.delitos D ON D.id = OI.delitos_id ";
	$sql .= "LEFT JOIN catalogosgral.tiposoa T ON G.tiposoa_id = T.id ";
	$sql .= "WHERE ".((Session::get('todas')=='S')?"":" G.subprocuradurias_id = ".Session::get('subprocuradurias_id')." AND ");
	switch ($tipo){
		case 1: $sql .= "G.feclib BETWEEN '".$fecini."' AND '".$fecfin."' "; break;
		case 2: $sql .= "I.fecejec BETWEEN '".$fecini."' AND '".$fecfin."' "; break;
		case 3: $sql .= "G.feclib BETWEEN '".$fecini."' AND '".$fecfin."' AND I.estadosoa_id = 1 "; break;
		case 4: $sql .= "I.fecedo BETWEEN '".$fecini."' AND '".$fecfin."' AND I.estadosoa_id = 1 "; break;
		case 5: $sql .= "G.feclib BETWEEN '".$fecini."' AND '".$fecfin."' AND I.estadosoa_id IN (6,3,9) "; break;
	}	
	
	return $this->find_all_by_sql($sql); 
  }*/
  
  function reportes($tipo=1,$fecini=0,$fecfin=0,$completa=1){
	$fecini = $this->fecEquipo($fecini);
	$fecfin = $this->fecEquipo($fecfin);
	
	$sql = "SELECT DISTINCT G.id,G.exppenal,G.anipenal,J.id as juzgados_id,T.clave as tipoorden,G.ocasion,J.juzgado,DATE_FORMAT(G.feclib,'%d/%m/%Y') as feclib, DATE_FORMAT(G.fecrec,'%d/%m/%Y') as fecrec,";
	$sql .= "REPLACE(I.nombre,'Ã‘','Ñ') as nombre,REPLACE(I.paterno,'Ã‘','Ñ') as paterno,REPLACE(I.materno,'Ã‘','Ñ') as materno,";
	$sql .= "REPLACE(I.sobren,'Ã‘','Ñ') as sobren,I.edad,CASE  WHEN I.sexo IN ('F','M') THEN I.sexo ELSE 'D' END AS sexo,REPLACE(D.delito,'Ã‘','Ñ') as delito,";
	$sql .= "CASE OI.culposo WHEN 'S' THEN 'SI' ELSE 'NO' END culposo,";
	$sql .= "CASE OI.tentativa WHEN 'S' THEN 'SI' ELSE 'NO' END tentativa,";
	$sql .= "CASE I.estadosoa_id ";
  	$sql .= "  WHEN 6 THEN 'VIGENTE' ";
	$sql .= "  WHEN 9 THEN 'TRAMITE' ";
	$sql .= "  WHEN 3 THEN 'RESERVA' ";
	$sql .= "  WHEN 2 THEN 'CUMPLIDA' ";
	$sql .= "  WHEN 1 THEN 'SIN EFECTO' ";
	$sql .= "END AS edoorden, ";
	$sql .= "CASE I.motivosoa_id ";
  	$sql .= "  WHEN 1 THEN 'PREESCRIPCION' ";
	$sql .= "  WHEN 2 THEN 'AMPARO CONCEDIDO' ";
	$sql .= "  WHEN 3 THEN 'DEFUNSION' ";
	$sql .= "  WHEN 4 THEN 'COMPARECENCIA VOLUNTARIA' ";
	$sql .= "  WHEN 5 THEN 'REVOCADA' ";
	$sql .= "  WHEN 6 THEN 'PERDON' ";
	$sql .= "  ELSE '' ";
	$sql .= "END AS motivo,DATE_FORMAT(I.fecedo,'%d/%m/%Y') AS fecedo,DATE_FORMAT(I.fecejec,'%d/%m/%Y') as fecejec ";
	if ($completa==2){
		$sql .= ",REPLACE(O.nombre,'Ã‘','Ñ') as nombre_ofen,REPLACE(O.paterno,'Ã‘','Ñ') as paterno_ofen,REPLACE(O.materno,'Ã‘','Ñ') as materno_ofen,";
		$sql .= "REPLACE(O.sobren,'Ã‘','Ñ') as sobren_ofen,IFNULL(I.edad,0) AS edad_ofen,CASE I.sexo WHEN 'F' THEN 'F' WHEN 'M' THEN 'M' ELSE 'D' END AS sexo_ofen ";
	}
	$sql .= "FROM ".$this->mandamientos.".ordenes G ";
	$sql .= "LEFT JOIN ".$this->catalogos.".juzgados J ON G.juzgados_id = J.id ";	
	$sql .= " LEFT JOIN ".$this->mandamientos.".indiciados I ON G.id = I.ordenes_id ";
	if ($completa==2){
		$sql .= " LEFT JOIN ".$this->mandamientos.".ofeindis OI ON OI.ordenes_id = I.ordenes_id AND OI.indiciados_id = I.id ";
		$sql .= " LEFT JOIN ".$this->mandamientos.".ofendidos O ON O.ordenes_id = OI.ordenes_id AND O.id = OI.ofendidos_id ";
	}
	else{
		$sql .= " LEFT JOIN ".$this->mandamientos.".ofeindis OI ON G.id = OI.ordenes_id ";
	}
	$sql .= " LEFT JOIN ".$this->catalogos.".delitos D ON D.id = OI.delitos_id ";
	$sql .= " LEFT JOIN ".$this->catalogos.".tiposoa T ON G.tiposoa_id = T.id ";
	$sql .= " WHERE ".((Session::get('todas')=='S')?"":" G.subprocuradurias_id = ".Session::get('subprocuradurias_id')." AND ");
	switch ($tipo){
		case 1: $sql .= "G.feclib BETWEEN '".$fecini."' AND '".$fecfin."' "; break;
		case 2: $sql .= "I.fecejec BETWEEN '".$fecini."' AND '".$fecfin."' "; break;
		case 3: $sql .= "G.feclib BETWEEN '".$fecini."' AND '".$fecfin."' AND I.estadosoa_id = 1 "; break;
		case 4: $sql .= "I.fecedo BETWEEN '".$fecini."' AND '".$fecfin."' AND I.estadosoa_id = 1 "; break;
		case 5: $sql .= "G.feclib BETWEEN '".$fecini."' AND '".$fecfin."' AND I.estadosoa_id IN (6,3,9) "; break;
		case 6: $sql .= "G.fecrec BETWEEN '".$fecini."' AND '".$fecfin."' "; break;
	}	
	echo $sql;
	return $this->find_all_by_sql($sql); 
  }
  
 
  public $before_validation_on_create="aux_before_val_create"; 
  public $before_save="aux_before_save"; 
  public function aux_before_val_create(){ 		
		$this->ocasion = $this->maximum("ocasion","conditions: exppenal = ".$this->exppenal." and anipenal = ".$this->anipenal." and juzgados_id = ".$this->juzgados_id." and tiposoa_id = ".$this->tiposoa_id)+1;
		$this->feclib = $this->fecEquipo($this->feclib);
		$this->fecfirma = $this->fecEquipo($this->fecfirma);
		$this->fecentpj = $this->fecEquipo($this->fecentpj);
		$this->fecha = $this->fecEquipo($this->fecha);
		$this->fecrec = $this->fecEquipo($this->fecrec);  
		$this->feccaptu = date('Y-m-d');
		$this->subprocuradurias_id = Session::get('subprocuradurias_id');
		$this->cvecaptu = Session::get('login');
  
  }
  
	public function aux_before_save(){ 		
		$this->feclib = $this->fecEquipo($this->feclib);
		$this->fecfirma = $this->fecEquipo($this->fecfirma);
		$this->fecentpj = $this->fecEquipo($this->fecentpj);
		$this->fecha = $this->fecEquipo($this->fecha);  
		$this->fecrec = $this->fecEquipo($this->fecrec);
  }  
}
?>