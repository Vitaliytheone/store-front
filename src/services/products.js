import request from "./request";
import nanoid from "nanoid";

export async function addProduct(payload) {
  request.post(`/product/`, payload);
  const mockResponse = { ...payload, id: nanoid() };
  return Promise.resolve(mockResponse);
}

export async function addPackage(productId, payload) {
  // const response = request.post(`/products/${productId}`, payload);
  const mockResponse = {
    ...payload,
    id: nanoid()
  };
  return Promise.resolve(mockResponse);
}

export function updateProduct(id, payload) {
  request.put(`/product/${id}`, payload);
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
  const mockResponse = { ...payload };
  return Promise.resolve(mockResponse);
}

export function changePositionPackage(productId, payload) {
  request.put(`/product/${productId}/change-position-package`, payload);
  const mockResponse = { ...payload };
  return Promise.resolve(mockResponse);
}
