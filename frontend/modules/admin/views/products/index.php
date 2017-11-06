<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use frontend\helpers\Ui;
use frontend\assets\ProductsAsset;

/* @var $this yii\web\View */

$this->title = 'Products';

$formater = Yii::$app->formatter;

ProductsAsset::register($this);
?>

<!-- Product Search -->
<div class="row sommerce-products__actions">

    <div class="col-lg-10 col-sm-12">
        <div class="page-content">
            <button class="btn btn-primary m-btn--air" data-toggle="modal" data-target=".add_product" data-backdrop="static">Add product</button>
            <button class="btn btn-primary m-btn--air" data-toggle="modal" data-target=".add_package" data-backdrop="static">Add package</button>
        </div>
    </div>
    <div class="col-lg-2 col-sm-12 d-flex align-items-center">
        <div class="input-group m-input-group--air">
            <input type="text" class="form-control" placeholder="Search for..." aria-label="Search for...">
            <span class="input-group-btn">
                        <button class="btn btn-primary" type="button"><span class="fa fa-search"></span></button>
                      </span>
        </div>
    </div>
</div>
<!--/ Product Search -->

<!-- Products-Packages list -->
<div class="row">

    <div class="col-12">

        <div class="sommerce_dragtable">

            <div class="sortable">
                <div class="row group-caption">
                    <div class="col-12 sommerce_dragtable__category">
                        <div class="sommerce_dragtable__category-title">
                            <div class="row align-items-center">
                                <div class="col-12">
                                    <div class="sommerce_dragtable__category-move move">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                            <title>Drag-Handle</title>
                                            <path d="M7 2c-1.104 0-2 .896-2 2s.896 2 2 2 2-.896 2-2-.896-2-2-2zm0 6c-1.104 0-2 .896-2 2s.896 2 2 2 2-.896 2-2-.896-2-2-2zm0 6c-1.104 0-2 .896-2 2s.896 2 2 2 2-.896 2-2-.896-2-2-2zm6-8c1.104 0 2-.896 2-2s-.896-2-2-2-2 .896-2 2 .896 2 2 2zm0 2c-1.104 0-2 .896-2 2s.896 2 2 2 2-.896 2-2-.896-2-2-2zm0 6c-1.104 0-2 .896-2 2s.896 2 2 2 2-.896 2-2-.896-2-2-2z" fill="#d4d4d4"></path>
                                        </svg>
                                    </div>
                                    Buy Pinterest Followers
                                    <a href="#" class="btn btn-outline-primary btn-sm 	m-btn m-btn--icon m-btn--pill"  data-toggle="modal" data-target=".add_product">Edit</a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 group-items">
                        <div class="group-item sommerce_dragtable__tr align-items-center">
                            <div class="col-lg-5 padding-null-left">
                                <div class="sommerce_dragtable__category-move move">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                        <title>Drag-Handle</title>
                                        <path d="M7 2c-1.104 0-2 .896-2 2s.896 2 2 2 2-.896 2-2-.896-2-2-2zm0 6c-1.104 0-2 .896-2 2s.896 2 2 2 2-.896 2-2-.896-2-2-2zm0 6c-1.104 0-2 .896-2 2s.896 2 2 2 2-.896 2-2-.896-2-2-2zm6-8c1.104 0 2-.896 2-2s-.896-2-2-2-2 .896-2 2 .896 2 2 2zm0 2c-1.104 0-2 .896-2 2s.896 2 2 2 2-.896 2-2-.896-2-2-2zm0 6c-1.104 0-2 .896-2 2s.896 2 2 2 2-.896 2-2-.896-2-2-2z" fill="#d4d4d4"></path>
                                    </svg>
                                </div>
                                <strong>100</strong> Easy
                            </div>
                            <div class="col-lg-2">
                                $0.30
                            </div>
                            <div class="col-lg-2">
                                provider1.com
                            </div>
                            <div class="col-lg-2 text-lg-center">
                                Enabled
                            </div>
                            <div class="col-lg-1 padding-null-lg-right text-lg-right text-sm-left">
                                <button type="button" class="btn m-btn--pill m-btn--air btn-primary btn-sm sommerce_dragtable__action" data-toggle="modal" data-target=".add_package">
                                    Edit
                                </button>
                            </div>
                        </div>
                        <div class="group-item sommerce_dragtable__tr align-items-center">
                            <div class="col-lg-5 padding-null-left">
                                <div class="sommerce_dragtable__category-move move">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                        <title>Drag-Handle</title>
                                        <path d="M7 2c-1.104 0-2 .896-2 2s.896 2 2 2 2-.896 2-2-.896-2-2-2zm0 6c-1.104 0-2 .896-2 2s.896 2 2 2 2-.896 2-2-.896-2-2-2zm0 6c-1.104 0-2 .896-2 2s.896 2 2 2 2-.896 2-2-.896-2-2-2zm6-8c1.104 0 2-.896 2-2s-.896-2-2-2-2 .896-2 2 .896 2 2 2zm0 2c-1.104 0-2 .896-2 2s.896 2 2 2 2-.896 2-2-.896-2-2-2zm0 6c-1.104 0-2 .896-2 2s.896 2 2 2 2-.896 2-2-.896-2-2-2z" fill="#d4d4d4"></path>
                                    </svg>
                                </div>
                                <strong>1000000</strong> Reverbnation Indian Followers
                            </div>
                            <div class="col-lg-2">
                                $30.10
                            </div>
                            <div class="col-lg-2">
                                panel.greensocials.com
                            </div>
                            <div class="col-lg-2 text-lg-center">
                                Enabled
                            </div>
                            <div class="col-lg-1 padding-null-lg-right text-lg-right text-sm-left">
                                <button type="button" class="btn m-btn--pill m-btn--air btn-primary btn-sm sommerce_dragtable__action" data-toggle="modal" data-target=".add_package">
                                    Edit
                                </button>
                            </div>
                        </div>
                        <div class="group-item sommerce_dragtable__tr align-items-center disabled-product">
                            <div class="col-lg-5 padding-null-left">
                                <div class="sommerce_dragtable__category-move move">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                        <title>Drag-Handle</title>
                                        <path d="M7 2c-1.104 0-2 .896-2 2s.896 2 2 2 2-.896 2-2-.896-2-2-2zm0 6c-1.104 0-2 .896-2 2s.896 2 2 2 2-.896 2-2-.896-2-2-2zm0 6c-1.104 0-2 .896-2 2s.896 2 2 2 2-.896 2-2-.896-2-2-2zm6-8c1.104 0 2-.896 2-2s-.896-2-2-2-2 .896-2 2 .896 2 2 2zm0 2c-1.104 0-2 .896-2 2s.896 2 2 2 2-.896 2-2-.896-2-2-2zm0 6c-1.104 0-2 .896-2 2s.896 2 2 2 2-.896 2-2-.896-2-2-2z" fill="#d4d4d4"></path>
                                    </svg>
                                </div>
                                <strong>1000000</strong> Reverbnation Indian Followers
                            </div>
                            <div class="col-lg-2">
                                $35.30
                            </div>
                            <div class="col-lg-2">
                                Manul
                            </div>
                            <div class="col-lg-2 text-lg-center">
                                Disabled
                            </div>
                            <div class="col-lg-1 padding-null-lg-right text-lg-right text-sm-left">
                                <button type="button" class="btn m-btn--pill m-btn--air btn-primary btn-sm sommerce_dragtable__action" data-toggle="modal" data-target=".add_package">
                                    Edit
                                </button>
                            </div>
                        </div>

                    </div>
                </div>
                <div class="row group-caption">
                    <div class="col-12 sommerce_dragtable__category">
                        <div class="sommerce_dragtable__category-title">
                            <div class="row align-items-center">
                                <div class="col-12">
                                    <div class="sommerce_dragtable__category-move move">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                            <title>Drag-Handle</title>
                                            <path d="M7 2c-1.104 0-2 .896-2 2s.896 2 2 2 2-.896 2-2-.896-2-2-2zm0 6c-1.104 0-2 .896-2 2s.896 2 2 2 2-.896 2-2-.896-2-2-2zm0 6c-1.104 0-2 .896-2 2s.896 2 2 2 2-.896 2-2-.896-2-2-2zm6-8c1.104 0 2-.896 2-2s-.896-2-2-2-2 .896-2 2 .896 2 2 2zm0 2c-1.104 0-2 .896-2 2s.896 2 2 2 2-.896 2-2-.896-2-2-2zm0 6c-1.104 0-2 .896-2 2s.896 2 2 2 2-.896 2-2-.896-2-2-2z" fill="#d4d4d4"></path>
                                        </svg>
                                    </div>
                                    Buy Pinterest Followers
                                    <a href="#" class="btn btn-outline-primary btn-sm 	m-btn m-btn--icon m-btn--pill"  data-toggle="modal" data-target=".add_product">Edit</a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 group-items">
                        <div class="group-item sommerce_dragtable__tr align-items-center">
                            <div class="col-lg-5 padding-null-left">
                                <div class="sommerce_dragtable__category-move move">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                        <title>Drag-Handle</title>
                                        <path d="M7 2c-1.104 0-2 .896-2 2s.896 2 2 2 2-.896 2-2-.896-2-2-2zm0 6c-1.104 0-2 .896-2 2s.896 2 2 2 2-.896 2-2-.896-2-2-2zm0 6c-1.104 0-2 .896-2 2s.896 2 2 2 2-.896 2-2-.896-2-2-2zm6-8c1.104 0 2-.896 2-2s-.896-2-2-2-2 .896-2 2 .896 2 2 2zm0 2c-1.104 0-2 .896-2 2s.896 2 2 2 2-.896 2-2-.896-2-2-2zm0 6c-1.104 0-2 .896-2 2s.896 2 2 2 2-.896 2-2-.896-2-2-2z" fill="#d4d4d4"></path>
                                    </svg>
                                </div>
                                <strong>1000000</strong> Reverbnation Indian Followers
                            </div>
                            <div class="col-lg-2">
                                $2.5
                            </div>
                            <div class="col-lg-2">
                                panel.greensocials.com
                            </div>
                            <div class="col-lg-2 text-lg-center">
                                Enabled
                            </div>
                            <div class="col-lg-1 padding-null-lg-right text-lg-right text-sm-left">
                                <button type="button" class="btn m-btn--pill m-btn--air btn-primary btn-sm sommerce_dragtable__action" data-toggle="modal" data-target=".add_package">
                                    Edit
                                </button>
                            </div>
                        </div>
                        <div class="group-item sommerce_dragtable__tr align-items-center">
                            <div class="col-lg-5 padding-null-left">
                                <div class="sommerce_dragtable__category-move move">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                        <title>Drag-Handle</title>
                                        <path d="M7 2c-1.104 0-2 .896-2 2s.896 2 2 2 2-.896 2-2-.896-2-2-2zm0 6c-1.104 0-2 .896-2 2s.896 2 2 2 2-.896 2-2-.896-2-2-2zm0 6c-1.104 0-2 .896-2 2s.896 2 2 2 2-.896 2-2-.896-2-2-2zm6-8c1.104 0 2-.896 2-2s-.896-2-2-2-2 .896-2 2 .896 2 2 2zm0 2c-1.104 0-2 .896-2 2s.896 2 2 2 2-.896 2-2-.896-2-2-2zm0 6c-1.104 0-2 .896-2 2s.896 2 2 2 2-.896 2-2-.896-2-2-2z" fill="#d4d4d4"></path>
                                    </svg>
                                </div>
                                <strong>300</strong> Super
                            </div>
                            <div class="col-lg-2">
                                $0.25
                            </div>
                            <div class="col-lg-2">
                                sitecsdas.com
                            </div>
                            <div class="col-lg-2 text-lg-center">
                                Enabled
                            </div>
                            <div class="col-lg-1 padding-null-lg-right text-lg-right text-sm-left">
                                <button type="button" class="btn m-btn--pill m-btn--air btn-primary btn-sm sommerce_dragtable__action" data-toggle="modal" data-target=".add_package">
                                    Edit
                                </button>
                            </div>
                        </div>
                        <div class="group-item sommerce_dragtable__tr align-items-center">
                            <div class="col-lg-5 padding-null-left">
                                <div class="sommerce_dragtable__category-move move">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                        <title>Drag-Handle</title>
                                        <path d="M7 2c-1.104 0-2 .896-2 2s.896 2 2 2 2-.896 2-2-.896-2-2-2zm0 6c-1.104 0-2 .896-2 2s.896 2 2 2 2-.896 2-2-.896-2-2-2zm0 6c-1.104 0-2 .896-2 2s.896 2 2 2 2-.896 2-2-.896-2-2-2zm6-8c1.104 0 2-.896 2-2s-.896-2-2-2-2 .896-2 2 .896 2 2 2zm0 2c-1.104 0-2 .896-2 2s.896 2 2 2 2-.896 2-2-.896-2-2-2zm0 6c-1.104 0-2 .896-2 2s.896 2 2 2 2-.896 2-2-.896-2-2-2z" fill="#d4d4d4"></path>
                                    </svg>
                                </div>
                                <strong>1000000</strong> Reverbnation Indian Followers
                            </div>
                            <div class="col-lg-2">
                                $2.5
                            </div>
                            <div class="col-lg-2">
                                panel.greensocials.com
                            </div>
                            <div class="col-lg-2 text-lg-center">
                                Enabled
                            </div>
                            <div class="col-lg-1 padding-null-lg-right text-lg-right text-sm-left">
                                <button type="button" class="btn m-btn--pill m-btn--air btn-primary btn-sm sommerce_dragtable__action" data-toggle="modal" data-target=".add_package">
                                    Edit
                                </button>
                            </div>
                        </div>
                        <div class="group-item sommerce_dragtable__tr align-items-center">
                            <div class="col-lg-5 padding-null-left">
                                <div class="sommerce_dragtable__category-move move">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                        <title>Drag-Handle</title>
                                        <path d="M7 2c-1.104 0-2 .896-2 2s.896 2 2 2 2-.896 2-2-.896-2-2-2zm0 6c-1.104 0-2 .896-2 2s.896 2 2 2 2-.896 2-2-.896-2-2-2zm0 6c-1.104 0-2 .896-2 2s.896 2 2 2 2-.896 2-2-.896-2-2-2zm6-8c1.104 0 2-.896 2-2s-.896-2-2-2-2 .896-2 2 .896 2 2 2zm0 2c-1.104 0-2 .896-2 2s.896 2 2 2 2-.896 2-2-.896-2-2-2zm0 6c-1.104 0-2 .896-2 2s.896 2 2 2 2-.896 2-2-.896-2-2-2z" fill="#d4d4d4"></path>
                                    </svg>
                                </div>
                                <strong>1000000</strong> Reverbnation Indian Followers
                            </div>
                            <div class="col-lg-2">
                                $2.5
                            </div>
                            <div class="col-lg-2">
                                Manual
                            </div>
                            <div class="col-lg-2 text-lg-center">
                                Enabled
                            </div>
                            <div class="col-lg-1 padding-null-lg-right text-lg-right text-sm-left">
                                <button type="button" class="btn m-btn--pill m-btn--air btn-primary btn-sm sommerce_dragtable__action" data-toggle="modal" data-target=".add_package">
                                    Edit
                                </button>
                            </div>
                        </div>
                        <div class="group-item sommerce_dragtable__tr align-items-center">
                            <div class="col-lg-5 padding-null-left">
                                <div class="sommerce_dragtable__category-move move">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                        <title>Drag-Handle</title>
                                        <path d="M7 2c-1.104 0-2 .896-2 2s.896 2 2 2 2-.896 2-2-.896-2-2-2zm0 6c-1.104 0-2 .896-2 2s.896 2 2 2 2-.896 2-2-.896-2-2-2zm0 6c-1.104 0-2 .896-2 2s.896 2 2 2 2-.896 2-2-.896-2-2-2zm6-8c1.104 0 2-.896 2-2s-.896-2-2-2-2 .896-2 2 .896 2 2 2zm0 2c-1.104 0-2 .896-2 2s.896 2 2 2 2-.896 2-2-.896-2-2-2zm0 6c-1.104 0-2 .896-2 2s.896 2 2 2 2-.896 2-2-.896-2-2-2z" fill="#d4d4d4"></path>
                                    </svg>
                                </div>
                                <strong>125000</strong> NamePackage
                            </div>
                            <div class="col-lg-2">
                                $0.99
                            </div>
                            <div class="col-lg-2">
                                Manual
                            </div>
                            <div class="col-lg-2 text-lg-center">
                                Enabled
                            </div>
                            <div class="col-lg-1 padding-null-lg-right text-lg-right text-sm-left">
                                <button type="button" class="btn m-btn--pill m-btn--air btn-primary btn-sm sommerce_dragtable__action" data-toggle="modal" data-target=".add_package">
                                    Edit
                                </button>
                            </div>
                        </div>
                        <div class="group-item sommerce_dragtable__tr align-items-center disabled-product">
                            <div class="col-lg-5 padding-null-left">
                                <div class="sommerce_dragtable__category-move move">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                        <title>Drag-Handle</title>
                                        <path d="M7 2c-1.104 0-2 .896-2 2s.896 2 2 2 2-.896 2-2-.896-2-2-2zm0 6c-1.104 0-2 .896-2 2s.896 2 2 2 2-.896 2-2-.896-2-2-2zm0 6c-1.104 0-2 .896-2 2s.896 2 2 2 2-.896 2-2-.896-2-2-2zm6-8c1.104 0 2-.896 2-2s-.896-2-2-2-2 .896-2 2 .896 2 2 2zm0 2c-1.104 0-2 .896-2 2s.896 2 2 2 2-.896 2-2-.896-2-2-2zm0 6c-1.104 0-2 .896-2 2s.896 2 2 2 2-.896 2-2-.896-2-2-2z" fill="#d4d4d4"></path>
                                    </svg>
                                </div>
                                <strong>1000000</strong> Reverbnation Indian Followers
                            </div>
                            <div class="col-lg-2">
                                $2.5
                            </div>
                            <div class="col-lg-2">
                                panpem.sdia.com
                            </div>
                            <div class="col-lg-2 text-lg-center">
                                Disabled
                            </div>
                            <div class="col-lg-1 padding-null-lg-right text-lg-right text-sm-left">
                                <button type="button" class="btn m-btn--pill m-btn--air btn-primary btn-sm sommerce_dragtable__action" data-toggle="modal" data-target=".add_package">
                                    Edit
                                </button>
                            </div>
                        </div>
                        <div class="group-item sommerce_dragtable__tr align-items-center">
                            <div class="col-lg-5 padding-null-left">
                                <div class="sommerce_dragtable__category-move move">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                        <title>Drag-Handle</title>
                                        <path d="M7 2c-1.104 0-2 .896-2 2s.896 2 2 2 2-.896 2-2-.896-2-2-2zm0 6c-1.104 0-2 .896-2 2s.896 2 2 2 2-.896 2-2-.896-2-2-2zm0 6c-1.104 0-2 .896-2 2s.896 2 2 2 2-.896 2-2-.896-2-2-2zm6-8c1.104 0 2-.896 2-2s-.896-2-2-2-2 .896-2 2 .896 2 2 2zm0 2c-1.104 0-2 .896-2 2s.896 2 2 2 2-.896 2-2-.896-2-2-2zm0 6c-1.104 0-2 .896-2 2s.896 2 2 2 2-.896 2-2-.896-2-2-2z" fill="#d4d4d4"></path>
                                    </svg>
                                </div>
                                <strong>1000000</strong> Reverbnation Indian Followers
                            </div>
                            <div class="col-lg-2">
                                $2.5
                            </div>
                            <div class="col-lg-2">
                                Manual
                            </div>
                            <div class="col-lg-2 text-lg-center">
                                Enabled
                            </div>
                            <div class="col-lg-1 padding-null-lg-right text-lg-right text-sm-left">
                                <button type="button" class="btn m-btn--pill m-btn--air btn-primary btn-sm sommerce_dragtable__action" data-toggle="modal" data-target=".add_package">
                                    Edit
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row group-caption">
                    <div class="col-12 sommerce_dragtable__category">
                        <div class="sommerce_dragtable__category-title">
                            <div class="row align-items-center">
                                <div class="col-12">
                                    <div class="sommerce_dragtable__category-move move">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                            <title>Drag-Handle</title>
                                            <path d="M7 2c-1.104 0-2 .896-2 2s.896 2 2 2 2-.896 2-2-.896-2-2-2zm0 6c-1.104 0-2 .896-2 2s.896 2 2 2 2-.896 2-2-.896-2-2-2zm0 6c-1.104 0-2 .896-2 2s.896 2 2 2 2-.896 2-2-.896-2-2-2zm6-8c1.104 0 2-.896 2-2s-.896-2-2-2-2 .896-2 2 .896 2 2 2zm0 2c-1.104 0-2 .896-2 2s.896 2 2 2 2-.896 2-2-.896-2-2-2zm0 6c-1.104 0-2 .896-2 2s.896 2 2 2 2-.896 2-2-.896-2-2-2z" fill="#d4d4d4"></path>
                                        </svg>
                                    </div>
                                    Buy Pinterest Followers
                                    <a href="#" class="btn btn-outline-primary btn-sm 	m-btn m-btn--icon m-btn--pill"  data-toggle="modal" data-target=".add_product">Edit</a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 group-items">
                        <div class="group-item sommerce_dragtable__tr align-items-center">
                            <div class="col-lg-5 padding-null-left">
                                <div class="sommerce_dragtable__category-move move">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                        <title>Drag-Handle</title>
                                        <path d="M7 2c-1.104 0-2 .896-2 2s.896 2 2 2 2-.896 2-2-.896-2-2-2zm0 6c-1.104 0-2 .896-2 2s.896 2 2 2 2-.896 2-2-.896-2-2-2zm0 6c-1.104 0-2 .896-2 2s.896 2 2 2 2-.896 2-2-.896-2-2-2zm6-8c1.104 0 2-.896 2-2s-.896-2-2-2-2 .896-2 2 .896 2 2 2zm0 2c-1.104 0-2 .896-2 2s.896 2 2 2 2-.896 2-2-.896-2-2-2zm0 6c-1.104 0-2 .896-2 2s.896 2 2 2 2-.896 2-2-.896-2-2-2z" fill="#d4d4d4"></path>
                                    </svg>
                                </div>
                                <strong>1000000</strong> Reverbnation Indian Followers
                            </div>
                            <div class="col-lg-2">
                                $2.5
                            </div>
                            <div class="col-lg-2">
                                panel.greensocials.com
                            </div>
                            <div class="col-lg-2 text-lg-center">
                                Enabled
                            </div>
                            <div class="col-lg-1 padding-null-lg-right text-lg-right text-sm-left">
                                <button type="button" class="btn m-btn--pill m-btn--air btn-primary btn-sm sommerce_dragtable__action" data-toggle="modal" data-target=".add_package">
                                    Edit
                                </button>
                            </div>
                        </div>
                        <div class="group-item sommerce_dragtable__tr align-items-center">
                            <div class="col-lg-5 padding-null-left">
                                <div class="sommerce_dragtable__category-move move">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                        <title>Drag-Handle</title>
                                        <path d="M7 2c-1.104 0-2 .896-2 2s.896 2 2 2 2-.896 2-2-.896-2-2-2zm0 6c-1.104 0-2 .896-2 2s.896 2 2 2 2-.896 2-2-.896-2-2-2zm0 6c-1.104 0-2 .896-2 2s.896 2 2 2 2-.896 2-2-.896-2-2-2zm6-8c1.104 0 2-.896 2-2s-.896-2-2-2-2 .896-2 2 .896 2 2 2zm0 2c-1.104 0-2 .896-2 2s.896 2 2 2 2-.896 2-2-.896-2-2-2zm0 6c-1.104 0-2 .896-2 2s.896 2 2 2 2-.896 2-2-.896-2-2-2z" fill="#d4d4d4"></path>
                                    </svg>
                                </div>
                                <strong>1000000</strong> Reverbnation Indian Followers
                            </div>
                            <div class="col-lg-2">
                                $2.5
                            </div>
                            <div class="col-lg-2">
                                panel.greensocials.com
                            </div>
                            <div class="col-lg-2 text-lg-center">
                                Enabled
                            </div>
                            <div class="col-lg-1 padding-null-lg-right text-lg-right text-sm-left">
                                <button type="button" class="btn m-btn--pill m-btn--air btn-primary btn-sm sommerce_dragtable__action" data-toggle="modal" data-target=".add_package">
                                    Edit
                                </button>
                            </div>
                        </div>
                        <div class="group-item sommerce_dragtable__tr align-items-center">
                            <div class="col-lg-5 padding-null-left">
                                <div class="sommerce_dragtable__category-move move">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                        <title>Drag-Handle</title>
                                        <path d="M7 2c-1.104 0-2 .896-2 2s.896 2 2 2 2-.896 2-2-.896-2-2-2zm0 6c-1.104 0-2 .896-2 2s.896 2 2 2 2-.896 2-2-.896-2-2-2zm0 6c-1.104 0-2 .896-2 2s.896 2 2 2 2-.896 2-2-.896-2-2-2zm6-8c1.104 0 2-.896 2-2s-.896-2-2-2-2 .896-2 2 .896 2 2 2zm0 2c-1.104 0-2 .896-2 2s.896 2 2 2 2-.896 2-2-.896-2-2-2zm0 6c-1.104 0-2 .896-2 2s.896 2 2 2 2-.896 2-2-.896-2-2-2z" fill="#d4d4d4"></path>
                                    </svg>
                                </div>
                                <strong>1000000</strong> Reverbnation Indian Followers
                            </div>
                            <div class="col-lg-2">
                                $2.5
                            </div>
                            <div class="col-lg-2">
                                panel.greensocials.com
                            </div>
                            <div class="col-lg-2 text-lg-center">
                                Enabled
                            </div>
                            <div class="col-lg-1 padding-null-lg-right text-lg-right text-sm-left">
                                <button type="button" class="btn m-btn--pill m-btn--air btn-primary btn-sm sommerce_dragtable__action" data-toggle="modal" data-target=".add_package">
                                    Edit
                                </button>
                            </div>
                        </div>

                    </div>
                </div>
                <div class="row group-caption">
                    <div class="col-12 sommerce_dragtable__category">
                        <div class="sommerce_dragtable__category-title">
                            <div class="row align-items-center">
                                <div class="col-12">
                                    <div class="sommerce_dragtable__category-move move">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                            <title>Drag-Handle</title>
                                            <path d="M7 2c-1.104 0-2 .896-2 2s.896 2 2 2 2-.896 2-2-.896-2-2-2zm0 6c-1.104 0-2 .896-2 2s.896 2 2 2 2-.896 2-2-.896-2-2-2zm0 6c-1.104 0-2 .896-2 2s.896 2 2 2 2-.896 2-2-.896-2-2-2zm6-8c1.104 0 2-.896 2-2s-.896-2-2-2-2 .896-2 2 .896 2 2 2zm0 2c-1.104 0-2 .896-2 2s.896 2 2 2 2-.896 2-2-.896-2-2-2zm0 6c-1.104 0-2 .896-2 2s.896 2 2 2 2-.896 2-2-.896-2-2-2z" fill="#d4d4d4"></path>
                                        </svg>
                                    </div>
                                    Buy Pinterest Followers
                                    <a href="#" class="btn btn-outline-primary btn-sm 	m-btn m-btn--icon m-btn--pill"  data-toggle="modal" data-target=".add_product">Edit</a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 group-items">
                        <div class="group-item sommerce_dragtable__tr align-items-center">
                            <div class="col-lg-5 padding-null-left">
                                <div class="sommerce_dragtable__category-move move">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                        <title>Drag-Handle</title>
                                        <path d="M7 2c-1.104 0-2 .896-2 2s.896 2 2 2 2-.896 2-2-.896-2-2-2zm0 6c-1.104 0-2 .896-2 2s.896 2 2 2 2-.896 2-2-.896-2-2-2zm0 6c-1.104 0-2 .896-2 2s.896 2 2 2 2-.896 2-2-.896-2-2-2zm6-8c1.104 0 2-.896 2-2s-.896-2-2-2-2 .896-2 2 .896 2 2 2zm0 2c-1.104 0-2 .896-2 2s.896 2 2 2 2-.896 2-2-.896-2-2-2zm0 6c-1.104 0-2 .896-2 2s.896 2 2 2 2-.896 2-2-.896-2-2-2z" fill="#d4d4d4"></path>
                                    </svg>
                                </div>
                                <strong>1000000</strong> Reverbnation Indian Followers
                            </div>
                            <div class="col-lg-2">
                                $2.5
                            </div>
                            <div class="col-lg-2">
                                panel.greensocials.com
                            </div>
                            <div class="col-lg-2 text-lg-center">
                                Enabled
                            </div>
                            <div class="col-lg-1 padding-null-lg-right text-lg-right text-sm-left">
                                <button type="button" class="btn m-btn--pill m-btn--air btn-primary btn-sm sommerce_dragtable__action" data-toggle="modal" data-target=".add_package">
                                    Edit
                                </button>
                            </div>
                        </div>
                        <div class="group-item sommerce_dragtable__tr align-items-center">
                            <div class="col-lg-5 padding-null-left">
                                <div class="sommerce_dragtable__category-move move">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                        <title>Drag-Handle</title>
                                        <path d="M7 2c-1.104 0-2 .896-2 2s.896 2 2 2 2-.896 2-2-.896-2-2-2zm0 6c-1.104 0-2 .896-2 2s.896 2 2 2 2-.896 2-2-.896-2-2-2zm0 6c-1.104 0-2 .896-2 2s.896 2 2 2 2-.896 2-2-.896-2-2-2zm6-8c1.104 0 2-.896 2-2s-.896-2-2-2-2 .896-2 2 .896 2 2 2zm0 2c-1.104 0-2 .896-2 2s.896 2 2 2 2-.896 2-2-.896-2-2-2zm0 6c-1.104 0-2 .896-2 2s.896 2 2 2 2-.896 2-2-.896-2-2-2z" fill="#d4d4d4"></path>
                                    </svg>
                                </div>
                                <strong>1000000</strong> Reverbnation Indian Followers
                            </div>
                            <div class="col-lg-2">
                                $2.5
                            </div>
                            <div class="col-lg-2">
                                panel.greensocials.com
                            </div>
                            <div class="col-lg-2 text-lg-center">
                                Enabled
                            </div>
                            <div class="col-lg-1 padding-null-lg-right text-lg-right text-sm-left">
                                <button type="button" class="btn m-btn--pill m-btn--air btn-primary btn-sm sommerce_dragtable__action" data-toggle="modal" data-target=".add_package">
                                    Edit
                                </button>
                            </div>
                        </div>
                        <div class="group-item sommerce_dragtable__tr align-items-center">
                            <div class="col-lg-5 padding-null-left">
                                <div class="sommerce_dragtable__category-move move">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                        <title>Drag-Handle</title>
                                        <path d="M7 2c-1.104 0-2 .896-2 2s.896 2 2 2 2-.896 2-2-.896-2-2-2zm0 6c-1.104 0-2 .896-2 2s.896 2 2 2 2-.896 2-2-.896-2-2-2zm0 6c-1.104 0-2 .896-2 2s.896 2 2 2 2-.896 2-2-.896-2-2-2zm6-8c1.104 0 2-.896 2-2s-.896-2-2-2-2 .896-2 2 .896 2 2 2zm0 2c-1.104 0-2 .896-2 2s.896 2 2 2 2-.896 2-2-.896-2-2-2zm0 6c-1.104 0-2 .896-2 2s.896 2 2 2 2-.896 2-2-.896-2-2-2z" fill="#d4d4d4"></path>
                                    </svg>
                                </div>
                                <strong>100</strong> Mega
                            </div>
                            <div class="col-lg-2">
                                $12.10
                            </div>
                            <div class="col-lg-2">
                                Manual
                            </div>
                            <div class="col-lg-2 text-lg-center">
                                Enabled
                            </div>
                            <div class="col-lg-1 padding-null-lg-right text-lg-right text-sm-left">
                                <button type="button" class="btn m-btn--pill m-btn--air btn-primary btn-sm sommerce_dragtable__action" data-toggle="modal" data-target=".add_package">
                                    Edit
                                </button>
                            </div>
                        </div>

                    </div>
                </div>
                <div class="row group-caption">
                    <div class="col-12 sommerce_dragtable__category">
                        <div class="sommerce_dragtable__category-title">
                            <div class="row align-items-center">
                                <div class="col-12">
                                    <div class="sommerce_dragtable__category-move move">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                            <title>Drag-Handle</title>
                                            <path d="M7 2c-1.104 0-2 .896-2 2s.896 2 2 2 2-.896 2-2-.896-2-2-2zm0 6c-1.104 0-2 .896-2 2s.896 2 2 2 2-.896 2-2-.896-2-2-2zm0 6c-1.104 0-2 .896-2 2s.896 2 2 2 2-.896 2-2-.896-2-2-2zm6-8c1.104 0 2-.896 2-2s-.896-2-2-2-2 .896-2 2 .896 2 2 2zm0 2c-1.104 0-2 .896-2 2s.896 2 2 2 2-.896 2-2-.896-2-2-2zm0 6c-1.104 0-2 .896-2 2s.896 2 2 2 2-.896 2-2-.896-2-2-2z" fill="#d4d4d4"></path>
                                        </svg>
                                    </div>
                                    Buy Pinterest Followers
                                    <a href="#" class="btn btn-outline-primary btn-sm 	m-btn m-btn--icon m-btn--pill"  data-toggle="modal" data-target=".add_product">Edit</a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 group-items">
                        <div class="group-item sommerce_dragtable__tr align-items-center">
                            <div class="col-lg-5 padding-null-left">
                                <div class="sommerce_dragtable__category-move move">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                        <title>Drag-Handle</title>
                                        <path d="M7 2c-1.104 0-2 .896-2 2s.896 2 2 2 2-.896 2-2-.896-2-2-2zm0 6c-1.104 0-2 .896-2 2s.896 2 2 2 2-.896 2-2-.896-2-2-2zm0 6c-1.104 0-2 .896-2 2s.896 2 2 2 2-.896 2-2-.896-2-2-2zm6-8c1.104 0 2-.896 2-2s-.896-2-2-2-2 .896-2 2 .896 2 2 2zm0 2c-1.104 0-2 .896-2 2s.896 2 2 2 2-.896 2-2-.896-2-2-2zm0 6c-1.104 0-2 .896-2 2s.896 2 2 2 2-.896 2-2-.896-2-2-2z" fill="#d4d4d4"></path>
                                    </svg>
                                </div>
                                <strong>1000000</strong> Reverbnation Indian Followers
                            </div>
                            <div class="col-lg-2">
                                $2.5
                            </div>
                            <div class="col-lg-2">
                                panel.greensocials.com
                            </div>
                            <div class="col-lg-2 text-lg-center">
                                Enabled
                            </div>
                            <div class="col-lg-1 padding-null-lg-right text-lg-right text-sm-left">
                                <button type="button" class="btn m-btn--pill m-btn--air btn-primary btn-sm sommerce_dragtable__action" data-toggle="modal" data-target=".add_package">
                                    Edit
                                </button>
                            </div>
                        </div>
                        <div class="group-item sommerce_dragtable__tr align-items-center">
                            <div class="col-lg-5 padding-null-left">
                                <div class="sommerce_dragtable__category-move move">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                        <title>Drag-Handle</title>
                                        <path d="M7 2c-1.104 0-2 .896-2 2s.896 2 2 2 2-.896 2-2-.896-2-2-2zm0 6c-1.104 0-2 .896-2 2s.896 2 2 2 2-.896 2-2-.896-2-2-2zm0 6c-1.104 0-2 .896-2 2s.896 2 2 2 2-.896 2-2-.896-2-2-2zm6-8c1.104 0 2-.896 2-2s-.896-2-2-2-2 .896-2 2 .896 2 2 2zm0 2c-1.104 0-2 .896-2 2s.896 2 2 2 2-.896 2-2-.896-2-2-2zm0 6c-1.104 0-2 .896-2 2s.896 2 2 2 2-.896 2-2-.896-2-2-2z" fill="#d4d4d4"></path>
                                    </svg>
                                </div>
                                <strong>1000000</strong> Reverbnation Indian Followers
                            </div>
                            <div class="col-lg-2">
                                $2.5
                            </div>
                            <div class="col-lg-2">
                                panel.greensocials.com
                            </div>
                            <div class="col-lg-2 text-lg-center">
                                Enabled
                            </div>
                            <div class="col-lg-1 padding-null-lg-right text-lg-right text-sm-left">
                                <button type="button" class="btn m-btn--pill m-btn--air btn-primary btn-sm sommerce_dragtable__action" data-toggle="modal" data-target=".add_package">
                                    Edit
                                </button>
                            </div>
                        </div>
                        <div class="group-item sommerce_dragtable__tr align-items-center">
                            <div class="col-lg-5 padding-null-left">
                                <div class="sommerce_dragtable__category-move move">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                        <title>Drag-Handle</title>
                                        <path d="M7 2c-1.104 0-2 .896-2 2s.896 2 2 2 2-.896 2-2-.896-2-2-2zm0 6c-1.104 0-2 .896-2 2s.896 2 2 2 2-.896 2-2-.896-2-2-2zm0 6c-1.104 0-2 .896-2 2s.896 2 2 2 2-.896 2-2-.896-2-2-2zm6-8c1.104 0 2-.896 2-2s-.896-2-2-2-2 .896-2 2 .896 2 2 2zm0 2c-1.104 0-2 .896-2 2s.896 2 2 2 2-.896 2-2-.896-2-2-2zm0 6c-1.104 0-2 .896-2 2s.896 2 2 2 2-.896 2-2-.896-2-2-2z" fill="#d4d4d4"></path>
                                    </svg>
                                </div>
                                <strong>100</strong> Mega
                            </div>
                            <div class="col-lg-2">
                                $12.10
                            </div>
                            <div class="col-lg-2">
                                Manual
                            </div>
                            <div class="col-lg-2 text-lg-center">
                                Enabled
                            </div>
                            <div class="col-lg-1 padding-null-lg-right text-lg-right text-sm-left">
                                <button type="button" class="btn m-btn--pill m-btn--air btn-primary btn-sm sommerce_dragtable__action" data-toggle="modal" data-target=".add_package">
                                    Edit
                                </button>
                            </div>
                        </div>

                    </div>
                </div>
                <div class="row group-caption">
                    <div class="col-12 sommerce_dragtable__category">
                        <div class="sommerce_dragtable__category-title">
                            <div class="row align-items-center">
                                <div class="col-12">
                                    <div class="sommerce_dragtable__category-move move">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                            <title>Drag-Handle</title>
                                            <path d="M7 2c-1.104 0-2 .896-2 2s.896 2 2 2 2-.896 2-2-.896-2-2-2zm0 6c-1.104 0-2 .896-2 2s.896 2 2 2 2-.896 2-2-.896-2-2-2zm0 6c-1.104 0-2 .896-2 2s.896 2 2 2 2-.896 2-2-.896-2-2-2zm6-8c1.104 0 2-.896 2-2s-.896-2-2-2-2 .896-2 2 .896 2 2 2zm0 2c-1.104 0-2 .896-2 2s.896 2 2 2 2-.896 2-2-.896-2-2-2zm0 6c-1.104 0-2 .896-2 2s.896 2 2 2 2-.896 2-2-.896-2-2-2z" fill="#d4d4d4"></path>
                                        </svg>
                                    </div>
                                    Buy Pinterest Followers
                                    <a href="#" class="btn btn-outline-primary btn-sm 	m-btn m-btn--icon m-btn--pill"  data-toggle="modal" data-target=".add_product">Edit</a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 group-items">
                        <div class="group-item sommerce_dragtable__tr align-items-center">
                            <div class="col-lg-5 padding-null-left">
                                <div class="sommerce_dragtable__category-move move">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                        <title>Drag-Handle</title>
                                        <path d="M7 2c-1.104 0-2 .896-2 2s.896 2 2 2 2-.896 2-2-.896-2-2-2zm0 6c-1.104 0-2 .896-2 2s.896 2 2 2 2-.896 2-2-.896-2-2-2zm0 6c-1.104 0-2 .896-2 2s.896 2 2 2 2-.896 2-2-.896-2-2-2zm6-8c1.104 0 2-.896 2-2s-.896-2-2-2-2 .896-2 2 .896 2 2 2zm0 2c-1.104 0-2 .896-2 2s.896 2 2 2 2-.896 2-2-.896-2-2-2zm0 6c-1.104 0-2 .896-2 2s.896 2 2 2 2-.896 2-2-.896-2-2-2z" fill="#d4d4d4"></path>
                                    </svg>
                                </div>
                                <strong>1000000</strong> Reverbnation Indian Followers
                            </div>
                            <div class="col-lg-2">
                                $2.5
                            </div>
                            <div class="col-lg-2">
                                panel.greensocials.com
                            </div>
                            <div class="col-lg-2 text-lg-center">
                                Enabled
                            </div>
                            <div class="col-lg-1 padding-null-lg-right text-lg-right text-sm-left">
                                <button type="button" class="btn m-btn--pill m-btn--air btn-primary btn-sm sommerce_dragtable__action" data-toggle="modal" data-target=".add_package">
                                    Edit
                                </button>
                            </div>
                        </div>
                        <div class="group-item sommerce_dragtable__tr align-items-center">
                            <div class="col-lg-5 padding-null-left">
                                <div class="sommerce_dragtable__category-move move">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                        <title>Drag-Handle</title>
                                        <path d="M7 2c-1.104 0-2 .896-2 2s.896 2 2 2 2-.896 2-2-.896-2-2-2zm0 6c-1.104 0-2 .896-2 2s.896 2 2 2 2-.896 2-2-.896-2-2-2zm0 6c-1.104 0-2 .896-2 2s.896 2 2 2 2-.896 2-2-.896-2-2-2zm6-8c1.104 0 2-.896 2-2s-.896-2-2-2-2 .896-2 2 .896 2 2 2zm0 2c-1.104 0-2 .896-2 2s.896 2 2 2 2-.896 2-2-.896-2-2-2zm0 6c-1.104 0-2 .896-2 2s.896 2 2 2 2-.896 2-2-.896-2-2-2z" fill="#d4d4d4"></path>
                                    </svg>
                                </div>
                                <strong>1000000</strong> Reverbnation Indian Followers
                            </div>
                            <div class="col-lg-2">
                                $2.5
                            </div>
                            <div class="col-lg-2">
                                panel.greensocials.com
                            </div>
                            <div class="col-lg-2 text-lg-center">
                                Enabled
                            </div>
                            <div class="col-lg-1 padding-null-lg-right text-lg-right text-sm-left">
                                <button type="button" class="btn m-btn--pill m-btn--air btn-primary btn-sm sommerce_dragtable__action" data-toggle="modal" data-target=".add_package">
                                    Edit
                                </button>
                            </div>
                        </div>
                        <div class="group-item sommerce_dragtable__tr align-items-center">
                            <div class="col-lg-5 padding-null-left">
                                <div class="sommerce_dragtable__category-move move">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                        <title>Drag-Handle</title>
                                        <path d="M7 2c-1.104 0-2 .896-2 2s.896 2 2 2 2-.896 2-2-.896-2-2-2zm0 6c-1.104 0-2 .896-2 2s.896 2 2 2 2-.896 2-2-.896-2-2-2zm0 6c-1.104 0-2 .896-2 2s.896 2 2 2 2-.896 2-2-.896-2-2-2zm6-8c1.104 0 2-.896 2-2s-.896-2-2-2-2 .896-2 2 .896 2 2 2zm0 2c-1.104 0-2 .896-2 2s.896 2 2 2 2-.896 2-2-.896-2-2-2zm0 6c-1.104 0-2 .896-2 2s.896 2 2 2 2-.896 2-2-.896-2-2-2z" fill="#d4d4d4"></path>
                                    </svg>
                                </div>
                                <strong>100</strong> Mega
                            </div>
                            <div class="col-lg-2">
                                $12.10
                            </div>
                            <div class="col-lg-2">
                                Manual
                            </div>
                            <div class="col-lg-2 text-lg-center">
                                Enabled
                            </div>
                            <div class="col-lg-1 padding-null-lg-right text-lg-right text-sm-left">
                                <button type="button" class="btn m-btn--pill m-btn--air btn-primary btn-sm sommerce_dragtable__action" data-toggle="modal" data-target=".add_package">
                                    Edit
                                </button>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
