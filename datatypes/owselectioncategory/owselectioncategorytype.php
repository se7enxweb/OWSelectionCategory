<?php

/*!
  \class   OWSelectionCategoryType OWSelectionCategoryType.php
  \ingroup eZDatatype
  \brief   Handles the category ans sub category.
  \date    26/12/2013 17:38:45 pm
  \author  jrad anis

*/
require_once( 'kernel/common/i18n.php' );

class OWSelectionCategoryType extends eZDataType
{
    const DATA_TYPE_STRING = "owselectioncategory";

    /*!
      Constructor
    */
    function OWSelectionCategoryType()
    {
        $this->eZDataType( self::DATA_TYPE_STRING, ezpI18n::tr( 'kernel/classes/datatypes', "Category Selection", 'Datatype name' ),
                           array( 'serialize_supported' => true ) );
    }

    /*!
     Validates all variables given on content class level
     \return eZInputValidator::STATE_ACCEPTED or eZInputValidator::STATE_INVALID if
             the values are accepted or not
    */
    function validateClassAttributeHTTPInput( $http, $base, $contentObjectAttribute )
    {
        return eZInputValidator::STATE_ACCEPTED;
    }

    /*!
     Fetches all variables inputed on content class level
     \return true if fetching of class attributes are successfull, false if not
    */
    function fetchClassAttributeHTTPInput( $http, $base, $classAttribute )
    {
        $attributeContent = $this->classAttributeContent( $classAttribute );
        $classAttributeID = $classAttribute->attribute( 'id' );

        $currentOptions = $attributeContent['options'];
        $hasPostData = false;

        if ( $http->hasPostVariable( $base . "_owselectioncategory_option_name_array_" . $classAttributeID ) )
        {
            $nameArray = $http->postVariable( $base . "_owselectioncategory_option_name_array_" . $classAttributeID );

            // Fill in new names for options
            foreach ( array_keys( $currentOptions ) as $key )
            {
                $currentOptions[$key]['name'] = $nameArray[$currentOptions[$key]['id']];
            }
            $hasPostData = true;

        }

        if ( $http->hasPostVariable( $base . "_owselectioncategory_newoption_button_" . $classAttributeID ) )
        {
            $currentCount = 0;
            foreach ( $currentOptions as $option )
            {
                $currentCount = max( $currentCount, $option['id'] );
            }
            $currentCount += 1;
            $currentOptions[] = array( 'id' => $currentCount,
                                       'name' => '',
                                        'group' => '0'
                                    );
            $hasPostData = true;

        }

        if ( $http->hasPostVariable( $base . "_owselectioncategory_newgroupoption_button_" . $classAttributeID ) )
        {
            $currentCount = 0;
            foreach ( $currentOptions as $option )
            {
                $currentCount = max( $currentCount, $option['id'] );
            }
            $currentCount += 1;
            $currentOptions[] = array( 'id' => $currentCount,
                'name' => '',
                'group' => '1'
            );
            $hasPostData = true;

        }


        if ( $http->hasPostVariable( $base . "_owselectioncategory_removeoption_button_" . $classAttributeID ) )
        {
            if ( $http->hasPostVariable( $base . "_owselectioncategory_option_remove_array_". $classAttributeID ) )
            {
                $removeArray = $http->postVariable( $base . "_owselectioncategory_option_remove_array_". $classAttributeID );

                foreach ( array_keys( $currentOptions ) as $key )
                {
                    if ( isset( $removeArray[$currentOptions[$key]['id']] ) and
                         $removeArray[$currentOptions[$key]['id']] )
                        unset( $currentOptions[$key] );
                }
                $hasPostData = true;
            }
        }

        if ( $hasPostData )
        {

            // Serialize XML
            $doc = new DOMDocument( '1.0', 'utf-8' );
            $root = $doc->createElement( "owselectioncategory" );
            $doc->appendChild( $root );
var_dump($currentOptions);
            $options = $doc->createElement( "options" );
            $root->appendChild( $options );
            foreach ( $currentOptions as $optionArray )
            {
                unset( $optionNode );
                $optionNode = $doc->createElement( "option" );
                $optionNode->setAttribute( 'id', $optionArray['id'] );
                $optionNode->setAttribute( 'name', $optionArray['name'] );
                $optionNode->setAttribute( 'group', $optionArray['group'] );

                $options->appendChild( $optionNode );
            }

            $xml = $doc->saveXML();

            $classAttribute->setAttribute( "data_text5", $xml );

        }
        return true;
    }
    /*!
     Validates input on content object level
     \return eZInputValidator::STATE_ACCEPTED or eZInputValidator::STATE_INVALID if
             the values are accepted or not
    */
    function validateObjectAttributeHTTPInput( $http, $base, $contentObjectAttribute )
    {
        $classAttribute = $contentObjectAttribute->contentClassAttribute();

        if ( $http->hasPostVariable( $base . '_ezselect_selected_array_' . $contentObjectAttribute->attribute( 'id' ) ) )
        {
            $data = $http->postVariable( $base . '_ezselect_selected_array_' . $contentObjectAttribute->attribute( 'id' ) );

            if ( $data == "" )
            {
                if ( !$classAttribute->attribute( 'is_information_collector' ) &&
                     $contentObjectAttribute->validateIsRequired() )
                {
                    $contentObjectAttribute->setValidationError( ezpI18n::tr( 'kernel/classes/datatypes',
                                                                         'Input required.' ) );
                    return eZInputValidator::STATE_INVALID;
                }
            }
        }
        else if ( !$classAttribute->attribute( 'is_information_collector' ) && $contentObjectAttribute->validateIsRequired() )
        {
            $contentObjectAttribute->setValidationError( ezpI18n::tr( 'kernel/classes/datatypes', 'Input required.' ) );
            return eZInputValidator::STATE_INVALID;
        }
        return eZInputValidator::STATE_ACCEPTED;
    }

