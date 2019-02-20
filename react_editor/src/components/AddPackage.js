import React, { Component } from 'react';
import { Button, Modal, ModalHeader, ModalFooter, Col, Row } from 'reactstrap';
import { Formik, Form } from 'formik';
import PackageModal from './modals/PackageModal';
import PropTypes from 'prop-types';
import { toast } from 'react-toastify';
import { options } from '../helpers/toast';
import { scrollModalTop } from '../helpers/scrolling';

class AddPackage extends React.PureComponent {
	state = {
		modalIsOpen: false
	};

	toggle = () => {
		this.setState((prevstate) => ({
			modalIsOpen: !prevstate.modalIsOpen,
		}));
	};

	handleSubmit = async (values, actions) => {
	   try{
		const response = await this.props.onSubmit(values, actions);
		this.setState({
			modalIsOpen: !response.success,
		});
		toast("Package was successfully created!", options);
	 } catch(error) {
		   actions.setStatus([error.success, error.error_message]);
		   scrollModalTop(this.modal);
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
						{({ setFieldValue, status }) => (
							<Form>
								<ModalHeader toggle={this.toggle}>Create package</ModalHeader>
								<PackageModal
									setFieldValue={setFieldValue}
									providers={this.props.providers}
									choseService={this.choseService}
									services={this.props.response.services}
									choseProviders={this.props.choseProviders}
									status={status}
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
