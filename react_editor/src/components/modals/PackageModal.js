import React from 'react';
import { ModalBody, Label, FormGroup } from 'reactstrap';
import { Field } from 'formik';
import { PackageInput } from '../Inputs';
import { Select } from '../SelectProviders';
import { get_providers_services } from '../../services/url';

class PackageModal extends React.PureComponent {
	state = {
		providerServices: [ { service: null, name: 'Chose provider service' } ],
		error: null,
		message: null
	};

	choseService = async (provider_id) => {
		if (provider_id !== 'none') {
			var response = await get_providers_services(provider_id);
			console.log(response);
			this.props.setFieldValue('provider_id', provider_id);
			response.data.unshift({ service: null, name: 'Chose provider service' });
			const error = response.data[1].error;
			const message = response.data[1].message;
			console.log(error);
			this.setState({
				providerServices: response.data,
				error: error,
				message: message
			});
		} else {
			this.setState({
				providerServices: [ { service: null, name: 'Chose provider service' } ]
			});
		}
	};

	componentDidMount = () => {
		setTimeout(() => this.name.focus(), 200);
	};

	render() {
		const { providerServices } = this.state;
		const { providers } = this.props;
		return (
			<ModalBody>
				{/* Alert Error */}

				{this.props.showError && (
					<div className="alert alert-danger alert-dismissible fade show" role="alert">
						<strong>{this.props.errorMessage}</strong>
					</div>
				)}

				{/* Alert Error End */}

				<FormGroup>
					<Field
						name="name"
						type="text"
						component={PackageInput}
						label="Package name *"
						id="package-name"
						innerRef={(input) => (this.name = input)}
					/>
				</FormGroup>

				<FormGroup>
					<Field name="price" type="number" component={PackageInput} label="Price" id="package-price" />
				</FormGroup>

				<FormGroup>
					<Field
						name="quantity"
						type="number"
						component={PackageInput}
						label="Quantity"
						id="package-quantity"
					/>
				</FormGroup>

				<FormGroup>
					<Field
						name="overflow"
						type="number"
						component={PackageInput}
						label="Overflow, %"
						id="package-overflow"
					/>
				</FormGroup>

				<FormGroup>
					<Label htmlFor="package-best">Best package</Label>
					<Field name="best" component="select" id="package-best" className="form-control">
						<option value="1">Enabled</option>
						<option value="0">Disabled</option>
					</Field>
				</FormGroup>

				<FormGroup>
					<Label htmlFor="package-link-type">Link Type</Label>
					<Field name="link_type" component="select" id="package-link-type" className="form-control">
						<option value="0">None</option>
						<option value="1">Instagram Profile</option>
						<option value="2">Instagram Post</option>
						<option value="3">Facebook Page</option>
						<option value="4">Facebook Profile</option>
						<option value="5">Facebook Post</option>
						<option value="6">Facebook Group</option>
						<option value="7">Facebook Event</option>
						<option value="8">Twitter Profile</option>
						<option value="9">Twitter Post</option>
						<option value="10">Youtube Channel</option>
						<option value="11">Youtube Video</option>
						<option value="12">VINE Picture</option>
						<option value="13">VINE Profile</option>
						<option value="14">Pinterest Profile</option>
						<option value="15">Pinterest Board</option>
						<option value="16">Pinterest Post</option>
						<option value="17">Soundcloud Track</option>
						<option value="18">Soundcloud Profile</option>
						<option value="19">Mixcloud Track</option>
						<option value="20">Mixcloud Profile</option>
						<option value="21">Periscope Profile</option>
						<option value="22">Periscope Video</option>
						<option value="25">Linkedin Profile</option>
						<option value="26">Linkedin Group</option>
						<option value="27">Linkedin Post</option>
						<option value="28">Radiojavan Video</option>
						<option value="29">Radiojavan Track</option>
						<option value="30">Radiojavan Podcast</option>
						<option value="31">Radiojavan Playlist</option>
						<option value="32">Shazam Profile</option>
						<option value="33">Shazam Track</option>
						<option value="34">Reverbnation Track</option>
						<option value="35">Reverbnation Video</option>
						<option value="36">Reverbnation Profile</option>
						<option value="37">Tumblr Profile</option>
						<option value="38">Tumblr Post</option>
						<option value="39">Vimeo Channel</option>
						<option value="40">Vimeo Video</option>
						<option value="41">Fyuse Profile</option>
						<option value="42">Fyuse Picture</option>
						<option value="43">Google+ Profile</option>
						<option value="44">Google+ Post</option>
						<option value="45">Twitch Channel</option>
					</Field>
				</FormGroup>
				<hr />

				<FormGroup>
					<Label htmlFor="visibility">Availability</Label>
					<Field name="visibility" className="form-control" component="select">
						<option value="1">Enabled</option>
						<option value="0">Disabled</option>
					</Field>
				</FormGroup>

				<hr />

				<FormGroup>
					<Label htmlFor="mode">Mode</Label>
					<Field name="mode" className="form-control" component="select">
						<option value="0">Manual</option>
						<option value="1">Auto</option>
					</Field>
				</FormGroup>

				<FormGroup>
					<Field
						providers={providers}
						className="form-control"
						component={Select}
						name="provider_id"
						type="select"
						label="Provider"
						choseService={this.choseService}
					/>
				</FormGroup>

				{/* <FormGroup>
					<Label htmlFor="provider_service">Provider service</Label>
					<Field className="form-control" component="select" name="provider_service">
					{providerServices.map(item => <option key={item.service} value={item.service}>{item.name}</option>)}
					</Field>
				</FormGroup> */}

				{this.state.error ? (
					<span className="m--font-danger">{this.state.message}</span>
				) : (
					<FormGroup>
						<Label htmlFor="provider_service">Provider service</Label>
						<Field className="form-control" component="select" name="provider_service">
							{providerServices.map((item) => (
								<option key={item.service} value={item.service}>
									{item.name}
								</option>
							))}
						</Field>
					</FormGroup>
				)}
			</ModalBody>
		);
	}
}

export default PackageModal;
