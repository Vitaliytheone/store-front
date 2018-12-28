import React, { Component } from "react";
import { Button, Modal, ModalHeader, ModalFooter } from "reactstrap";
import { Formik, Form } from "formik";
import ProductModal from "../modals/ProductModal";

class EditProduct extends Component {
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
    const { productValue } = this.props;
    return <React.Fragment>
        <span className="edit_product">
          <button onClick={this.toggle} href="#" className="btn m-btn--pill m-btn--air btn-primary btn-sm sommerce_dragtable__action product-pointer-events">
            Edit
          </button>
          <Modal isOpen={this.state.modalIsOpen} toggle={this.toggle} size="lg" backdrop={false}>
            <Formik onSubmit={this.handleSubmit} initialValues={{ name: productValue.name, visibility: productValue.visibility }}>
              <Form>
                <ModalHeader toggle={this.toggle}>Edit product</ModalHeader>
                <ProductModal />
                <ModalFooter className="justify-content-start">
                  <Button color="primary" type="submit">
                    Edit product
                  </Button> <Button color="secondary" onClick={this.toggle}>
                    Cancel
                  </Button>
                </ModalFooter>
              </Form>
            </Formik>
          </Modal>
        </span>
      </React.Fragment>;
  }
}

export default EditProduct;