<!-- Products-Packages list -->

<!-- Modal `Add/Edit Product` -->
<div class="modal fade add_product" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add product</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">

                <div class="form-group">
                    <label for="edit-page-title">Product name</label>
                    <input type="text" class="form-control" id="edit-page-title">
                </div>

                <div class="form-group">
                    <div id="summernote"></div>
                </div>

                <div class="form-group">
                    <label for="package-product">Properties</label>
                    <div class="input-group">
                        <input type="text" class="form-control input-properties" placeholder="Add properties">
                        <span class="input-group-btn">
                                <button class="btn btn-primary add-properies" type="button">Add</button>
                              </span>
                    </div>

                    <ul class="list-group list-preperties">
                        <li class="list-group-item">Properties #1 <span class="fa fa-times delete-properies"></span></li>
                        <li class="list-group-item">Properties #2 <span class="fa fa-times delete-properies"></span></li>
                        <li class="list-group-item">Properties #3 <span class="fa fa-times delete-properies"></span></li>
                        <li class="list-group-item">Properties #4 <span class="fa fa-times delete-properies"></span></li>
                        <li class="list-group-item">Properties #5 <span class="fa fa-times delete-properies"></span></li>
                    </ul>
                </div>

                <div class="card card-white">
                    <div class="card-body">

                        <div class="row seo-header align-items-center">
                            <div class="col-sm-8">
                                Search engine listing preview
                            </div>
                            <div class="col-sm-4 text-sm-right">
                                <button class="btn btn-sm btn-link" data-toggle="collapse" href="#seo-block">Edit website SEO</button>
                            </div>
                        </div>

                        <div class="seo-preview">
                            <div class="seo-preview__title edit-seo__title">Product</div>
                            <div class="seo-preview__url">http://fastinsta.sommerce.net/<span class="edit-seo__url">product</span></div>
                            <div class="seo-preview__description edit-seo__meta">
                                A great About Us page helps builds trust between you and your customers. The more content you provide about you and your business, the more confident people wil...
                            </div>
                        </div>

                        <div class="collapse" id="seo-block">
                            <div class="form-group">
                                <label for="edit-seo__title">Page title</label>
                                <input class="form-control" id="edit-seo__title" value="Product">
                                <small class="form-text text-muted"><span class="edit-seo__title-muted"></span> of 70 characters used</small>
                            </div>
                            <div class="form-group">
                                <label for="edit-seo__meta">Meta description</label>
                                <textarea class="form-control" id="edit-seo__meta" rows="3">A great About Us page helps builds trust between you and your customers. The more content you provide about you and your business, the more confident people will text</textarea>
                                <small class="form-text text-muted"><span class="edit-seo__meta-muted"></span> of 160 characters used</small>
                            </div>
                            <div class="form-group">
                                <label for="edit-seo__url">URL</label>
                                <div class="input-group">
                                    <span class="input-group-addon" id="basic-addon3">http://fastinsta.sommerce.net/</span>
                                    <input type="text" class="form-control" id="edit-seo__url" value="about-us">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>


            <div class="modal-footer justify-content-start">
                <button type="button" class="btn btn-primary">Add product</button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
            </div>
        </div>
    </div>
