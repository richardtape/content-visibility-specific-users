import { Fill, Disabled } from '@wordpress/components';
import { __ } from '@wordpress/i18n';
import { registerPlugin } from '@wordpress/plugins';
import { addFilter } from '@wordpress/hooks';

import ContentVisibilitySpecificUsersBodyControl from './content-visibility-specific-users-panel-body';

export function ContentVisibilitySpecificUsersControl( data ) {

    let { props } = { ...data };

    let rulesEnabled    = props.attributes.contentVisibilityRules.contentVisibilityRulesEnabled;
    let contentVisibility = props.attributes.hasOwnProperty( 'contentVisibility' );

    if ( ! rulesEnabled || ! contentVisibility ) {
        return (
            <Disabled><ContentVisibilitySpecificUsersBodyControl props={ props } /></Disabled>
        );
    }

    return (
        <ContentVisibilitySpecificUsersBodyControl props={ props } />
    );

}

/**
 * Render the <ContentVisibilitySpecificUsersControl> component by adding
 * it to the block-visibility-extra-controls Fill.
 *
 * @return {Object} A Fill component wrapping the ContentVisibilitySpecificUsersControl component.
 */
function ContentVisibilitySpecificUsersFill() {
    return (
        <Fill name="content-visibility-extra-controls">
            {
                ( fillProps ) => {
                    return (
                        <ContentVisibilitySpecificUsersControl props={ fillProps } />
                    )
                }
            }
        </Fill>
    );

}

// Add our component to the Slot provided by BlockVisibilityControls
registerPlugin( 'content-visibility-06-specific-users-fill', { render: ContentVisibilitySpecificUsersFill } );


// Register our visibility rule with the main plugin
addFilter( 'contentVisibility.defaultContentVisibilityRules', 'content-visibility-specific-users/block-visibility-rules', registerContentVisibilityRule );

function registerContentVisibilityRule( defaultRules ) {

    defaultRules.specificusers = {};

    return defaultRules;

}
