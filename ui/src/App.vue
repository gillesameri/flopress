<template>
    <div class="flopress-wrap full-absolute" v-loading="loading">
        <template>
            <div id="uploadZone"></div>
            <router-view/>
            <feature-infos />
            <codex-picker />
        </template>
        <premium-dialog />
    </div>
</template>

<script>
import store from './Store'
import { EventBus } from './event';
import featureInfos from './components/dialogs/featureInfos'
import codexPicker from './components/dialogs/codexPicker'
import premiumDialog from './components/dialogs/premiumDialog'

export default {
    components: {
        featureInfos,
        codexPicker,
        premiumDialog
    },
    data: function () {
        return {
            
        }
    },
    computed: {
        loading:{
            get: function () {
                false
            },
            set: function (newValue) {
                return newValue;
            }
        },
        isHacked() {
            return (this.isFPPremium === undefined) ? true : false;
        }
    },
}
</script>

<style>

    .flex-full{
        flex:1;
    }
    
    #wpcontent{

        position: absolute !important;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
    }
    #wpbody{
        /*height:800px;*/
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0px;
    }
    #wpfooter{
        display:none;
    }

    .flopress-wrap{
        border-bottom:1px solid #ccc;
        background:#f1f1f1;
    }

    .el-dialog__body{
        padding:20px;
    }

    /*::-webkit-scrollbar-button{ display: block; height: 13px; border-radius: 0px; background-color: #AAA; } 
    ::-webkit-scrollbar-button:hover{ background-color: #AAA; } */
    ::-webkit-scrollbar-thumb{ background-color: #CCC; } 
    ::-webkit-scrollbar-thumb:hover{ background-color: #409eff; } 
    ::-webkit-scrollbar-track{ background-color: #efefef; } 
    ::-webkit-scrollbar-track:hover{ background-color: #efefef; } 
    ::-webkit-scrollbar{ width: 5px; height:5px; }

    .menu{
        margin-bottom:20px;
    }

    .el-form-item__content small{
        line-height:inherit;
    }

    .full-absolute{
        position:absolute;
        top:0;
        left:0;
        right:0;
        bottom:0;
    }

    .p-relative{
        position:relative;
    }

    .bg-white{
        background:white;
    }

    .color-white{
        color:white;
    }

    .padding-10{
        padding:10px;
    }

    .padding-20{
        padding:20px;
    }

    .padding-v-10{
        padding-top:10px;
        padding-bottom:10px;
    }

    .padding-v-20{
        padding-top:20px;
        padding-bottom:20px;
    }

    .padding-h-10{
        padding-left:10px;
        padding-right:10px;
    }

    .padding-h-20{
        padding-left:20px;
        padding-right:20px;
    }

    .margin-5{
        margin:5px;
    }

    .margin-10{
        margin:10px;
    }

    .text-left{
        text-align:left;
    }

    .text-center{
        text-align:center;
    }

    .text-right{
        text-align:right;
    }

    .d-inline-block{
        display:inline-block;
    }

    .d-block{
        display:block;
    }

    .overflow-auto{
        overflow:auto;
    }

    .overflow-hidden{
        overflow:hidden;
    }

    .overflow-y-scroll{
        overflow-y:scroll;
    }

    .border-none{
        border:none;
    }

    .cursor-pointer{
        cursor:pointer;
    }

    .el-select input.el-input__inner{
        background:white !important;
    }

    .el-dialog__header{
        background: #409eff;
        color: white;
    }
    .el-dialog__close.el-icon-close{
        color:white;
    }

    .el-dialog__title{
        font-weight:bold;
        color:white;
    }

    .el-dialog__footer{
        background:#f5f7fa;
        padding:15px;
    }

    .el-dialog__wrapper{
        background:rgba(0,0,0,0.75);
    }

    .grid-bg{
    }
    .el-form-item__content{
        line-height: inherit !important;
    }
    
    .flopress-wrap .el-select__input {
        box-shadow: none !important;
        border:none !important;
        background:transparent;
    }
</style>
