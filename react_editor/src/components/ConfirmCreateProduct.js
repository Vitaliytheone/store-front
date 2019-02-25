import React from "react";
import { Button, Modal, ModalBody, ModalHeader, ModalFooter } from "reactstrap";

const ConfirmProduct = ({  productName, modalIsOpen, toggle, confirmCreate }) => (
  <React.Fragment>
    <Modal isOpen={modalIsOpen} toggle={toggle} >
      <ModalHeader toggle={toggle}>
        <h3 id="conrirm_label">Confirm</h3>
      </ModalHeader>
      <ModalBody>
        <p>Do you want to create menu item {productName}</p>
      </ModalBody>
      <ModalFooter className="justify-content-start">
        <Button id="confirm_yes" color="primary" onClick={confirmCreate}>
          Yes
        </Button>{" "}
        <Button color="secondary" onClick={toggle}>
          No
        </Button>
      </ModalFooter>
    </Modal>
  </React.Fragment>
);

export default ConfirmProduct;