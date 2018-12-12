import React, { Component } from "react";
import Header from "./components/Header";
import AddProduct from "./components/AddProduct";
import "./App.css";
import data from "./data.json";

//parse of json
const arrayData = Object.values(data).map(item => ({
  ...item,
  packages: Object.values(item.packages)
}));
console.log(arrayData);

class App extends Component {
  render() {
    return (
      <div>
        <Header />
        <div className="page-container">
          <div className="m-container-sommerce container-fluid">
            <AddProduct />

            <div className="row">
              <div className="col-12">
                <div className="sommerce_dragtable">
                  <div className="sortable">
                    {arrayData.map(data => (
                      <div className="row group-caption">
                        <div className="col-12 sommerce_dragtable__category">
                          <div className="sommerce_dragtable__category-title">
                            <div className="row align-items-center">
                              <div className="col-12">
                                <div className="sommerce_dragtable__category-move move">
                                  <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    viewBox="0 0 20 20"
                                  >
                                    <title>Drag-Handle</title>
                                    <path
                                      d="M7 2c-1.104 0-2 .896-2 2s.896 2 2 2 2-.896 2-2-.896-2-2-2zm0 6c-1.104 0-2 .896-2 2s.896 2 2 2 2-.896 2-2-.896-2-2-2zm0 6c-1.104 0-2 .896-2 2s.896 2 2 2 2-.896 2-2-.896-2-2-2zm6-8c1.104 0 2-.896 2-2s-.896-2-2-2-2 .896-2 2 .896 2 2 2zm0 2c-1.104 0-2 .896-2 2s.896 2 2 2 2-.896 2-2-.896-2-2-2zm0 6c-1.104 0-2 .896-2 2s.896 2 2 2 2-.896 2-2-.896-2-2-2z"
                                      fill="#d4d4d4"
                                    />
                                  </svg>
                                </div>
                                {data.name}
                                <a
                                  href="#"
                                  className="btn m-btn--pill m-btn--air btn-primary btn-sm sommerce_dragtable__action"
                                  data-toggle="modal"
                                  data-target=".add_product"
                                >
                                  Edit
                                </a>
                              </div>
                            </div>
                          </div>
                        </div>
                        <div className="col-12 group-items">
                          {data.packages.map(pack => (
                            <div className="group-item sommerce_dragtable__tr align-items-center">
                              <div className="col-lg-5 padding-null-left">
                                <div className="sommerce_dragtable__category-move move">
                                  <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    viewBox="0 0 20 20"
                                  >
                                    <title>Drag-Handle</title>
                                    <path
                                      d="M7 2c-1.104 0-2 .896-2 2s.896 2 2 2 2-.896 2-2-.896-2-2-2zm0 6c-1.104 0-2 .896-2 2s.896 2 2 2 2-.896 2-2-.896-2-2-2zm0 6c-1.104 0-2 .896-2 2s.896 2 2 2 2-.896 2-2-.896-2-2-2zm6-8c1.104 0 2-.896 2-2s-.896-2-2-2-2 .896-2 2 .896 2 2 2zm0 2c-1.104 0-2 .896-2 2s.896 2 2 2 2-.896 2-2-.896-2-2-2zm0 6c-1.104 0-2 .896-2 2s.896 2 2 2 2-.896 2-2-.896-2-2-2z"
                                      fill="#d4d4d4"
                                    />
                                  </svg>
                                </div>
                                <strong>{pack.name}</strong>
                              </div>
                              <div className="col-lg-2">{pack.price}</div>
                              <div className="col-lg-2">{pack.provider}</div>
                              <div className="col-lg-2 text-lg-center">
                                Enabled
                              </div>
                              <div className="col-lg-1 padding-null-lg-right text-lg-right text-sm-left">
                                <button
                                  type="button"
                                  className="btn m-btn--pill m-btn--air btn-primary btn-sm sommerce_dragtable__action"
                                  data-toggle="modal"
                                  data-target=".edit_package"
                                >
                                  Edit
                                </button>
                                <a
                                  href="#"
                                  className="m-portlet__nav-link btn m-btn m-btn--hover-danger m-btn--icon m-btn--icon-only m-btn--pill"
                                  data-toggle="modal"
                                  data-target="#delete-modal"
                                  data-backdrop="static"
                                  title="Delete"
                                >
                                  <i className="la la-trash" />
                                </a>
                              </div>
                            </div>
                          ))}
                          <div className="mt-2 mb-3">
                            <button
                              className="btn btn-primary btn-sm m-btn m-btn--icon btm-sm m-btn--air"
                              data-toggle="modal"
                              data-target=".add_package"
                              data-backdrop="static"
                            >
                              Add package
                            </button>
                          </div>
                        </div>
                      </div>
                    ))}
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    );
  }
}

export default App;
