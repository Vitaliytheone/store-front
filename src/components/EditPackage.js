import React, { Component } from "react";
import { Button, Modal, ModalHeader, ModalFooter } from "reactstrap";
import { Formik, Form } from "formik";
import PackageModal from "./modals/PackageModal";
import PropTypes from "prop-types";

class EditPackage extends Component {
  state = {
    modalIsOpen: false
  };

  handleGetPackage = (...params) => {
    this.setState(prevstate => ({
      modalIsOpen: !prevstate.modalIsOpen
    }));
    this.props.getPackage(...params);
  }

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
    console.log(this.props.response.package);
    const { response } = this.props;
    return (
      <React.Fragment>
        <Button 
          onClick={this.handleGetPackage}
          color="primary"
          size="sm"
          className="m-btn--pill sommerce_dragtable__action"
        >
          Edit
        </Button>
        <Modal isOpen={this.state.modalIsOpen} backdrop='static'
          keyboard={false}>
          <Formik
            onSubmit={this.handleSubmit}
            enableReinitialize={true}
            initialValues={{
              name: response.package.name,
              price: response.package.price,
              quantity: response.package.quantity,
              overflow: response.package.overflow,
              availability: response.package.availability,
              mode: response.package.mode,
              provider_id: response.package.provider_id,
              provider_service_id: response.package.provider_service_id
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

EditPackage.propTypes = {
  initialValues: PropTypes.shape({
    name: PropTypes.string,
    price: PropTypes.number,
    quantity: PropTypes.number,
    overflow: PropTypes.number,
    availability: PropTypes.string,
    mode: PropTypes.string,
    provider: PropTypes.string
  })
};

EditPackage.defaultProps = {
  initialValues: {
    name: "",
    price: 0,
    quantity: 0,
    overflow: 0,
    best: "2",
    availability: "1",
    mode: "2",
    provider_id: "",
    provider_service_id: ""
  }
};

export default EditPackage;
