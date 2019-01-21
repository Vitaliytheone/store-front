import React from "react";
import { SortableContainer } from "react-sortable-hoc";
import Property from "./Property"

const PropertiesList = SortableContainer(({ properties, deleteProperty }) => (
  <ol className="dd-list">
    {" "}
    {properties.map((item, index) => (
      <Property
        key={`item-${index}`}
        item={item}
        index={index}
        deleteProperty={deleteProperty(index)}
      />
    ))}
  </ol>
));

export default PropertiesList;