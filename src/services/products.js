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

export function changePositionProduct(payload) {
  axiosInstance.post(`/products/change-position-product`, payload);
  const mockResponse = { ...payload };
  return Promise.resolve(mockResponse);
}

export function changePositionPackage(productId, payload) {
  axiosInstance.post(`/product/${productId}/change-position-package`, payload);
  const mockResponse = { productId, ...payload };
  return Promise.resolve(mockResponse);
}

export function deletePackage(productId, packageId, payload) {
  axiosInstance.post(`/product/${productId}/package/${packageId}`)
  const mockResponse = { data: payload, success: true};
  return Promise.resolve(mockResponse);
}
