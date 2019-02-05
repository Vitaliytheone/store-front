import React, { Component } from 'react';
import Header from './components/Header';
import './App.css';
import CategorieProducts from './CategorieProducts';
import { BrowserRouter as Router, Route } from 'react-router-dom';

class App extends Component {
	render() {
		return (
			<Router>
				<React.Fragment>
					<Header />
					<Route exact path="/" />
					<Route exact path="/admin/orders" />
					<Route exact path="/admin/payments" />
					<Route exact path="/admin/products" component={CategorieProducts} />
					<Route exact path="/admin/settings" />
				</React.Fragment>
			</Router>
		);
	}
}

export default App;
