import React, { Component } from 'react';
import { Button } from 'reactstrap';
import EditPackageModal from './EditPackageModal';
import { toast } from 'react-toastify';
import { options } from '../helpers/toast';
import { scrollModalTop } from '../helpers/scrolling';

class EditPackage extends React.PureComponent {
  state = {
    modalIsOpen: false,
    isFetched: false
  };

  getPackage() {
    this.setState(prevstate => ({
      modalIsOpen: !prevstate.modalIsOpen
    }));
  }

  toggle = () => {
    this.setState(prevstate => ({
      modalIsOpen: !prevstate.modalIsOpen
    }));
  };

  handleSubmit = async (values, actions) => {
    try {
      const response = await this.props.onSubmit(values, actions);
      this.setState({
        modalIsOpen: !response.success
      });
      toast('Package was successfully updated!', options);
    } catch (error) {
      actions.setStatus([error.success, error.error_message]);
      scrollModalTop(this.modal);
    }
  };

  async componentDidMount(...params) {
    if (this.state.modalIsOpen) {
      await this.props.getPackage(...params);
      this.state({
        isFetched: true
      });
    }
  }

  render() {
    const { response, providers, choseProviders } = this.props;
    console.log(this.state.isFetched);
    return (
      <React.Fragment>
        <Button
          onClick={() => this.getPackage()}
          color="primary"
          size="sm"
          className="m-btn--pill sommerce_dragtable__action m-btn--air"
          active
        >
          Edit
        </Button>
        {this.state.modalIsOpen && <EditPackageModal {...this.props} toggle={this.toggle} />}
      </React.Fragment>
    );
  }
}

export default EditPackage;
