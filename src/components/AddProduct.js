import React, { Component } from "react";
import { Button, Modal, ModalHeader, ModalFooter } from "reactstrap";
import { Formik, Form } from "formik";
import ProductModal from "./modals/ProductModal";
import PropTypes from 'prop-types';

class AddProduct extends Component {
  state = {
    modalIsOpen: false
  };

  toggle = () => {
    this.setState(prevstate => ({
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
    const { isSubmitting } = this.props;
    return (
      <React.Fragment>
        <div className="row sommerce-products__actions">
          <div className="col-lg-12">
            <div className="page-content">
              <button
                onClick={this.toggle}
                className="btn btn-primary m-btn--air"
              >
                Add product
              </button>
            </div>
          </div>
        </div>
        <Modal
          isOpen={this.state.modalIsOpen}
          toggle={this.toggle}
          size="lg"
          backdrop="static"
          keyboard={false}
        >
          <Formik
            onSubmit={this.handleSubmit}
            initialValues={this.props.initialValues}
          >
            <Form>
              <ModalHeader toggle={this.toggle}>Add product</ModalHeader>
              <ProductModal />
              <ModalFooter className="justify-content-start">
                <Button color="primary" type="submit" disabled={isSubmitting}>
                  {isSubmitting ? 'Loading...' : 'Add product'}
                </Button>{" "}
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


AddProduct.propTypes = {
    name: PropTypes.string,
    visibility: PropTypes.number
}

AddProduct.defaultProps = { 
    initialValues: { 
      name: " ",
      visibility: 1
  } 
};


export default AddProduct;