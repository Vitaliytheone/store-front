import React from 'react';
import { SortableElement } from 'react-sortable-hoc';
import DragHandle from '../SortableComponents/DragHandle';

const Property = SortableElement(({ item, deleteProperty }) => (
	<li className="dd-item" data-id="3">
		<div className="dd-handle">
			<div className="dd-handle__icon">
				<DragHandle />
			</div>
			{item}
		</div>
		<div className="dd-edit-button">
			<a
				href="#"
				className="m-portlet__nav-link btn m-btn m-btn--hover-danger m-btn--icon m-btn--icon-only m-btn--pill"
				title="Delete"
				onClick={deleteProperty}
			>
				<i className="la la-trash" />
			</a>
		</div>
	</li>
));

export default Property;
