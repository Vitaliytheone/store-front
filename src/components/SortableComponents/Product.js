import React from 'react';
import { Row, Col } from 'reactstrap';
import { SortableElement } from 'react-sortable-hoc';
import DragHandle from './DragHandle';
import EditProduct from '../EditProduct';
import PackageList from './PackageList';

const SortableProduct = SortableElement(
	({
		product,
		response,
		handlePackageSwitch,
		onPackageAdd,
		editProduct,
		editPackage,
		deletePackage,
		getPackage,
		getProduct
	}) => {
		return (
			<Row className="group-caption">
				<Col className="sommerce_dragtable__category">
					<div className="sommerce_dragtable__category-title">
						<Row className="align-items-center">
							<Col sm="12">
								<div className="sommerce_dragtable__category-move move">
									<DragHandle />
								</div>
								{product.name}
								<EditProduct onSubmit={editProduct} getProduct={getProduct} response={response} />
							</Col>
						</Row>
					</div>
				</Col>
				<PackageList
					lockAxis={'y'}
					lockToContainerEdges={true}
					product={product}
					onSortEnd={handlePackageSwitch}
					useDragHandle={true}
					response={response}
					onPackageAdd={onPackageAdd}
					editPackage={editPackage}
					deletePackage={deletePackage}
					getPackage={getPackage}
				/>
			</Row>
		);
	}
);

export default SortableProduct;