    /*!
     Fetches all variables from the object
     \return true if fetching of class attributes are successfull, false if not
    */
    function fetchObjectAttributeHTTPInput( $http, $base, $contentObjectAttribute )
    {
        if ( $http->hasPostVariable( $base . '_ezselect_selected_array_' . $contentObjectAttribute->attribute( 'id' ) ) )
        {
            $selectOptions = $http->postVariable( $base . '_ezselect_selected_array_' . $contentObjectAttribute->attribute( 'id' ) );
            $idString = ( is_array( $selectOptions ) ? implode( '-', $selectOptions ) : "" );
            $contentObjectAttribute->setAttribute( 'data_text', $idString );
            return true;
        }
        return false;
    }

    function validateCollectionAttributeHTTPInput( $http, $base, $contentObjectAttribute )
    {
        if ( $http->hasPostVariable( $base . '_ezselect_selected_array_' . $contentObjectAttribute->attribute( 'id' ) ) )
        {
            $data = $http->postVariable( $base . '_ezselect_selected_array_' . $contentObjectAttribute->attribute( 'id' ) );

            if ( $data == "" && $contentObjectAttribute->validateIsRequired() )
            {
                $contentObjectAttribute->setValidationError( ezpI18n::tr( 'kernel/classes/datatypes', 'Input required.' ) );
                return eZInputValidator::STATE_INVALID;
            }
            else
            {
                return eZInputValidator::STATE_ACCEPTED;
            }
        }
        else
        {
            return eZInputValidator::STATE_INVALID;
        }
    }

   /*!
    Fetches the http post variables for collected information
   */
    function fetchCollectionAttributeHTTPInput( $collection, $collectionAttribute, $http, $base, $contentObjectAttribute )
    {
        if ( $http->hasPostVariable( $base . '_ezselect_selected_array_' . $contentObjectAttribute->attribute( 'id' ) ) )
        {
            $selectOptions = $http->postVariable( $base . '_ezselect_selected_array_' . $contentObjectAttribute->attribute( 'id' ) );
            $idString = ( is_array( $selectOptions ) ? implode( '-', $selectOptions ) : "" );
            $collectionAttribute->setAttribute( 'data_text', $idString );
            return true;
        }
        return false;
    }

    /*!
     Sets the default value.
    */
    function initializeObjectAttribute( $contentObjectAttribute, $currentVersion, $originalContentObjectAttribute )
    {
        if ( $currentVersion != false )
        {
            $idString = $originalContentObjectAttribute->attribute( "data_text" );
            $contentObjectAttribute->setAttribute( "data_text", $idString );
            $contentObjectAttribute->store();
        }
    }

    /*!
     Returns the selected options by id.
    */
    function objectAttributeContent( $contentObjectAttribute )
    {
        $idString = explode( '-', $contentObjectAttribute->attribute( 'data_text' ) );
        return $idString;
    }

    /*!
     Returns the content data for the given content class attribute.
    */
    function classAttributeContent( $classAttribute )
    {
        $dom = new DOMDocument( '1.0', 'utf-8' );
        $xmlString = $classAttribute->attribute( 'data_text5' );
        $optionArray = array();
        if ( $xmlString != '' )
        {
            $success = $dom->loadXML( $xmlString );
            if ( $success )
            {
                $options = $dom->getElementsByTagName( 'option' );

                foreach ( $options as $optionNode )
                {
                    $optionArray[] = array( 'id' => $optionNode->getAttribute( 'id' ),
                                            'name' => $optionNode->getAttribute( 'name' ),
                                            'group' => $optionNode->getAttribute( 'group' ) );
                }
            }
        }

        if ( count( $optionArray ) == 0 )
        {
            $optionArray[] = array( 'id' => 0,
                                    'name' => '',
                                    'group' => '1');
        }
        $attrValue = array( 'options' => $optionArray);
        return $attrValue;
    }

