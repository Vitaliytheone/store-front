import React, { Component } from 'react';
import { Button, Modal, ModalHeader, ModalFooter } from 'reactstrap';
import { Formik, Form } from 'formik';
import PackageModal from './modals/PackageModal';
import { toast } from "react-toastify";
import { options } from '../helpers/toast';
import { scrollModalTop } from '../helpers/scrolling';
import { get_providers_services } from '../services/url';

class EditPackage extends Component {
  state = {
    modalIsOpen: false,
    showError: false,
    errorMessage: null,

    services: {
      providerServices: [],
      errorService: null,
      messageService: null
    }
  };

  // static getDerivedStateFromProps(props, state) {
  //   return { ...state, services: { providerServices: props.response.providerServices} }
  // }

  choseService = async provider_id => {
    if (provider_id !== "none") {
      var response = await get_providers_services(provider_id);
      response.data.unshift({ service: null, name: "Chose provider service" });
      const error = response.data[1].error;
      const message = response.data[1].message;
      this.setState(prevstate => ({
        services: {
          ...prevstate,
          providerServices: response.data,
          errorService: error,
          messageService: message
        }
      }));
    } else {
      this.setState(prevstate => ({
        services: {
          ...prevstate,
          providerServices: [{ service: null, name: "Chose provider service" }]
        }
      }));
    }
  };

  getPackage = (...params) => {
    this.setState(prevstate => ({
      modalIsOpen: !prevstate.modalIsOpen
    }));
    this.props.getPackage(...params);
    const services = this.props.response.providerServices;
    console.log(services);
  };

  toggle = () => {
    this.setState(prevstate => ({
      modalIsOpen: !prevstate.modalIsOpen,
      showError: false,
      errorMessage: null
    }));
  };

  handleSubmit = async (...params) => {
    const response = await this.props.onSubmit(...params);
    this.setState({
      showError: !response.success,
      modalIsOpen: !response.success,
      errorMessage: response.error_message
    });
    if (this.state.showError) {
      scrollModalTop(this.modal);
    } else {
      toast("Package was successfully updated!", options);
    }
  };

  render() {
    const { response } = this.props;
    return (
      <React.Fragment>
        <Button
          onClick={this.getPackage}
          color="primary"
          size="sm"
          className="m-btn--pill sommerce_dragtable__action m-btn--air"
          active
        >
          Edit
        </Button>
        <Modal
          innerRef={el => (this.modal = el)}
          isOpen={this.state.modalIsOpen}
          backdrop="static"
          keyboard={true}
          toggle={this.toggle}
        >
          <Formik
            onSubmit={this.handleSubmit}
            enableReinitialize={true}
            initialValues={response.package}
          >
            {({ setFieldValue }) => (
              <Form>
                <ModalHeader toggle={this.toggle}>
                  Edit package (ID: {response.package.id})
                </ModalHeader>
                <PackageModal
                  setFieldValue={setFieldValue}
                  showError={this.state.showError}
                  errorMessage={this.state.errorMessage}
                  providers={this.props.providers}
                  choseService={this.choseService}
                  services={this.state.services}
                />
                <ModalFooter className="justify-content-start">
                  <Button color="primary" type="submit">
                    Save package
                  </Button>{" "}
                  <Button color="secondary" onClick={this.toggle}>
                    Cancel
                  </Button>
                </ModalFooter>
              </Form>
            )}
          </Formik>
        </Modal>
      </React.Fragment>
    );
  }
}

export default EditPackage;
