import React, { Component } from "react";
import { Button, Modal, ModalHeader, ModalFooter } from "reactstrap";
import PackageModal from "../modals/PackageModal";

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
            className="btn btn-primary btn-sm m-btn m-btn--icon btm-sm m-btn--air product-pointer-events"
            onClick={this.toggle}
          >
            Add package
          </button>
        </div>
        <Modal isOpen={this.state.modal} toggle={this.toggle} backdrop={false}>
          <ModalHeader toggle={this.toggle}>
            Create package (ID : 23)
          </ModalHeader>
          <PackageModal />
          <ModalFooter className="justify-content-start">
            <Button color="primary" onClick={this.toggle}>
              Add package
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

export default AddPackage;
