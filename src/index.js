import { registerBlockType } from '@wordpress/blocks';
import { InnerBlocks, useBlockProps } from '@wordpress/block-editor';
import { Edit } from './edit';

let attributes = {
    label: {
        type: 'string',
    },
    type: {
        type: 'string',
    },
};


registerBlockType('custom/wpe-contact-form-field', {
    title: 'Field',
    category: 'wpe-layout',
    attributes: attributes,
    edit: ( { attributes, setAttributes, isSelected } ) => {
        return <Edit
            attributes={attributes}
            setAttributes={setAttributes}
            isSelected={isSelected}
        />
    },
    save: () => {
        return (
            <div {...useBlockProps.save()}>
                <InnerBlocks.Content />
            </div>
        );
    },
});
