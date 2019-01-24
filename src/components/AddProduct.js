import React, { Component } from "react";
import { Button, Modal, ModalHeader, ModalFooter, Row, Col } from "reactstrap";
import { Formik, Form } from "formik";
import ProductModal from "./modals/ProductModal";
import PropTypes from "prop-types";

class AddProduct extends Component {
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
    const { isSubmitting } = this.props;
    return (
      <React.Fragment>
        <Row className="sommerce-products__actions">
          <Col lg="12">
            <div className="page-content">
              <Button onClick={this.toggle} color="primary">
                Add product
              </Button>
            </div>
          </Col>
        </Row>
        <Modal
          isOpen={this.state.modalIsOpen}
          toggle={this.toggle}
          size="lg"
          backdrop="static"
          keyboard={false}
        >
          <Formik
            onSubmit={this.handleSubmit}
            initialValues={this.props.initialValues}
          >
            {({ setFieldValue, values }) => (
              <Form>
                <ModalHeader toggle={this.toggle}>Add product</ModalHeader>
                <ProductModal setFieldValue={setFieldValue} values={values} />
                <ModalFooter className="justify-content-start">
                  <Button color="primary" type="submit" disabled={isSubmitting}>
                    {isSubmitting ? "Loading..." : "Add product"}
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

AddProduct.propTypes = {
  initialValues: PropTypes.shape({
    name: PropTypes.string,
    visibility: PropTypes.string,
    color: PropTypes.string,
    description: PropTypes.string,
    properties: PropTypes.array,
    seo_title: PropTypes.string,
    seo_keywords: PropTypes.string,
    url: PropTypes.string
  })
};

AddProduct.defaultProps = {
  initialValues: {
    item: "",
    name: "",
    visibility: "1",
    color: "",
    description: "",
    properties: [],
    seo_title: "",
    seo_description: "",
    seo_keywords: "",
    url: ""
  }
};

export default AddProduct;
