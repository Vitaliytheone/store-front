import React, { Component } from "react";
import { Button, Modal, ModalHeader, ModalBody, ModalFooter } from "reactstrap";
import "../App.css";

class AddPackage extends Component {
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
        <div className="mt-2 mb-3">
          <button
            className="btn btn-primary btn-sm m-btn m-btn--icon btm-sm m-btn--air"
            data-toggle="modal"
            data-target=".add_package"
            data-backdrop="static"
          >
            Add package
          </button>
        </div>
        <Modal isOpen={this.state.modal} toggle={this.toggle}>
          <ModalBody />
        </Modal>
      </div>
    );
  }
}

export default AddPackage;
