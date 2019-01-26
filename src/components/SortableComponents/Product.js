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
		handleEditProduct,
		handleEditPackage,
		handleDeletePackage,
		handleGetEditPackage,
		handleGetEditProduct
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
								<EditProduct
									onSubmit={handleEditProduct}
									getProduct={handleGetEditProduct}
									response={response}
								/>
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
					handleEditPackage={handleEditPackage}
					handleDeletePackage={handleDeletePackage}
					handleGetEditPackage={handleGetEditPackage}
				/>
			</Row>
		);
	}
);

export default SortableProduct;
