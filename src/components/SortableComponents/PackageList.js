import React from "react";
import { SortableContainer } from "react-sortable-hoc";
import AddPackage from "../AddPackage";
import SortablePackage from "./Package";

const PackageList = SortableContainer(({ product, onPackageAdd, handleEditPackage, handleDeletePackage }) => (
    <div className="col-12 group-items">
        {product.packages.map((pack, index) => (
            <SortablePackage key={`item-${index}`} pack={pack} index={index} handleEditPackage={handleEditPackage(index)} handleDeletePackage={handleDeletePackage(index)}/>
        ))}
        <AddPackage onSubmit={onPackageAdd} />
    </div>
));

export default PackageList;
