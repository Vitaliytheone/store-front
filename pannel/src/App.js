import React, { Component } from "react";
import { DragDropContext } from "react-beautiful-dnd";
import Header from "./components/Header";
import Product from "./components/Product";
import AddProduct from "./components/AddProduct";
import "./App.css";
import data from "./data.json";

//parse of json
const arrayData = Object.values(data).map(item => ({
  ...item,
  packages: Object.values(item.packages)
}));

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
                    {arrayData.map((data, index) => (
                      <Product key={index} data={data} />
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
