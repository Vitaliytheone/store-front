import React, { Component } from "react";
 import axios from "axios";
import { Container, Row, Col, Jumbotron } from "reactstrap";
import { arrayMove } from "react-sortable-hoc";
import AddProduct from "./components/AddProduct";
import ProductList from "./components/SortableComponents/ProductList";
import {
  changePositionProduct,
  changePositionPackage,
  addPackage,
  addProduct,
  updateProduct,
  updatePackage,
  deletePackage,
  addListing
} from "./services/url";
import { sortBy, pick } from "lodash";
import data from "./data.json";


// //parse of json
// const arrayDataParse = Object.values(data).map(item => ({
//   ...item,
//   position: +(item.position), //cast position to a number 
//   packages: sortBy(Object.values(item.packages), "position")//sort packages by position
// }));

// //sort data of elements position
// const arrayData = sortBy(arrayDataParse, "position");

class CategorieProducts extends Component {
  state = {
    data: []
  };

  componentDidMount() {

    axios.get(`http://www.mocky.io/v2/5c4097d30f0000e21ae7b59f`)
      .then(res => {
        const response = res.data; 
         console.log(response.data.products);
        const responseDataParse = response.data.products.map(item => ({
          ...item,
          position: +(item.position), //cast position to a number 
          packages: sortBy(Object.values(item.packages), "position")//sort packages by position
        }));
        console.log(responseDataParse);
        const responseData = sortBy(responseDataParse, "position");
        this.setState({ data: responseData });
  })
}

 

  handleProductSwitch = ({ oldIndex, newIndex }) => {
    const { data } = this.state;
    //take product index
    const productIndex = this.state.data[oldIndex];
    const arrayData = arrayMove(data, oldIndex, newIndex);
    // new position = new index
    const newData = arrayData.map((product, index) => ({
      ...product,
      position: index
    }));

    this.setState({
      data: newData
    });

    const products = newData.map(product => pick(product, ["id", "position"]));
    const Data = { id: productIndex.id, list: products };

    changePositionProduct(productIndex.id, Data);
  };

  handlePackageSwitch = productIndex => ({ oldIndex, newIndex }) => {
    const newData = [...this.state.data]; //copy state
    const product = newData[productIndex]; //initial product
    //take package index
    const packageIndex = newData[productIndex].packages[oldIndex];
    //move package and assign package positon = package index
    product.packages = arrayMove(product.packages, oldIndex, newIndex).map(
      (pack, index) => ({
        ...pack,
        position: index
      })
    );

    this.setState({
      data: newData
    });

    const packages = product.packages.map(pack =>
      pick(pack, ["id", "position"])
    );
    const Data = { id: packageIndex.id, list: packages };

    changePositionPackage(packageIndex.id, Data);
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
    //add new product to array end (server return)
    newData[newProductIndex] = response.data;
    this.setState({
      data: newData
    });
    actions.setSubmitting(false);
  };

  handleAddPackage = productIndex => async (values, actions) => {
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
    for (let i = 0; i <= 3000; i++) {
      newData[productIndex].packages.push(newPackage);
    }
    this.setState({
      data: newData
    });
    const response = await addPackage(productIndex, newPackage);
    newData[productIndex].packages[newPackageIndex] = response.data;
    this.setState({
      data: newData
    });
    actions.setSubmitting(false);
  };

  handleEditProduct = productIndex => async (values, actions) => {
    const editedProduct = [...this.state.data]; //сopy state
    editedProduct[productIndex] = {
      //change fields of product
      ...this.state.data[productIndex], //copy all  unchanged fields of product
      name: values.name,
      visibility: values.visibility
    };
    this.setState({
      data: editedProduct
    });
    const response = await updateProduct(
      productIndex,
      editedProduct[productIndex]
    );
    editedProduct[productIndex] = response.data;
    this.setState({
      data: editedProduct
    });
    actions.setSubmitting(false);
  };

  handleEditPackage = productIndex => packageIndex => async (
    values,
    actions
  ) => {
    const editedPackage = [...this.state.data];
    editedPackage[productIndex].packages[packageIndex] = {
      ...this.state.data[productIndex].packages[packageIndex],
      name: values.name,
      price: values.price,
      quantity: values.quantity,
      overflow: values.overflow,
      availability: values.availability,
      mode: values.mode,
      provider: values.provider
    };
    this.setState({
      data: editedPackage
    });
    const response = await updatePackage(
      productIndex,
      packageIndex,
      editedPackage[productIndex].packages[packageIndex]
    );
    editedPackage[productIndex].packages[packageIndex] = response.data;
    this.setState({
      data: editedPackage
    });
    actions.setSubmitting(false);
  };

  handleDeletePackage = productIndex => packageIndex => async () => {
    const newData = [...this.state.data];
    newData[productIndex].packages.splice(packageIndex, 1);
    newData[productIndex].packages = newData[productIndex].packages.map(
      (pack, index) => ({
        ...pack,
        position: index
      })
    );
    this.setState({
      data: newData
    });
    const response = await deletePackage(
      productIndex,
      packageIndex,
      newData[productIndex].packages
    );
    console.log(newData[productIndex].packages);
    newData[productIndex].packages = response.data;
    //        this.setState({
    //          data: newData
    // });
  };

  render() {
    const { data } = this.state;
    const { isSubmitting } = this.props;
    console.log(data);
    return (
      <React.Fragment>
        <Jumbotron className="page-container">
          <Container fluid className="m-container-sommerce">
            <AddProduct
              onSubmit={this.handleAddProduct}
              isSubmitting={isSubmitting}
            />
            <Row>
              <Col xs="12">
                <div className="sommerce_dragtable">
                  <ProductList
                    helperClass="sortable-helper"
                    handleEditProduct={this.handleEditProduct}
                    handleEditPackage={this.handleEditPackage}
                    handlePackageSwitch={this.handlePackageSwitch}
                    handleDeletePackage={this.handleDeletePackage}
                    data={data}
                    useDragHandle={true}
                    onSortEnd={this.handleProductSwitch}
                    onPackageAdd={this.handleAddPackage}
                    handleProductSwitch={this.handleProductSwitch}
                  />
                </div>
              </Col>
            </Row>
          </Container>
        </Jumbotron>
      </React.Fragment>
    );
  }
}

export default CategorieProducts;
