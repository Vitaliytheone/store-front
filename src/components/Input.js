import React from "react";

export const ProductInput = ({
  field,
  form: { touched, errors },
  label,
  ...props
}) => (
  <div>
    <label htmlFor={field.name}>{label}</label>
    <input
      {...field}
      {...props}
      className="form-control"
      id="edit-page-title"
    />
    {touched[field.name] && errors[field.name] && (
      <div className="invalid-feedback error">{errors[field.name]}</div>
    )}
  </div>
);
