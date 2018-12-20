import React, { Component } from "react";
import { addProduct } from "../services/products";
import { Button, Modal, ModalHeader, ModalFooter } from "reactstrap";
import { Formik, Form } from "formik";
import ProductModal from "../modals/ProductModal";

class AddProduct extends Component {
  state = {
    modal: false
  };

  toggle = () => {
    this.setState({
      modal: !this.state.modal
    });
  };

  // onSubmit = ({ productName, visibility }) => {
  //   console.log(productName, visibility);
  // };

  render() {
    return (
      <div>
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
          isOpen={this.state.modal}
          toggle={this.toggle}
          size="lg"
          backdrop={false}
        >
          <Formik
            onSubmit={this.props.onSubmit}
            initialValues={{
              productName: " ",
              visibility: "enabled"
            }}
          >
            <Form>
              <ModalHeader toggle={this.toggle}>Add product</ModalHeader>
              <ProductModal />
              <ModalFooter className="justify-content-start">
                <Button color="primary" type="submit">
                  Add product
                </Button>{" "}
                <Button color="secondary" onClick={this.toggle}>
                  Cancel
                </Button>
              </ModalFooter>
            </Form>
          </Formik>
        </Modal>
      </div>
    );
  }
}

export default AddProduct;
