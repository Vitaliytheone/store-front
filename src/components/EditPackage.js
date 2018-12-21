import React, { Component } from "react";
import { Button, Modal, ModalHeader, ModalFooter } from "reactstrap";
import { Formik, Form } from "formik";
import PackageModal from "../modals/PackageModal";

class EditPackage extends Component {
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
          type="button"
          className="btn m-btn--pill m-btn--air btn-primary btn-sm sommerce_dragtable__action product-pointer-events"
        >
          Edit
        </button>
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
                Edit package (ID : 25)
              </ModalHeader>
              <PackageModal />
              <ModalFooter className="justify-content-start">
                <Button color="primary" onClick={this.toggle}>
                  Save package
                </Button>{" "}
                <Button color="secondary" onClick={this.toggle}>
                  Cancel
                </Button>
              </ModalFooter>
            </Form>
          </Formik>
        </Modal>
      </span>
    );
  }
}

export default EditPackage;
