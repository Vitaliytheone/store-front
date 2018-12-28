import React from "react";
import DragHandle from "./DragHandle";
import { SortableElement } from "react-sortable-hoc";
import EditPackage from "../EditPackage";
import DeletePackage from "../DeletePackage";

export const SortablePackage = SortableElement(({ pack, handleEditPackage }) => {
    return <div className="group-item sommerce_dragtable__tr align-items-center">
        <div className="col-lg-5 padding-null-left">
          <DragHandle />
          <strong>{pack.name}</strong>
        </div>
        <div className="col-lg-2">{pack.price}</div>
        <div className="col-lg-2">{pack.provider}</div>
        <div className="col-lg-2 text-lg-center">Enabled</div>
        <div className="col-lg-1 padding-null-lg-right text-lg-right text-sm-left">
          <EditPackage packageValue={pack} onSubmit={handleEditPackage} />
           <DeletePackage/>
        </div>
      </div>;
});

export default SortablePackage;