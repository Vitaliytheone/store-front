import React, { Component } from "react";
import { Button, Modal, ModalHeader, ModalFooter } from "reactstrap";
import { Formik, Form } from "formik";
import ProductModal from "./modals/ProductModal";

class EditProduct extends Component {
  state = {
    modalIsOpen: false
  };

  handleGetProduct = (...params) => {
      this.setState(prevstate => ({
        modalIsOpen: !prevstate.modalIsOpen
      }));
      this.props.handleGetProduct(...params);
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
    console.log(this.props);
    const { response } = this.props;
    return <React.Fragment>
        <span className="edit_product">
          <Button onClick={this.toggle} color="primary" size="sm" className="m-btn--pill sommerce_dragtable__action">
            Edit
          </Button>
          <Modal isOpen={this.state.modalIsOpen} toggle={this.toggle} size="lg" backdrop="static" keyboard={false}>
            <Formik onSubmit={this.handleSubmit} initialValues={{ name: response.product.name, visibility: response.product.visibility, color: response.product.color, description: response.product.description, properties: response.product.properties, seo_title: response.product.seo_title, seo_description: response.product.seo_description, seo_keywords: response.product.seo_keywords, url: response.product.url }}>
              {/* http://www.mocky.io/v2/5c49e75234000053002693c9 */}
              <Form>
                <ModalHeader toggle={this.toggle}>Edit product</ModalHeader>
                <ProductModal />
                <ModalFooter className="justify-content-start">
                  <Button color="primary" type="submit">
                    Edit product
                  </Button> <Button color="secondary" onClick={this.toggle}>
                    Cancel
                  </Button>
                </ModalFooter>
              </Form>
            </Formik>
          </Modal>
        </span>
      </React.Fragment>;
  }
}

export default EditProduct;
