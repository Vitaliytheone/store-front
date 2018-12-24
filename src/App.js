import React, { Component } from "react";
import Header from "./components/Header";
import "./App.css";
import CategorieProducts from "./CategorieProducts";
import { BrowserRouter as Router, Route } from "react-router-dom";

class App extends Component {
  render() {
    return (
      <Router>
        <div>
          <Header />
          <Route exact path="/" />
          <Route exact path="/orders" />
          <Route exact path="/payments"/>
          <Route exact path="/products" component={CategorieProducts} />
          <Route exact path="/settings" />
        </div>
      </Router>
    );
  }
}

export default App;
