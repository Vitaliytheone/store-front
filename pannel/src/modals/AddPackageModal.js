import React from "react";
import "../App.css";
import "../styles/AddPackage.css";

const AddPackageModal = props => (
  <div class="modal fade add_package2" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-lg">
          <div class="modal-content">
              <div class="modal-header">
                  <h5 class="modal-title">Add package (ID: 23)</h5>
                  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                      <span aria-hidden="true">&times;</span>
                  </button>
              </div>
              <div class="modal-body">
                  <form action="">
                      <div class="row">
                          <div class="col-md-6">
                              <div class="form-group">
                                  <label for="package-name">Package name *</label>
                                  <input type="email" class="form-control" id="package-name"/>
                              </div>
                              <div class="form-group">
                                  <label for="package-price">Price *</label>
                                  <input type="email" class="form-control" id="package-price"/>
                              </div>
                              <div class="form-group">
                                  <label for="package-quantity">Quantity *</label>
                                  <input type="email" class="form-control" id="package-quantity"/>
                              </div>
                              <div class="form-group">
                                  <label for="package-link-type">Link Type</label>
                                  <select id="package-link-type" class="form-control" >
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

                                  <div class="form-group">
                                      <label for="package-mode">Mode</label>
                                      <select id="package-mode" class="form-control">
                                          <option value="1">Manual</option>
                                          <option value="2" selected>Auto</option>
                                      </select>
                                  </div>
                                  <div class="form-group">
                                      <label for="package-mode">Provider</label>
                                      <select id="package-mode" class="form-control">
                                          <option value="1">perfectpanel.com</option>
                                          <option value="2">Auto</option>
                                      </select>
                                  </div>
                                  <div class="form-group">
                                      <label for="package-mode">Provider service</label>
                                      <select id="package-mode" class="form-control">
                                          <option value="1">Facebook likes</option>
                                          <option value="2">Auto</option>
                                      </select>
                                  </div>

                          </div>
                          <div class="col-md-6">


                              <div style="    border: 1px solid #ebedf2;
      border-radius: 4px;
      padding: 25px 15px 7px 15px;
      position: relative;
      margin-top: 27px;">
                                  <div class="package-option" style="position: absolute;
      top: -12px;
      background: #ffff;
      padding: 0px 5px;
      color: #9da2ab;
      left: 7px;">Options</div>
                                  <div class="m-form__group form-group row">
                                      <label class="col-7 col-form-label">Best package</label>
                                      <div class="col-5 text-right">
  											<span class="m-switch">
  												<label>
  						                        <input type="checkbox" name=""/>
  						                        <span></span>
  						                        </label>
  						                    </span>
                                      </div>
                                  </div>
                                  <div class="m-form__group form-group row">
                                      <label class="col-7 col-form-label">Availability</label>
                                      <div class="col-5 text-right">
  											<span class="m-switch">
  												<label>
  						                        <input type="checkbox" checked="checked" name=""/>
  						                        <span></span>
  						                        </label>
  						                    </span>
                                      </div>
                                  </div>
                                  <div class="m-form__group form-group row">
                                      <label class="col-7 col-form-label">Color</label>
                                      <div class="col-5 text-right">


                                          </style>
                                          <input type="text" id="package-color">
                                          <!--
                                          <div class="m-dropdown m-dropdown--down m-dropdown--inline m-dropdown--align-left" data-dropdown-toggle="click">
                                              <a href="#" class="m-dropdown__toggle">
                                                  								<span style="    display: inline-block;
      width: 57px;
      height: 30px;
      border-radius: 30px;
      border: 1px solid #ebedf3;
      cursor: pointer;">
                                              </a>
                                              <div class="m-dropdown__wrapper">
                                                  <div class="m-dropdown__inner">
                                                      <div class="m-dropdown__body">
                                                          <div class="m-dropdown__content">
                                                              <input type="text" id="package-color">
                                                          </div>
                                                      </div>
                                                  </div>
                                              </div>
                                          </div>
  -->




                                              </span>
                                      </div>
                                  </div>
                              </div>

                          </div>
                      </div>
                  </form>
              </div>
              <div class="modal-footer justify-content-start">
                  <button type="button" class="btn btn-primary">Add package</button>
                  <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
              </div>
          </div>
      </div>
  </div>
);

export default AddPackageModal;
