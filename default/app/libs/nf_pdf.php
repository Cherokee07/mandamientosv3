<?php
Load::lib('fpdf');
class NFPDF extends FPDF{ 
	var $titulo;
	var $alto;	
	var $linea;
	var $margen;
	var $encabezado = array();
	var $agregar;
	var $orden;
	var $tipo;
	function Header(){ 
		//Logo
		$agregar = 0;
		if (strtoupper(substr($this->CurOrientation,0,1)) == 'L'){
			$agregar = 76;
		}
		$this->Image($_SERVER['DOCUMENT_ROOT'].PUBLIC_PATH.'default/public/img/formato/logotipo.jpg',10,5,25,17);
		$this->SetTextColor(0,0,0);	
		$this->Image($_SERVER['DOCUMENT_ROOT'].PUBLIC_PATH.'default/public/img/formato/logogob.jpg',165,4,42,18);
		$this->SetTextColor(0,0,0);
		$this->SetFont('Arial','B',10);		
		$this->Cell(78,20);
		$this->Cell(0,0,'FISCALIA GENERAL DEL ESTADO',0,1,'L');
		$this->SetFont('Arial','B',8);
		$this->Cell(73,11);
		//$this->Cell(40,11,'ORDEN DE APREHENSION : '.$this->orden,0,0,'L');
		//$this->Cell(40,11,'ORDEN DE APREHENSION : ',0,0,'L');
		$this->Cell(44,11,$this->tipo,0,0,'C');
		$this->SetFont('Arial','',9);
		$this->Cell(0,11,$this->orden,0,1,'l');
		$this->Line(82,$this->gety(),152,$this->gety());
		$this->Ln($this->linea);
	} 

	function Footer(){ 
		//Posición: a 1,5 cm del final
		$this->SetY(-15);
		//Arial italic 8
		$this->SetFont('Arial','',8);
		//Número de página
		//$this->Cell(0,1,$this->orden,0,0,'L',false);
		$this->Cell(0,3,'Página '.$this->PageNo().'/{nb}',0,0,'C',false);
		$this->SetFont('Arial','I',8);
		$this->Cell(0,2,date('d-m-Y H:i'),0,0,'R',false);		
	}
}
?>