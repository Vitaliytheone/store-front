import React from 'react';
import { Label, Input } from 'reactstrap';

export const Select = ({ choseService, providers, field, label, ...props }) => (
	<div>
		<Label htmlFor={field.name}>{label}</Label>
		<Input
			{...field}
			{...props}
			onChange={() => {
				choseService(providers.id);
				field.onChange(providers.id);
			}}
		>
			{' '}
			{providers.map((item) => <option value={item.id}>{item.name}</option>)}
		</Input>
	</div>
);

// const Select = ({ name }) => {
//   return (
//     <Field name={name}>
//       {({ field, form}) => {
//         const onChange = (e) => {
//           //make request
//         }
//         return <BootstrapSelect {...field} onChange={(e) => onChange(e); field.onChange(e); } />
//       }}
//     </Field>
//   )
// }

export default Select;

// {values.onotherProviders && <Select>{values.anotherProviders.map(v => <option>{v.name}</option></Select>)} }

{
	/* <Formik>
  {() => (
    <Select name="providers" />
  )} */
}
