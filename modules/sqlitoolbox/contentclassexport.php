<?php

//@TODO Permettre la sélection de la colonne et du sens du tri depuis le BO

$errormsg = '';
$logList = array();
$tpl = eZTemplate::factory();
$Module = $Params["Module"];
$http = eZHTTPTool::instance();

if ($http->hasPostVariable('ExportIDArray'))
{
    $classFilter = $http->postVariable('ExportIDArray');
    $contentClasses = eZContentClass::fetchList( $version = eZContentClass::VERSION_STATUS_DEFINED, true, false, null, null, $classFilter);

    $optList = array();
    foreach ($contentClasses as $class )
    {
        $optList[$class->attribute('identifier')] = array();
        foreach( $class->attribute('data_map') as $attribute )
        {
            $dataType = $attribute->attribute( 'data_type' );

            $doc = new DOMDocument;
            $attributeNode = $doc->createElement( 'attribute' );
            $attributeParametersNode = $doc->createElement( 'datatype-parameters' );
            $attributeNode->appendChild( $attributeParametersNode );

            $dataType->serializeContentClassAttribute( $attribute, $attributeNode, $attributeParametersNode );
            $doc->appendChild( $attributeNode );

            $content = $doc->saveXML();
            $content = str_replace( '<?xml version="1.0" encoding="UTF-8"?>', '', $content );
            $content = str_replace( '<?xml version="1.0"?>', '', $content );
//         $content = str_replace( '<datatype-parameters>', '', $content );
//         $content = str_replace( '</datatype-parameters>', '', $content );

            $optList[$class->attribute('identifier')][$attribute->attribute('identifier')] = $content;
        }
    }

    $tpl->setVariable( 'class_list', $contentClasses );
    $tpl->setVariable( 'opt_list', $optList );
    $tpl->setVariable( "class_count", count( $contentClasses ) );

    $result = $tpl->fetch( 'design:sqlitoolbox/xml/contentclassexport.tpl' );

    echo $result;

    eZExecution::cleanup();
    eZExecution::setCleanExit();
    header('Content-Type: text/xml');
    header('Content-Disposition: attachment;');
    header('Pragma: no-cache' );
    header('Expires: 0' );
    exit(0);
}

$classList = eZContentClass::fetchAllClasses();

$objectCounts = array();
foreach ($classList as $contentClass)
{
    $contentClassID = $contentClass->attribute('id');
    $objectCounts[$contentClassID] = eZContentObject::fetchSameClassListCount( $contentClassID );
}

$page_uri = $Module->redirectionURI( 'sqlitoolbox', 'contentclassexport');

$tpl->setVariable('page_uri', $page_uri );
$tpl->setVariable( 'classList', $classList );
$tpl->setVariable( 'objectCounts', $objectCounts );

$Result['left_menu'] = 'design:sqlitoolbox/parts/leftmenu.tpl';
$Result['content'] = $tpl->fetch( 'design:sqlitoolbox/contentclassexport.tpl' );