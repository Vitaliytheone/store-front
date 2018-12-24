import React from "react";
import "../styles/AddProduct.css";
import { Field } from "formik";
import { ProductInput } from "../components/Inputs";

const ProductModal = () => (
<div className="modal-body">
  <div className="form-group">
    <Field
      name="name"
      component={ProductInput}
      label="Product name"
      placeholder="create a product"
      required
    />
  </div>

  <div className="form-group">
    <label htmlFor="visibility">Visibility</label>
    <Field className="form-control" component="select" name="visibility">
      <option value="1">Enabled</option>
      <option value="2">Disabled</option>
    </Field>
  </div>

  <div className="form-group">
    <label>Color</label>
    <div className="product-color__wrap">
      <input
        type="text"
        className="product-color"
        id="package-color2"
        value="#ffffff"
      />
    </div>
  </div>

  <div className="form-group">
    <textarea id="summernote" />
  </div>
  <div className="card card-white mb-3">
    <div className="card-body">
      <div className="row seo-header align-items-center">
        <div className="col-sm-8">Properties</div>
        <div className="col-sm-4 text-sm-right">
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
        </div>
      </div>
      <div className="form-group">
        <div className="input-group">
          <input type="text" className="form-control input-properties" />
          <span className="input-group-btn">
            <button
              className="btn btn-primary add-properies"
              type="button"
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
        <ol className="dd-list">
          <li className="dd-item" data-id="3">
            <div className="dd-handle">
              <div className="dd-handle__icon">
                <svg
                  xmlns="http://www.w3.org/2000/svg"
                  viewBox="0 0 20 20"
                >
                  <title>Drag-Handle</title>
                  <path
                    d="M7 2c-1.104 0-2 .896-2 2s.896 2 2 2 2-.896 2-2-.896-2-2-2zm0 6c-1.104 0-2 .896-2 2s.896 2 2 2 2-.896 2-2-.896-2-2-2zm0 6c-1.104 0-2 .896-2 2s.896 2 2 2 2-.896 2-2-.896-2-2-2zm6-8c1.104 0 2-.896 2-2s-.896-2-2-2-2 .896-2 2 .896 2 2 2zm0 2c-1.104 0-2 .896-2 2s.896 2 2 2 2-.896 2-2-.896-2-2-2zm0 6c-1.104 0-2 .896-2 2s.896 2 2 2 2-.896 2-2-.896-2-2-2z"
                    fill="#c6cad4"
                  />
                </svg>
              </div>
              Est.Delivery Time: 6-12 hrs
                  </div>
            <div className="dd-edit-button">
              <a
                href="#"
                className="m-portlet__nav-link btn m-btn m-btn--hover-danger m-btn--icon m-btn--icon-only m-btn--pill"
                title="Delete"
              >
                <i className="la la-trash" />
              </a>
            </div>
          </li>
          <li className="dd-item" data-id="3">
            <div className="dd-handle">
              <div className="dd-handle__icon">
                <svg
                  xmlns="http://www.w3.org/2000/svg"
                  viewBox="0 0 20 20"
                >
                  <title>Drag-Handle</title>
                  <path
                    d="M7 2c-1.104 0-2 .896-2 2s.896 2 2 2 2-.896 2-2-.896-2-2-2zm0 6c-1.104 0-2 .896-2 2s.896 2 2 2 2-.896 2-2-.896-2-2-2zm0 6c-1.104 0-2 .896-2 2s.896 2 2 2 2-.896 2-2-.896-2-2-2zm6-8c1.104 0 2-.896 2-2s-.896-2-2-2-2 .896-2 2 .896 2 2 2zm0 2c-1.104 0-2 .896-2 2s.896 2 2 2 2-.896 2-2-.896-2-2-2zm0 6c-1.104 0-2 .896-2 2s.896 2 2 2 2-.896 2-2-.896-2-2-2z"
                    fill="#c6cad4"
                  />
                </svg>
              </div>
              Password/Admin access Not Required
                  </div>
            <div className="dd-edit-button">
              <a
                href="#"
                className="m-portlet__nav-link btn m-btn m-btn--hover-danger m-btn--icon m-btn--icon-only m-btn--pill"
                title="Delete"
              >
                <i className="la la-trash" />
              </a>
            </div>
          </li>
          <li className="dd-item" data-id="3">
            <div className="dd-handle">
              <div className="dd-handle__icon">
                <svg
                  xmlns="http://www.w3.org/2000/svg"
                  viewBox="0 0 20 20"
                >
                  <title>Drag-Handle</title>
                  <path
                    d="M7 2c-1.104 0-2 .896-2 2s.896 2 2 2 2-.896 2-2-.896-2-2-2zm0 6c-1.104 0-2 .896-2 2s.896 2 2 2 2-.896 2-2-.896-2-2-2zm0 6c-1.104 0-2 .896-2 2s.896 2 2 2 2-.896 2-2-.896-2-2-2zm6-8c1.104 0 2-.896 2-2s-.896-2-2-2-2 .896-2 2 .896 2 2 2zm0 2c-1.104 0-2 .896-2 2s.896 2 2 2 2-.896 2-2-.896-2-2-2zm0 6c-1.104 0-2 .896-2 2s.896 2 2 2 2-.896 2-2-.896-2-2-2z"
                    fill="#c6cad4"
                  />
                </svg>
              </div>
              Follow Others Not Required
                  </div>
            <div className="dd-edit-button">
              <a
                href="#"
                className="m-portlet__nav-link btn m-btn m-btn--hover-danger m-btn--icon m-btn--icon-only m-btn--pill"
                title="Delete"
              >
                <i className="la la-trash" />
              </a>
            </div>
          </li>
          <li className="dd-item" data-id="3">
            <div className="dd-handle">
              <div className="dd-handle__icon">
                <svg
                  xmlns="http://www.w3.org/2000/svg"
                  viewBox="0 0 20 20"
                >
                  <title>Drag-Handle</title>
                  <path
                    d="M7 2c-1.104 0-2 .896-2 2s.896 2 2 2 2-.896 2-2-.896-2-2-2zm0 6c-1.104 0-2 .896-2 2s.896 2 2 2 2-.896 2-2-.896-2-2-2zm0 6c-1.104 0-2 .896-2 2s.896 2 2 2 2-.896 2-2-.896-2-2-2zm6-8c1.104 0 2-.896 2-2s-.896-2-2-2-2 .896-2 2 .896 2 2 2zm0 2c-1.104 0-2 .896-2 2s.896 2 2 2 2-.896 2-2-.896-2-2-2zm0 6c-1.104 0-2 .896-2 2s.896 2 2 2 2-.896 2-2-.896-2-2-2z"
                    fill="#c6cad4"
                  />
                </svg>
              </div>
              High Quality Followers
                  </div>
            <div className="dd-edit-button">
              <a
                href="#"
                className="m-portlet__nav-link btn m-btn m-btn--hover-danger m-btn--icon m-btn--icon-only m-btn--pill"
                title="Delete"
              >
                <i className="la la-trash" />
              </a>
            </div>
          </li>
          <li className="dd-item" data-id="3">
            <div className="dd-handle">
              <div className="dd-handle__icon">
                <svg
                  xmlns="http://www.w3.org/2000/svg"
                  viewBox="0 0 20 20"
                >
                  <title>Drag-Handle</title>
                  <path
                    d="M7 2c-1.104 0-2 .896-2 2s.896 2 2 2 2-.896 2-2-.896-2-2-2zm0 6c-1.104 0-2 .896-2 2s.896 2 2 2 2-.896 2-2-.896-2-2-2zm0 6c-1.104 0-2 .896-2 2s.896 2 2 2 2-.896 2-2-.896-2-2-2zm6-8c1.104 0 2-.896 2-2s-.896-2-2-2-2 .896-2 2 .896 2 2 2zm0 2c-1.104 0-2 .896-2 2s.896 2 2 2 2-.896 2-2-.896-2-2-2zm0 6c-1.104 0-2 .896-2 2s.896 2 2 2 2-.896 2-2-.896-2-2-2z"
                    fill="#c6cad4"
                  />
                </svg>
              </div>
              Customer satisfection
                  </div>
            <div className="dd-edit-button">
              <a
                href="#"
                className="m-portlet__nav-link btn m-btn m-btn--hover-danger m-btn--icon m-btn--icon-only m-btn--pill"
                title="Delete"
              >
                <i className="la la-trash" />
              </a>
            </div>
          </li>
        </ol>
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
        <div className="seo-preview__title edit-seo__title">Product</div>
        <div className="seo-preview__url">
          http://fastinsta.sommerce.net/
                <span className="edit-seo__url">product</span>
        </div>
        <div className="seo-preview__description edit-seo__meta">
          A great About Us page helps builds trust between you and your
          customers. The more content you provide about you and your
          business, the more confident people wil...
                    </div>
      </div>

      <div className="collapse" id="seo-block">
        <div className="form-group">
          <label for="edit-seo__title">Page title</label>
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
          <label for="edit-seo__meta">Meta description</label>
          <textarea className="form-control" id="edit-seo__meta" rows="3">
            A great About Us page helps builds trust between you and your
            customers. The more content you provide about you and your
            business, the more confident people will text
                </textarea>
          <small className="form-text text-muted">
            <span className="edit-seo__meta-muted" /> of 160 characters
            used
                </small>
        </div>
        <div className="form-group">
          <label for="edit-seo__meta-keyword">Meta keywords</label>
          <textarea
            className="form-control"
            id="edit-seo__meta-keyword"
            rows="3"
          />
        </div>
        <div className="form-group">
          <label for="edit-seo__url">URL</label>
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
</div>);


export default ProductModal;
