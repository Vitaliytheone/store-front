import React from "react";
import { Label, Input, FormGroup } from "reactstrap";

export const ProductInput = ({
  field,
  form: { touched, errors },
  label,
  ...props
}) => (
  <FormGroup>
    <Label htmlFor={field.name}>{label}</Label>
    <Input 
      {...field}
      {...props}
    />
    {touched[field.name] && errors[field.name] && (
      <div className="invalid-feedback error">{errors[field.name]}</div>
    )}
  </FormGroup>
);

export const PackageInput = ({
  field,
  form: { touched, errors },
  label,
  ...props
}) => (
  <FormGroup>
    <Label htmlFor={field.name}>{label}</Label>
    <Input {...field} {...props}  />
    {touched[field.name] && errors[field.name] && (
      <div className="invalid-feedback error">{errors[field.name]}</div>
    )}
  </FormGroup>
);
