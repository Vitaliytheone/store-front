import React, { Component } from "react";
import SortablePackage from "./Package";
import EditProduct from "./EditProduct";
import AddPackage from "./AddPackage";
import { SortableContainer } from "react-sortable-hoc";

const PackageList = SortableContainer(({ product }) => (
  <div className="col-12 group-items">
    {product.packages.map((pack, index) => (
      <SortablePackage key={`item-${index}`} pack={pack} index={index} />
    ))}
    <AddPackage />
  </div>
));

class Product extends Component {
  render() {
    const {
      product,
      dragProvided,
      innerRef,
      handlePackageSwitch,
      ...rest
    } = this.props;

    return (
      <div
        className="row group-caption"
        ref={dragProvided.innerRef}
        {...dragProvided.draggableProps}
        {...rest}
      >
        <div className="col-12 sommerce_dragtable__category">
          <div className="sommerce_dragtable__category-title">
            <div className="row align-items-center">
              <div className="col-12">
                <div
                  className="sommerce_dragtable__category-move move"
                  {...dragProvided.dragHandleProps}
                >
                  <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                    <title>Drag-Handle</title>
                    <path
                      d="M7 2c-1.104 0-2 .896-2 2s.896 2 2 2 2-.896 2-2-.896-2-2-2zm0 6c-1.104 0-2 .896-2 2s.896 2 2 2 2-.896 2-2-.896-2-2-2zm0 6c-1.104 0-2 .896-2 2s.896 2 2 2 2-.896 2-2-.896-2-2-2zm6-8c1.104 0 2-.896 2-2s-.896-2-2-2-2 .896-2 2 .896 2 2 2zm0 2c-1.104 0-2 .896-2 2s.896 2 2 2 2-.896 2-2-.896-2-2-2zm0 6c-1.104 0-2 .896-2 2s.896 2 2 2 2-.896 2-2-.896-2-2-2z"
                      fill="#d4d4d4"
                    />
                  </svg>
                </div>
                {product.name}
                <EditProduct />
              </div>
            </div>
          </div>
        </div>
        <PackageList
          lockAxis={"y"}
          lockToContainerEdges={true}
          product={product}
          onSortEnd={handlePackageSwitch}
          useDragHandle={true}
        />
      </div>
    );
  }
}

export default Product;
