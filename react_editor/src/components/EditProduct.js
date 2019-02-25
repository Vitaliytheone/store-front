import React, { Component } from 'react';
import { Button, Modal, ModalHeader, ModalFooter } from 'reactstrap';
import { Formik, Form } from 'formik';
import EditProductModal from './EditProductModal';

class EditProduct extends React.PureComponent {
  state = {
    modalIsOpen: false
  };

  // getProduct() {
  //   this.setState(prevstate => ({
  //     modalIsOpen: !prevstate.modalIsOpen
  //   }));

  // }

  toggle = () => {
    document.body.classList.remove("scroll-off");
    this.setState(prevstate => ({
      modalIsOpen: !prevstate.modalIsOpen
    }));
  };


  render() {
    return (
      <React.Fragment>
        <span className="edit_product">
          <Button
            onClick={this.toggle}
            color="primary"
            size="sm"
            className="m-btn--pill sommerce_dragtable__action m-btn--air"
          >
            Edit
          </Button>
          {this.state.modalIsOpen && <EditProductModal {...this.props} toggle={this.toggle} />}
        </span>
      </React.Fragment>
    );
  }
}

export default EditProduct;
