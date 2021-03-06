import React, { Component } from 'react';
import PropertiesList from './PropertiesList';
import { ModalBody, Label, FormGroup, Input } from 'reactstrap';
import { Field } from 'formik';
import { ProductInput } from '../Inputs';
import { arrayMove } from 'react-sortable-hoc';
import { SketchPicker } from 'react-color';

// // Import bootstrap(v3 or v4) dependencies
// import 'bootstrap/js/src/dropdown';
// import 'bootstrap/js/src/tooltip';
import 'bootstrap/js/src/modal';

class ProductModal extends React.PureComponent {
	state = {
		colorSchema: false,
		editSeo: false
	};

	clearColor = (event) => {
		this.props.setFieldValue('color', event.target.value);
	};

	addProperty = () => {
		if (this.props.values.item.replace(/\s/g, '') !== '') {
			this.props.setFieldValue('properties', [ ...this.props.values.properties, this.props.values.item ]);
			this.props.values.item = '';
		}
	};

	deleteProperty = (index) => () => {
		this.props.setFieldValue('properties', this.props.values.properties.filter((_, i) => i !== index));
	};

	handleKeyPress = (event) => {
		if (event.key === 'Enter') {
			if (this.props.values.item.replace(/\s/g, '') !== '') {
				this.props.setFieldValue('properties', [ ...this.props.values.properties, this.props.values.item ]);
				this.props.values.item = '';
			}
			event.preventDefault();
		}
	};

	handlePropertiesSwitch = ({ oldIndex, newIndex }) => {
		const propertiesMove = arrayMove(this.props.values.properties, oldIndex, newIndex);
		this.props.setFieldValue('properties', propertiesMove);
	};

	handleChangeComplete = (color) => {
		this.props.setFieldValue('color', color.hex);
	};

	openSeoEditor = () => {
		this.setState((prevstate) => ({
			editSeo: !prevstate.editSeo
		}));
	};

	openColorSchema = () => {
		this.setState((prevstate) => ({
			colorSchema: !prevstate.colorSchema
		}));
	};

	closeColorSchema = () => {
		this.setState({
			colorSchema: false
		});
	};

	componentDidMount = () => {
		setTimeout(() => this.name.focus(), 200);
	};

