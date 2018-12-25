import React, { Component } from "react";
import { Button, Modal, ModalHeader, ModalFooter } from "reactstrap";
import { Formik, Form } from "formik";
import PackageModal from "../modals/PackageModal";

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
    return <div>
        <div className="mt-2 mb-3">
          <button className="btn btn-primary btn-sm m-btn m-btn--icon btm-sm m-btn--air product-pointer-events" onClick={this.toggle}>
            Add package
          </button>
        </div>
        <Modal isOpen={this.state.modalIsOpen} toggle={this.toggle} backdrop={false}>
          <Formik onSubmit={this.handleSubmit} initialValues={{ name: "", price: "", quantity: "", overflow: "", availability: "Enabled", mode: "Auto", provider: "bulkfollows.com" }}>
            <Form>
              <ModalHeader toggle={this.toggle}>
                Create package (ID : 23)
              </ModalHeader>
              <PackageModal />
              <ModalFooter className="justify-content-start">
                <Button type="submit" color="primary">
                  Add package
                </Button> <Button color="secondary" onClick={this.toggle}>
                  Cancel
                </Button>
              </ModalFooter>
            </Form>
          </Formik>
        </Modal>
      </div>;
  }
}

export default AddPackage;
