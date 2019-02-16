import React from 'react';
import { Label, Input } from 'reactstrap';

export const Select = ({ choseService, providers, field, form, label, ...props }) => (
	<div>
		<Label htmlFor={field.name}>{label}</Label>
		<Input
			{...field}
			{...props}
			onChange={(event) => {
				choseService(event.target.value);
				form.setFieldValue('provider_id', event.target.value);
				field.onChange(event)
			}}
		>
			{' '}
			{providers.map((item) => <option key={item.id} value={item.id}>{item.name}</option>)}
		</Input>
	</div>
);

export default Select;

