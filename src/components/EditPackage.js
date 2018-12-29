import React, { Component } from "react";
import { Button, Modal, ModalHeader, ModalFooter } from "reactstrap";
import { Formik, Form } from "formik";
import PackageModal from "./modals/PackageModal";

class EditPackage extends Component {
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
    const { packageValue } = this.props;
    return (
      <React.Fragment>
        <button
          onClick={this.toggle}
          type="button"
          className="btn m-btn--pill m-btn--air btn-primary btn-sm sommerce_dragtable__action product-pointer-events"
        >
          Edit
        </button>
        <Modal isOpen={this.state.modalIsOpen} toggle={this.toggle}  backdrop='static'
          keyboard={false}>
          <Formik
            onSubmit={this.handleSubmit}
            initialValues={{
              name: packageValue.name,
              price: packageValue.price,
              quantity: packageValue.quantity,
              overflow: packageValue.overflow,
              availability: packageValue.availability,
              mode: packageValue.mode,
              provider: packageValue.provider
            }}
          >
            <Form>
              <ModalHeader toggle={this.toggle}>
                Edit package
              </ModalHeader>
              <PackageModal />
              <ModalFooter className="justify-content-start">
                <Button color="primary" type="submit">
                  Save package
                </Button>{" "}
                <Button color="secondary" onClick={this.toggle}>
                  Cancel
                </Button>
              </ModalFooter>
            </Form>
          </Formik>
        </Modal>
      </React.Fragment>
    );
  }
}

export default EditPackage;
