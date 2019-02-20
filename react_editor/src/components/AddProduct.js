import React, { Component } from 'react';
import { Button, Modal, ModalHeader, ModalFooter, Row, Col } from 'reactstrap';
import { Formik, Form } from 'formik';
import ProductModal from './modals/ProductModal';
import ConfirmProduct from './ConfirmCreateProduct';
import PropTypes from 'prop-types';
import { confirm_add_product } from '../services/url';
import { ToastContainer, toast } from 'react-toastify';
import 'react-toastify/dist/ReactToastify.css';
import { options } from '../helpers/toast';
import { scrollModalTop } from '../helpers/scrolling';

class AddProduct extends React.PureComponent {
  state = {
    confirmModal: false,
    modalIsOpen: false,
    productId: ""
  };

  closeConfirmModal = () => {
    this.setState(prevstate => ({
      confirmModal: !prevstate.confirmModal
    }));
    toast("Product was successfully created!", options);
  };

  confirmCreate = async () => {
    const productId = this.state.productId;
    await confirm_add_product(productId);
    this.setState(prevstate => ({
      confirmModal: !prevstate.confirmModal
    }));
    toast("Product was successfully created!", options);
    toast("Product was successfully created!", options);
  };

  toggle = () => {
    document.body.classList.remove("scroll-off");
    this.setState(prevstate => ({
      modalIsOpen: !prevstate.modalIsOpen,
    }));
  };

  handleSubmit = async (values, actions) => {
    try {
      const response = await this.props.onSubmit(values, actions);
      this.setState(prevstate => ({
        modalIsOpen: !response.success,
        productId: response.data.id,
        confirmModal: !prevstate.confirmModal
      }));
    } catch (error) {
		actions.setStatus([error.success, error.error_message]);
		scrollModalTop(this.modal);
    }
  };

  render() {
    const { isSubmitting, products } = this.props;
    return (
      <React.Fragment>
        <Row className="sommerce-products__actions">
          <Col lg="12">
            <div className="page-content">
              <Button
                onClick={this.toggle}
                className="m-btn--air"
                color="primary"
              >
                Add product
              </Button>
            </div>
          </Col>
        </Row>
        <ToastContainer animation="fade" />
        <Modal
          innerRef={el => (this.modal = el)}
          isOpen={this.state.modalIsOpen}
          toggle={this.toggle}
          size="lg"
          backdrop="static"
          keyboard={true}
        >
          <Formik
            onSubmit={this.handleSubmit}
            initialValues={this.props.initialValues}
          >
            {({ setFieldValue, values, status }) => (
              <Form>
                <ModalHeader toggle={this.toggle}>Create product</ModalHeader>
                <ProductModal
                  values={values}
                  setFieldValue={setFieldValue}
                  products={products}
                  properties={this.props.initialValues.properties}
                  status={status}
                />
                <ModalFooter className="justify-content-start">
                  <Button color="primary" type="submit" disabled={isSubmitting}>
                    {isSubmitting ? "Loading..." : "Add product"}
                  </Button>{" "}
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
