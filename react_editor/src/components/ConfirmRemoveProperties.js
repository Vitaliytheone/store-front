import React from 'react';
import { Button, Modal, ModalBody, Row, Col } from 'reactstrap';

const ConfirmRemove = ({ toggle, modalIsOpen, setFieldValue, properties }) => {
	return (
		<Modal isOpen={modalIsOpen} toggle={() => toggle} backdrop="static" keyboard={false}>
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

{
	/* <div className="col modal-delete-block text-center">
    <span className="la la-warning" style={{ fontSize: '60px' }}></span>
    <p>All current properties will be deleted</p>
    <button className="btn btn-secondary cursor-pointer m-btn--air" data-dismiss="modal">No</button>
    <button className="btn btn-primary btn__submit_copy" id="m-btn--air" data-dismiss="modal">Ok</button>
</div> */
}
export default ConfirmRemove;
