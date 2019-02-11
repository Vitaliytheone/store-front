import React from 'react';
import { Label, Input } from 'reactstrap';

export const Select = ({ handleChange, values, choseService, providers, field, label, ...props }) => (
	<div>
		<Label htmlFor={field.name}>{label}</Label>
		<Input
			{...field}
			{...props}
			onChange={ (e) => 
				choseService(values.provider_id, e)
			}
		>
			{' '}
			{providers.map((item) => <option key={item.id} value={item.id}>{item.name}</option>)}
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
