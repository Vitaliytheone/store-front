import React from "react";
import { Col } from "reactstrap";
import { SortableContainer } from "react-sortable-hoc";
import SortableProduct from "./Product";

const ProductList = SortableContainer(({ data, handlePackageSwitch, onPackageAdd, handleEditProduct, handleEditPackage, handleDeletePackage }) => (
    <div className="sortable">
        {data.map((product, index) => (
            <SortableProduct
                key={`item-${index}`}
                product={product}
                index={index}
                handlePackageSwitch={handlePackageSwitch(index)}
                handleEditProduct={handleEditProduct(index)}
                handleEditPackage={handleEditPackage(index)}
                handleDeletePackage={handleDeletePackage(index)}
                onPackageAdd={onPackageAdd(index)}
            />
        ))}
    </div>
));

export default ProductList;