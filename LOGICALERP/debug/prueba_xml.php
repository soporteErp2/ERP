<?php
  // crear(); //Creamos el archivo
  // leer();  //Luego lo leemos

  //Para crear el archivo
  function crear(){
    $xml = new DomDocument('1.0', 'UTF-8');
    $xml->xmlStandalone = 'no';

    $biblioteca = $xml->createElement('bibliotecaaqui');
    $biblioteca = $xml->appendChild($biblioteca);

    $libro = $xml->createElement('libro');
    $libro = $biblioteca->appendChild($libro);

    // Agregar un atributo al libro
    $libro->setAttribute('seccion', 'favoritos');

    $autor = $xml->createElement('autor','Paulo Coelho');
    $autor = $libro->appendChild($autor);

    $titulo = $xml->createElement('titulo','El Alquimista');
    $titulo = $libro->appendChild($titulo);

    $anio = $xml->createElement('anio','1988');
    $anio = $libro->appendChild($anio);

    $editorial = $xml->createElement('editorial','Maxico D.F. - Editorial Grijalbo');
    $editorial = $libro->appendChild($editorial);

    $xml->formatOutput = true;
    $el_xml = $xml->saveXML();
    $xml->save('libros.xml');

    //Mostramos el XML puro
    echo "<p><b>El XML ha sido creado.... Mostrando en texto plano:</b></p>".
         htmlentities($el_xml)."<br/><hr>";
  }

  //Para leerlo
  function leer(){
    echo "<p><b>Ahora mostrandolo con estilo</b></p>";
    $xml = simplexml_load_file('libros.xml');
    $salida ="";
    foreach($xml->libro as $item){
      $salida .=
        "<b>Autor:</b> " . $item->autor . "<br/>".
        "<b>Título:</b> " . $item->titulo . "<br/>".
        "<b>Ano:</b> " . $item->anio . "<br/>".
        "<b>Editorial:</b> " . $item->editorial . "<br/><hr/>";
    }
    echo $salida;
  }

  $xml = simplexml_load_file("mundo.xml");
  // include 'file';
  // $xml = new SimpleXMLElement("libros.xml");
  // $xml = new DomDocument('1.0', 'utf-8');
  // $xml->preserveWhiteSpace = false;
  // $xml->formatOutput = true;
  // $xml->load('mundo.xml');
  // $xml->loadXML( $xmlLoad->asXML() );

  // $ubls = $xml->getElementsByTagName('cbc:UBLVersionID');

  // foreach ($ubls as $ubl) {
  //   echo $ubl->nodeValue, PHP_EOL;
  // }

  // print_r( $xml->getElementsByTagName('cbc:UBLVersionID') ) ;
  // $titels = array();
  // $marker = $xml->getElementsByTagName(  );

  // for ( $i = $marker->length - 1; $i >= 0; $i-- ) {
  //   $new = $marker->item( $i )->textContent;
  //   array_push( $titels, $new );
  // }

  // print_r( $titels );


  // $extUBLExtensions = "ext:UBLExtensions";
  // $extUBLExtension = "ext:UBLExtension";
  // $extExtensionContent = "ext:ExtensionContent";
  // $dsSignature = "ds:Signature";
  // $dsSignedInfo = "ds:SignedInfo";
  // $dsReference = "ds:Reference";
  // $xml->extUBLExtensions->addChild("DigestValue",$this->digest,"ds");
  // $xml->libro->addChild("aqui2","funciona2");

  // $xml->asXML("mundo.xml");
  // echo "<br>Funciona";
  // echo $xml->asXML();
  // echo "<br>";
  // echo $xml->UBLExtension;
  // var_dump($xml);
  // htmlentities(print_r($xml));
  // var_dump($xml);
  print_r($xml);
  // echo $xml;
  // echo $xml->save();

?>
