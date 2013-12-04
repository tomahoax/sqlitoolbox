<?php

class eZCreateOrUpdateClass extends eZCreateClass
{

	public function eZCreateOrUpdateClass( )
	{
	}

	public function execute( $xml )
	{
		$classList = $xml->getElementsByTagName( 'ContentClass' );
		$refArray = array();
		$availableLanguageList = eZContentLanguage::fetchLocaleList();
		foreach ( $classList as $class )
		{
			$user = eZUser::currentUser();
			$userID = $user->attribute( 'contentobject_id' );

			$classIdentifier        = $class->getAttribute( 'identifier' );
			$classRemoteID          = $class->getAttribute( 'remoteID' );
			$classObjectNamePattern = $class->getAttribute( 'objectNamePattern' );
			$classExistAction       = $class->getAttribute( 'classExistAction' );
			$referenceID            = $class->getAttribute( 'referenceID' );
			$classURLAliasPattern   = $class->getAttribute( 'urlAliasPattern' );

			$this->writeMessage( "\tClass '$classIdentifier' will be updated.", 'notice' );

			$classIsContainer       = $class->getAttribute( 'isContainer' );
			if ( $classIsContainer !== false )
			$classIsContainer = $classIsContainer == 'true' ? 1 : 0;

			$classGroupsNode        = $class->getElementsByTagName( 'Groups' )->item( 0 );
			$classAttributesNode    = $class->getElementsByTagName( 'Attributes' )->item( 0 );

			$nameListObject = $class->getElementsByTagName( 'Names' )->item( 0 );
			if ( $nameListObject->hasAttributes() )
			{
				if ( $nameListObject->hasAttributes())
				{
					$attributes = $nameListObject->attributes;
					if ( !is_null($attributes) )
					{
						$nameList = array();
						foreach ( $attributes as $index=>$attr )
						{
							if ( in_array( $attr->name, $availableLanguageList ) )
							{
								$nameList[$attr->name] = $attr->value;
							}
						}
					}
				}
			}


			$classNameList = new eZContentClassNameList( serialize($nameList) );
			$classNameList->validate( );


			$dateTime = time();
			$classCreated = $dateTime;
			$classModified = $dateTime;

			$class = eZContentClass::fetchByRemoteID( $classRemoteID );

			if (!$class)
			{
				$class = eZContentClass::fetchByIdentifier( $classIdentifier );
			}

			if ( $class )
			{
				$className = $class->name();
				switch( $classExistAction )
				{
					case 'replace':
						{
							$this->writeMessage( "\t\tClass '$classIdentifier' will be replaced.", 'notice' );
							foreach ( $nameList as $lang => $name )
							{
								if ( in_array( $lang, $availableLanguageList ) )
								{
									$class->setName( $name, $lang );
								}
							}
							$class->store();
							/* TODO: Remove all attributes */
						} break;
					case 'new':
						{
							unset( $class );
							$class = false;
							break;
						} break;
					case 'extend':
						{
							$this->writeMessage( "\t\tClass '$classIdentifier' will be extended.", 'notice' );
							foreach ( $nameList as $lang => $name )
							{
								if ( in_array( $lang, $availableLanguageList ) )
								{
									$class->setName( $name, $lang );
								}
							}
							$class->store();
						} break;
					case 'skip':
					default:
						{
							continue;
						} break;
				}
			}
			if (!$class)
			{
				// Try to create a unique class identifier
				$currentClassIdentifier = $classIdentifier;
				$unique = false;
				while( !$unique )
				{
					$classList = eZContentClass::fetchByIdentifier( $currentClassIdentifier );
					if ( $classList )
					{
						// "increment" class identifier
						if ( preg_match( '/^(.*)_(\d+)$/', $currentClassIdentifier, $matches ) )
						$currentClassIdentifier = $matches[1] . '_' . ( $matches[2] + 1 );
						else
						$currentClassIdentifier = $currentClassIdentifier . '_1';
					}
					else
					$unique = true;
					unset( $classList );
				}
				$classIdentifier = $currentClassIdentifier;

				// create class
				eZLog::write(print_r($classURLAliasPattern, true), 'classURLAliasPattern.log');

				$class = eZContentClass::create( $userID,
				array( 'version' => 0,
                                                        'serialized_name_list' => $classNameList->serializeNames(),
                                                        'create_lang_if_not_exist' => true,
                                                        'identifier' => $classIdentifier,
                                                        'remote_id' => $classRemoteID,
                                                        'contentobject_name' => $classObjectNamePattern,
                                                        'url_alias_name' => $classURLAliasPattern,
                                                        'is_container' => $classIsContainer,
                                                        'created' => $classCreated,
                                                        'modified' => $classModified ) );
				$class->store();
				$classID = $class->attribute( 'id' );
				$this->writeMessage( "\t\tClass '$classIdentifier' will be newly created.", 'notice' );
			}

			// create class attributes
			$classAttributeList = $classAttributesNode->getElementsByTagName( 'Attribute' );
			$classDataMap = $class->attribute( 'data_map' );

			if( $classDataMap == NULL ) $classDataMap = array();

			foreach ( $classAttributeList as $classAttributeNode )
			{
				$attributeDatatype = $classAttributeNode->getAttribute( 'datatype' );
				$attributeIsRequired = strtolower( $classAttributeNode->getAttribute( 'required' ) ) == 'true';
				$attributeIsSearchable = strtolower( $classAttributeNode->getAttribute( 'searchable' ) ) == 'true';
				$attributeIsInformationCollector = strtolower( $classAttributeNode->getAttribute( 'informationCollector' ) ) == 'true';
				$attributeIsTranslatable = (strtolower( $classAttributeNode->getAttribute( 'translatable' ) ) == 'false') ? 0 : 1;
				$attributeIdentifier = $classAttributeNode->getAttribute( 'identifier' );
				$attributeNewIdentifier = $classAttributeNode->getAttribute( 'newIdentifier' );
				$attributePlacement = $classAttributeNode->getAttribute( 'placement' );

				//ajout Damien Venot Noven - 13/11/2009 Gestion de la suppression d'attributs
				$attributeDelete = (strtolower( $classAttributeNode->getAttribute( 'deleteAttribute' ) ) == 'true') ? true : false;

				$attributeNameListObject = $classAttributeNode->getElementsByTagName( 'Names' )->item( 0 );
				if ( $attributeNameListObject->hasAttributes() )
				{
					if ( $attributeNameListObject->hasAttributes())
					{
						$attributes = $attributeNameListObject->attributes;
						if ( !is_null($attributes) )
						{
							$attributeNameList = array();
							foreach ( $attributes as $index=>$attr )
							{
								$attributeNameList[$attr->name] = $attr->value;
							}
						}
					}
				}

				$classAttributeNameList = new eZContentClassNameList( serialize($attributeNameList) );
				$classAttributeNameList->validate( );

				$attributeDatatypeParameterNode = $classAttributeNode->getElementsByTagName( 'DatatypeParameters' )->item( 0 );
				$classAttribute = $class->fetchAttributeByIdentifier( $attributeIdentifier );

				//modif Damien Venot Noven - 13/11/2009 -  Gestion de la suppression d'attributs - Cas nouvel attribut
				if ( !array_key_exists( $attributeIdentifier, $classDataMap ) && !$attributeDelete )
				{
					$this->writeMessage( "\t\tClass '$classIdentifier' will get new Attribute '$attributeIdentifier'.", 'notice' );

					$params = array();
					$params['identifier']               = $attributeIdentifier;
					$params['serialized_name_list']     = $classAttributeNameList->serializeNames();
					$params['data_type_string']         = $attributeDatatype;
					$params['default_value']            = '';
					$params['can_translate']            = $attributeIsTranslatable;
					$params['is_required']              = $attributeIsRequired;
					$params['is_searchable']            = $attributeIsSearchable;
					$params['content']                  = '';
					$params['placement']                = $attributePlacement;
					$params['is_information_collector'] = $attributeIsInformationCollector;
					$params['datatype-parameter']       = $attributeDatatypeParameterNode;
					$params['attribute-node']           = $classAttributeNode;

					$this->addClassAttribute( $class, $params );
				}
				//ajout Damien Venot Noven - 13/11/2009 -  Gestion de la suppression d'attributs - Cas attribut deja supprimé
				elseif(!array_key_exists( $attributeIdentifier, $classDataMap ) && $attributeDelete)
				{
					$this->writeMessage( "\t\tAttribute '$attributeIdentifier' From Class '$classIdentifier' was already deleted.", 'notice' );
				}
				//ajout Damien Venot Noven - 13/11/2009 -  Gestion de la suppression d'attributs - Cas attribut à supprimer
				elseif(array_key_exists( $attributeIdentifier, $classDataMap ) && $attributeDelete)
				{
					if ($classAttribute->removeThis())
					{
						$this->writeMessage( "\t\tAttribute '$attributeIdentifier' From Class '$classIdentifier' has been deleted.", 'notice' );
					}
				}
				//  Cas attribut à modifier
				else
				{
					$this->writeMessage( "\t\tClass '$classIdentifier' will get updated Attribute '$attributeIdentifier'.", 'notice' );
					foreach ( $attributeNameList as $lang => $name )
					{
						if ( in_array( $lang, $availableLanguageList ) )
						{
							$classAttribute->setName( $name, $lang );
						}
					}
					$classAttribute->setAttribute( 'is_required', $attributeIsRequired ? 1 : 0 );

					if (!empty($attributePlacement))
						$classAttribute->setAttribute( 'placement', $attributePlacement  );

					if (!empty($attributeDatatypeParameterNode) && $attributeDatatype == 'ezobjectrelationlist')
					{
						$dataType = $classAttribute->dataType();
						if ( $dataType )
        				{
					        $dataType->initializeClassAttribute( $classAttribute );
					        $classAttribute->store();
					        $dataType->unserializeContentClassAttribute( $classAttribute, $classAttributeNode, $attributeDatatypeParameterNode );
					        $classAttribute->sync();
       					 }
					}

					//udate attribute identifier
					if (!empty($attributeNewIdentifier) && $class->fetchAttributeByIdentifier( $attributeNewIdentifier ) == null)
					{
						$this->writeMessage( "\t\tAttribute '$attributeIdentifier' become '$attributeNewIdentifier' ");
						$classAttribute->setAttribute('identifier', $attributeNewIdentifier);

						unset($classDataMap[$attributeIdentifier]);
					}

					$classAttribute->store();
					/* TODO update! */
				}

				$classNameList->store( $class );
			}

			// Adjust attributes placement
			$aClassAttributeList = $class->fetchAttributes();
			$class->adjustAttributePlacements( $aClassAttributeList );
			foreach( $aClassAttributeList as $attr )
	        {
	            $attr->storeDefined();
	        }

			// add class to a class group
			$classGroupsList = $classGroupsNode->getElementsByTagName( 'Group' );
			foreach ( $classGroupsList as $classGroupNode )
			{
				$classGroupName = $classGroupNode->getAttribute( 'name' );
				$classGroup = eZContentClassGroup::fetchByName( $classGroupName );
				if ( !$classGroup )
				{
					$classGroup = eZContentClassGroup::create();
					$classGroup->setAttribute( 'name', $classGroupName );
					$classGroup->store();
				}
				$classGroup->appendClass( $class );
			}

			if ( $referenceID )
			{
				$refArray[$referenceID] = $class->attribute( 'id' );
			}
		}
		$this->addReference( $refArray );
		eZContentCacheManager::clearAllContentCache();

	}

