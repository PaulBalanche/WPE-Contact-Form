import { TextControl, SelectControl } from '@wordpress/components';
import { useState } from '@wordpress/element';

export function Edit( { attributes, setAttributes, isSelected } ) {

    const [ label, setLabel ] = useState( typeof attributes['label'] != 'undefined' ? attributes['label'] : '' );
    const [ type, setType ] = useState( typeof attributes['type'] != 'undefined' ? attributes['type'] : 'text' );

    return (<>
            <TextControl
                label="Label"
                value={ label }
                onChange={ (newValue) => {
                    setLabel(newValue);
                    setAttributes( { label: newValue } );
                } }
            />
            <SelectControl
                label="Type"
                value={ type }
                options={ [
                    { label: 'Text', value: 'text' },
                    { label: 'Textarea', value: 'textarea' }
                ] }
                onChange={ (newValue) => {
                    setType(newValue);
                    setAttributes( { type: newValue } );
                } }
            />
        </>
    );
}