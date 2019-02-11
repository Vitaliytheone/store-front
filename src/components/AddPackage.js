import React, { Component } from 'react';
import { Button, Modal, ModalHeader, ModalFooter, Col, Row } from 'reactstrap';
import { Formik, Form } from 'formik';
import PackageModal from './modals/PackageModal';
import PropTypes from 'prop-types';
import { toast } from 'react-toastify';
import { options } from '../helpers/toast';
import { scrollModalTop } from '../helpers/scrolling';
import { get_providers_services } from '../services/url';

class AddPackage extends Component {
	state = {
		providerServices: [],
		modalIsOpen: false,
		showError: false,
		errorMessage: null
	};

	toggle = () => {
		this.setState((prevstate) => ({
			modalIsOpen: !prevstate.modalIsOpen,
			showError: false,
			errorMessage: null
		}));
	};

	handleSubmit = async (...params) => {
		const response = await this.props.onSubmit(...params);
		this.setState({
			showError: !response.success,
			modalIsOpen: !response.success,
			errorMessage: response.error_message
		});
		if (this.state.showError) {
			scrollModalTop(this.modal);
		} else {
			toast('Package was successfully created!', options);
		}
	};

	choseService = (provider_id) => async () => {
		console.log(provider_id);
		const response = await get_providers_services(provider_id);
		console.log(response);
	};

	render() {
		console.log(this.props.providers);
		return (
			<React.Fragment>
				<Row>
					<Col className="mt-2 mb-3">
						<Button color="primary" size="sm" onClick={this.toggle}>
							Add package
						</Button>
					</Col>
				</Row>
				<Modal
					innerRef={(el) => (this.modal = el)}
					isOpen={this.state.modalIsOpen}
					toggle={this.toggle}
					backdrop="static"
					keyboard={false}
				>
					<Formik onSubmit={this.handleSubmit} initialValues={this.props.initialValues}>
						<Form>
							<ModalHeader toggle={this.toggle}>Create package</ModalHeader>
							<PackageModal
								showError={this.state.showError}
								errorMessage={this.state.errorMessage}
								providers={this.props.providers}
								choseService={this.choseService}
							/>
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
		provider_id: 'none',
		provider_service_id: ''
	}
};

export default AddPackage;
