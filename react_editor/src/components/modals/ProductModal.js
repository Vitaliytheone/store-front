import React from 'react';
import PropertiesList from './PropertiesList';
import { ModalBody, Label, FormGroup, Input } from 'reactstrap';
import { Field } from 'formik';
import { ProductInput } from '../Inputs';
import { arrayMove } from 'react-sortable-hoc';
import {  ChromePicker } from 'react-color';

// // Import bootstrap(v3 or v4) dependencies
// import 'bootstrap/js/src/dropdown';
// import 'bootstrap/js/src/tooltip';
import 'bootstrap/js/src/modal';

const colors = ["#f44336", "#E91E63", "#9C27B0", "#673AB7", "#3F51B5", "#2196F3", "#03A9F4", "#00BCD4", "#009688", "#4CAF50", "#8BC34A", "#CDDC39", "#FFEB3B", "#FFC107", "#FF9800", "#FF5722", "#795548", "#9E9E9E", "#607D8B",
	"#ffebee", "#FCE4EC", "#F3E5F5", "#EDE7F6", "#E8EAF6", "#E3F2FD", "#E1F5FE", "#E0F7FA", "#E0F2F1", "#E8F5E9", "#F1F8E9", "#F9FBE7", "#FFFDE7", "#FFF8E1", "#FFF3E0", "#FBE9E7", "#EFEBE9", "#FAFAFA", "#ECEFF1",
	"#ffcdd2", "#F8BBD0", "#E1BEE7", "#D1C4E9", "#C5CAE9", "#BBDEFB", "#B3E5FC", "#B2EBF2", "#B2DFDB", "#C8E6C9", "#DCEDC8", "#F0F4C3", "#FFF9C4", "#FFECB3", "#FFE0B2", "#FFCCBC", "#D7CCC8", "#F5F5F5", "#CFD8DC",
	"#ef9a9a", "#F48FB1", "#CE93D8", "#B39DDB", "#9FA8DA", "#90CAF9", "#81D4FA", "#80DEEA", "#80CBC4", "#A5D6A7", "#C5E1A5", "#E6EE9C", "#FFF59D", "#FFE082", "#FFCC80", "#FFAB91", "#BCAAA4", "#EEEEEE", "#B0BEC5",
	"#e57373", "#F06292", "#BA68C8", "#9575CD", "#7986CB", "#64B5F6", "#4FC3F7", "#4DD0E1", "#4DB6AC", "#81C784", "#AED581", "#DCE775", "#FFF176", "#FFD54F", "#FFB74D", "#FF8A65", "#A1887F", "#E0E0E0", "#90A4AE",
	"#ef5350", "#EC407A", "#AB47BC", "#7E57C2", "#5C6BC0", "#42A5F5", "#29B6F6", "#26C6DA", "#26A69A", "#66BB6A", "#9CCC65", "#D4E157", "#FFEE58", "#FFCA28", "#FFA726", "#FF7043", "#8D6E63", "#BDBDBD", "#78909C",
	"#f44336", "#E91E63", "#9C27B0", "#673AB7", "#3F51B5", "#2196F3", "#03A9F4", "#00BCD4", "#009688", "#4CAF50", "#8BC34A", "#CDDC39", "#FFEB3B", "#FFC107", "#FF9800", "#FF5722", "#795548", "#9E9E9E", "#607D8B",
	"#e53935", "#D81B60", "#8E24AA", "#5E35B1", "#3949AB", "#1E88E5", "#039BE5", "#00ACC1", "#00897B", "#43A047", "#7CB342", "#C0CA33", "#FDD835", "#FFB300", "#FB8C00", "#F4511E", "#6D4C41", "#757575", "#546E7A",
	"#d32f2f", "#C2185B", "#7B1FA2", "#512DA8", "#303F9F", "#1976D2", "#0288D1", "#0097A7", "#00796B", "#388E3C", "#689F38", "#AFB42B", "#FBC02D", "#FFA000", "#F57C00", "#E64A19", "#5D4037", "#616161", "#455A64",
	"#c62828", "#AD1457", "#6A1B9A", "#4527A0", "#283593", "#1565C0", "#0277BD", "#00838F", "#00695C", "#2E7D32", "#558B2F", "#9E9D24", "#F9A825", "#FF8F00", "#EF6C00", "#D84315", "#4E342E", "#424242", "#37474F",
	"#b71c1c", "#880E4F", "#4A148C", "#311B92", "#1A237E", "#0D47A1", "#01579B", "#006064", "#004D40", "#1B5E20", "#33691E", "#827717", "#F57F17", "#FF6F00", "#E65100", "#BF360C", "#3E2723", "#212121", "#263238"]

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
					{/* <SketchPicker color={this.props.values.color} onChange={this.handleChangeComplete} /> */}
					<div className="sommerce-colopicker">
						<div className="sommerce-colopicker__body">
							<div className="sommerce-colopicker__body-left">
							{colors.map(item => (
								<div className="sommerce-colopicker__color-pie" style={{background: item }}></div>
							))}
							</div>
							<div className="sommerce-colopicker__body-right">
								<div className="sommerce-colopicker__picker">
									<ChromePicker className="chrome-picker" color={this.props.values.color} onChange={this.handleChangeComplete} />
									</div>
								<div className="sommerce-colopicker__picker-actions text-right">
									<button className="btn btn-sm m-btn--pill m-btn--air btn-primary">Choose</button>
								</div>
							</div>
						</div>
					</div>
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
										className={"sp-preview-inner " + (this.props.values.color ? null : "sp-clear-display")}
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
