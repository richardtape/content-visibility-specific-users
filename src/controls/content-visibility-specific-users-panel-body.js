import { PanelBody, PanelRow } from '@wordpress/components';
import { withInstanceId } from '@wordpress/compose';
import { __ } from '@wordpress/i18n';

import { ContentVisibilitySpecificUsersMultiSelect } from '../controls/content-visibility-specific-users-multiselect';

/**
 * PHP sends through a list of all the users on the site. We massage that data to be
 * usable by our Dropdown.
 *
 */
function getUsers() {

    const users = [];

    if ( BlockVisibilitySpecificUsers.users.length === 0 ) {
        return [ {
            label: __( 'No users found.', 'content-visibility' ),
            value: 0,
            notes: '',
        } ]
    }

    for ( const user in BlockVisibilitySpecificUsers.users ) {
    
        users.push( { 
            label: BlockVisibilitySpecificUsers.users[user].display_name + '(ID: ' + BlockVisibilitySpecificUsers.users[user].ID + ')',
            value: BlockVisibilitySpecificUsers.users[user].ID,
        } );
    
    }

    return users;

}// end getUsers()

function ContentVisibilitySpecificUsersBodyControl( { instanceId, props } ) {

    const data = getUsers();
    const type = 'specificusers';

    return (
        <PanelBody
            title={ __( 'Specific Users', 'content-visibility-specific-users' ) }
            initialOpen={ false }
            className="content-visibility-control-panel content-visibility-specific-users-controls"
        >
            <PanelRow>
                <ContentVisibilitySpecificUsersMultiSelect data={ data } labelledBy="Select Specific Users" props={ props } type={ type } />
            </PanelRow>

            { props.attributes.contentVisibility && (
                <p className="specific-users-help-intro content-visibility-help-text">{ __( 'Select one or more users to whom this block will be ' + props.attributes.contentVisibility + '. If no users are selected, this block will be ' + props.attributes.contentVisibility + ' to all users.', 'content-visibility-specific-users' ) }</p>
            ) }
        </PanelBody>
    );

}

export default withInstanceId( ContentVisibilitySpecificUsersBodyControl );