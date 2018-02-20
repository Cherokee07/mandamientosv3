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
	
	var $B=0;
    var $I=0;
    var $U=0;
    var $HREF='';
    var $ALIGN='';
	function Header(){ 
		//Logo
		$agregar = 0;
		if (strtoupper(substr($this->CurOrientation,0,1)) == 'L'){
			$agregar = 76;
		}
		$this->Image($_SERVER['DOCUMENT_ROOT'].PUBLIC_PATH.'/default/public/img/formato/logoprocu.jpg',8,6,54,16);
		$this->Image($_SERVER['DOCUMENT_ROOT'].PUBLIC_PATH.'/default/public/img/formato/logogob.jpg',182,6,26,16);	
		$this->SetTextColor(0,0,0);
		$this->SetFont('Arial','B',10);		
		$this->Cell(60,20);
		$this->Cell(0,0,'PROCURADURIA GENERAL DE JUSTICIA DEL ESTADO',0,2,'L');
		$this->SetFont('Arial','B',8);
		$this->Cell(12);
		//$this->Cell(40,11,'ORDEN DE APREHENSION : '.$this->orden,0,0,'L');
		//$this->Cell(40,11,'ORDEN DE APREHENSION : ',0,0,'L');
		$this->Cell(44,11,$this->tipo,0,0,'R');
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
	

    function WriteHTML($html)
    {
        //HTML parser
        $html=str_replace("\n",' ',$html);
        $a=preg_split('/<(.*)>/U',$html,-1,PREG_SPLIT_DELIM_CAPTURE);
        foreach($a as $i=>$e)
        {
            if($i%2==0)
            {
                //Text
                if($this->HREF)
                    $this->PutLink($this->HREF,$e);
                elseif($this->ALIGN=='center')
                    $this->Cell(0,5,$e,0,1,'C');
                else
                    $this->Write(5,$e);
            }
            else
            {
                //Tag
                if($e[0]=='/')
                    $this->CloseTag(strtoupper(substr($e,1)));
                else
                {
                    //Extract properties
                    $a2=explode(' ',$e);
                    $tag=strtoupper(array_shift($a2));
                    $prop=array();
                    foreach($a2 as $v)
                    {
                        if(preg_match('/([^=]*)=["\']?([^"\']*)/',$v,$a3))
                            $prop[strtoupper($a3[1])]=$a3[2];
                    }
                    $this->OpenTag($tag,$prop);
                }
            }
        }
    }

    function OpenTag($tag,$prop)
    {
        //Opening tag
        if($tag=='B' || $tag=='I' || $tag=='U')
            $this->SetStyle($tag,true);
        if($tag=='A')
            $this->HREF=$prop['HREF'];
        if($tag=='BR')
            $this->Ln(5);
        if($tag=='P')
            $this->ALIGN=$prop['ALIGN'];
        if($tag=='HR')
        {
            if( !empty($prop['WIDTH']) )
                $Width = $prop['WIDTH'];
            else
                $Width = $this->w - $this->lMargin-$this->rMargin;
            $this->Ln(2);
            $x = $this->GetX();
            $y = $this->GetY();
            $this->SetLineWidth(0.4);
            $this->Line($x,$y,$x+$Width,$y);
            $this->SetLineWidth(0.2);
            $this->Ln(2);
        }
    }

    function CloseTag($tag)
    {
        //Closing tag
        if($tag=='B' || $tag=='I' || $tag=='U')
            $this->SetStyle($tag,false);
        if($tag=='A')
            $this->HREF='';
        if($tag=='P')
            $this->ALIGN='';
    }

    function SetStyle($tag,$enable)
    {
        //Modify style and select corresponding font
        $this->$tag+=($enable ? 1 : -1);
        $style='';
        foreach(array('B','I','U') as $s)
            if($this->$s>0)
                $style.=$s;
        $this->SetFont('',$style);
    }

    function PutLink($URL,$txt)
    {
        //Put a hyperlink
        $this->SetTextColor(0,0,255);
        $this->SetStyle('U',true);
        $this->Write(5,$txt,$URL);
        $this->SetStyle('U',false);
        $this->SetTextColor(0);
    }
}
?>