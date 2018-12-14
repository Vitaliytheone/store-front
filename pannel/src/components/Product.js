import React, { Component } from "react";
import { Droppable } from "react-beautiful-dnd";
import Package from "./Package";
import EditProduct from "./EditProduct";
import AddPackage from "./AddPackage";

class Product extends Component {
  render() {
    const { data, innerRef, ...rest } = this.props;

    return (
      <div className="row group-caption" ref={innerRef} {...rest}>
        <div className="col-12 sommerce_dragtable__category">
          <div className="sommerce_dragtable__category-title">
            <div className="row align-items-center">
              <div className="col-12">
                <div className="sommerce_dragtable__category-move move product-pointer-events">
                  <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                    <title>Drag-Handle</title>
                    <path
                      d="M7 2c-1.104 0-2 .896-2 2s.896 2 2 2 2-.896 2-2-.896-2-2-2zm0 6c-1.104 0-2 .896-2 2s.896 2 2 2 2-.896 2-2-.896-2-2-2zm0 6c-1.104 0-2 .896-2 2s.896 2 2 2 2-.896 2-2-.896-2-2-2zm6-8c1.104 0 2-.896 2-2s-.896-2-2-2-2 .896-2 2 .896 2 2 2zm0 2c-1.104 0-2 .896-2 2s.896 2 2 2 2-.896 2-2-.896-2-2-2zm0 6c-1.104 0-2 .896-2 2s.896 2 2 2 2-.896 2-2-.896-2-2-2z"
                      fill="#d4d4d4"
                    />
                  </svg>
                </div>
                {data.name}
                <EditProduct />
              </div>
            </div>
          </div>
        </div>
        <div className="col-12 group-items">
          {data.packages.map((pack, index) => (
            <Package key={index} pack={pack} />
          ))}
          <AddPackage />
        </div>
      </div>
    );
  }
}

export default Product;
