import React, { Component } from "react";
import { Button, Modal, ModalHeader, ModalBody, ModalFooter } from "reactstrap";
import AddProductModal from "./AddProductModal";
import "../App.css";

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
                data-toggle="modal"
                data-target=".add_product"
                data-backdrop="static"
              >
                Add product
              </button>
            </div>
          </div>
        </div>
        <Modal
          isOpen={this.state.modal}
          toggle={this.toggle}
          dialogClassName="my-modal"
          size="lg"
        >
          <AddProductModal />
        </Modal>
      </div>
    );
  }
}

export default AddProduct;
