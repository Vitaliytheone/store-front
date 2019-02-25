import React, { Component } from "react";
import { Button, Modal, ModalHeader, ModalFooter } from "reactstrap";
import { Formik, Form } from "formik";
import ProductModal from "./modals/ProductModal";
import { toast } from "react-toastify";
import { options } from "../helpers/toast";
import { scrollModalTop } from "../helpers/scrolling";


class EditProductModal extends React.Component {
    state = {
        isFetching: false,
        response: {
            product:{
                name: '',
                visibility: '1',
                color: '',
                description: null,
                properties: [],
                seo_title: '',
                seo_description: '',
                seo_keywords: '',
                url: ''
        },
      }
    };

    handleSubmit = async (values, actions) => {
        try {
            const response = await this.props.onSubmit(values, actions);
            console.log(response.actions);
            this.props.toggle();
            toast("Product was successfully updated!", options);
        } catch (error) {
            actions.setSubmitting(false);
            actions.setStatus([error.success, error.error_message]);
            scrollModalTop(this.modal);
        }
    };

    async componentDidMount() {
        const response = await this.props.getProduct();
        this.setState({
            isFetching: true,
            response: response.response
        });
    }

    render() {
        const { products, toggle } = this.props;
        const { response } = this.state;
        return (
            <React.Fragment>
                    <Modal
                        innerRef={el => (this.modal = el)}
                        isOpen={true}
                        toggle={toggle}
                        size="lg"
                        backdrop="static"
                        keyboard={true}
                    >
                    {!this.state.isFetching && <div className="loader" />}
                        <Formik
                            enableReinitialize={true}
                            onSubmit={this.handleSubmit}
                            initialValues={response.product}
                        >
                            {({ setFieldValue, values, status, isSubmitting }) => (
                                <Form>
                                {isSubmitting && <div className="loader" />}
                                    <ModalHeader toggle={toggle}>Edit product</ModalHeader>
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
            </React.Fragment>
        );
    }
}

export default EditProductModal;
