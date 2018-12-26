import { SortableContainer, arrayMove } from "react-sortable-hoc";
import React, { Component } from "react";
import { SortableProduct } from "./components/Product";
import AddProduct from "./components/AddProduct";
import {
  changePositionProduct,
  changePositionPackage,
  addPackage,
  addProduct,
  updateProduct,
  updatePackage
} from "./services/products";
import data from "./data.json";
import { sortBy } from "lodash";

//parse of json
const arrayDataParse = Object.values(data).map(item => ({
  ...item,
  position: +(item.position),
  packages: Object.values(item.packages)
}));

//sort data of elements position
const arrayData = sortBy(arrayDataParse, "position");

const ProductList = SortableContainer(({ data, handlePackageSwitch, onPackageAdd, handleEditProduct }) => (
  <div className="sortable">
    {data.map((product, index) => (
      <SortableProduct
        key={`item-${index}`}
        product={product}
        index={index}
        handlePackageSwitch={handlePackageSwitch(index)}
        handleEditProduct={handleEditProduct(index)}
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
    const arrayData = arrayMove(data, oldIndex, newIndex);
    // new position = new index
    const newData = arrayData.map((product, index) => ({
      ...product, position: index
    }))
    this.setState({
      data: newData
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
    //add new product to array end (server)
    newData[newProductIndex] = response.data;
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

  handleEditProduct = (productIndex) => async (values, actions) => {
      const editedProduct = [...this.state.data]//сopy state
      editedProduct[productIndex] = { //change fields of product 
        name: values.name,
        visibility: values.visibility
      }
      this.setState(prevState => ({
        ...prevState,
        data: editedProduct
      }))

      const response = await updateProduct(productIndex, editedProduct[productIndex]);

  };

  // handleEditPackage = (productIndex) => (packageIndex) => async (values, actions) => {

  // };

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
                    handleEditProduct={this.handleEditProduct}
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
