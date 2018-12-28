import React, { Component } from "react";
import { Button, Modal, ModalBody } from "reactstrap";

class DeletePackage extends Component {
  state = {
    modalIsOpen: false
  };

  toggle = () => {
    this.setState(prevstate =>({
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
    return (
      <React.Fragment>
        <button
          onClick={this.toggle}
          href="#"
          className="m-portlet__nav-link btn m-btn m-btn--hover-danger m-btn--icon m-btn--icon-only m-btn--pill product-pointer-events"
        >
          <i className="la la-trash" />
        </button>
        <Modal
          isOpen={this.state.modalIsOpen}
          toggle={this.toggle}
          backdrop={false}
          size="sm"
        >
          <ModalBody>
            <div className="container-fluid">
              <div className="row">
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
                    onClick={this.handleSubmit}
                  >
                    Yes, delete it!
                  </Button>
                </div>
              </div>
            </div>
          </ModalBody>
        </Modal>
      </React.Fragment>
    );
  }
}

export default DeletePackage;
