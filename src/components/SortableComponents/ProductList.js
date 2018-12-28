import React from "react";
import { SortableContainer } from "react-sortable-hoc";
import SortableProduct from "./Product";


const ProductList = SortableContainer(({ data, handlePackageSwitch, onPackageAdd, handleEditProduct, handleEditPackage }) => (
    <div className="sortable">
        {data.map((product, index) => (
            <SortableProduct
                key={`item-${index}`}
                product={product}
                index={index}
                handlePackageSwitch={handlePackageSwitch(index)}
                handleEditProduct={handleEditProduct(index)}
                handleEditPackage={handleEditPackage(index)}
                onPackageAdd={onPackageAdd(index)}
            />
        ))}
    </div>
));

export default ProductList;