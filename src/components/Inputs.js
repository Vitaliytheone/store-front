import React from "react";
import { Label, Input } from "reactstrap";

export const ProductInput = ({
  field,
  form: { touched, errors },
  label,
  ...props
}) => (
  <div>
    <Label htmlFor={field.name}>{label}</Label>
    <Input 
      {...field}
      {...props}
    />
    {touched[field.name] && errors[field.name] && (
      <div className="invalid-feedback error">{errors[field.name]}</div>
    )}
  </div>
);

export const PackageInput = ({
  field,
  form: { touched, errors },
  label,
  ...props
}) => (
  <div>
    <Label htmlFor={field.name}>{label}</Label>
    <Input {...field} {...props} />
    {touched[field.name] && errors[field.name] && (
      <div className="invalid-feedback error">{errors[field.name]}</div>
    )}
  </div>
);

// export const CustomComponent = ({
//   field,
//   form,
//   ...props
// }) => (
//   <div classname="summernote" {...field} {...props}></div>
// );
