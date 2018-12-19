import React from "react";
import { NavLink } from "react-router-dom";

const Header = props => (
  <div>
    <header
      className="m-grid__item		m-header "
      data-minimize="minimize"
      data-minimize-offset="200"
      data-minimize-mobile-offset="200"
    >
      <div className="m-header__top">
        <div className="m-container m-container--responsive m-container--xxl m-container--full-height m-page__container">
          <div className="m-stack m-stack--ver m-stack--desktop">
            <div className="m-stack__item m-brand">
              <div className="m-stack m-stack--ver m-stack--general m-stack--inline">
                <div className="m-stack__item m-stack__item--middle m-brand__logo">
                  <a href="order.html" className="m-brand__logo-wrapper">
                    Name panel
                  </a>
                </div>
                <div className="m-stack__item m-stack__item--middle m-brand__tools">
                  <a
                    id="m_aside_header_menu_mobile_toggle"
                    href="javascript:;"
                    className="m-brand__icon m-brand__toggler m--visible-tablet-and-mobile-inline-block"
                  >
                    <span />
                  </a>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div className="m-header__bottom">
        <div className="m-container m-container--responsive m-container--fuild m-container--full-height m-page__container">
          <div className="m-stack m-stack--ver m-stack--desktop">
            <div className="m-stack__item m-stack__item--middle m-stack__item--fluid">
              <button
                className="m-aside-header-menu-mobile-close  m-aside-header-menu-mobile-close--skin-light "
                id="m_aside_header_menu_mobile_close_btn"
              >
                <i className="la la-close" />
              </button>
              <div
                id="m_header_menu"
                className="m-header-menu m-aside-header-menu-mobile m-aside-header-menu-mobile--offcanvas  m-header-menu--skin-dark m-header-menu--submenu-skin-light m-aside-header-menu-mobile--skin-light m-aside-header-menu-mobile--submenu-skin-light "
              >
                <ul className="m-menu__nav  m-menu__nav--submenu-arrow ">
                  <li className="m-menu__item" aria-haspopup="true">
                    <NavLink to="/orders" className="m-menu__link ">
                      <span className="m-menu__link-text">Orders</span>
                    </NavLink>
                  </li>
                  <li className="m-menu__item" aria-haspopup="true">
                    <NavLink to="/payments" className="m-menu__link ">
                      <span className="m-menu__link-text">Payments</span>
                    </NavLink>
                  </li>
                  <li className="m-menu__item" aria-haspopup="true">
                    <NavLink to="/products" className="m-menu__link">
                      <span className="m-menu__link-text">Products</span>
                    </NavLink>
                  </li>
                  <li
                    className="m-menu__item mobile-hidden"
                    aria-haspopup="true"
                  >
                    <NavLink to="/settings" className="m-menu__link ">
                      <span className="m-menu__link-text">Settings</span>
                    </NavLink>
                  </li>
                  <li
                    className="m-menu__item m-menu__item--submenu m-menu__item--rel m-menu__item--more m-menu__item--icon-only mobile-show"
                    data-menu-submenu-toggle="hover"
                    data-redirect="true"
                    aria-haspopup="true"
                  >
                    <a
                      href="settings.html"
                      className="m-menu__link m-menu__toggle"
                    >
                      <span className="m-menu__link-text"> Settings</span>
                      <i className="m-menu__ver-arrow la la-angle-right" />
                    </a>
                    <div className="m-menu__submenu m-menu__submenu--classNameic m-menu__submenu--left m-menu__submenu--pull">
                      <span className="m-menu__arrow m-menu__arrow--adjust" />
                      <ul className="m-menu__subnav">
                        <li
                          className="m-menu__item "
                          data-redirect="true"
                          aria-haspopup="true"
                        >
                          <a href="inner.html" className="m-menu__link ">
                            <i className="m-menu__link-icon icon-settings" />
                            <span className="m-menu__link-text">General</span>
                          </a>
                        </li>
                        <li
                          className="m-menu__item "
                          data-redirect="true"
                          aria-haspopup="true"
                        >
                          <a href="inner.html" className="m-menu__link ">
                            <i className="m-menu__link-icon icon-wallet" />
                            <span className="m-menu__link-text">Payments</span>
                          </a>
                        </li>
                        <li
                          className="m-menu__item "
                          data-redirect="true"
                          aria-haspopup="true"
                        >
                          <a href="inner.html" className="m-menu__link ">
                            <i className="m-menu__link-icon icon-share" />
                            <span className="m-menu__link-text">Providers</span>
                          </a>
                        </li>
                        <li
                          className="m-menu__item "
                          data-redirect="true"
                          aria-haspopup="true"
                        >
                          <a href="inner.html" className="m-menu__link ">
                            <i className="m-menu__link-icon flaticon-list-1" />
                            <span className="m-menu__link-text">
                              Navigation
                            </span>
                          </a>
                        </li>
                        <li
                          className="m-menu__item "
                          data-redirect="true"
                          aria-haspopup="true"
                        >
                          <a href="inner.html" className="m-menu__link ">
                            <i className="m-menu__link-icon icon-docs" />
                            <span className="m-menu__link-text">Pages</span>
                          </a>
                        </li>
                        <li
                          className="m-menu__item "
                          data-redirect="true"
                          aria-haspopup="true"
                        >
                          <a href="inner.html" className="m-menu__link ">
                            <i className="m-menu__link-icon icon-puzzle" />
                            <span className="m-menu__link-text">Themes</span>
                          </a>
                        </li>
                        <li
                          className="m-menu__item "
                          data-redirect="true"
                          aria-haspopup="true"
                        >
                          <a href="inner.html" className="m-menu__link ">
                            <i className="m-menu__link-icon icon-layers" />
                            <span className="m-menu__link-text">Blocks</span>
                          </a>
                        </li>
                      </ul>
                    </div>
                  </li>
                </ul>
              </div>
            </div>
            <div
              className="m-stack__item m-stack__item--middle m-dropdown m-dropdown--arrow m-dropdown--large m-dropdown--mobile-full-width m-dropdown--align-right m-dropdown--skin-light m-header-search m-header-search--expandable m-header-search--skin-"
              id="m_quicksearch"
              data-search-type="default"
            >
              <form className="m-header-search__form">
                <ul className="m-menu--right">
                  <li>
                    <a href="account.html">Account</a>
                  </li>
                  <li>
                    <a href="#">Logout</a>
                  </li>
                </ul>
              </form>
            </div>
          </div>
        </div>
      </div>
    </header>
  </div>
);

export default Header;
