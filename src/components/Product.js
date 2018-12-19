import React from "react";
import { SortablePackage, DragHandle } from "./Package";
import EditProduct from "./EditProduct";
import AddPackage from "./AddPackage";
import { SortableContainer, SortableElement } from "react-sortable-hoc";

const PackageList = SortableContainer(({ product }) => (
  <div className="col-12 group-items">
    {product.packages.map((pack, index) => (
      <SortablePackage key={`item-${index}`} pack={pack} index={index} />
    ))}
    <AddPackage />
  </div>
));

export const SortableProduct = SortableElement(
  ({ product, handlePackageSwitch }) => {
    return (
      <div className="row group-caption">
        <div className="col-12 sommerce_dragtable__category">
          <div className="sommerce_dragtable__category-title">
            <div className="row align-items-center">
              <div className="col-12">
                <DragHandle />
                {product.name}
                <EditProduct />
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
        />
      </div>
    );
  }
);
