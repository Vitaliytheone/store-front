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
	get_update_product,
	get_providers_services
} from './services/url';
import { sortBy, pick } from 'lodash';

class CategorieProducts extends Component {
	state = {
		loading: true,
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
			package: '',
			services: {
				providerServices: [],
				serviceError: null,
				messageError: null
			}
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
		this.setState({
			loading: false,
			data: newData,
			providers: providers,
			response: {
				...this.state.response,
				services: { providerServices: [ { service: 'none', name: 'Chose provider service' } ] }
			}
		});
		console.log(data);
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
			visibility: values.visibility,
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
			// position: newPackageIndex,
			price: values.price,
			best: values.best,
			link_type: values.link_type,
			quantity: values.quantity,
			overflow: values.overflow,
			visibility: values.visibility,
			mode: values.mode,
			provider_id: values.provider_id,
			provider_service: values.provider_service
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
		console.log(response.data);
		const responseServices = await get_providers_services(response.data.provider_id);
		const newServices = [{ service: 'none', name: 'Chose provider service' }, ...responseServices.data ];
			this.setState({
				response: {
					...this.state.response,
					package: response.data,
					services: {
						providerServices: newServices
					}
				}
			});
	};

	choseProviders = async (provider_id) => {
		if (provider_id !== 'none') {
			var response = await get_providers_services(provider_id);
			response.data.unshift({ service: 'none', name: 'Chose provider service' });
			const error = response.data[1].error;
			const message = response.data[1].message;
			this.setState({
				response: {
					...this.state.response,
					services: {
						providerServices: response.data,
						errorService: error,
						messageService: message
					}
				}
			});
		} else {
			this.setState({
				response: {
					...this.state.response,
					services: {
						providerServices: [ { service: 'none', name: 'Chose provider service' } ]
					}
				}
			});
		}
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
		delete editedProduct[productIndex].position;
		const ProductId = editedProduct[productIndex].id;
		const response = await updateProduct(ProductId, editedProduct[productIndex]);
		const productPackages = editedProduct[productIndex].packages;
		if (response.success) {
			editedProduct[productIndex] = {
				...response.data,
				packages: productPackages
			};
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
			price: values.price,
			quantity: values.quantity,
			overflow: values.overflow,
			best: values.best,
			link_type: values.link_type,
			visibility: values.visibility,
			mode: values.mode,
			provider_id: values.provider_id,
			provider_service: values.provider_service
		};
		delete editedPackage[productIndex].packages[packageIndex].position;
		const PackageId = editedPackage[productIndex].packages[packageIndex].id;
		const response = await updatePackage(PackageId, editedPackage[productIndex].packages[packageIndex]);
		if (response.success) {
			editedPackage[productIndex].packages[packageIndex] = response.data;
			this.setState({
				data: editedPackage
			});
			actions.setSubmitting(false);
		}
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
		if (this.state.loading) {
			return (
				<div className="sk-circle">
					<div className="sk-circle1 sk-child" />
					<div className="sk-circle2 sk-child" />
					<div className="sk-circle3 sk-child" />
					<div className="sk-circle4 sk-child" />
					<div className="sk-circle5 sk-child" />
					<div className="sk-circle6 sk-child" />
					<div className="sk-circle7 sk-child" />
					<div className="sk-circle8 sk-child" />
					<div className="sk-circle9 sk-child" />
					<div className="sk-circle10 sk-child" />
					<div className="sk-circle11 sk-child" />
					<div className="sk-circle12 sk-child" />
				</div>
			);
		}
		return (
			<React.Fragment>
				<Jumbotron className="page-container">
					<Container fluid className="m-container-sommerce">
						<AddProduct
							onSubmit={this.addProduct}
							response={this.state.response}
							isSubmitting={isSubmitting}
							products={data}
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
										choseProviders={this.choseProviders}
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
