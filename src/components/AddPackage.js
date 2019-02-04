import React, { Component } from 'react';
import { Button, Modal, ModalHeader, ModalFooter, Col, Row } from 'reactstrap';
import { Formik, Form } from 'formik';
import PackageModal from './modals/PackageModal';
import PropTypes from 'prop-types';

class AddPackage extends Component {
	state = {
		modalIsOpen: false,
		showError: false
	};

	toggle = () => {
		this.setState((prevstate) => ({
			modalIsOpen: !prevstate.modalIsOpen
		}));
	};

	handleSubmit = async (...params) => {
		// const response = await Promise.resolve({
		// 	success: false
		// });
		const response = await this.props.onSubmit(...params);
		console.log(response);
		// this.setState({ showError: !response.success, modalIsOpen: !response.success });
	};

	// handleSubmit = (...params) => {
	// 	this.setState({
	// 		modalIsOpen: false
	// 	});
	// 	this.props.onSubmit(...params);
	// };

	render() {
		return (
			<React.Fragment>
				<Row>
					<Col className="mt-2 mb-3">
						<Button color="primary" size="sm" onClick={this.toggle}>
							Add package
						</Button>
					</Col>
				</Row>
				<Modal isOpen={this.state.modalIsOpen} toggle={this.toggle} backdrop="static" keyboard={false}>
					<Formik onSubmit={this.handleSubmit} initialValues={this.props.initialValues}>
						<Form>
							<ModalHeader toggle={this.toggle}>Create package</ModalHeader>
							{/* showError={this.state.showError}  */}
							<PackageModal />
							<ModalFooter className="justify-content-start">
								<Button color="primary" type="submit">
									Add package
								</Button>
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

AddPackage.propTypes = {
	initialValues: PropTypes.shape({
		name: PropTypes.string,
		price: PropTypes.number,
		quantity: PropTypes.number,
		overflow: PropTypes.number,
		availability: PropTypes.string,
		mode: PropTypes.string,
		provider_id: PropTypes.string,
		provider_service_id: PropTypes.string
	})
};

AddPackage.defaultProps = {
	initialValues: {
		name: '',
		price: 0,
		quantity: 0,
		overflow: 0,
		best: '2',
		availability: '1',
		mode: '2',
		provider_id: '',
		provider_service_id: ''
	}
};

export default AddPackage;
