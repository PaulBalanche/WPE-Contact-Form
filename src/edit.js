import { TextControl, TextareaControl, SelectControl, RadioControl } from '@wordpress/components';
import { useState } from '@wordpress/element';

export function Edit( { props } ) {

    const [ label, setLabel ] = useState( typeof props.attributes['label'] != 'undefined' ? props.attributes['label'] : '' );
    const [ type, setType ] = useState( typeof props.attributes['type'] != 'undefined' ? props.attributes['type'] : 'text' );

    const render = [];

    if( props.isSelected ) {
        render.push(<>
            <TextControl
                key={ props.clientId + "_selected_label" }
                label="Label"
                value={ label }
                onChange={ (newValue) => {
                    setLabel(newValue);
                    props.setAttributes( { label: newValue } );
                } }
            />
            <RadioControl
                key={ props.clientId + "_selected_type" }
                label="Type"
                selected={type}
                options={ [
                    { label: 'Text', value: 'text' },
                    { label: 'Textarea', value: 'textarea' },
                    { label: 'Select', value: 'select' }
                ] }
                onChange={ (newValue) => {
                    setType(newValue);
                    props.setAttributes( { type: newValue } );
                } }
            />
        </>);
    }
    else {

        switch(type) {
            case 'text':
                render.push(<TextControl
                    key={ props.clientId + "_label" }
                    label={label}
                />);
                break;

            case 'textarea':
                render.push(<TextareaControl
                    key={ props.clientId + "_type" }
                    label={label}
                />);
                break;
            
            case 'select':
                render.push(<SelectControl
                    key={ props.clientId + "_type" }
                    label={label}
                    options={ [
                        { label: 'Text', value: 'text' },
                        { label: 'Textarea', value: 'textarea' },
                        { label: 'Select', value: 'select' }
                    ] }
                />);
                break;
        }
    }

    return <div
        key={ props.clientId + "_container" }
        style={{'padding': '1rem', 'margin': '1rem'}}
    >{render}</div>
}