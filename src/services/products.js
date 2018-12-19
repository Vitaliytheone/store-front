import request from "./request";

export function addProduct(id, payload) {
  request.post(`/product/${id}`, payload);
  const mockResponse = {};
  return Promise.resolve(mockResponse);
}

export function updateProduct(id, payload) {
  request.put(`/product/${id}`, payload);
  const mockResponse = {};
  return Promise.resolve(mockResponse);
}

export function addPackage(id, payload) {
  request.post(`/product/${id}/package/${id}`, payload);
  const mockResponse = {};
  return Promise.resolve(mockResponse);
}

export function updatePackage(id, payload) {
  request.put(`/product/${id}/package/${id}`, payload);
  const mockResponse = {};
  return Promise.resolve(mockResponse);
}

export function changePositionProduct(id, payload) {
  request.put(`/product/${id}/change-position-product`, payload);
  const mockResponse = {};
  return Promise.resolve(mockResponse);
}

export function changePositionPackage(productId, payload) {
  request.put(`/product/${productId}/change-position-package`, payload);
  const mockResponse = {};
  return Promise.resolve(mockResponse);
}
