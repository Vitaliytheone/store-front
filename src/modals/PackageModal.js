import React from "react";

const EditPackageModal = props => (
  <div class="modal-body">
    <form action="">
      {/* Alert Error */}

      <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <button
          type="button"
          class="close"
          data-dismiss="alert"
          aria-label="Close"
        />
        <strong>Oh snap!</strong> Error message!
      </div>

      {/* Alert Error End */}

      <div class="form-group">
        <label for="package-name">Package name *</label>
        <input type="email" class="form-control" id="package-name" />
      </div>
      <div class="form-group">
        <label for="package-price">Price *</label>
        <input type="email" class="form-control" id="package-price" />
      </div>
      <div class="form-group">
        <label for="package-quantity">Quantity *</label>
        <input type="email" class="form-control" id="package-quantity" />
      </div>
      <div class="form-group">
        <label for="package-overflow">Overflow, % *</label>
        <input type="email" class="form-control" id="package-overflow" />
      </div>
      <div class="form-group">
        <label for="package-best">Best package</label>
        <select id="package-best" class="form-control">
          <option value="1">Enabled</option>
          <option value="2">Disabled</option>
        </select>
      </div>
      <div class="form-group">
        <label for="package-link-type">Link Type</label>
        <select id="package-link-type" class="form-control">
          <option value="">None</option>
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
        </select>
      </div>
      <hr />
      <div class="form-group">
        <label for="package-availability">Availability</label>
        <select id="package-availability" class="form-control">
          <option value="1">Enabled</option>
          <option value="2">Disabled</option>
        </select>
      </div>
      <hr />
      <div class="form-group">
        <label for="package-mode">Mode</label>
        <select id="package-mode" class="form-control">
          <option value="1">Manual</option>
          <option value="2" selected>
            Auto
          </option>
        </select>
      </div>
      <div class="form-group">
        <label for="package-provider_id">Provider</label>
        <select
          id="package-provider_id"
          class="form-control form_field__provider_id"
          name="PackageForm[provider_id]"
        >
          <option
            value="2"
            data-action-url="/admin/products/get-provider-services?provider_id=2"
          >
            test.myperfectpanel.com{" "}
          </option>
          <option
            value="3"
            data-action-url="/admin/products/get-provider-services?provider_id=3"
          >
            bulkfollows.com{" "}
          </option>
          <option
            value="4"
            data-action-url="/admin/products/get-provider-services?provider_id=4"
          >
            demo.perfectpanel.com{" "}
          </option>
          <option
            value="5"
            data-action-url="/admin/products/get-provider-services?provider_id=5"
          >
            autosmo.com{" "}
          </option>
        </select>
      </div>
      <span class="m--font-danger">
        API responce errors: Incorrect required
      </span>
    </form>
  </div>
);

export default EditPackageModal;