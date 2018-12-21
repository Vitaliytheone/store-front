import { SortableContainer, arrayMove } from "react-sortable-hoc";
import React, { Component } from "react";
import { SortableProduct } from "./components/Product";
import AddProduct from "./components/AddProduct";
import {
  changePositionProduct,
  changePositionPackage
} from "./services/products";
import data from "./data.json";
import axios from "axios";
import { map, sortBy } from "lodash";

const ProductList = SortableContainer(({ data, handlePackageSwitch }) => (
  <div className="sortable">
    {sortBy(data, "position").map((product, index) => (
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
    data
  };

  handleProductSwitch = ({ oldIndex,  newIndex, collection }) => {
    const [firstItemId, secondItemId] = [collection[oldIndex], collection[newIndex]].map(({id}) => id);

    const { data } = this.state;
    this.setState(prevState => ({
      ...prevState,
      data: {
        ...prevState.data,
        [firstItemId]: collection[newIndex],
        [secondItemId]: collection[oldIndex]
      }
    });

    changePositionProduct(oldIndex, { oldIndex, newIndex });
  };

  handlePackageSwitch = productIndex => ({ oldIndex, newIndex, collection }) => {
    const newData = [...this.state.data];
    const product = newData[productIndex];
    product.packages = arrayMove(product.packages, oldIndex, newIndex);

    this.setState({
      data: newData
    });

    changePositionPackage(productIndex, { oldIndex, newIndex });
  };

  handleAddProduct = async (values, actions) => {
    const newProduct = {
      id: null,
      name: values.name,
      position: this.state.products.length,
      visibility: values.visibility
    };
    this.setState(prevState => ({
      ...prevState,
      data: [...prevState.data, newProduct]
    }));
    await axios.post("/products", newProduct);
    actions.setSubmitting(false);
  };

  render() {
    const { data } = this.state;
    console.log(data);
    return (
      <div>
        <div className="page-container">
          <div className="m-container-sommerce container-fluid">
            <AddProduct onSubmit={this.handleAddProduct} />
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
