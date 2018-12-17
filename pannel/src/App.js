import React, { Component } from "react";
import { DragDropContext, Droppable, Draggable } from "react-beautiful-dnd";
import Header from "./components/Header";
import Product from "./components/Product";
import AddProduct from "./components/AddProduct";
import "./App.css";
import { SortableContainer } from "react-sortable-hoc";
import data from "./data.json";

//parse of json
const arrayData = Object.values(data).map(item => ({
  ...item,
  packages: Object.values(item.packages)
}));

// const ProductList = SortableContainer(({ data }) => (
//   <div className="sortable">
//     {data.map((product, index) => (
//       <SortableProduct key={`item-${index}`} product={product} index={index} />
//     ))}
//   </div>
// ));

class App extends Component {
  state = {
    data: arrayData
  };

  handleDragEnd = ({ source, destination }) => {
    if (!destination) return;

    const data = [...this.state.data];
    [data[source.index], data[destination.index]] = [
      data[destination.index],
      data[source.index]
    ];

    this.setState({
      data
    });
  };

  handleProductSwitch = ({ oldIndex, newIndex }) => {
    const data = [...this.state.data];
    [data[oldIndex], data[newIndex]] = [data[newIndex], data[oldIndex]];
  };

  handlePackageSwitch = productIndex => ({ oldIndex, newIndex }) => {
    const newData = [...this.state.data];
    const product = newData[productIndex];
    const packages = product.packages;
    [packages[oldIndex], packages[newIndex]] = [
      packages[newIndex],
      packages[oldIndex]
    ];

    this.setState(prevState => ({
      data: newData
    }));
  };

  render() {
    const { data } = this.state;
    return (
      <DragDropContext onDragEnd={this.handleDragEnd}>
        <div>
          <Header />
          <div className="page-container">
            <div className="m-container-sommerce container-fluid">
              <AddProduct />
              <div className="row">
                <div className="col-12">
                  <div className="sommerce_dragtable">
                    <Droppable droppableId="someId">
                      {dropProvided => (
                        <div
                          className="sortable"
                          ref={dropProvided.innerRef}
                          {...dropProvided.droppableProps}
                        >
                          {data.map((product, index) => (
                            <Draggable
                              key={product.id}
                              draggableId={product.id}
                              index={index}
                            >
                              {(dragProvided, snapshot) => (
                                <Product
                                  handlePackageSwitch={this.handlePackageSwitch(
                                    index
                                  )}
                                  key={index}
                                  product={product}
                                  dragProvided={dragProvided}
                                />
                              )}
                            </Draggable>
                          ))}
                        </div>
                      )}
                    </Droppable>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </DragDropContext>
    );
  }
}

export default App;