    /*!
     Returns the meta data used for storing search indeces.
    */
    function metaData( $contentObjectAttribute )
    {
        $selected = $this->objectAttributeContent( $contentObjectAttribute );
        $classContent = $this->classAttributeContent( $contentObjectAttribute->attribute( 'contentclass_attribute' ) );
        $return = '';
        if ( count( $selected ) == 0)
        {
            return '';
        }

        $count = 0;
        $optionArray = $classContent['options'];
        foreach ( $selected as $id )
        {
            if ( $count++ != 0 )
                $return .= ' ';
            foreach ( $optionArray as $option )
            {
                $optionID = $option['id'];
                if ( $optionID == $id )
                    $return .= $option['name'];
            }
        }
        return $return;
    }

    function toString( $contentObjectAttribute )
    {
        $selected = $this->objectAttributeContent( $contentObjectAttribute );
        $classContent = $this->classAttributeContent( $contentObjectAttribute->attribute( 'contentclass_attribute' ) );

        if ( count( $selected ) )
        {
            $returnData = array();
            $optionArray = $classContent['options'];
            foreach ( $selected as $id )
            {
                foreach ( $optionArray as $option )
                {
                    $optionID = $option['id'];
                    if ( $optionID == $id )
                        $returnData[] = $option['name'];
                }
            }
            return eZStringUtils::implodeStr( $returnData, '|' );
        }
        return '';
    }


    function fromString( $contentObjectAttribute, $string )
    {
        if ( $string == '' )
            return true;
        $selectedNames = eZStringUtils::explodeStr( $string, '|' );
        $selectedIDList = array();
        $classContent = $this->classAttributeContent( $contentObjectAttribute->attribute( 'contentclass_attribute' ) );
        $optionArray = $classContent['options'];

        foreach ( $selectedNames as $name )
        {
            $selectParent = eZStringUtils::explodeStr( $name, '#' );
            $parent = null;
            if(count($selectParent) > 0) {
                $name = $selectParent[0];
                $parent = $selectParent[1];
            }

            $vParent = true;
            if(!is_null($parent)) {
                $vParent = false;
            }
            foreach ( $optionArray as $option )
            {
                $optionName = $option['name'];
                if(!is_null($parent)) {
                    if ( $optionName == $parent ) {
                        $vParent = true;
                        continue;
                    }
                }
                if ( $optionName == $name &&  $vParent == true) {
                    $selectedIDList[] = $option['id'];
                    break;
                }
            }
        }
        $idString = ( is_array( $selectedIDList ) ? implode( '-', $selectedIDList ) : "" );
        $contentObjectAttribute->setAttribute( 'data_text', $idString );
        return true;
    }

    /*!
     Returns the value as it will be shown if this attribute is used in the object name pattern.
    */
    function title( $contentObjectAttribute, $name = null )
    {
        $selected = $this->objectAttributeContent( $contentObjectAttribute );
        $classContent = $this->classAttributeContent( $contentObjectAttribute->attribute( 'contentclass_attribute' ) );
        $return = '';
        if ( count( $selected ) )
        {
            $selectedNames = array();
            foreach ( $classContent['options'] as $option )
            {
                if ( in_array( $option['id'], $selected ) )
                    $selectedNames[] = $option['name'];
            }
            $return = implode( ', ', $selectedNames );
        }
        return $return;
    }

    function hasObjectAttributeContent( $contentObjectAttribute )
    {
        $selected = $this->objectAttributeContent( $contentObjectAttribute );
        return isset( $selected[0] ) && $selected[0] != '';
    }

    function sortKey( $contentObjectAttribute )
    {
        return strtolower( $contentObjectAttribute->attribute( 'data_text' ) );
    }

    function sortKeyType()
    {
        return 'string';
    }

    /*!
     \return true if the datatype can be indexed
    */
    function isIndexable()
    {
        return true;
    }

    function isInformationCollector()
    {
        return true;
    }

    function serializeContentClassAttribute( $classAttribute, $attributeNode, $attributeParametersNode )
    {
        $xmlString = $classAttribute->attribute( 'data_text5' );

        $selectionDom = new DOMDocument( '1.0', 'utf-8' );
        $success = $selectionDom->loadXML( $xmlString );
        $domRoot = $selectionDom->documentElement;
        $options = $domRoot->getElementsByTagName( 'options' )->item( 0 );

        $dom = $attributeParametersNode->ownerDocument;

        $importedOptionsNode = $dom->importNode( $options, true );
        $attributeParametersNode->appendChild( $importedOptionsNode );

    }

    function unserializeContentClassAttribute( $classAttribute, $attributeNode, $attributeParametersNode )
    {
        $options = $attributeParametersNode->getElementsByTagName( 'options' )->item( 0 );

        $doc = new DOMDocument( '1.0', 'utf-8' );
        $root = $doc->createElement( 'owselectioncategory' );
        $doc->appendChild( $root );

        $importedOptions = $doc->importNode( $options, true );
        $root->appendChild( $importedOptions );

        $xml = $doc->saveXML();
        $classAttribute->setAttribute( 'data_text5', $xml );
    }

    function supportsBatchInitializeObjectAttribute()
    {
        return true;
    }
}

eZDataType::register( OWSelectionCategoryType::DATA_TYPE_STRING, "OWSelectionCategoryType" );
?>
