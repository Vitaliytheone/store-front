import React, { Component } from "react";
import { Button, Modal, ModalHeader, ModalFooter } from "reactstrap";
import { Formik, Form } from "formik";
import PackageModal from "./modals/PackageModal";

class AddPackage extends Component {
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
    return <React.Fragment>
        <div className="mt-2 mb-3">
          <button className="btn btn-primary btn-sm m-btn m-btn--icon btm-sm m-btn--air product-pointer-events" onClick={this.toggle}>
            Add package
          </button>
        </div>
        <Modal isOpen={this.state.modalIsOpen} toggle={this.toggle} backdrop="static" keyboard={false}>
          <Formik onSubmit={this.handleSubmit} initialValues={{ name: "", price: "", quantity: "", overflow: "", availability: "Enabled", mode: "Auto", provider: "bulkfollows.com" }}>
            <Form>
              <ModalHeader toggle={this.toggle}>Create package</ModalHeader>
              <PackageModal />
              <ModalFooter className="justify-content-start">
                <Button color="primary" type="submit">
                  Add package
                </Button> <Button color="secondary" onClick={this.toggle}>
                  Cancel
                </Button>
              </ModalFooter>
            </Form>
          </Formik>
        </Modal>
      </React.Fragment>;
  }
}

export default AddPackage;
