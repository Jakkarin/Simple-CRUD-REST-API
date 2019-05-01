<!DOCTYPE html>
<html lang="en">
<head>
    <base href="<?php echo $this->config->base_url(); ?>"/>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Products Management</title>

    <script src="assets/axios.js"></script>
    <script src="assets/vue.js"></script>
    <link rel="stylesheet" href="assets/bootstrap.min.css">
</head>
<body>
    <div id="app" style="visibility: hidden;">
        <div class="container">
            <div class="d-flex mt-4 mb-2">
                <div class="flex-fill">
                    <h1>จัดการสินค้า</h1>
                </div>
                <div class="flex-fill align-self-center text-right">
                    <button class="btn btn-dark" @click="openCreateModal">เพิ่มสินค้า</button>
                </div>
            </div>

            <table class="table table-striped table-bordered">
                <thead>
                    <tr>
                        <th>รหัสสินค้า</th>
                        <th>ชื่อสินค้า</th>
                        <th>ราคาสินค้า</th>
                        <th>จำนวนสินค้า</th>
                        <th class="text-center">ดำเนินการ</th>
                    </tr>

                    <tr v-if="loading">
                        <td colspan="5" class="text-center">กำหลังโหลดข้อมูล ...</td>
                    </tr>
                </thead>
                <tbody>
                    <tr v-if="items.length < 1">
                        <td colspan="5" class="text-center">ยังไม่มีข้อมูล</td>
                    </tr>

                    <tr v-for="item of items">
                        <td>{{ item.prodid }}</td>
                        <td>{{ item.prodname }}</td>
                        <td>{{ item.prodprice }}</td>
                        <td>{{ item.prodqty }} {{ item.produnit }}</td>
                        <td class="text-center">
                            <div class="btn-group">
                                <button class="btn btn-warning" @click="editProduct(item)">แก้ไข</button>
                                <button class="btn btn-danger" @click="removeProduct(item.prodid)">ลบ</button>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>

            <nav class="float-right">
                <ul class="pagination">
                    <li :class="['page-item', current_page < 2 ? 'disabled' : '']">
                        <button class="page-link" :disabled="current_page < 2" @click="previousPage()">Previous</button>
                    </li>
                    <li :class="['page-item', i === current_page ? 'active' : '']" v-for="i in total_page">
                        <button class="page-link" @click="clickPage(i)">{{ i }}</button>
                    </li>
                    <li :class="['page-item', current_page === total_page ? 'disabled' : '']">
                        <button class="page-link" :disabled="current_page === total_page" @click="nextPage()">Next</button>
                    </li>
                </ul>
            </nav>

            <div class="clearfix"></div>

            <div class="modal" tabindex="-1" role="dialog" id="create_modal">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">New Product</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="form-group">
                                <label>Product Name</label>
                                <input type="text" class="form-control" v-model="product.name">
                            </div>
                            <div class="form-group">
                                <label>Product Price</label>
                                <input type="text" class="form-control" v-model="product.price">
                            </div>
                            <div class="form-group">
                                <label>Product Quantiy</label>
                                <input type="text" class="form-control" v-model="product.qty">
                            </div>
                            <div class="form-group">
                                <label>Product Unit</label>
                                <input type="text" class="form-control" v-model="product.unit">
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                            <button type="button" class="btn btn-primary" @click="submitCreateModal">Save</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="assets/jquery-3.3.1.slim.min.js"></script>
    <script src="assets/popper.min.js"></script>
    <script src="assets/bootstrap.min.js"></script>

    <script>
        var app = new Vue({
            el: '#app',
            data: {
                loading: false,
                total_page: 1,
                current_page: 1,
                items: [],
                product: {
                    name: '',
                    price: '',
                    qty: '',
                    unit: '',
                },
                product_id: null,
            },
            mounted: function() {
                this.reloadData();

                $('#app').css('visibility', 'visible');

                $('#create_modal').on('bs.modal.hidden', () => {
                    if (this.product_id) {
                        this.clearCreateModel();
                    }
                });
            },
            methods: {
                reloadData: function(page = 1) {
                    this.loading = true;

                    axios.get('api/v1/product?p=' + page).then((response) => {
                        console.log(response.data.items);
                        this.total_page = response.data.total_page;
                        this.current_page = response.data.current_page;
                        this.items = Object.assign([], response.data.items);
                        this.loading = false;
                    }).catch((error) => {
                        alert(error);
                    });
                },
                openCreateModal: function() {
                    $('#create_modal').modal('show');
                },
                clearCreateModel: function() {
                    this.product_id = null;
                    this.product = Object.assign({});
                    $('#create_modal').modal('hide');
                },
                submitCreateModal: function() {
                    if (this.product_id) {
                        return this.submitEditProduct();
                    }
                    
                    axios.post('api/v1/product', this.product).then((response) => {
                        console.log(response.data);
                        this.reloadData();
                        this.clearCreateModel();
                    }).catch((error) => {
                        alert(error.response.data.text);
                    });
                },
                editProduct: function(product) {
                    this.product_id = product.prodid;
                    
                    this.product = Object.assign({}, {
                        name: product.prodname,
                        price: product.prodprice,
                        qty: product.prodqty,
                        unit: product.produnit,
                    });

                    this.openCreateModal();
                },
                submitEditProduct: function() {
                    axios.patch('api/v1/product/' + this.product_id, this.product).then((response) => {
                        console.log(response.data);
                        this.reloadData();
                        this.clearCreateModel();
                    }).catch((error) => {
                        alert(error.response.data.text);
                    });
                },
                removeProduct: function(prodid) {
                    if (confirm('ต้องการลบสินค้ารหัส ' + prodid + ' ใช่หรือไม่?')) {
                        axios.delete('api/v1/product/' + prodid).then(() => this.reloadData());
                    }
                },
                nextPage: function() {
                    var newpage = this.current_page + 1;

                    if (newpage <= this.total_page) {
                        this.reloadData(newpage);
                    }
                },
                clickPage: function(page) {
                    var newpage = parseInt(page);

                    if (newpage === this.current_page) {
                        return;
                    }

                    if (1 <= newpage && newpage <= this.total_page) {
                        this.reloadData(newpage);
                    }
                },
                previousPage: function() {
                    var newpage = this.current_page - 1;

                    if (newpage >= 1) {
                        this.reloadData(newpage);
                    }
                }
            },
        })
    </script>

</body>
</html>