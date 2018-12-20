import React, { Component } from "react";
import { Button, Modal, ModalHeader, ModalFooter } from "reactstrap";
import ProductModal from "../modals/ProductModal";

class EditProduct extends Component {
  state = {
    modal: false
  };

  toggle = () => {
    this.setState({
      modal: !this.state.modal
    });
  };

  render() {
    return (
      <span>
        <button
          onClick={this.toggle}
          href="#"
          className="btn m-btn--pill m-btn--air btn-primary btn-sm sommerce_dragtable__action product-pointer-events"
        >
          Edit
        </button>
        <Modal
          isOpen={this.state.modal}
          toggle={this.toggle}
          size="lg"
          backdrop={false}
        >
          <ModalHeader toggle={this.toggle}>Edit product</ModalHeader>
          <ProductModal />
          <ModalFooter className="justify-content-start">
            <Button color="primary" onClick={this.toggle}>
              Edit product
            </Button>{" "}
            <Button color="secondary" onClick={this.toggle}>
              Cancel
            </Button>
          </ModalFooter>
        </Modal>
      </span>
    );
  }
}

export default EditProduct;
