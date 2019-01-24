import React from 'react';
import { Col } from 'reactstrap';
import DragHandle from './DragHandle';
import { SortableElement } from 'react-sortable-hoc';
import EditPackage from '../EditPackage';
import DeletePackage from '../DeletePackage';

const SortablePackage = SortableElement(
	({ pack, response, handleEditPackage, handleDeletePackage, handleGetEditPackage }) => {
		return (
			<div className="group-item sommerce_dragtable__tr align-items-center">
				<Col lg="5" className="padding-null-left">
					<div className="sommerce_dragtable__category-move move">
						<DragHandle />
					</div>
					<strong>{pack.name}</strong>
				</Col>
				<Col lg="2">{pack.price}</Col>
				<Col lg="2">{pack.provider}</Col>
				<Col lg="2" className="ext-lg-center">
					Enabled
				</Col>
				<Col lg="1" className="padding-null-lg-right text-lg-right text-sm-left">
					<EditPackage response={response} onSubmit={handleEditPackage} getPackage={handleGetEditPackage} />
					<DeletePackage onSubmit={handleDeletePackage} />
				</Col>
			</div>
		);
	}
);

export default SortablePackage;
