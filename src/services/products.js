import axiosInstance from "./request";
import nanoid from "nanoid";

export function addProduct(payload) {
  axiosInstance.post(`/products/`, payload);
  const mockResponse = {  data: {id: nanoid(), ...payload}, success: true };
  return Promise.resolve(mockResponse);
}

export function addPackage(productId, payload) {
  axiosInstance.post(`/products/${productId}`, payload);
  const mockResponse = { data: { id: nanoid(), ...payload }, success: true };
  return Promise.resolve(mockResponse);
}

export function updateProduct(productId, payload) {
  axiosInstance.post(`/product/${productId}`, payload);
  const mockResponse = { data: { ...payload }, success: true};
  return Promise.resolve(mockResponse);
}

export function updatePackage(productId, packageId, payload) {
  axiosInstance.put(`/product/${productId}/package/${packageId}`, payload);
  const mockResponse = { data: { ...payload }, success: true };
  return Promise.resolve(mockResponse);
}

export function changePositionProduct(product_id, payload) {
  axiosInstance.post(`/move-product/${product_id}`, payload);
  const mockResponse = { ...payload };
  return Promise.resolve(mockResponse);
}

export function changePositionPackage(package_id, payload) {
  axiosInstance.post(`/move-package/${package_id}`, payload);
  const mockResponse = { package_id, ...payload };
  return Promise.resolve(mockResponse);
}

export function deletePackage(productId, packageId, payload) {
  axiosInstance.post(`/product/${productId}/package/${packageId}`)
  const mockResponse = { data: payload, success: true};
  return Promise.resolve(mockResponse);
}
