import axiosInstance from './request';
import nanoid from 'nanoid';

const point = { ...window.appConfig.api_endpoints };

export function addListing() {
	return axiosInstance.get(point.add_listing);
}

export function addProduct(payload) {
	return axiosInstance.post(point.add_product, payload);
	const mockResponse = { data: { id: nanoid(), ...payload }, success: true };
	return Promise.resolve(mockResponse);
}

export function connfirm_addProduct(product_id) {
	axiosInstance.post(`/admin/products/create-product-menu?id=${product_id}`);
}

export function addPackage(payload) {
	return axiosInstance.post(point.add_package, payload);
	const mockResponse = { data: { id: nanoid(), ...payload }, success: true };
	return Promise.resolve(mockResponse);
}

export function get_update_product(product_id) {
	return axiosInstance.get(point.get_updateProduct + `${product_id}`); //+ `${product_id}`
}

export function updateProduct(product_id, payload) {
	return axiosInstance.post(point.update_product + `${product_id}`, payload); //+ `${product_id}`
	const mockResponse = { data: { ...payload }, success: true };
	return Promise.resolve(mockResponse);
}

export function get_update_package(package_id) {
	return axiosInstance.get(point.get_updatePackage + `${package_id}`); //+ `${package_id}`
}

export function updatePackage(package_id, payload) {
	return axiosInstance.post(point.update_package + `${package_id}`, payload); //+ `${package_id}`
	const mockResponse = { data: { ...payload }, success: true };
	return Promise.resolve(mockResponse);
}

export function changePositionProduct(product_id, payload) {
	axiosInstance.post(point.change_position_product + `${product_id}`, payload);
	const mockResponse = { ...payload };
	return Promise.resolve(mockResponse);
}

export function changePositionPackage(package_id, payload) {
	axiosInstance.post(point.change_position_package + `${package_id}`, payload);
	const mockResponse = { package_id, ...payload };
	return Promise.resolve(mockResponse);
}

export function deletePackage(package_id) {
	axiosInstance.post(point.delete_package + `${package_id}`);
	// const mockResponse = { data: payload, success: true };
	// return Promise.resolve(mockResponse);
}

export function get_providers(provider_id) {
	axiosInstance.get(`/admin/products/get-provider-services?id=${provider_id}`);
}
