import React from "react";
import { Col } from "reactstrap";
import { SortableContainer } from "react-sortable-hoc";
import AddPackage from "../AddPackage";
import SortablePackage from "./Package";

const PackageList = SortableContainer(({ product, response, onPackageAdd, handleEditPackage, handleDeletePackage, handleGetEditPackage}) => (
    <Col sm="12" className="group-items">
        {product.packages.map((pack, index) => (
            <SortablePackage key={`item-${index}`} pack={pack} index={index} response={response} handleEditPackage={handleEditPackage(index)} handleDeletePackage={handleDeletePackage(index)} handleGetEditPackage={handleGetEditPackage(index)} />
        ))}
        <AddPackage onSubmit={onPackageAdd} />
    </Col>
));

export default PackageList;
