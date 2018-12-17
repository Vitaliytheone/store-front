import React from "react";
import EditPackage from "./EditPackage";
import DeletePackage from "./DeletePackage";
import { SortableElement, SortableHandle } from "react-sortable-hoc";

const DragHandle = SortableHandle(() => (
  <div className="sommerce_dragtable__category-move move">
    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
      <title>Drag-Handle</title>
      <path
        d="M7 2c-1.104 0-2 .896-2 2s.896 2 2 2 2-.896 2-2-.896-2-2-2zm0 6c-1.104 0-2 .896-2 2s.896 2 2 2 2-.896 2-2-.896-2-2-2zm0 6c-1.104 0-2 .896-2 2s.896 2 2 2 2-.896 2-2-.896-2-2-2zm6-8c1.104 0 2-.896 2-2s-.896-2-2-2-2 .896-2 2 .896 2 2 2zm0 2c-1.104 0-2 .896-2 2s.896 2 2 2 2-.896 2-2-.896-2-2-2zm0 6c-1.104 0-2 .896-2 2s.896 2 2 2 2-.896 2-2-.896-2-2-2z"
        fill="#d4d4d4"
      />
    </svg>
  </div>
));

const SortablePackage = SortableElement(({ pack }) => {
  return (
    <div className="group-item sommerce_dragtable__tr align-items-center">
      <div className="col-lg-5 padding-null-left">
        <DragHandle />
        <strong>{pack.name}</strong>
      </div>
      <div className="col-lg-2">{pack.price}</div>
      <div className="col-lg-2">{pack.provider}</div>
      <div className="col-lg-2 text-lg-center">Enabled</div>
      <div className="col-lg-1 padding-null-lg-right text-lg-right text-sm-left">
        <EditPackage />
        <DeletePackage />
      </div>
    </div>
  );
});

export default SortablePackage;
