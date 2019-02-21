import React from 'react';
import { Button, Modal, ModalBody, Row, Col } from 'reactstrap';

const ConfirmRemove = ({ toggle, modalIsOpen, setFieldValue, properties }) => {
	return (
		<Modal isOpen={modalIsOpen} backdrop="static">
			<ModalBody>
				<Row>
					<Col className="modal-delete-block text-center">
						<span className="la la-warning" style={{ fontSize: '60px' }} />
						<p>All current properties will be deleted</p>
						<Button color="secondary" className="m-btn--air" onClick={() => toggle()}>
							No
						</Button>
						<Button
							color="primary"
							className="btn__submit_copy m-btn--air"
							onClick={() => {
								toggle();
								setFieldValue('properties', properties);
							}}
						>
							Ok
						</Button>
					</Col>
				</Row>
			</ModalBody>
		</Modal>
	);
};

export default ConfirmRemove;
