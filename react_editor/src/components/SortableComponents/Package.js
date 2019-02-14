import React from 'react';
import { Col } from 'reactstrap';
import DragHandle from './DragHandle';
import { SortableElement } from 'react-sortable-hoc';
import EditPackage from '../EditPackage';
import DeletePackage from '../DeletePackage';

const SortablePackage = SortableElement(({ providers, pack, response, editPackage, deletePackage, getPackage }) => {
	const providerName = providers.filter(item => item.id == pack.provider_id)[0];
	return (
		<div
			className={
			'group-item sommerce_dragtable__tr align-items-center ' + (pack.visibility == 0 ? 'disabled-package' : null)}
			
		>
			<Col lg="5" className="padding-null-left">
				<div className="sommerce_dragtable__category-move move">
					<DragHandle />
				</div>
				<strong>{pack.name}</strong>
			</Col>
			<Col lg="2">{pack.price}</Col>
			<Col lg="2">{providerName.name}</Col>
			<Col lg="2" className="ext-lg-center active-center">
				{pack.visibility == 1 ? 'Enabled' : 'Disabled'}
			</Col>
			<Col lg="1" className="padding-null-lg-right text-lg-right text-sm-left">
				<EditPackage response={response} onSubmit={editPackage} getPackage={getPackage} providers={providers} />
				<DeletePackage onSubmit={deletePackage} />
			</Col>
		</div>
	);
});

export default SortablePackage;
