import React from 'react';
import { SortableContainer } from 'react-sortable-hoc';
import SortableProduct from './Product';

const ProductList = SortableContainer(
	({
		data,
		response,
		handlePackageSwitch,
		onPackageAdd,
		handleEditProduct,
		handleEditPackage,
		handleDeletePackage,
		handleGetEditPackage,
		handleGetEditProduct
	}) => (
		<div className="sortable">
			{data.map((product, index) => (
				<SortableProduct
					key={`item-${index}`}
					product={product}
					index={index}
					handleGetEditProduct={handleGetEditProduct(index)}
					handleGetEditPackage={handleGetEditPackage(index)}
					handlePackageSwitch={handlePackageSwitch(index)}
					handleEditProduct={handleEditProduct(index)}
					handleEditPackage={handleEditPackage(index)}
					handleDeletePackage={handleDeletePackage(index)}
					onPackageAdd={onPackageAdd(index)}
					response={response}
				/>
			))}
		</div>
	)
);

export default ProductList;
