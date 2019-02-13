import axiosInstance from './request';

const point = { ...window.appConfig.api_endpoints };

export function addListing() {
	return axiosInstance.get(point.add_listing);
}

export function addProduct(payload) {
	return axiosInstance.post(point.add_product, payload);
}

export function confirm_add_product(product_id) {
	axiosInstance.post(point.confirm_add_product + `${product_id}`);
}

export function addPackage(payload) {
	return axiosInstance.post(point.add_package, payload);
}

export function get_update_product(product_id) {
	return axiosInstance.get(point.get_update_product + `${product_id}`); //+ `${product_id}`
}

export function updateProduct(product_id, payload) {
	return axiosInstance.post(point.update_product + `${product_id}`, payload); //+ `${product_id}`
}

export function get_update_package(package_id) {
	return axiosInstance.get(point.get_update_package + `${package_id}`); //+ `${package_id}`
}

export function updatePackage(package_id, payload) {
	return axiosInstance.post(point.update_package + `${package_id}`, payload); //+ `${package_id}`
}

export function changePositionProduct(product_id, payload) {
	axiosInstance.post(point.change_position_product + `${product_id}`, payload);
}

export function changePositionPackage(package_id, payload) {
	axiosInstance.post(point.change_position_package + `${package_id}`, payload);
}

export function deletePackage(package_id) {
	axiosInstance.post(point.delete_package + `${package_id}`);

}

export function get_providers_services(provider_id) {
	return axiosInstance.get(point.get_providers_services + `${provider_id}`);
}