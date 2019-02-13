import React, { Component } from 'react';
import { Container, Row, Col, Jumbotron } from 'reactstrap';
import { arrayMove } from 'react-sortable-hoc';
import AddProduct from './components/AddProduct';
import ProductList from './components/SortableComponents/ProductList';
import {
	changePositionProduct,
	changePositionPackage,
	addPackage,
	addProduct,
	updateProduct,
	updatePackage,
	deletePackage,
	addListing,
	get_update_package,
	get_update_product
} from './services/url';
import { sortBy, pick } from 'lodash';

class CategorieProducts extends Component {
	state = {
		data: [],
		providers: [],
		response: {
			product: {
				name: '',
				visibility: '1',
				color: '',
				description: null,
				properties: [],
				seo_title: '',
				seo_description: '',
				seo_keywords: '',
				url: ''
			},
			package: ''
		}
	};

	async componentDidMount() {
		const response = await addListing();
		const data = response.data;
		const providers = response.data.providers;
		providers.unshift({ id: 'none', name: 'Chose providers' });
		const dataParse = data.products.map((item) => ({
			...item,
			position: +item.position, //cast position to a number
			packages: sortBy(Object.values(item.packages), 'position') //sort packages by position
		}));
		const newData = sortBy(dataParse, 'position');
		this.setState({ data: newData, providers: providers });
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

		const products = newData.map((product) => pick(product, [ 'id', 'position' ]));
		const Data = { id: productIndex.id, list: products };

		changePositionProduct(productIndex.id, Data);
	};

	handlePackageSwitch = (productIndex) => ({ oldIndex, newIndex }) => {
		const newData = [ ...this.state.data ]; //copy state
		const product = newData[productIndex]; //initial product
		//take package index
		const packageIndex = newData[productIndex].packages[oldIndex];
		//move package and assign package positon = package index
		product.packages = arrayMove(product.packages, oldIndex, newIndex).map((pack, index) => ({
			...pack,
			position: index
		}));

		this.setState({
			data: newData
		});

		const packages = product.packages.map((pack) => pick(pack, [ 'id', 'position' ]));
		const Data = { id: packageIndex.id, list: packages };

		changePositionPackage(packageIndex.id, Data);
	};

	addProduct = async (values, actions) => {
		//let product position
		const newProductIndex = this.state.data.length;
		const newProduct = {
			name: values.name,
			// position: newProductIndex,
			// visibility: values.visibility,
			color: values.color,
			description: values.description,
			properties: values.properties,
			seo_title: values.seo_title,
			seo_description: values.seo_description,
			seo_keywords: values.seo_keywords,
			url: values.url,
			packages: []
		};
		// const requestProduct = omit(newProduct, [ 'position', 'packages' ]);
		// this.setState((prevState) => ({
		// 	...prevState,
		// 	data: [ ...prevState.data, newProduct ]
		// }));
		const response = await addProduct(newProduct);
		const newData = [ ...this.state.data ];
		// add new product to array end (server return)
		if (response.success) {
			newData[newProductIndex] = response.data;
			this.setState({
				data: newData,
				response: { ...this.state.response, product: response.data }
			});
			actions.setSubmitting(false);
		}
		return response;
	};

	addPackage = (productIndex) => async (values, actions) => {
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
			provider_id: values.provider_id,
			provider_service_id: values.provider_service_id
		};
		const newData = [ ...this.state.data ];
		// newData[productIndex].packages.push(newPackage);
		// this.setState({
		// 	data: newData
		// });
		const response = await addPackage(newPackage);
		if (response.success) {
			newData[productIndex].packages[newPackageIndex] = response.data;
			this.setState({
				data: newData
			});
			actions.setSubmitting(false);
		}
		return response;
	};

	getProduct = (productIndex) => async () => {
		const getProduct = this.state.data[productIndex].id;
		const response = await get_update_product(getProduct);
		this.setState({
			response: { ...this.state.response, product: response.data }
		});
	};

	getPackage = (productIndex) => (packageIndex) => async () => {
		const getPackageId = this.state.data[productIndex].packages[packageIndex].id;
		const response = await get_update_package(getPackageId);
		this.setState({
			response: { ...this.state.response, package: response.data }
		});
	};

	editProduct = (productIndex) => async (values, actions) => {
		const editedProduct = [ ...this.state.data ]; //сopy state
		editedProduct[productIndex] = {
			//change fields of product
			...this.state.data[productIndex], //copy all  unchanged fields of product
			name: values.name,
			visibility: values.visibility,
			color: values.color,
			description: values.description,
			properties: values.properties,
			seo_title: values.seo_title,
			seo_description: values.seo_description,
			seo_keywords: values.seo_keywords,
			url: values.url
		};
		const ProductId = editedProduct[productIndex].id;
		const response = await updateProduct(ProductId, editedProduct[productIndex]);
		const productPackages = editedProduct[productIndex].packages;
		if (response.success) {
			editedProduct[productIndex] = {
				...response.data,
				packages: productPackages
			}
			console.log(editedProduct);
			this.setState({
				data: editedProduct
			});
			actions.setSubmitting(false);
		}
		return response;
	};

	editPackage = (productIndex) => (packageIndex) => async (values, actions) => {
		const editedPackage = [ ...this.state.data ];
		editedPackage[productIndex].packages[packageIndex] = {
			...this.state.data[productIndex].packages[packageIndex],
			name: values.name,
			visibility: values.visibility,
			price: values.price,
			quantity: values.quantity,
			overflow: values.overflow,
			availability: values.availability,
			mode: values.mode,
			provider_id: values.provider_id,
			provider_service_id: values.provider_service_id
		};
		const PackageId = editedPackage[productIndex].packages[packageIndex].id;
		const response = await updatePackage(PackageId, editedPackage[productIndex].packages[packageIndex]);
		if (response.success) {
			editedPackage[productIndex].packages[packageIndex] = response.data;
			this.setState({
				data: editedPackage
			});
			actions.setSubmitting(false);
		}
		console.log(response);
		return response;
	};

	deletePackage = (productIndex) => (packageIndex) => async () => {
		const newData = [ ...this.state.data ];
		const deletePack = newData[productIndex].packages.splice(packageIndex, 1);
		const PackId = deletePack.map((pack) => pack.id).join();
		newData[productIndex].packages = newData[productIndex].packages.map((pack, index) => ({
			...pack,
			position: index
		}));
		this.setState({
			data: newData
		});
		await deletePackage(PackId);
	};

	render() {
		const { data, response, providers } = this.state;
		const { isSubmitting } = this.props;
		return (
			<React.Fragment>
				<Jumbotron className="page-container">
					<Container fluid className="m-container-sommerce">
						<AddProduct
							onSubmit={this.addProduct}
							response={this.state.response}
							isSubmitting={isSubmitting}
						/>
						<Row>
							<Col xs="12">
								<div className="sommerce_dragtable">
									<ProductList
										helperClass="sortable-helper"
										getProduct={this.getProduct}
										getPackage={this.getPackage}
										editProduct={this.editProduct}
										editPackage={this.editPackage}
										handlePackageSwitch={this.handlePackageSwitch}
										deletePackage={this.deletePackage}
										response={response}
										data={data}
										providers={providers}
										useDragHandle={true}
										onSortEnd={this.handleProductSwitch}
										onPackageAdd={this.addPackage}
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
