import React from "react";
import { Row, Col } from "reactstrap";
import { SortableElement } from "react-sortable-hoc";
import  DragHandle  from "./DragHandle";
import EditProduct from "../EditProduct";
import PackageList from "./PackageList";

const SortableProduct = SortableElement(
    ({ product, handlePackageSwitch, onPackageAdd, handleEditProduct, handleEditPackage, handleDeletePackage }) => {
        return (
            <Row className="group-caption">
                <Col className="sommerce_dragtable__category">
                    <div className="sommerce_dragtable__category-title">
                        <Row className="align-items-center">
                            <Col sm="12">
                                <DragHandle />
                                {product.name}
                                <EditProduct productValue={product} onSubmit={handleEditProduct} />
                            </Col>
                        </Row>
                    </div>
                </Col>
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
            </Row>
        );
    }
);

export default SortableProduct;