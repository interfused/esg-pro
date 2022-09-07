
import { InspectorControls, BlockControls } from '@wordpress/block-editor';
import { Disabled, PanelBody, PanelRow, SelectControl } from '@wordpress/components';
import { ServerSideRender } from '@wordpress/editor';
import { Component } from '@wordpress/element';
import { __, _e } from '@wordpress/i18n';

const plugin = 'acf-frontend-form-element';

class FormSelect extends Component {

    constructor(props) {
		super(props);
		this.state = {
			editMode: true,
            formID: 0
		}
	}
 
	getInspectorControls = () => {
		const { attributes, setAttributes } = this.props;
 
        
        let choices = [];
        if (this.props.posts) {
            choices.push({ value: 0, label: __( 'Select a form', plugin ) });
            this.props.posts.forEach(post => {
                choices.push({ value: post.id, label: post.title.rendered });
            });
        } else {
            choices.push({ value: 0, label: __( 'Loading...', plugin ) })
        }

		return (
			<InspectorControls 
                key='fea-inspector-controls'
                >
                <PanelBody
						title={__("Form Settings", plugin )}
						initialOpen={true}
					>
						<PanelRow>
                        <SelectControl
                            label={__('Form', plugin )}
                            options={choices}
                            value={attributes.formID}
                            onChange={(newval) => setAttributes({ formID: parseInt(newval) })}
                        />
                        </PanelRow>
                </PanelBody>
			</InspectorControls>
		);
	}
 
	getBlockControls = () => {
        const { attributes, setAttributes } = this.props;        
        let choices = [];
        if (this.props.posts) {
            choices.push({ value: 0, label: __( 'Select a form', plugin ) });
            this.props.posts.forEach(post => {
                choices.push({ value: post.id, label: post.title.rendered });
            });
        } else {
            choices.push({ value: 0, label: __( 'Loading...', plugin ) })
        }
        return (
            <BlockControls 
                key='fea-block-controls'
                >
                <SelectControl
                    options={choices}
                    value={attributes.formID}
                    onChange={(newval) => setAttributes({ formID: parseInt(newval) })}
                />                       
            </BlockControls>
        );
    }
 
	render() {
        const { attributes, setAttributes } = this.props;
        const alignmentClass = (attributes.textAlignment != null) ? 'has-text-align-' + attributes.textAlignment : '';
        return ([
            this.getInspectorControls(),
            this.getBlockControls(),
            <Disabled 
                key='fea-disabled-render'
                >
                <ServerSideRender
                    block={this.props.name}
                    attributes={{ 
                        formID: attributes.formID,
                        editMode: this.state.editMode
                    }}
                />
            </Disabled>            

        ]);
    }
}
 
export default FormSelect;