<?php


View::template('sbadmin');
Load::models('juzgados');
Load::models('ordenes');

class OrdenController extends AppController



{

    public function inicia(){
        
    }
    public function crearorden(){
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
