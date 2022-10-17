@extends('layouts.main')
@section('title')
Employees
@endsection
@section('pagetitle')
Employees
@endsection
@section('content')
<style type="text/css">
    .emp-profile{
        background-color: white;
    }
    .col-md-12{
        margin-bottom: 1px !important;
    }
    .emp-profile{
        margin: auto;
    }
    .header-container{
        margin-top: 20px;
    }
    #search_employee{
        padding-left: 5px;
    }
    .alphabet-search{
        display: inline-flex;
        list-style: none;
    }
    .alphabet-search li{
        margin-left: 10px;
    }
    .header-list{

    }
    .employee-description{
        color: #777;
        font-size: 12px;
    }
    h1, h2, h3, h4, h5, h6 {
        color: #777;
    }
    li a.selected{
        font-weight: 900!important;
    }
    .pagination>li:first-child>a, .pagination>li:first-child>span{
        border-top-left-radius: 0px !important;
        border-bottom-left-radius: 0px !important;
    }
    .pagination>li:last-child>a, .pagination>li:last-child>span {
        border-top-right-radius: 0px !important;
        border-bottom-right-radius: 0px !important;
    }
    .emp-profile .fa{
        color: #555;
    }
    select{
        cursor: pointer !important;
    }
</style>

<div class="container-fluid">
    <div class="alert alert-success" role="alert">
        Information Update successfully sent for approval. You may coordinate with your Supervior and the HR.
    </div>
</div>

@endsection 