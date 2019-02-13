import React, { Component } from 'react';
import './App.css';
import CategorieProducts from './CategorieProducts';

class App extends Component {
	render() {
		return (
			<CategorieProducts/>
			// <Router>
			// 	<React.Fragment>
			// 		{/* <Header /> */}
			// 		<Route exact path="/" />
			// 		<Route exact path="/admin/orders" />
			// 		<Route exact path="/admin/payments" />
			// 		<Route exact path="/admin/products" component={CategorieProducts} />
			// 		<Route exact path="/admin/settings" />
			// 	</React.Fragment>
			// </Router>
		);
	}
}

export default App;
