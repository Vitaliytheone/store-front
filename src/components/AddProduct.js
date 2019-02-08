import React, { Component } from 'react';
import { Button, Modal, ModalHeader, ModalFooter, Row, Col } from 'reactstrap';
import { Formik, Form } from 'formik';
import ProductModal from './modals/ProductModal';
import ConfirmProduct from './ConfirmCreateProduct';
import PropTypes from 'prop-types';
import { connfirm_add_product } from '../services/url';
import { ToastContainer, toast } from 'react-toastify';
import 'react-toastify/dist/ReactToastify.css';

class AddProduct extends Component {
	state = {
		confirmModal: false,
		modalIsOpen: false,
		showError: false,
		errorMessage: null,
		productId: ''
	};

	closeConfirmModal = () => {
		this.setState((prevstate) => ({
			confirmModal: !prevstate.confirmModal
		}));
		toast('🦄 Product was successfully created!', {
			position: 'bottom-right',
			autoClose: 5000,
			hideProgressBar: false,
			closeOnClick: true,
			pauseOnHover: true,
			draggable: true
		});
	};

	confirmCreate = async () => {
		const productId = this.state.productId;
		await connfirm_add_product(productId);
		this.setState((prevstate) => ({
			confirmModal: !prevstate.confirmModal
		}));

		toast('🦄 Product was successfully created!', {
			position: 'bottom-right',
			autoClose: 5000,
			hideProgressBar: true,
			closeOnClick: true,
			pauseOnHover: true,
			draggable: true
		});

		toast('🦄 Menu item successfully created!', {
			position: 'bottom-right',
			autoClose: 5000,
			hideProgressBar: false,
			closeOnClick: true,
			pauseOnHover: true,
			draggable: true
		});
	};

	toggle = () => {
		document.body.classList.remove('scroll-off');
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
			errorMessage: response.error_message,
			productId: response.data.id
		});
		if (this.state.showError) {
			this.modal.scrollTop = 0;
		} else {
			this.setState((prevstate) => ({
				confirmModal: !prevstate.confirmModal
			}));
		}
	};

	render() {
		const { isSubmitting } = this.props;
		return (
			<React.Fragment>
				<Row className="sommerce-products__actions">
					<Col lg="12">
						<div className="page-content">
							<Button onClick={this.toggle} color="primary">
								Add product
							</Button>
						</div>
					</Col>
				</Row>
				<ToastContainer />
				<Modal
					innerRef={(el) => (this.modal = el)}
					isOpen={this.state.modalIsOpen}
					toggle={this.toggle}
					size="lg"
					backdrop="static"
					keyboard={false}
				>
					<Formik onSubmit={this.handleSubmit} initialValues={this.props.initialValues}>
						{({ setFieldValue, values }) => (
							<Form>
								<ModalHeader toggle={this.toggle}>Create product</ModalHeader>
								<ProductModal
									setFieldValue={setFieldValue}
									values={values}
									showError={this.state.showError}
									errorMessage={this.state.errorMessage}
								/>
								<ModalFooter className="justify-content-start">
									<Button color="primary" type="submit" disabled={isSubmitting}>
										{isSubmitting ? 'Loading...' : 'Add product'}
									</Button>{' '}
									<Button color="secondary" onClick={this.toggle}>
										Cancel
									</Button>
								</ModalFooter>
							</Form>
						)}
					</Formik>
				</Modal>
				<ConfirmProduct
					response={this.props.response}
					modalIsOpen={this.state.confirmModal}
					toggle={this.closeConfirmModal}
					confirmCreate={this.confirmCreate}
				/>
			</React.Fragment>
		);
	}
}

AddProduct.propTypes = {
	initialValues: PropTypes.shape({
		name: PropTypes.string,
		visibility: PropTypes.string,
		color: PropTypes.string,
		description: PropTypes.string,
		properties: PropTypes.array,
		seo_title: PropTypes.string,
		seo_keywords: PropTypes.string,
		url: PropTypes.string
	})
};

AddProduct.defaultProps = {
	initialValues: {
		item: '',
		name: '',
		visibility: '1',
		color: '',
		description: '',
		properties: [],
		seo_title: '',
		seo_description: '',
		seo_keywords: '',
		url: ''
	}
};

export default AddProduct;
