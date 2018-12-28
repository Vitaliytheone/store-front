import React from "react";
import { SortableElement } from "react-sortable-hoc";
import  DragHandle  from "./DragHandle";
import EditProduct from "../EditProduct";
import PackageList from "./PackageList";

const SortableProduct = SortableElement(
    ({ product, handlePackageSwitch, onPackageAdd, handleEditProduct, handleEditPackage, handleDeletePackage }) => {
        return (
            <div className="row group-caption">
                <div className="col-12 sommerce_dragtable__category">
                    <div className="sommerce_dragtable__category-title">
                        <div className="row align-items-center">
                            <div className="col-12">
                                 <DragHandle/>
                                {product.name}
                                <EditProduct productValue={product} onSubmit={handleEditProduct}/>
                            </div>
                        </div>
                    </div>
                </div>
                <PackageList
                    lockAxis={"y"}
                    lockToContainerEdges={true}
                    product={product}
                    onSortEnd={handlePackageSwitch}
                    useDragHandle={true}
                    onPackageAdd={onPackageAdd}
                    handleEditPackage={handleEditPackage}
                    handleDeletePackage={handleDeletePackage}
                />
            </div>
        );
    }
);

export default SortableProduct;