	static public function handlerInfo()
	{
		return array( 'XMLName' => 'CreateOrUpdateClass', 'Info' => 'create content class' );
	}

    /**
     * Surcharge de eZCreateClass::addClassAttribute
     * Supprime simplement le reajustement de l'ordre des attributs.
     * Le reajustement de l'ordre des attributs est positionne dans la methode execute() une fois tous les attributs mis a jour
     * (non-PHPdoc)
     * @see extension/ezxmlinstaller/xmlinstallerhandler/eZCreateClass#addClassAttribute($class, $params)
     */
	function addClassAttribute( $class, $params )
    {
        $classID = $class->attribute( 'id' );

        $classAttributeIdentifier = $params['identifier'];
        $classAttributeName = $params['serialized_name_list'];

        $datatype = $params['data_type_string'];
        $defaultValue = isset( $params['default_value'] ) ? $params['default_value'] : false;
        $canTranslate = isset( $params['can_translate'] ) ? $params['can_translate'] : 1;
        $isRequired   = isset( $params['is_required']   ) ? $params['is_required'] : 0;
        $isSearchable = isset( $params['is_searchable'] ) ? $params['is_searchable'] : 0;
        $attrContent  = isset( $params['content'] )       ? $params['content'] : false;

        $attrCreateInfo = array( 'identifier' => $classAttributeIdentifier,
                                    'serialized_name_list' => $classAttributeName,
                                    'can_translate' => $canTranslate,
                                    'is_required' => $isRequired,
                                    'is_searchable' => $isSearchable );
        $newAttribute = eZContentClassAttribute::create( $classID, $datatype, $attrCreateInfo  );

        $dataType = $newAttribute->dataType();
        if ( !$dataType )
        {
            $this->writeMessage( "\t\tUnknown datatype: '$datatype'", 'error' );
            return false;
        }
        $dataType->initializeClassAttribute( $newAttribute );
        $newAttribute->store();
        $dataType->unserializeContentClassAttribute( $newAttribute, $params['attribute-node'], $params['datatype-parameter'] );
        $newAttribute->sync();


        // not all datatype can have 'default_value'. do check here.
        if( $defaultValue !== false  )
        {
            switch( $datatype )
            {
                case 'ezboolean':
                {
                    $newAttribute->setAttribute( 'data_int3', $defaultValue );
                }
                break;

                default:
                    break;
            }
        }

        if( $attrContent )
            $newAttribute->setContent( $attrContent );

        // store attribute, update placement, etc...
        $attributes = $class->fetchAttributes();
        $attributes[] = $newAttribute;

        // remove temporary version
        if ( $newAttribute->attribute( 'id' ) !== null )
        {
            $newAttribute->remove();
        }

        $newAttribute->setAttribute( 'version', eZContentClass::VERSION_STATUS_DEFINED );
        $newAttribute->setAttribute( 'placement', isset( $params['placement'] ) ?  $params['placement']  : count( $attributes ) );

//        $class->adjustAttributePlacements( $attributes );
        foreach( $attributes as $attribute )
        {
            $attribute->storeDefined();
        }

        // update objects
        $classAttributeID = $newAttribute->attribute( 'id' );
        $objects = eZContentObject::fetchSameClassList( $classID );
        foreach( $objects as $object )
        {
            $contentobjectID = $object->attribute( 'id' );
            $objectVersions = $object->versions();
            foreach( $objectVersions as $objectVersion )
            {
                $translations = $objectVersion->translations( false );
                $version = $objectVersion->attribute( 'version' );
                foreach( $translations as $translation )
                {
                    $objectAttribute = eZContentObjectAttribute::create( $classAttributeID, $contentobjectID, $version );
                    $objectAttribute->setAttribute( 'language_code', $translation );
                    $objectAttribute->initialize();
                    $objectAttribute->store();
                    $objectAttribute->postInitialize();
                }
            }
        }
    }
}
?>