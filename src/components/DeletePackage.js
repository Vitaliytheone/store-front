import React, { Component } from 'react';
import { Button, Modal, ModalBody, Container, Row, Col } from 'reactstrap';

class DeletePackage extends Component {
	state = {
		modalIsOpen: false
	};

	toggle = () => {
		this.setState((prevstate) => ({
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
		return (
			<React.Fragment>
				<Button
					onClick={this.toggle}
					color="light"
					className="m-btn--hover-danger m-btn--icon m-btn--icon-only m-btn--pill"
				>
					<i className="la la-trash" />
				</Button>
				<Modal
					isOpen={this.state.modalIsOpen}
					toggle={this.toggle}
					backdrop="static"
					keyboard={false}
					size="sm"
				>
					<ModalBody>
						<Container fluid>
							<Row>
								<Col className="modal-delete-block text-center">
									<span className="fa fa-trash-o" />
									<p>Are your sure that your want to delete this Package?</p>
									<Button color="secondary" className="m-btn--air" onClick={this.toggle}>
										Cancel
									</Button>
									<Button color="danger" id="feature-delete m-btn--air" onClick={this.handleSubmit}>
										Yes, delete it!
									</Button>
								</Col>
							</Row>
						</Container>
					</ModalBody>
				</Modal>
			</React.Fragment>
		);
	}
}

export default DeletePackage;
