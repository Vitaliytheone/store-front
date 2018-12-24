import axiosInstance from "./request";
import nanoid from "nanoid";

export function addProduct(payload) {
  axiosInstance.post(`/products/`, payload);
  const mockResponse = { id: nanoid(), ...payload };
  return Promise.resolve(mockResponse);
}

export function addPackage(productId, payload) {
  // const response = axiosInstance.post(`/products/${productId}`, payload);
  const mockResponse = {
    ...payload,
    id: nanoid()
  };
  return Promise.resolve(mockResponse);
}

export function updateProduct(id, payload) {
  axiosInstance.put(`/product/${id}`, payload);
  const mockResponse = {};
  return Promise.resolve(mockResponse);
}

export function updatePackage(id, payload) {
  axiosInstance.put(`/product/${id}/package/${id}`, payload);
  const mockResponse = {};
  return Promise.resolve(mockResponse);
}

export function changePositionProduct(payload) {
  axiosInstance.put(`/products/change-position-product`, payload);
  const mockResponse = { ...payload };
  return Promise.resolve(mockResponse);
}

export function changePositionPackage(productId, payload) {
  axiosInstance.put(`/product/${productId}/change-position-package`, payload);
  const mockResponse = { productId, ...payload };
  return Promise.resolve(mockResponse);
}