</div>
<!--/ Modal `Add/Edit Product` -->


<!-- Modal Add/Edit Package -->
<div class="modal fade add_package" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add package</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="">
                    <!-- Alert Error -->
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        </button>
                        <strong>Oh snap!</strong> Error message!
                    </div>
                    <!-- Alert Error END -->
                    <div class="form-group">
                        <label for="package-name">Package name *</label>
                        <input type="email" class="form-control" id="package-name">
                    </div>
                    <div class="form-group">
                        <label for="package-price">Price *</label>
                        <input type="email" class="form-control" id="package-price">
                    </div>
                    <div class="form-group">
                        <label for="package-quantity">Quantity *</label>
                        <input type="email" class="form-control" id="package-quantity">
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
                    <hr>
                    <div class="form-group">
                        <label for="package-product">Product</label>
                        <select id="package-product" class="form-control">
                            <option value="1">Buy YouTube Views</option>
                            <option value="2"> Buy Google Plus Followers</option>
                            <option value="3">Buy Twitter Followers</option>
                            <option value="4">Buy SoundCloud Followers</option>
                            <option value="5">Buy GooglePlus Followers</option>
                            <option value="6">Buy Pinterest Followers</option>
                            <option value="7">Buy SoundCloud Plays</option>
                            <option value="8">Buy Vine Followers</option>
                            <option value="9">Buy SoundCloud Likes</option>
                            <option value="10">Buy SoundCloud Downloads</option>
                            <option value="11">Buy Periscope Followers</option>
                            <option value="12">Buy Snapchat Followers</option>
                            <option value="13">instagram</option>
                            <option value="14">test</option>
                            <option value="15">test</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="package-availability">Availability</label>
                        <select id="package-availability" class="form-control">
                            <option value="1">Enabled</option>
                            <option value="2">Disabled</option>
                        </select>
                    </div>
                    <hr>
                    <div class="form-group">
                        <label for="package-mode">Mode</label>
                        <select id="package-mode" class="form-control">
                            <option value="1">Manual</option>
                            <option value="2">Auto</option>
                        </select>
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
<!--/ Modal Add/Edit Package -->