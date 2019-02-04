import React, { Component } from 'react';
import { Button, Modal, ModalHeader, ModalFooter } from 'reactstrap';
import { Formik, Form } from 'formik';
import ProductModal from './modals/ProductModal';

class EditProduct extends Component {
	state = {
		modalIsOpen: false
	};

	getProduct = (...params) => {
		this.setState((prevstate) => ({
			modalIsOpen: !prevstate.modalIsOpen
		}));
		this.props.getProduct(...params);
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
				<span className="edit_product">
					<Button
						onClick={this.getProduct}
						color="primary"
						size="sm"
						className="m-btn--pill sommerce_dragtable__action"
					>
						Edit
					</Button>
					<Modal
						isOpen={this.state.modalIsOpen}
						size="lg"
						backdrop="static"
						keyboard={false}
						autoFocus={true}
					>
						<Formik enableReinitialize={true} onSubmit={this.handleSubmit} initialValues={response.product}>
							{({ setFieldValue, values }) => (
								<Form>
									<ModalHeader toggle={this.toggle}>Edit product</ModalHeader>
									<ProductModal setFieldValue={setFieldValue} values={values} />
									<ModalFooter className="justify-content-start">
										<Button color="primary" type="submit">
											Edit product
										</Button>{' '}
										<Button color="secondary" onClick={this.toggle}>
											Cancel
										</Button>
									</ModalFooter>
								</Form>
							)}
						</Formik>
					</Modal>
				</span>
			</React.Fragment>
		);
	}
}

export default EditProduct;
