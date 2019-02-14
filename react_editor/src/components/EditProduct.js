import React, { Component } from 'react';
import { Button, Modal, ModalHeader, ModalFooter } from 'reactstrap';
import { Formik, Form } from 'formik';
import ProductModal from './modals/ProductModal';
import { toast } from "react-toastify";
import { options } from "../helpers/toast";
import { scrollModalTop } from '../helpers/scrolling';

class EditProduct extends Component {
	state = {
		modalIsOpen: false,
		showError: false,
		errorMessage: null
	};

	getProduct = (...params) => {
		this.setState((prevstate) => ({
			modalIsOpen: !prevstate.modalIsOpen
		}));
		this.props.getProduct(...params);
	};

	toggle = () => {
		document.body.classList.remove("scroll-off");
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
    if(this.state.showError) {
      scrollModalTop(this.modal);
    } else {
    toast("Product was successfully updated!", options);
    }
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
            className="m-btn--pill sommerce_dragtable__action m-btn--air"
          >
            Edit
          </Button>
          <Modal
            innerRef={el => (this.modal = el)}
            isOpen={this.state.modalIsOpen}
            toggle={this.toggle}
            size="lg"
            backdrop="static"
            keyboard={true}
          >
            <Formik
              enableReinitialize={true}
              onSubmit={this.handleSubmit}
              initialValues={response.product}
            >
              {({ setFieldValue, values }) => (
                <Form>
                  <ModalHeader toggle={this.toggle}>
                    Edit product
                  </ModalHeader>
                  <ProductModal
                    setFieldValue={setFieldValue}
                    values={values}
                    showError={this.state.showError}
                    errorMessage={this.state.errorMessage}
                  />
                  <ModalFooter className="justify-content-start">
                    <Button color="primary" type="submit">
                      Edit product
                    </Button>{" "}
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
