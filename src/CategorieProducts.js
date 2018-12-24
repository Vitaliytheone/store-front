import { SortableContainer, arrayMove } from "react-sortable-hoc";
import React, { Component } from "react";
import { SortableProduct } from "./components/Product";
import AddProduct from "./components/AddProduct";
import {
  changePositionProduct,
  changePositionPackage,
  addPackage,
  addProduct
} from "./services/products";
import data from "./data.json";

//parse of json
const arrayData = Object.values(data).map(item => ({
  ...item,
  packages: Object.values(item.packages)
}));

const ProductList = SortableContainer(({ data, handlePackageSwitch, onPackageAdd }) => (
  <div className="sortable">
    {data.map((product, index) => (
      <SortableProduct
        key={`item-${index}`}
        product={product}
        index={index}
        handlePackageSwitch={handlePackageSwitch(index)}
        onPackageAdd={onPackageAdd(index)}
      />
    ))}
  </div>
));

class CategorieProducts extends Component {
  state = {
    data: arrayData
  };

  handleProductSwitch = ({ oldIndex, newIndex }) => {
    const { data } = this.state;
    this.setState({
      data: arrayMove(data, oldIndex, newIndex)
    });

    changePositionProduct({ oldIndex, newIndex });
  };

  handlePackageSwitch = productIndex => ({ oldIndex, newIndex }) => {
    const newData = [...this.state.data];
    const product = newData[productIndex];
    product.packages = arrayMove(product.packages, oldIndex, newIndex);

    this.setState({
      data: newData
    });

    changePositionPackage(productIndex, { oldIndex, newIndex });
  };

  handleAddProduct = async (values, actions) => {
    //let product position
    const newProductIndex = this.state.data.length;
    const newProduct = {
      name: values.name,
      position: newProductIndex,
      visibility: values.visibility,
      packages: []
    };
    this.setState(prevState => ({
      ...prevState,
      data: [...prevState.data, newProduct]
    }));
    const response = await addProduct(newProduct);
    const newData = [...this.state.data];
    //add new product to array end
    newData[newProductIndex] = response;
    this.setState(prevState => ({
      ...prevState,
      data: newData
    }));
    actions.setSubmitting(false);
  };

  handleAddPackage = (productIndex) => async (values, actions) => {
    //let packages position 
    const newPackageIndex = this.state.data[productIndex].packages.length;
    const newPackage = {
      product_id: this.state.data[productIndex].id,
      name: values.name,
      position: newPackageIndex,
      visibility: this.state.data[productIndex].visibility,
      price: values.price,
      quantity: values.quantity,
      overflow: values.overflow,
      availability: values.availability,
      mode: values.mode,
      provider: values.provider
    };
    const newData = this.state.data;
    newData[productIndex].packages.push(newPackage);
    this.setState(prevState => {
      return {
        ...prevState,
        data: newData
      };
    });
    const response = await addPackage(productIndex, newPackage);
    newData[productIndex].packages[newPackageIndex] = response;
    this.setState(prevState => ({
      ...prevState,
      data: newData
    }));
    actions.setSubmitting(false);
  };

  render() {
    const { data } = this.state;
    const { isSubmitting } = this.props;
    console.log(data);
    return (
      <div>
        <div className="page-container">
          <div className="m-container-sommerce container-fluid">
            <AddProduct onSubmit={this.handleAddProduct} isSubmitting={isSubmitting} />
            <div className="row">
              <div className="col-12">
                <div className="sommerce_dragtable">
                  <ProductList
                    handlePackageSwitch={this.handlePackageSwitch}
                    data={data}
                    useDragHandle={true}
                    onSortEnd={this.handleProductSwitch}
                    onPackageAdd={this.handleAddPackage}
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

export default CategorieProducts;
