import React, { Component } from "react";
import { Button, Modal, ModalHeader, ModalFooter } from "reactstrap";
import { Formik, Form } from "formik";
import PackageModal from "../modals/PackageModal";
import { addPackage } from "../services/products";

class AddPackage extends Component {
  state = {
    modal: false
  };

  toggle = () => {
    this.setState({
      modal: !this.state.modal
    });
  };

  // onSubmit = ({
  //   packageName,
  //   price,
  //   quantity,
  //   overflow,
  //   availability,
  //   mode,
  //   provider
  // }) => {
  //   addPackage(4, {
  //     packageName,
  //     price,
  //     quantity,
  //     overflow,
  //     availability,
  //     mode,
  //     provider
  //   });
  // };

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
          <Formik
            onSubmit={this.onSubmit}
            initialValues={{
              packageName: "ajsajsajaskfasf",
              price: "131",
              quantity: "",
              overflow: "404",
              availability: "2",
              mode: "2",
              provider: "4"
            }}
          >
            <Form>
              <ModalHeader toggle={this.toggle}>
                Create package (ID : 23)
              </ModalHeader>
              <PackageModal />
              <ModalFooter className="justify-content-start">
                <Button type="submit" color="primary">
                  Add package
                </Button>{" "}
                <Button color="secondary" onClick={this.toggle}>
                  Cancel
                </Button>
              </ModalFooter>
            </Form>
          </Formik>
        </Modal>
      </div>
    );
  }
}

export default AddPackage;
