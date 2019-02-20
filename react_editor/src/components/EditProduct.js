import React, { Component } from 'react';
import { Button, Modal, ModalHeader, ModalFooter } from 'reactstrap';
import { Formik, Form } from 'formik';
import ProductModal from './modals/ProductModal';
import { toast } from 'react-toastify';
import { options } from '../helpers/toast';
import { scrollModalTop } from '../helpers/scrolling';

class EditProduct extends React.PureComponent {
  state = {
    modalIsOpen: false
  };

  getProduct() {
    this.setState(prevstate => ({
      modalIsOpen: !prevstate.modalIsOpen
    }));
  }

  toggle = () => {
    document.body.classList.remove("scroll-off");
    this.setState(prevstate => ({
      modalIsOpen: !prevstate.modalIsOpen
    }));
  };

  handleSubmit = async (values, actions) => {
    try {
      const response = await this.props.onSubmit(values, actions);
      this.setState({
        modalIsOpen: !response.success
      });
      toast("Product was successfully updated!", options);
    } catch (error) {
      actions.setStatus([error.success, error.error_message]);
      scrollModalTop(this.modal);
    }
  };

  componentDidMount(...params) {
    this.props.getProduct(...params);
  }

  render() {
	const { response, products } = this.props;
    return (
      <React.Fragment>
        <span className="edit_product">
          <Button
            onClick={() => {
              this.getProduct();
              this.props.getProduct();
            }}
            color="primary"
            size="sm"
            className="m-btn--pill sommerce_dragtable__action m-btn--air"
          >
            Edit
          </Button>
          <Modal
            innerRef={el => (this.modal = el)}
            isOpen={this.state.modalIsOpen}
            toggle={this.toggle}
            size="lg"
            backdrop="static"
            keyboard={true}
          >
            <Formik
              enableReinitialize={true}
              onSubmit={this.handleSubmit}
              initialValues={response.product}
            >
              {({ setFieldValue, values, status }) => (
                <Form>
                  <ModalHeader toggle={this.toggle}>Edit product</ModalHeader>
                  <ProductModal
                    setFieldValue={setFieldValue}
                    values={values}
                    products={products}
                    status={status}
                  />
                  <ModalFooter className="justify-content-start">
                    <Button color="primary" type="submit">
                      Edit product
                    </Button>{" "}
                    <Button color="secondary" onClick={this.toggle}>
                      Cancel
                    </Button>
                  </ModalFooter>
                </Form>
              )}
            </Formik>
          </Modal>
        </span>
      </React.Fragment>
    );
  }
}

export default EditProduct;
