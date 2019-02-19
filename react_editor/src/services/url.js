import axiosInstance from './request';
import nanoid from 'nanoid';

const point = { ...window.appConfig.api_endpoints };

export function addListing() {
	return axiosInstance.get(
		point.add_listing + `key=3!b8bc0)a(a3ff470fc$f1b)89b0*f*4c535!(7f3b21e44@4f9a6dffc(bc*5fd`
	);
}

export function addProduct(payload) {
  return axiosInstance.post(
    point.add_product +
      "key=3!b8bc0)a(a3ff470fc$f1b)89b0*f*4c535!(7f3b21e44@4f9a6dffc(bc*5fd",
    payload
  );
}


export function confirm_add_product(product_id) {
	axiosInstance.post(point.confirm_add_product + `${product_id}`);
}

export function addPackage(payload) {
	axiosInstance.post(point.add_package, payload);
	const mockResponse = {
		data: { id: nanoid(), ...payload },
		success: true,
		error_message: 'erororororororooror'
	};
	return Promise.resolve(mockResponse);
}

export function get_update_product(product_id) {
	return axiosInstance.get(point.get_update_product); //+ `${product_id}`
}

export function updateProduct(product_id, payload) {
	axiosInstance.post(point.update_product, payload); //+ `${product_id}`
	const mockResponse = { data: { ...payload }, success: true, error_message: 'dasdasdsadsa' };
	return Promise.resolve(mockResponse);
}

export function get_update_package(package_id) {
	return axiosInstance.get(point.get_update_package); //+ `${package_id}`
}

export function updatePackage(package_id, payload) {
	axiosInstance.post(point.update_package, payload); //+ `${package_id}`
	const mockResponse = {
		data: { ...payload },
		success: true,
		error_message: 'new error message here'
	};
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

export function get_providers_services(provider_id) {
	return axiosInstance.get(
		point.get_providers_services +
			`${provider_id}&key=3!b8bc0)a(a3ff470fc$f1b)89b0*f*4c535!(7f3b21e44@4f9a6dffc(bc*5fd`
	);
}
