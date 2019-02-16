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
		modalIsOpen: false,
		showError: false,
		errorMessage: null,

		services: {
			providerServices: [{ service: null, name: 'Chose provider service' }],
			errorService: null,
			messageService: null
		}
	};

	choseService = async (provider_id) => {
		if (provider_id !== 'none') {
			var response = await get_providers_services(provider_id);
			response.data.unshift({ service: null, name: 'Chose provider service' });
			const error = response.data[1].error;
			const message = response.data[1].message;
			this.setState(prevstate => ({
				services: {
					...prevstate,
					providerServices: response.data,
					errorService: error,
					messageService: message
				}
			}));
		} else {
			this.setState(prevstate => ({
				services: {
					...prevstate,
					providerServices: [{ service: null, name: 'Chose provider service' }]
				}
			}));
		}
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

	render() {
		return (
			<React.Fragment>
				<Row>
					<Col className="mt-2 mb-3">
						<Button color="primary" className="m-btn--air" size="sm" onClick={this.toggle}>
							Add package
						</Button>
					</Col>
				</Row>
				<Modal
					innerRef={(el) => (this.modal = el)}
					isOpen={this.state.modalIsOpen}
					toggle={this.toggle}
					backdrop="static"
					keyboard={true}
				>
					<Formik onSubmit={this.handleSubmit} initialValues={this.props.initialValues}>
						{({ setFieldValue}) => (
						<Form>
							<ModalHeader toggle={this.toggle}>Create package</ModalHeader>
							<PackageModal 
								setFieldValue={setFieldValue}
								showError={this.state.showError}
								errorMessage={this.state.errorMessage}
								providers={this.props.providers}
								choseService={this.choseService}
								services={this.state.services}
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
						)}
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
		mode: PropTypes.string
	})
};

AddPackage.defaultProps = {
	initialValues: {
		name: '',
		price: 0,
		quantity: 0,
		overflow: 0,
		best: '2',
		link_type: '1',
		visibility: '1',
		mode: '2',
		provider_id: 'none',
		provider_service: 'none'
	}
};

export default AddPackage;
