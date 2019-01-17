import axiosInstance from "./request";
// import nanoid from "nanoid";

export function addListing() {
  axiosInstance.get(`http://www.mocky.io/v2/5c3f1e2c3500004e00ec371e`);
}

export function addProduct(payload) {
  axiosInstance.post(`/admin/products/create-product`, payload);
  // const mockResponse = {  data: {id: nanoid(), ...payload}, success: true };
  // return Promise.resolve(mockResponse);
}

export function connfirm_addProduct(product_id) {
  axiosInstance.post(`/admin/products/create-product-menu?id=${product_id}`);
}

export function addPackage(payload) {
  axiosInstance.post(`/admin/products/create-package`, payload);
  // const mockResponse = { data: { id: nanoid(), ...payload }, success: true };
  // return Promise.resolve(mockResponse);
}

export function get_updateProduct(product_id) {
  axiosInstance.get(`/admin/products/update-product?id=${product_id}`);
}

export function updateProduct(product_id, payload) {
  axiosInstance.post(`/admin/products/update-product?id=${product_id}`, payload);
  // const mockResponse = { data: { ...payload }, success: true};
  // return Promise.resolve(mockResponse);
}

export function get_updatePackage(package_id) {
  axiosInstance.get(`/admin/products/update-package?id=${package_id}`);
}

export function updatePackage(package_id, payload) {
  axiosInstance.post(`/admin/products/update-package?id=${package_id}`, payload);
  // const mockResponse = { data: { ...payload }, success: true };
  // return Promise.resolve(mockResponse);
}

export function changePositionProduct(product_id, payload) {
  axiosInstance.post(`/admin/products/move-product?id=${product_id}`, payload);
  // const mockResponse = { ...payload };
  // return Promise.resolve(mockResponse);
}

export function changePositionPackage(package_id, payload) {
  axiosInstance.post(`/admin/products/move-package?id=${package_id}`, payload);
  // const mockResponse = { package_id, ...payload };
  // return Promise.resolve(mockResponse);
}

export function deletePackage(package_id, payload) {
  axiosInstance.post(`/admin/products/delete-package?id=${package_id}`);
  // const mockResponse = { data: payload, success: true};
  // return Promise.resolve(mockResponse);
}

export function get_providers(provider_id) {
  axiosInstance.get(`/admin/products/get-provider-services?id=${provider_id}`);
}
