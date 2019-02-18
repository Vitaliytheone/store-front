import React from 'react';
import { Label, Input } from 'reactstrap';

export const Select = ({ name, choseService, entities, field, form, label, ...props }) => (
  <div>
    <Label htmlFor={field.name}>{label}</Label>
    <Input
      {...field}
      {...props}
      onChange={event => {
        choseService && choseService(event.target.value);
        form.setFieldValue(name, event.target.value);
        field.onChange(event);
      }}
    >
      {' '}
      {entities.map((item,index) => (
        <option key={index} value={item.id}>
          {item.name}
        </option>
      ))}
    </Input>
  </div>
);

export default Select;
