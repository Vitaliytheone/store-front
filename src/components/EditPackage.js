import React, { Component } from 'react';
import { Button, Modal, ModalHeader, ModalFooter } from 'reactstrap';
import { Formik, Form } from 'formik';
import PackageModal from './modals/PackageModal';

class EditPackage extends Component {
	state = {
		modalIsOpen: false
	};

	GetPackage = (...params) => {
		this.setState((prevstate) => ({
			modalIsOpen: !prevstate.modalIsOpen
		}));
		this.props.getPackage(...params);
	};

	toggle = () => {
		this.setState((prevstate) => ({
			modalIsOpen: !prevstate.modalIsOpen
		}));
	};

	handleSubmit = (...params) => {
		this.setState({
			modalIsOpen: false
		});
		this.props.onSubmit(...params);
	};

	render() {
		const { response } = this.props;
		return (
			<React.Fragment>
				<Button
					onClick={this.GetPackage}
					color="primary"
					size="sm"
					className="m-btn--pill sommerce_dragtable__action"
				>
					Edit
				</Button>
				<Modal isOpen={this.state.modalIsOpen} backdrop="static" keyboard={false}>
					<Formik onSubmit={this.handleSubmit} enableReinitialize={true} initialValues={response.package}>
						<Form>
							<ModalHeader toggle={this.toggle}>Edit package (ID: {response.package.id})</ModalHeader>
							<PackageModal />
							<ModalFooter className="justify-content-start">
								<Button color="primary" type="submit">
									Save package
								</Button>{' '}
								<Button color="secondary" onClick={this.toggle}>
									Cancel
								</Button>
							</ModalFooter>
						</Form>
					</Formik>
				</Modal>
			</React.Fragment>
		);
	}
}

export default EditPackage;
