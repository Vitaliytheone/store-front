import React, { Component } from "react";
import { ModalBody, Label, FormGroup, Button } from "reactstrap";
import { Field } from "formik";
import { PackageInput } from "../Inputs";

class PackageModal extends Component {
  render() {
    return <ModalBody>
        {/* Alert Error */}

        {/* <div className="alert alert-danger alert-dismissible fade show" role="alert">
          <button className="close" data-dismiss="alert" aria-label="Close" />
          <strong>Oh snap!</strong> Error message!
        </div> */}

        {/* Alert Error End */}

        <FormGroup>
          <Field name="name" type="text" component={PackageInput} label="Package name *" id="package-name" required />
        </FormGroup>

        <FormGroup>
          <Field name="price" type="number" component={PackageInput} label="Price *" id="package-price" required />
        </FormGroup>

        <FormGroup>
          <Field name="quantity" type="number" component={PackageInput} label="Quantity *" id="package-quantity" required />
        </FormGroup>

        <FormGroup>
          <Field name="overflow" type="number" component={PackageInput} label="Overflow, % *" id="package-overflow" required />
        </FormGroup>

        <FormGroup>
          <Label htmlFor="package-best">Best package</Label>
          <Field name="best" component="select" id="package-best" className="form-control">
            <option value="1">Enabled</option>
            <option value="2">Disabled</option>
          </Field>
        </FormGroup>

        <FormGroup>
          <Label htmlFor="package-link-type">Link Type</Label>
          <Field name="link_type" component="select" id="package-link-type" className="form-control">
            <option value="0">None</option>
            <option value="1">Instagram Profile</option>
            <option value="2">Instagram Post</option>
            <option value="3">Facebook Page</option>
            <option value="4">Facebook Profile</option>
            <option value="5">Facebook Post</option>
            <option value="6">Facebook Group</option>
            <option value="7">Facebook Event</option>
            <option value="8">Twitter Profile</option>
            <option value="9">Twitter Post</option>
            <option value="10">Youtube Channel</option>
            <option value="11">Youtube Video</option>
            <option value="12">VINE Picture</option>
            <option value="13">VINE Profile</option>
            <option value="14">Pinterest Profile</option>
            <option value="15">Pinterest Board</option>
            <option value="16">Pinterest Post</option>
            <option value="17">Soundcloud Track</option>
            <option value="18">Soundcloud Profile</option>
            <option value="19">Mixcloud Track</option>
            <option value="20">Mixcloud Profile</option>
            <option value="21">Periscope Profile</option>
            <option value="22">Periscope Video</option>
            <option value="25">Linkedin Profile</option>
            <option value="26">Linkedin Group</option>
            <option value="27">Linkedin Post</option>
            <option value="28">Radiojavan Video</option>
            <option value="29">Radiojavan Track</option>
            <option value="30">Radiojavan Podcast</option>
            <option value="31">Radiojavan Playlist</option>
            <option value="32">Shazam Profile</option>
            <option value="33">Shazam Track</option>
            <option value="34">Reverbnation Track</option>
            <option value="35">Reverbnation Video</option>
            <option value="36">Reverbnation Profile</option>
            <option value="37">Tumblr Profile</option>
            <option value="38">Tumblr Post</option>
            <option value="39">Vimeo Channel</option>
            <option value="40">Vimeo Video</option>
            <option value="41">Fyuse Profile</option>
            <option value="42">Fyuse Picture</option>
            <option value="43">Google+ Profile</option>
            <option value="44">Google+ Post</option>
            <option value="45">Twitch Channel</option>
          </Field>
        </FormGroup>
        <hr />

        <FormGroup>
          <Label htmlFor="availability">Availability</Label>
          <Field name="availability"  className="form-control" component="select" label="Availability">
            <option value="1">Enabled</option>
            <option value="2">Disabled</option>
          </Field>
        </FormGroup>

        <hr />

        <FormGroup>
        <Label htmlFor="mode">Mode</Label>
        <Field name="mode" className="form-control" component="select">
            <option value="1">Manual</option>
            <option value="2">Auto</option>
          </Field>
        </FormGroup>

        <FormGroup>
          <Label htmlFor="provider">Provider</Label>
          <Field className="form-control" component="select" name="provider">
            <option value="test.myperfectpanel.com" data-action-url="/admin/products/get-provider-services?provider_id=2">
              test.myperfectpanel.com{" "}
            </option>
            <option value="bulkfollows.com" data-action-url="/admin/products/get-provider-services?provider_id=3">
              bulkfollows.com{" "}
            </option>
            <option value="demo.perfectpanel.com" data-action-url="/admin/products/get-provider-services?provider_id=4">
              demo.perfectpanel.com{" "}
            </option>
            <option value="autosmo.com" data-action-url="/admin/products/get-provider-services?provider_id=5">
              autosmo.com{" "}
            </option>
          </Field>
        </FormGroup>

        <span className="m--font-danger">
          API responce errors: Incorrect required
        </span>
      </ModalBody>;
  }
}

export default PackageModal;
