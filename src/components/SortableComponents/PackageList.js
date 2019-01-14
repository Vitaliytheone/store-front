import React from "react";
import { Col } from "reactstrap";
import { SortableContainer } from "react-sortable-hoc";
import AddPackage from "../AddPackage";
import SortablePackage from "./Package";

const PackageList = SortableContainer(({ product, onPackageAdd, handleEditPackage, handleDeletePackage }) => (
    <Col sm="12" className="group-items">
        {product.packages.map((pack, index) => (
            <SortablePackage key={`item-${index}`} pack={pack} index={index} handleEditPackage={handleEditPackage(index)} handleDeletePackage={handleDeletePackage(index)}/>
        ))}
        <AddPackage onSubmit={onPackageAdd} />
    </Col>
));

export default PackageList;
