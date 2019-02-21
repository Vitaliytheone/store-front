import React from 'react';
import { SortableContainer } from 'react-sortable-hoc';
import SortableProduct from './Product';

const ProductList = SortableContainer(
	({	
		clearServices,
		choseProviders,
		providers,
		data,
		response,
		handlePackageSwitch,
		onPackageAdd,
		editProduct,
		editPackage,
		deletePackage,
		getPackage,
		getProduct
	}) => (
		<div className="sortable">
			{data.map((product, index) => (
				<SortableProduct
					key={`item-${index}`}
					product={product}
					index={index}
					getProduct={getProduct(index)}
					getPackage={getPackage(index)}
					handlePackageSwitch={handlePackageSwitch(index)}
					editProduct={editProduct(index)}
					editPackage={editPackage(index)}
					deletePackage={deletePackage(index)}
					onPackageAdd={onPackageAdd(index)}
					response={response}
					providers={providers}
					data={data}
					choseProviders={choseProviders}
					clearServices={clearServices}
				/>
			))}
		</div>
	)
);

export default ProductList;
