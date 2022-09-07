import { registerBlockType } from '@wordpress/blocks';
import { withSelect } from '@wordpress/data';
import { __, _e } from '@wordpress/i18n';
import { FormSelect } from './components/form-select';

const plugin = 'acf-frontend-form-element';

registerBlockType('acf-frontend/form', {
	title: __('Frontend Form', plugin ),
	category: 'frontend-admin',
	icon: 'feedback',
	description: __('Display a frontend admin form so that your users can update content from the frontend.', plugin ),
	keywords: ['frontend editing', 'admin form'],
	attributes: {
        formID: {
            type: 'number'
        },
        editMode: {
            type: 'boolean',
            default: true,
        }
    },
	edit: withSelect(select => {
        const query = {
            per_page: 10,
            status: 'any',
        }
        return {
            posts: select('core').getEntityRecords('postType', 'admin_form', query)
        }
    })(FormSelect),
    save: () => { return null }

});

registerBlockType('acf-frontend/submissions', {
	title: __('Frontend Submissions', plugin ),
	category: 'frontend-admin',
	icon: 'feedback',
	description: __('Display frontend submissions so that site admins can update content from the frontend.', plugin ),
	keywords: ['frontend editing', 'admin form'],
	attributes: {
        formID: {
            type: 'number'
        },
        editMode: {
            type: 'boolean',
            default: true,
        }
    },
	edit: withSelect(select => {
        const query = {
            per_page: 10,
            status: 'any',
        }
        return {
            posts: select('core').getEntityRecords('postType', 'admin_form', query)
        }
    })(FormSelect),
    save: () => { return null }

});