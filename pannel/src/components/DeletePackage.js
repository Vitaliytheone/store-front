import React, { Component } from "react";
import { Button, Modal, ModalBody } from "reactstrap";

class DeletePackage extends Component {
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
        <a
          onClick={this.toggle}
          href="#"
          className="m-portlet__nav-link btn m-btn m-btn--hover-danger m-btn--icon m-btn--icon-only m-btn--pill"
        >
          <i className="la la-trash" />
        </a>
        <Modal
          isOpen={this.state.modal}
          toggle={this.toggle}
          backdrop={false}
          size="sm"
        >
          <ModalBody>
            <div class="container-fluid">
              <div class="row">
                <div className="col modal-delete-block text-center">
                  <span className="fa fa-trash-o" />
                  <p>Are your sure that your want to delete this Package?</p>
                  <Button
                    color="secondary"
                    className="m-btn--air"
                    onClick={this.toggle}
                  >
                    Cancel
                  </Button>
                  <Button
                    color="danger"
                    id="feature-delete m-btn--air"
                    onClick={this.toggle}
                  >
                    Yes, delete it!
                  </Button>
                </div>
              </div>
            </div>
          </ModalBody>
        </Modal>
      </span>
    );
  }
}

export default DeletePackage;
