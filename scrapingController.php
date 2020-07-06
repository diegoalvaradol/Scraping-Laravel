<?php

namespace App\Http\Controllers;

use Goutte\Client;
use Illuminate\Http\Request;
use Symfony\Component\DomCrawler\Crawler;

class scrapingController extends Controller
{
    //protected $details;

    //Metodo generador de descarga.
    public function scrap(Client $client)
    {
        for ($i=1; $i <= 2; $i++) { 
            //Pagina de descarga.
            $offset = $i*1;
            $pageUrl = "https://www.solotodo.cl/notebooks?page=$offset";

            //Pagina de extraccion.
            $crawler = $client->request('GET', $pageUrl);
            $this -> extractInfoFrom($crawler);
        }

        //return $this -> startDownloadCSV();
    }

    //Metodo generador de archivo.
    /*public function startDownloadCSV(){

        //Crear archivo CSV
        $fileName = date('d-m-Y'). '.sql';
        $file = fopen($fileName, 'w');

        //Llenar archivo
        foreach($this -> details as $detail){
            fputcsv($file, $detail) ;
        }

        //Descarga
        return response() -> download($fileName);
    }*/

    //Metodo Crawler para establecer parametros de extraccion.
    public function extractInfoFrom(Crawler $crawler){

        //Valor de los div a extraer.
        $inlinePC = 'd-flex flex-column category-browse-result';


        //Metodo para recorrer cada uno de los div.
        $crawler -> filter("[class='$inlinePC']") -> each(function (Crawler $infoNode){
            //Extraccion de informacion por cada Div.
            $divs = $infoNode -> children() -> filter('div');

            //Div de especificaciones del notebook. 
            $sectionInfo = $divs -> eq(1);
            $textinfo = $sectionInfo -> text();
            $detail = $this -> extractInfoPc($textinfo);
                    
            //Div de precio del notebook. 
            $sectionPrecio = $divs -> eq(2);
            $detail['precio'] = $sectionPrecio -> text();

            $sectionPrecio = $divs -> eq(0);
            $detail['modelo'] = $sectionPrecio -> text();
            
            //Mostrado de todos los datos de la pagina.
            var_dump($detail);
            //$this -> details[] = $detail;
        });  
    }

    //Metodo publico para ontener informacion de los div
    public function extractInfoPc($textinfo){

        //Array donde guardara dicha informacion.
        $detail =[];

        $parts = explode('Procesador', $textinfo);
        $detail ['modelo'] = $parts[0];
        $textinfo = $parts[1];
        $parts = explode('RAM', $textinfo);
        $detail ['cpu'] = $parts[0];
        $textinfo = $parts[1];
        $parts = explode('Pantalla', $textinfo);
        $detail ['ram'] = $parts[0];
        $textinfo = $parts[1];
        $parts = explode('Almacenamiento', $textinfo);
        $detail ['screen'] = $parts[0];
        $textinfo = $parts[1];
        $parts = explode('Tarjetas de video', $textinfo);
        $detail ['storage'] = $parts[0];
        $textinfo = $parts[1];
        $parts = explode('Tarjetas de video', $textinfo);
        $detail ['graphics'] = $parts[0];
        
        //Elemento de retorno dek tipo $details.
        return $detail;
    }
}    
