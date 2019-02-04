import React from 'react';
import { Col } from 'reactstrap';
import { SortableContainer } from 'react-sortable-hoc';
import AddPackage from '../AddPackage';
import SortablePackage from './Package';

const PackageList = SortableContainer(({ product, response, onPackageAdd, editPackage, deletePackage, getPackage }) => (
	<Col sm="12" className="group-items">
		{product.packages.map((pack, index) => (
			<SortablePackage
				key={`item-${index}`}
				pack={pack}
				index={index}
				response={response}
				editPackage={editPackage(index)}
				deletePackage={deletePackage(index)}
				getPackage={getPackage(index)}
			/>
		))}
		<AddPackage onSubmit={onPackageAdd} />
	</Col>
));

export default PackageList;
