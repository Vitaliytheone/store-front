import React from 'react'
import { Field } from 'formik'

const Select = ({ name }) => {
  return (
    <Field name={name}>
      {({ field, form}) => {
        const onChange = (e) => {
          //make request
        }
        return <BootstrapSelect {...field} onChange={(e) => onChange(e); field.onChange(e); } />
      }}
    </Field>
  )
}

export default Select



{values.onotherProviders && <Select>{values.anotherProviders.map(v => <option>{v.name}</option></Select>)} }


<Formik>
  {() => (
    <Select name="providers" />
  )}