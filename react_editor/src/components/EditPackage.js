import React, { Component } from 'react';
import { Button, Modal, ModalHeader, ModalFooter } from 'reactstrap';
import { Formik, Form } from 'formik';
import PackageModal from './modals/PackageModal';
import { toast } from 'react-toastify';
import { options } from '../helpers/toast';
import { scrollModalTop } from '../helpers/scrolling';

class EditPackage extends React.PureComponent {
	state = {
		modalIsOpen: false
	};

	getPackage() {
		this.setState((prevstate) => ({
			modalIsOpen: !prevstate.modalIsOpen
		}));
	}

	toggle = () => {
		this.setState((prevstate) => ({
			modalIsOpen: !prevstate.modalIsOpen
		}));
	};

	handleSubmit = async (values, actions) => {
		try {
			const response = await this.props.onSubmit(values, actions);
			this.setState({
				modalIsOpen: !response.success
			});
			toast('Package was successfully updated!', options);
		} catch (error) {
			actions.setStatus([ error.success, error.error_message ]);
			scrollModalTop(this.modal);
		}
	};

	async componentDidMount(...params) {
		await this.props.getPackage(...params);
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
						{({ setFieldValue, status, values }) => (
							<Form>
								<ModalHeader toggle={this.toggle}>Edit package (ID: {response.package.id})</ModalHeader>
								<PackageModal
									setFieldValue={setFieldValue}
									providers={providers}
									choseProviders={choseProviders}
									services={response.services}
									status={status}
									values={values}
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
