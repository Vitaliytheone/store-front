import React, { Component } from "react";
// import PropertiesList from "./Properties"
import PropertiesList from "./PropertiesList";
import { ModalBody, Label, FormGroup, Input, Button } from "reactstrap";
import { Field } from "formik";
import { ProductInput } from "../Inputs";
import { arrayMove } from "react-sortable-hoc";
import { SketchPicker } from "react-color";

// import $ from 'jquery';
// import ReactSummernote from 'react-summernote';
// import 'react-summernote/dist/react-summernote.css'; // import styles


// // Import bootstrap(v3 or v4) dependencies
// import 'bootstrap/js/src/dropdown';
// import 'bootstrap/js/src/tooltip';
import "bootstrap/js/src/modal";

class ProductModal extends Component {
  state = {
    colorSchema: false,
    color: "",
    item: "",
    properties: []
  };

  // toggle = () => {
  //   this.setState(prevstate => ({
  //     colorSchema: !prevstate.colorSchema
  //   }));
  // };

  onChange = event => {
    this.setState({
      item: event.target.value
    });
  };

  addProperty = () => {
    this.setState({
      properties: [...this.state.properties, this.state.item]
    });
    this.props.setFieldValue("properties", this.state.properties)
  };

  deleteProperty = index => () => {
    const newProperties = [...this.state.properties];
    newProperties.splice(index, 1);
    this.setState({ properties: newProperties });
  };

  handlePropertiesSwitch = ({ oldIndex, newIndex }) => {
    const { properties } = this.state;
    const propertiesMove = arrayMove(properties, oldIndex, newIndex);
    this.setState({
      properties: propertiesMove
    });
  };

  handleChangeComplete = color => {
    this.setState({ color: color.hex });
    this.props.setFieldValue("color", color.hex);
  };

  openColorSchema = () => {
    this.setState({
      colorSchema: !this.state.colorSchema
    });
  };

  closeColorSchema = () => {
    this.setState({
      colorSchema: false
    })
  }

  componentDidMount() {
    window.$(document).ready(() => {
      window.$(".summernote").summernote({
        height: 300,
        minHeight: null,
        maxHeight: null
      });
      window.$(".summernote").on("summernote.change", event => {
        // callback as jquery custom event
        this.props.setFieldValue(
          "description",
        window.$(event.target).summernote("code")
        );
      });
    });
  }

  render() {
    const { values } = this.props;
    const { color, colorSchema } = this.state;
    const seoName = values.name.replace(/ /g, "-");

    let colorHex;
    if (colorSchema) {
      colorHex = ( <div className="color-schema">
          <div className="cover-schema" onClick={this.closeColorSchema} />
          <SketchPicker color={this.state.background} onChangeComplete={this.handleChangeComplete} />
        </div>
      );
    }

    return (
      <React.Fragment>
        <ModalBody>
          <FormGroup>
            <Field
              name="name"
              component={ProductInput}
              label="Product name"
              required
            />
          </FormGroup>

          <FormGroup>
            <Label htmlFor="visibility">Visibility</Label>
            <Field
              className="form-control"
              component="select"
              name="visibility"
            >
              <option value="Enabled">Enabled</option>
              <option value="Disabled">Disabled</option>
            </Field>
          </FormGroup>

          <FormGroup>
            <Label>Color</Label>
            <div className="product-color__wrap">
              <Input
                type="text"
                className="product-color"
                id="package-color2"
                value={color}
              />
              <div class="sp-replacer sp-light" onClick={this.openColorSchema}>
                <div class="sp-preview">
                  <div
                    class="sp-preview-inner sp-clear-display"
                    style={{ backgroundColor: "transparent" }}
                  />
                </div>
                <div class="sp-dd">▼</div>
              </div>
            </div>
              {colorHex}
          </FormGroup>

          <FormGroup>
            {/* <ReactSummernote
              value="Default value"
              options={{
                minHeight: 300,
                focus: true,
                toolbar: [
                  ['style', ['style', 'bold', 'italic']],
                  ['lists', ['ul', 'ol']],
                  ['para', ['paragraph']],
                  ['color', ['color']],
                  ['insert', ['link', 'picture', 'video']],
                  ['codeview', ['codeview']]
                ],
                disableDragAndDrop: true,
                styleTags: ['p', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6'],
                popover: {
                  image: [
                    ['float', ['floatLeft', 'floatRight', 'floatNone']],
                    ['remove', ['removeMedia']]
                  ],
                },
                dialogsFade: true
              }}
              onChange={this.onChange}
            /> */}
            <div class="summernote" />
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
                    value={this.state.item}
                    type="text"
                    className="form-control input-properties"
                    onChange={this.onChange}
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
              <div className="alert m-alert--default" role="alert">
                Create a new property or{" "}
                <b>
                  <span className="la la-clone" style={{ fontSize: "12px" }} />{" "}
                  copy properties
                </b>{" "}
                from another product
              </div>
            </div>

            <div className="dd-properties">
              <div className="dd" id="nestableProperties">
                <PropertiesList
                  properties={this.state.properties}
                  deleteProperty={this.deleteProperty}
                  onSortEnd={this.handlePropertiesSwitch}
                  useDragHandle={true}
                  lockAxis={"y"}
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
                  <a className="btn btn-sm btn-link" href="#seo-block">
                    Edit website SEO
                  </a>
                </div>
              </div>

              <div className="seo-preview">
                {values.name ? (
                  <div className="seo-preview__title edit-seo__title">
                    {values.name}
                  </div>
                ) : (
                  <div className="seo-preview__title edit-seo__title">
                    Page title
                  </div>
                )}
                <div className="seo-preview__url">
                  http://fastinsta.sommerce.net/
                  <span className="edit-seo__url">{seoName}</span>
                </div>
                <div className="seo-preview__description edit-seo__meta">
                  A great About Us page helps builds trust between you and your
                  customers. The more content you provide about you and your
                  business, the more confident people wil...
                </div>
              </div>

              <div className="collapse" id="seo-block">
                <div className="form-group">
                  <label htmlFor="edit-seo__title">Page title</label>
                  <input
                    className="form-control"
                    id="edit-seo__title"
                    value="Product"
                  />
                  <small className="form-text text-muted">
                    <span className="edit-seo__title-muted" /> of 70 characters
                    used
                  </small>
                </div>
                <div className="form-group">
                  <label htmlFor="edit-seo__meta">Meta description</label>
                  <textarea
                    className="form-control"
                    id="edit-seo__meta"
                    rows="3"
                  >
                    A great About Us page helps builds trust between you and
                    your customers. The more content you provide about you and
                    your business, the more confident people will text
                  </textarea>
                  <small className="form-text text-muted">
                    <span className="edit-seo__meta-muted" /> of 160 characters
                    used
                  </small>
                </div>
                <div className="form-group">
                  <label htmlFor="edit-seo__meta-keyword">Meta keywords</label>
                  <textarea
                    className="form-control"
                    id="edit-seo__meta-keyword"
                    rows="3"
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
                      value="about-us"
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


export default ProductModal;
