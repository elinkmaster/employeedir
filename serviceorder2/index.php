<?php
session_start();

// if (isset($_SESSION['user_id'])) {

// } else {
//     header('Location: /login.php');
// }

/**
 * Gets list of months between two dates
 * @param  int $start Unix timestamp
 * @param  int $end Unix timestamp
 * @return array
 */

function echoDate( $start, $end ){

    $current = $start;
    $ret = array(); 
    $m = array();

    while( $current<$end ){
        
        $next = @date('Y-M-01', $current) . "+1 month";
        $current = @strtotime($next);
        $ret[] = $current;
        $m[] = @date('F', $current);
    }
    return $m;
}
?>


<html><head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="hU67vQHEldNpweTdjkjrmeN3elcy8uce8jKDn0F0">
    <title>RM Service Order | Dashboard
</title>
    <link rel="icon" type="image/png" href="http://www.elink.com.ph/wp-content/uploads/2016/01/elink-logo-site.png">
    <link href="http://dir.elink.corp/public/css/bootstrap.min.css" rel="stylesheet" type="text/css">
    <link href="src/css/font-awesome.min.css" rel="stylesheet" type="text/css">
    <link href="http://dir.elink.corp/public/css/datepicker3.css" rel="stylesheet" type="text/css">
    <link href="http://dir.elink.corp/public/css/styles.css" rel="stylesheet" type="text/css">
    <link href="http://dir.elink.corp/public/css/custom.css" rel="stylesheet" type="text/css">
    <link href="http://dir.elink.corp/public/css/jquery.dataTables.css" rel="stylesheet" type="text/css">
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
    <link href="http://dir.elink.corp/public/css/select2.min.css" rel="stylesheet">
    <script src="http://dir.elink.corp/public/js/jquery-1.11.1.min.js"></script>
<style type="text/css">
body{
   
}
.width-110px{
    width: 110px;
    float: left;
    padding: 10px 10px 10px 0px;
}
.width-125px{
    width: 125px;
    float: left;
    padding: 10px 10px 10px 0px;
}
.width-180px{
    width: 180px;
    float: left;
    padding: 10px 10px 10px 0px;
}
.books-div:not(:first-child) .width-180px, .books-div:not(:first-child) .width-125px, .books-div:not(:first-child) .col-md-2{
      padding: 0px 10px 0px 0px !important;
}
.btn-success{
    background-color: #43A047  !important;
    border-color: #43A047 !important;
}
.btn-success{
    margin-top: 3px;
}

.panel-heading{
    background-color: #CC5500 !important;
}

