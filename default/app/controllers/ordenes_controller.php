<?php


View::template('sbadmin');
Load::models('juzgados');
Load::models('ordenes');

class OrdenesController extends AppController
{
    public function nuevaorden(){
	}
	
	public function crearorden(){
		$this->ordenes=Load::model('ordenes')->find();
	
    if(Input::hasPost('ordenes')){
		$datos=Input::post('ordenes');
        $orden= new Ordenes();
        
        $orden->exppenal=$datos['exppenal'];
        $orden->anipenal=$datos['anipenal'];
        $orden->juzgados_id=$datos['juzgados'];
        $orden->tiposoa_id=$datos['tiposoa'];
        $orden->ocasion=$datos['ocasion'];
        $orden->numaver=mb_strtoupper($datos['averiguacion'], 'utf-8');
        $orden->agencias_id=$datos['agencias'];
        $orden->aniagen=$datos['aniagen'];
        $orden->turno=$datos['turno'];
        $orden->feclib=$datos['feclib'];
        $orden->fecrec=$datos['fecrec'];
        $orden->ofirec=mb_strtoupper($datos['ofirec'], 'utf-8');
        $orden->fecfirma=$datos['fecfirma'];
        $orden->fecentpj=$datos['fecentpj'];
        $orden->ofientpj=mb_strtoupper($datos['ofientpj'], 'utf-8');
        $orden->cumpli=mb_strtoupper($datos['cumplimiento'], 'utf-8');
        $orden->envio=mb_strtoupper($datos['envio'], 'utf-8');
        $orden->oficiooa=mb_strtoupper($datos['oficiooa'], 'utf-8');
        $orden->fecha=$datos['fecha'];
        $orden->oficio=mb_strtoupper($datos['oficio'], 'utf-8');
        $orden->cvecaptu=mb_strtoupper($datos['cvecaptu'], 'utf-8');
        $orden->feccaptu=$datos['feccaptu'];
        $orden->localidades_id=$datos['localidades'];
        $orden->sintesis=mb_strtoupper($datos['sintesis'], 'utf-8');
        $orden->subprocuradurias_id=$datos['subprocuradurias'];


	    if(!$orden->save()){ 
			Flash::error('<div class="alert alert-danger" role="alert">
  					<strong>Error!</strong> La orden no pudo ser creado
					</div>'); 
		}else{ 
			Flash::valid('<div class="alert alert-success" role="alert">
  					<strong>Exito!</strong> Orden agregada Correctamente
					</div>');					
			Input::delete();
		}
    }			
	}

    public function getMunicipios()
    {
        //No es necesario el template
        View::template(null);
        //Carga la variable $region_id en la vista
        $this->distritos_id = Input::post('distritos_id');
    }

    public function getLocalidades()
    {
        //No es necesario el template
        View::template(null);
        //Carga la variable $comuna_id en la vista
        $this->municipios_id = Input::post('municipios_id');
    }

}

