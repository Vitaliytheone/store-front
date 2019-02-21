import React from 'react';
import { Row, Col } from 'reactstrap';
import { SortableElement } from 'react-sortable-hoc';
import DragHandle from './DragHandle';
import EditProduct from '../EditProduct';
import PackageList from './PackageList';

const SortableProduct = SortableElement(
	({
		clearServices,
		choseProviders,
		data,
		providers,
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
				<Col className={"sommerce_dragtable__category " + (product.visibility == 0 ? 'disabled-package' : null)}>
					<div className="sommerce_dragtable__category-title">
						<Row className="align-items-center">
							<Col sm="12">
								<div className="sommerce_dragtable__category-move move">
									<DragHandle />
								</div>
								{product.name}
								<EditProduct
									onSubmit={editProduct}
									getProduct={getProduct}
									response={response}
									products={data}
								/>
							</Col>
						</Row>
					</div>
				</Col>
				<PackageList
					clearServices={clearServices}
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
					providers={providers}
					choseProviders={choseProviders}
				/>
			</Row>
		);
	}
);

export default SortableProduct;