span.fa.fa-close{
    color: red;
    cursor: pointer;
}
a.btn-login{
    margin: 12px auto;
}
a.btn-login:hover, a.btn-login:active, a.btn-login:visited{
    color: white;
}
textarea {
    resize: none;
    border-radius: 0px !important;
    box-shadow: none !important;
    border-color: #ddd !important;
}
</style>
</head>
<body>
    <!-- nav header -->
    <nav class="navbar navbar-custom navbar-fixed-top" role="navigation" style="background-color: #FFFFFF !important;">
        <div class="container-fluid">
            <div class="navbar-header">
                <a class="navbar-brand" href="#" style="font-">
                    <span style="color: #CC5500;">
                        <img src="/public/img/Orange-Digital-Technologies-LOGO-ONLY.PNG" style="width: 40px; margin-top: -10px">
                        &nbsp;Service Order
                    </span>
                    
                    
                </a>
                <!-- <div class="pull-right" style="display: table; vertical-align: middle;">
                    <?php 
                        if (isset($_SESSION['user_id'])) {
                            echo '<a data-target="#" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false" class="btn btn-login" >' . $_SESSION['first_name'] . ' ' . $_SESSION['last_name'] . ' <span class="caret"></span></a> ';
                        } else {
                            echo '<a href="/login.php" class="btn btn-login">Login</a>';
                        }
                    ?>
                      <ul class="dropdown-menu" aria-labelledby="dLabel">
                        <li>Logout</li>
                      </ul>
                      <ul class="dropdown-menu" aria-labelledby="drop2">
                        <li><a target="_blank" href="http://dir.elink.corp/employee_info/<?php echo  $_SESSION['user_id']; ?>">My Profile</a></li>
                        <li role="separator" class="divider"></li>
                        <li><a href="/logout.php">Logout</a></li>
                    </ul>
                </div> -->
            </div>
        </div>
    </nav>

    <div class="content-holder">
          <form method="post" action="generate.php" id="invoice_form">
            <div style="padding: 10px">
        
      <div class="col-lg-12 col-md-12">
            <div class="panel panel-default ">
                <div class="panel-heading">
                    Generate Service Order
                    </div>
                    <div class="panel-body timeline-container">
                      <div class="col-md-3  no-padding">
                          <div class="form-group">
                            <label for="name">First Name</label>
                            <input type="text" class="form-control" id="first_name" name="first_name" placeholder="Enter First Name" required="">
                            <!-- <small id="emailHelp" class="form-text text-muted">We'll never share your email with anyone else.</small> -->
                          </div>
                          <div class="form-group">
                            <label for="name">Last Name</label>
                            <input type="text" class="form-control" id="last_name" name="last_name" placeholder="Enter Last Name" required="">
                            <!-- <small id="emailHelp" class="form-text text-muted">We'll never share your email with anyone else.</small> -->
                          </div>
                          <div class="form-group">
                            <label for="address">Address</label>
                            <textarea type="text" class="form-control" id="address" name="address" placeholder="Enter address" rows="3" required=""></textarea>
                          </div>
                          <div class="form-group">
                            <label for="emailaddress">Email Address</label>
                            <input type="email" class="form-control"  id="email_address" name="email_address" placeholder="Enter email address" required="">
                          </div>
                      </div>

                      <div class="col-md-3 ">
                        <div class="form-group">
                            <label for="transaction_ref_id">Service Order ID</label>
                            <input type="text" class="form-control" id="transaction_ref_id" name="transaction_ref_id" placeholder="Service Order ID" />
                        </div>
                        <div class="form-group">
                            <label for="date">Date</label>
                            <input type="text" class="form-control datepicker" id="date" name="date" placeholder="   Enter date" autocomplete="off" required="">
                        </div>
                        <!-- <div class="form-group">
                            <label for="department">LOB</label>
                            <select class="form-control" name="department">
                                <option></option>
                                <option>Publishing</option>
                                <option>Marketing</option>
                            </select>
                        </div> -->
                        <div class="form-group">
                            <label for="name_on_the_card">Name on the card</label>

                            <input type="text" class="form-control" id="name_on_the_card" name="name_on_the_card" placeholder="Enter Name on the Card"  required="">
                        </div>
                        <div class="form-group">
                            <label for="billing_address">Billing address</label>
                            <textarea type="text" class="form-control" id="billing_address" name="billing_address" placeholder="Enter Billing address" rows="3" required=""></textarea>
                        </div>
                        <div class="form-group">
                            <label for="last_4_digit">Last 4 digit</label>
                            <input type="text" class="form-control" id="last_4_digit" name="last_4_digit" placeholder="Enter Last 4 digit"  required="">
                        </div>

                      </div>
                      <div class="col-md-6 no-padding" >
                        <div class="col-md-3 no-padding">
                            <h4 style="margin-top: 0px;">Service Orders</h4>
                        </div>
                        <div class="col-md-9">
                            <a id="btn_new_order" class="btn no-padding pull-right"> <span class="fa fa-plus"></span> Add New Order</a>
                        </div>

                        <div class="col-md-12 no-padding" id="orders_box">
                            <div class="col-md-12 no-padding">
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="quantity">Quantity</label>
                                        <input type="number" class="form-control" id="quantity" name="quantity[]" placeholder="Quantity" required="" min="1"/>
                                    </div>
                                </div>
                                <div class="col-md-4 no-padding">
                                    <div class="form-group">
                                        <label for="description">Description</label>
                                        <input type="text" class="form-control" id="description" name="description[]" placeholder="Description"required=""/>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="price">Price</label>
                                        <input type="number" class="form-control" id="price" name="price[]" placeholder="Price" required="" min="1" step=".01"/>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="price">Discount</label>
                                        <input type="number" class="form-control" id="discount" name="discount[]" placeholder="Discount" min="0" step=".01"/>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="col-md-9">
                                <div class="pull-right">
                                    <b>Total</b>
                                </div>
                            </div>
                            <div class="col-md-1">
                                <b id="total_amount"></b>
                            </div>
                            <div class="col-md-1">
                                
                            </div>
                        </div>
                        <div class="col-md-12" style="margin-top: 50px;">
                            <h4>Payment Option</h4>
                            <div class="form-group">
                                <input type="checkbox" name="payment_option" value="full" class="check" id="payment_option_full" ><label for="payment_option_full">Full</label>
                                <br>
                                <input type="checkbox" name="payment_option" value="installment" class="check" id="payment_option_installment" ><label for="payment_option_installment">Installment</label>
                                <div id="installment_div" class="col-md-12">
                                    Number of Payments&nbsp;&nbsp;
                                    <input type="number" id="number_of_payments" name="number_of_payments">
                                    <a id="submit_number_of_payments" class="btn btn-default">Submit</a>
                                    <div class="col-md-6" id="payment_installments">
                                        
                                    </div>
                                    <div class="col-md-6" id="payment_surcharges">
                                        
                                    </div>
                                </div>
                                <div class="col-md-12" id="installment_total_div">
                                    
                                </div>
                            </div>
                        </div>
                        <br>
                        <br>
                      </div>
                        <div class="col-md-12 no-padding">
                            <button name="submit" value="excel" class="btn btn-success pull-right" style="margin-top: 0px;">Generate Excel</button>
                            <button name="submit" value="pdf" class="btn btn-primary pull-right" style="margin-right: 10px;">Generate PDF</button>
                        </div>
                    </div>
                </div>
                </div>
                
            </div>
        </form>
        <script src="http://dir.elink.corp/public/js/bootstrap.min.js"></script>
        <script src="http://dir.elink.corp/public/js/bootstrap-datepicker.js"></script>
        <script src="http://dir.elink.corp/public/js/jquery.dataTables.js"></script>
        <script src="http://dir.elink.corp/public/js/jquery.validate.min.js"></script>
        <script src="http://dir.elink.corp/public/js/dataTables.responsive.js"></script>
        <script src="http://dir.elink.corp/public/js/select2.full.js"></script>
        <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
        <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
        <script type="text/javascript" src="src/js/jquery.mask.js"></script>
        <!-- Modal -->
        <script type="text/javascript">
           
            $(document).on('click', '.removeRow', function() {
                var parent = $(this).parent().parent().parent();
                $(this).parent().parent().remove();
            });

            $('#btn_new_order').click(function(){

                var html_orders = '<div class="col-md-12 no-padding">';
                html_orders += '<div class="col-md-2">';
                html_orders +=  '<div class="form-group">';
                html_orders +=      '<input type="number" class="form-control" id="quantity" name="quantity[]" placeholder="Quanitty" required="" min="1"/>';
                html_orders +=  '</div>';
                html_orders += '</div>';

                html_orders += '<div class="col-md-4 no-padding">';
                html_orders +=  '<div class="form-group">';
                html_orders +=      '<input type="text" class="form-control" id="description" name="description[]" placeholder="Description"required=""/>';
                html_orders +=  '</div>';
                html_orders += '</div>';
                
                html_orders += '<div class="col-md-3">';
                html_orders +=  '<div class="form-group">';
                html_orders +=      '<input type="number" class="form-control" id="price" name="price[]" placeholder="Price" required="" min="1" step=".01"/>';
                html_orders +=  '</div>';
                html_orders += '</div>';

                html_orders += '<div class="col-md-2">';
                html_orders +=  '<div class="form-group">';
                html_orders +=      '<input type="number" class="form-control" id="discount" name="discount[]" placeholder="Discount"  min="0" step=".01"/>';
                html_orders +=  '</div>';
                html_orders += '</div>';



                html_orders += '<div class="col-md-1" style="margin-top: 10px">';
                html_orders += '<a class="removeRow"><span class="fa fa-close"></span></a>';
                html_orders += '</div>';
                html_orders += "</div>";

                $('#orders_box').append(html_orders);
            });

            $('.datepicker').daterangepicker({
                singleDatePicker: true,
                autoUpdateInput: false
            });

            $('.datepicker').on('apply.daterangepicker', function(ev, picker) {
                console.log(ev);
                console.log(picker);
                $(this).val(picker.startDate.format('M/D/Y'));
            });
            $('input[type="checkbox"].check').click(function() {
                $('input[type="checkbox"].check').not(this).prop('checked', false);
            });

            $('#payment_option_installment').change(function(){
                if(this.checked){
                    $('#installment_div').show();
                } else {
                    $('#installment_div').hide();
                    $('#payment_installments').empty();
                    $('#payment_surcharges').empty();
                }
            });

            $('#payment_option_full').change(function(){
                if(this.checked){
                    $('#installment_div').hide();
                    $('#payment_installments').empty();
                    $('#payment_surcharges').empty();
                }
            });

            $('#submit_number_of_payments').click(function(){
                var number_of_payments = parseInt($('#number_of_payments').val());
                $('#payment_installments').empty();
                $('#payment_surcharges').empty();

                // if(number_of_payments > 4){
                //     alert("Maximum number of payments is limited to 4 only.");
                // } else 
                
                if(number_of_payments == 0){
                    alert('Please input valid number.');
                } else {
                    
                    for(var i = 0; i < number_of_payments; i++){
                        $('#payment_installments').append('<div class="form-group">');
                        $('#payment_installments').append('<label>Payment ' + (i + 1) +'<label>');
                        $('#payment_installments').append('<input type="text" class="form-control" name="installment[]" required>');
                        $('#payment_installments').append('</div>');
                    }

                    $('#payment_surcharges').append('<div class="form-group">');
                    $('#payment_surcharges').append('<label>Surcharge<label>');
                    $('#payment_surcharges').append('<input type="text" name="surcharge" class="form-control">');
                    $('#payment_surcharges').append('</div>');
                }

            });
            $(document).ready(function(){
                $('#installment_div').hide();
            });

            $("#invoice_form").submit(function(){
                
                var full = $('#payment_option_full:checkbox:checked').length > 0;

                if (!(($('#payment_option_full:checkbox:checked').length > 0) || ($('#payment_option_installment:checkbox:checked').length > 0))){
                    alert("Please check at least one payment option");
                    return false;
                }
                
                var total = calculateTotal();
                var installmentTotal = calculateTotalInstallment();

                if(total == installmentTotal || full){
                    
                } else {
                    var proceed = confirm("Service order total amount and installment total amount are not the same. Do you still want to proceed ?");
                    if(proceed == true){

                    }else {
                        return false;
                    }
                }
            });


            $(document).on('keyup','input[name=quantity\\[\\]]', function(){
                var total = calculateTotal();
                $('#total_amount').html("$" +total.toFixed(2));
            });
            $(document).on('keyup','input[name=price\\[\\]]', function(){
                var total = calculateTotal();
                $('#total_amount').html("$" +total.toFixed(2));
            });
            $(document).on('keyup','input[name=discount\\[\\]]', function(){
                var total = calculateTotal();
                $('#total_amount').html("$" +total.toFixed(2));
            });

            function calculateTotal(){
                var quantity = new Array();
                var price = new Array();
                var discount = new Array();
                var total = 0.0;

                $('input[name=quantity\\[\\]]').each(function(index, element){

                    var q = parseFloat($(this).val());
                    if($.isNumeric(q)){
                        quantity[index] = q;
                    } else {
                        quantity[index] = 0;
                    }
                    
                });

                $('input[name=price\\[\\]]').each(function(index, element){

                    var p = parseFloat($(this).val());
                    if($.isNumeric(p)){
                        price[index] = p;
                    } else {
                        price[index] = 0;
                    }
                    
                });

                $('input[name=discount\\[\\]]').each(function(index, element){

                    var d = parseFloat($(this).val());
                    if($.isNumeric(d)){
                        total += (quantity[index] * price[index]) - parseFloat($(this).val());
                    } else {
                        if(quantity[index] != 0 && price[index] != 0){
                            total += (quantity[index] * price[index]);
                        } 
                   }
                });
                return total;
            }

            $(document).on('keyup', 'input[name=installment\\[\\]]', function(){
                var total = calculateTotalInstallment();
                displayTotalInstallment(total);
            });
            $(document).on('keyup', 'input[name=surcharge]', function(){
                var total = calculateTotalInstallment();
                displayTotalInstallment(total);
            });

            function displayTotalInstallment(total){
                if($.isNumeric(total)){
                    $('#installment_total_div').html('<div class="col-md-12"><br><b id="installment_total">Total: $' + total.toFixed(2) + '</b></div>');
                } else {
                    $('#installment_total_div').html('');
                }
            }
            function calculateTotalInstallment(){
                var total = 0.0;
                $('input[name=installment\\[\\]]').each(function(index, element){
                    
                    var installment = parseFloat($(this).val());
                    if($.isNumeric(installment)){
                        total += parseFloat($(this).val());
                    } 
                });
                var surcharge = parseFloat($('input[name=surcharge]').val());
                if($.isNumeric(surcharge)){
                    total += surcharge;
                }
                return total;
            }


        </script>
        <div style="min-height: 95vh;"><br>&nbsp;<br>&nbsp;<br>&nbsp;<br>&nbsp;</div>
        <center>
            <small style="color: #999;font-weight: 500;">Copyright 2018 eLink Systems &amp; Concepts Corp.</small>
        </center>
        <br>  
    </div>  
    </body>
</html>