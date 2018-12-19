import { SortableContainer, arrayMove } from "react-sortable-hoc";
import React, { Component } from "react";
import { SortableProduct } from "./components/Product";
import AddProduct from "./components/AddProduct";
import {
  changePositionProduct,
  changePositionPackage
} from "./services/products";
import data from "./data.json";

//parse of json
const arrayData = Object.values(data).map(item => ({
  ...item,
  packages: Object.values(item.packages)
}));

const ProductList = SortableContainer(({ data, handlePackageSwitch }) => (
  <div className="sortable">
    {data.map((product, index) => (
      <SortableProduct
        key={`item-${index}`}
        product={product}
        index={index}
        handlePackageSwitch={handlePackageSwitch(index)}
      />
    ))}
  </div>
));

class Products extends Component {
  state = {
    data: arrayData
  };

  handleProductSwitch = ({ oldIndex, newIndex }) => {
    // const data = [...this.state.data];
    // [data[oldIndex], data[newIndex]] = [data[newIndex], data[oldIndex]];
    //
    // this.setState({
    //   data
    // });

    const { data } = this.state;
    this.setState({
      data: arrayMove(data, oldIndex, newIndex)
    });

    changePositionProduct(oldIndex, { oldIndex, newIndex });
  };

  handlePackageSwitch = productIndex => ({ oldIndex, newIndex }) => {
    const newData = [...this.state.data];
    const product = newData[productIndex];
    product.packages = arrayMove(product.packages, oldIndex, newIndex);

    this.setState({
      data: newData
    });

    changePositionPackage(productIndex, { oldIndex, newIndex });
    // const newData = [...this.state.data];
    // const product = newData[productIndex];
    // const packages = product.packages;
    // [packages[oldIndex], packages[newIndex]] = [
    //   packages[newIndex],
    //   packages[oldIndex]
    // ];
    //
    // this.setState(prevState => ({
    //   data: newData
    // }));
  };

  render() {
    const { data } = this.state;
    return (
      <div>
        <div className="page-container">
          <div className="m-container-sommerce container-fluid">
            <AddProduct />
            <div className="row">
              <div className="col-12">
                <div className="sommerce_dragtable">
                  <ProductList
                    handlePackageSwitch={this.handlePackageSwitch}
                    data={data}
                    useDragHandle={true}
                    onSortEnd={this.handleProductSwitch}
                  />
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    );
  }
}

export default Products;
