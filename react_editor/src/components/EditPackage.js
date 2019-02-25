import React, { Component } from 'react';
import { Button } from 'reactstrap';
import EditPackageModal from './EditPackageModal';

class EditPackage extends React.PureComponent {
	state = {
		modalIsOpen: false,
	};

	toggle = () => {
		this.setState((prevstate) => ({
			modalIsOpen: !prevstate.modalIsOpen
		}));
	};
	
	render() {
		return (
			<React.Fragment>
				<Button
					onClick={() => this.toggle()}
					color="primary"
					size="sm"
					className="m-btn--pill sommerce_dragtable__action m-btn--air"
					active
				>
					Edit
				</Button>
				{this.state.modalIsOpen && <EditPackageModal {...this.props} toggle={this.toggle} />}
			</React.Fragment>
		);
	}
}

export default EditPackage;
