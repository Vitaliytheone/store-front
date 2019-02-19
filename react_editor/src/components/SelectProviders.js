import React from 'react';
import { Label, Input } from 'reactstrap';

export const Select = ({ name, choseProviders, entities, field, form, label, ...props }) => (
	<div>
		<Label htmlFor={field.name}>{label}</Label>
		<Input
			{...field}
			{...props}
			onChange={(event) => {
				choseProviders && choseProviders(event.target.value);
				form.setFieldValue(name, event.target.value);
				form.setFieldValue('provider_service', 'none');
				field.onChange(event);
			}}
		>
			{' '}
			{entities.map((item, index) => (
				<option key={index} value={item.id ? item.id : item.service}>
					{item.name}
				</option>
			))}
		</Input>
	</div>
);

export default Select;