	render() {
		const { values, setFieldValue } = this.props;
		const { colorSchema, editSeo } = this.state;
		const seoUrl = values.url && values.url.replace(/ /g, '-');

		let colorHex;
		if (colorSchema) {
			colorHex = (
				<div className="color-schema">
					<div className="cover-schema" onClick={this.closeColorSchema} />
					<SketchPicker color={this.props.values.color} onChange={this.handleChangeComplete} />
				</div>
			);
		}

		return (
			<React.Fragment>
				{(values.description === '' || values.description) && (
					<SummerNote description={values.description} setFieldValue={setFieldValue} />
				)}
				<ModalBody>
          				{/* Alert Error */}

				{this.props.showError && (
					<div className="alert alert-danger alert-dismissible fade show" role="alert">
						<button className="close" data-dismiss="alert" aria-label="Close" />
							<strong>{this.props.errorMessage}</strong> 
					</div>
				)}

				{/* Alert Error End */}
					<FormGroup>
						<Field
							name="name"
							component={ProductInput}
							label="Product name"
							innerRef={(input) => (this.name = input)}
						/>
					</FormGroup>

					<FormGroup>
						<Label htmlFor="visibility">Visibility</Label>
						<Field className="form-control" component="select" name="visibility">
							<option value="1">Enabled</option>
							<option value="2">Disabled</option>
						</Field>
					</FormGroup>

					<FormGroup>
						<Label>Color</Label>
						<div className="product-color__wrap">
							<Input
								type="text"
								className="product-color"
								id="package-color2"
								value={this.props.values.color}
								onChange={this.clearColor}
							/>
							<div className="sp-replacer sp-light" onClick={this.openColorSchema}>
								<div
									className="sp-preview"
									style={{
										background: this.props.values.color ? `${this.props.values.color}` : null
									}}
								>
									<div
										className="sp-preview-inner sp-clear-display"
										style={{ backgroundColor: 'transparent' }}
									/>
								</div>
								<div className="sp-dd">▼</div>
							</div>
						</div>
						{colorHex}
					</FormGroup>

					<FormGroup>
						<div className="summernote" />
					</FormGroup>

					<div className="card card-white mb-3">
						<div className="card-body">
							<div className="row seo-header align-items-center">
								<div className="col-sm-8">Properties</div>
								{/* <div className="col-sm-4 text-sm-right">
                  <div
                    className="m-dropdown m-dropdown--inline m-dropdown--large m-dropdown--arrow m-dropdown--align-left"
                    data-dropdown-toggle="hover"
                    aria-expanded="true"
                  >
                    <a
                      className="btn btn-sm btn-link m-dropdown__toggle"
                      href="#"
                    >
                      <span className="la 	la-clone" /> Copy properties
                    </a>
                    <div className="m-dropdown__wrapper">
                      <span className="m-dropdown__arrow m-dropdown__arrow--left" />
                      <div className="m-dropdown__inner">
                        <div className="m-dropdown__body">
                          <div className="m-dropdown__content dd-properties__max-height">
                            <div
                              className="m--font-primary dd-properties__alert"
                              role="alert"
                            >
                              Select the product from which you want to copy
                              properties
                            </div>
                            <ul className="m-nav">
                              <li className="m-nav__item">
                                <a href="" className="m-nav__link">
                                  <span className="m-nav__link-text">
                                    Buy Facebook likes
                                  </span>
                                </a>
                              </li>
                              <li className="m-nav__item">
                                <a href="" className="m-nav__link">
                                  <span className="m-nav__link-text">
                                    Buy Facebook followers
                                  </span>
                                </a>
                              </li>
                              <li className="m-nav__item">
                                <a href="" className="m-nav__link">
                                  <span className="m-nav__link-text">
                                    Buy Facebook photo/post likes
                                  </span>
                                </a>
                              </li>
                              <li className="m-nav__item">
                                <a href="" className="m-nav__link">
                                  <span className="m-nav__link-text">
                                    Buy Facebook vide views
                                  </span>
                                </a>
                              </li>
                              <li className="m-nav__item">
                                <a href="" className="m-nav__link">
                                  <span className="m-nav__link-text">
                                    Buy Twitter Followers
                                  </span>
                                </a>
                              </li>
                              <li className="m-nav__item">
                                <a href="" className="m-nav__link">
                                  <span className="m-nav__link-text">
                                    Buy Twitter Retweets
                                  </span>
                                </a>
                              </li>
                              <li className="m-nav__item">
                                <a href="" className="m-nav__link">
                                  <span className="m-nav__link-text">
                                    Buy Twitter Favorites
                                  </span>
                                </a>
                              </li>
                              <li className="m-nav__item">
                                <a href="" className="m-nav__link">
                                  <span className="m-nav__link-text">
                                    Buy Instagram Likes
                                  </span>
                                </a>
                              </li>
                            </ul>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div> */}
							</div>
							<div className="form-group">
								<div className="input-group">
									<input
										value={values.item}
										type="text"
										className="form-control input-properties"
										onKeyPress={this.handleKeyPress}
										onChange={(event) => setFieldValue('item', event.target.value)}
									/>
									<span className="input-group-btn">
										<button
											className="btn btn-primary add-properies"
											type="button"
											onClick={this.addProperty}
										>
											Add
										</button>
									</span>
								</div>
							</div>
							{values.properties == false ? (
								<div className="alert m-alert--default" role="alert">
									Create a new property or{' '}
									<b>
										<span className="la la-clone" style={{ fontSize: '12px' }} /> copy properties
									</b>{' '}
									from another product
								</div>
							) : null}
						</div>

						<div className="dd-properties">
							<div className="dd" id="nestableProperties">
								<PropertiesList
									properties={values.properties}
									deleteProperty={this.deleteProperty}
									onSortEnd={this.handlePropertiesSwitch}
									useDragHandle={true}
									lockAxis={'y'}
									lockToContainerEdges={true}
									helperClass="sortable-helper"
								/>
							</div>
						</div>
					</div>
					<div className="card card-white">
						<div className="card-body">
							<div className="row seo-header align-items-center">
								<div className="col-sm-8">Search engine listing preview</div>
								<div className="col-sm-4 text-sm-right">
									<a className="btn btn-sm btn-link" href="#" onClick={this.openSeoEditor}>
										Edit website SEO
									</a>
								</div>
							</div>

							<div className="seo-preview">
								{values.seo_title ? (
									<div className="seo-preview__title edit-seo__title">{values.seo_title}</div>
								) : (
									<div className="seo-preview__title edit-seo__title">Page title</div>
								)}
								<div className="seo-preview__url">
									http://fastinsta.sommerce.net/
									<span className="edit-seo__url">{seoUrl}</span>
								</div>
								<div className="seo-preview__description edit-seo__meta">{values.seo_description}</div>
							</div>

							<div className={editSeo ? null : 'collapse'} id="seo-block">
								<div className="form-group">
									<label htmlFor="edit-seo__title">Page title</label>
									<Input
										className="form-control"
										id="edit-seo__title"
										value={values.seo_title}
										onChange={(event) => setFieldValue('seo_title', event.target.value)}
									/>
									<small className="form-text text-muted">
										<span className="edit-seo__title-muted" />
										{values.seo_title.length} of 70 characters used
									</small>
								</div>
								<div className="form-group">
									<label htmlFor="edit-seo__meta">Meta description</label>
									<textarea
										className="form-control"
										id="edit-seo__meta"
										rows="3"
										onChange={(event) => setFieldValue('seo_description', event.target.value)}
									/>
									<small className="form-text text-muted">
										<span className="edit-seo__meta-muted" /> {values.seo_description.length} of 160
										characters used
									</small>
								</div>
								<div className="form-group">
									<label htmlFor="edit-seo__meta-keyword">Meta keywords</label>
									<textarea
										className="form-control"
										id="edit-seo__meta-keyword"
										rows="3"
										onChange={(event) => setFieldValue('seo_keywords', event.target.value)}
									/>
								</div>
								<div className="form-group">
									<label htmlFor="edit-seo__url">URL</label>
									<div className="input-group">
										<span className="input-group-addon" id="basic-addon3">
											http://fastinsta.sommerce.net/
										</span>
										<input
											type="text"
											className="form-control"
											id="edit-seo__url"
											value={values.url}
											onChange={(event) => setFieldValue('url', event.target.value)}
										/>
									</div>
								</div>
							</div>
						</div>
					</div>
				</ModalBody>
			</React.Fragment>
		);
	}
}

class SummerNote extends React.Component {
	componentDidMount() {
		window.$(document).ready(() => {
			window.$('.summernote').summernote({
				minHeight: 300,
				toolbar: [
					[ 'style', [ 'style', 'bold', 'italic' ] ],
					[ 'lists', [ 'ul', 'ol' ] ],
					[ 'para', [ 'paragraph' ] ],
					[ 'color', [ 'color' ] ],
					[ 'insert', [ 'link', 'picture', 'video' ] ],
					[ 'codeview', [ 'codeview' ] ]
				],
				styleTags: [ 'p', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6' ],
				popover: {
					image: [ [ 'float', [ 'floatLeft', 'floatRight', 'floatNone' ] ], [ 'remove', [ 'removeMedia' ] ] ]
				},
        dialogsFade: true,
        disableDragAndDrop: true,
				dialogsInBody: true
			});

			window.$('.summernote').summernote('code', this.props.description);
			window.$('.summernote').on('summernote.change', (event) => {
				// callback as jquery custom event
				this.props.setFieldValue('description', window.$(event.target).summernote('code'));
			});
		});
	}
	render() {
		return null;
	}
}

export default ProductModal;
