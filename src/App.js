import React, { Component } from "react";
import Header from "./components/Header";
import "./App.css";
import Products from "./Products";
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
          <Route exact path="/products" component={Products} />
          <Route exact path="/settings" />
        </div>
      </Router>
    );
  }
}

export default App;
