import React from "react";
import { Row, Col, Container } from "reactstrap";
import DragHandle from "./DragHandle";
import { SortableElement } from "react-sortable-hoc";
import EditPackage from "../EditPackage";
import DeletePackage from "../DeletePackage";

const SortablePackage = SortableElement(({ pack, handleEditPackage, handleDeletePackage }) => {
    return (
      <Col className="group-item sommerce_dragtable__tr align-items-center">
        <Col lg="5" className="padding-null-left">
          <DragHandle />
          <strong>{pack.name}</strong>
        </Col>
        <Col>{pack.price}</Col>
        <Col>{pack.provider}</Col>
        <Col className="ext-lg-center">Enabled</Col>
        <Col className="padding-null-lg-right text-lg-right text-sm-left">
          <EditPackage packageValue={pack} onSubmit={handleEditPackage} />
           <DeletePackage onSubmit={handleDeletePackage}/>
        </Col>
      </Col>
    );
});

export default SortablePackage;