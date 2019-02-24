import React from 'react';
import { Formik, Form } from 'formik';
import { Button, Modal, ModalHeader, ModalFooter } from 'reactstrap';
import PackageModal from './modals/PackageModal';

class EditPackageModal extends React.Component {
	state = {
		isFetching: false,
		response: {
			package: {},
			services: {
				providerServices: []
			}
		}
	};

	async componentDidMount() {
		const response = await this.props.getPackage();
		// await new Promise((res) => setTimeout(res, 10000));
		console.log(response);
		this.setState({
			isFetched: true,
			response
		});
	}

	render() {
		const { providers, choseProviders } = this.props;
		const { response } = this.state;
		return (
			<Modal
				innerRef={(el) => (this.modal = el)}
				isOpen={true}
				backdrop="static"
				keyboard={true}
				toggle={this.props.toggle}
			>
				{!this.state.isFetched && <div className="loader" />}
				<Formik onSubmit={this.handleSubmit} enableReinitialize={true} initialValues={response.package}>
					{({ setFieldValue, status, values }) => (
						<Form>
							<ModalHeader toggle={this.props.toggle}>
								Edit package (ID: {response.package.id})
							</ModalHeader>
							<PackageModal
								setFieldValue={setFieldValue}
								providers={providers}
								choseProviders={choseProviders}
								services={response.services}
								status={status}
								values={values}
							/>
							<ModalFooter className="justify-content-start">
								<Button color="primary" type="submit">
									Save package
								</Button>{' '}
								<Button type="button" color="secondary" onClick={this.props.toggle}>
									Cancel
								</Button>
							</ModalFooter>
						</Form>
					)}
				</Formik>
			</Modal>
		);
	}
}

export default EditPackageModal;
