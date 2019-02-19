import React, { Component } from 'react';
import { Button, Modal, ModalHeader, ModalFooter } from 'reactstrap';
import { Formik, Form } from 'formik';
import PackageModal from './modals/PackageModal';
import { toast } from 'react-toastify';
import { options } from '../helpers/toast';
import { scrollModalTop } from '../helpers/scrolling';

class EditPackage extends React.PureComponent {
	state = {
		modalIsOpen: false,
		showError: false,
		errorMessage: null

		// services: {
		//   providerServices: [{ service: null, name: 'Chose provider service' }],
		//   errorService: null,
		//   messageService: null
		// }
	};

	// choseService = async provider_id => {
	//   if (provider_id !== 'none') {
	//     var response = await get_providers_services(provider_id);
	//     response.data.unshift({ service: null, name: 'Chose provider service' });
	//     const error = response.data[1].error;
	//     const message = response.data[1].message;
	//     this.setState(prevState => ({
	//       ...prevState,
	//       services: {
	//         providerServices: response.data,
	//         errorService: error,
	//         messageService: message
	//       }
	//     }));
	//   } else {
	//     this.setState(prevState => ({
	//       ...prevState,
	//       services: {
	//         providerServices: [{ service: null, name: 'Chose provider service' }]
	//       }
	//     }));
	//   }
	// };

	getPackage() {
		this.setState((prevstate) => ({
			modalIsOpen: !prevstate.modalIsOpen
		}));
		// const services = this.props.response.providerServices;
		// this.setState(prevState => ({
		//   ...prevState,
		//   services: {
		//     providerServices: services
		//   }
		// }));
		// console.log(services);
	}

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
			toast('Package was successfully updated!', options);
		}
	};

	componentDidMount(...params) {
		this.props.getPackage(...params);
	}

	render() {
		const { response, providers, choseProviders } = this.props;
		return (
			<React.Fragment>
				<Button
					onClick={() => {
						this.getPackage();
						this.props.getPackage();
					}}
					color="primary"
					size="sm"
					className="m-btn--pill sommerce_dragtable__action m-btn--air"
					active
				>
					Edit
				</Button>
				<Modal
					innerRef={(el) => (this.modal = el)}
					isOpen={this.state.modalIsOpen}
					backdrop="static"
					keyboard={true}
					toggle={this.toggle}
				>
					<Formik onSubmit={this.handleSubmit} enableReinitialize={true} initialValues={response.package}>
						{({ setFieldValue }) => (
							<Form>
								<ModalHeader toggle={this.toggle}>Edit package (ID: {response.package.id})</ModalHeader>
								<PackageModal
									setFieldValue={setFieldValue}
									showError={this.state.showError}
									errorMessage={this.state.errorMessage}
									providers={providers}
									choseProviders={choseProviders}
									services={response.services}
								/>
								<ModalFooter className="justify-content-start">
									<Button color="primary" type="submit">
										Save package
									</Button>{' '}
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

export default EditPackage;
