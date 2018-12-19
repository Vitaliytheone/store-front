import React, { Component } from "react";
import { Button, Modal, ModalHeader, ModalFooter } from "reactstrap";
import AddProductModal from "../modals/AddProductModal";

class AddProduct extends Component {
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
          <ModalHeader toggle={this.toggle}>Add product</ModalHeader>
          <AddProductModal />
          <ModalFooter className="justify-content-start">
            <Button color="primary" onClick={this.toggle}>
              Add product
            </Button>{" "}
            <Button color="secondary" onClick={this.toggle}>
              Cancel
            </Button>
          </ModalFooter>
        </Modal>
      </div>
    );
  }
}

export default AddProduct;
