<template>
    <el-dialog :visible.sync="isVisible" :title="$t('dialog.component.title')" width="500px" class="componentDesc">
        <div slot="title">
            <div v-html="(tmpComponent) ? tmpComponent.name : $t('dialog.component.default_name')" style="font-weight:bold;font-size:1.5em;padding-bottom:5px;"></div>
            <div v-if="componentInfos && componentInfos.summary" v-html="componentInfos.summary" style=""></div>
        </div>

        <div style="margin-bottom:10px;" v-if="componentInfos && componentInfos.group">
            <strong>Category : </strong>
            <span v-html="componentInfos.group"></span>
            <hr />
        </div>
    
        <div style="margin-bottom:10px;" v-if="componentInfos && componentInfos.description">
            <strong>{{ $t('dialog.component.description') }} </strong>
            <el-button type="text" size="mini" @click="isDescription = !isDescription" v-html="(isDescription) ? $t('label.hide') :  $t('label.show')"></el-button>
            <div v-if="isDescription" v-html="componentInfos.description"></div>
            <hr />
        </div>
    
        <div style="margin-bottom:10px;" v-if="componentInfos && componentInfos.return_description">
            <strong>{{ $t('dialog.component.return') }} </strong>
            <el-button type="text" size="mini" @click="isReturn = !isReturn" v-html="(isReturn) ? $t('label.hide') :  $t('label.show')"></el-button>
            <div v-if="isReturn" v-html="componentInfos.return_description"></div>
            <hr />
        </div>
    
        <el-form label-position="top" ref="form" :model="tmpComponent" label-width="120px" v-if="(tmpComponent.inputs)" @submit.native.prevent>
    
            <div v-if="tmpComponent.inputs && (Object.keys(tmpComponent.inputs).length > 0) && (tmpComponent.type != 'variable')">
                <h4>{{ $t('dialog.component.parameters') }}</h4>
                <el-tabs tab-position="left">
                    <el-tab-pane :label="input.label" v-for="(input,indexInput) in tmpComponent.inputs" :key="indexInput" v-if="(input.type != 'efct')" style="padding:0 30px;">
                        <div class="d-flex">
                            <div class="">
                                <small v-if="componentInfos && componentInfos.inputs && componentInfos.inputs.hasOwnProperty(indexInput)" style="line-height:20px;" v-html="componentInfos.inputs[indexInput].description"></small>
                                <div  v-if="isFPPremium" v-show="!input.types || (input.types.length <= 1)" style="margin-top:10px;">
                                    <span style="display:block;font-weight:bold;"><small>({{ input.type }})</small></span>
                                </div>
                                <div  v-if="isFPPremium" v-show="input.types && (input.types.length > 1)" style="margin-top:10px;">
                                    <el-select v-show="input.types && (input.types.length > 1)" placeholder="Select" size="mini" v-if="input.types" v-model="input.type">
                                        <el-option v-for="item in input.types" :key="item" :label="item" :value="item"></el-option>
                                    </el-select>
                                </div>
                            </div>
                        </div>
                        <div style="margin-top:10px;" v-if="isFPPremium">
                            <el-input-number v-model="input.value" v-if="(input.type == 'int')"></el-input-number>
                            <el-input-number v-model="input.value" v-if="(input.type == 'float')" :precision="4" :step="0.1"></el-input-number>
                            <el-input v-model="input.value" v-if="(input.type == 'string')"></el-input>
                            <el-input v-model="input.value" v-if="(input.type == 'callable')"></el-input>
                            <el-switch v-model="input.value" v-if="(input.type == 'bool')"></el-switch>
                            <el-input v-model="input.value" v-if="((input.type != 'bool') && (input.type == 'string') && (input.type == 'int'))"></el-input>
                            <fpmarray v-model="input.value" v-if="((input.type == 'array') || input.type == 'object')"></fpmarray>
                            <span v-if="(input.type == 'mixed')">{{ $t('dialog.component.value_must_dynamic') }}</span>
                        </div>
                    </el-tab-pane>
                </el-tabs>
            </div>
    
        </el-form>
        <span slot="footer" class="dialog-footer">
                <el-button @click="close">{{ $t('buttons.close') }}</el-button>
                <el-button type="primary" @click="updateData()">{{ $t('buttons.insert') }}</el-button>
            </span>
    </el-dialog>
</template>

<script>
    import {
        EventBus
    } from '../../event';
    import fpmarray from '../fields/marray';
    import store from '../../Store'
    
    export default {
        name: 'componentDesc',
        props: ['componentId', 'hookType'],
        components: {
            fpmarray: fpmarray,
        },
        data() {
            return {
                tmpComponent: {},
                componentInfos: {},
                isVisible: false,
                isReturn: true,
                isDescription: true,
                isParameters: true,
            }
        },
        created() {
            EventBus.$on('edit-component-template', this.initDialog);
        },
        beforeDestroy(){
            EventBus.$off('edit-component-template', this.initDialog);
        },
        methods: {
            initDialog(component){
                this.isVisible = true;
                this.tmpComponent = JSON.parse(JSON.stringify(component));
                store.load_component_infos(this.tmpComponent)
                    .then(component => {
                        this.componentInfos = component;
                        this.$forceUpdate()
                    })
            },
            updateData() {
                var args = [];
                var txt;
                if(this.isFPPremium){
                    for(var ki in this.tmpComponent.inputs){
                        if(this.tmpComponent.inputs[ki].value == undefined){
                            args.push('false');
                        }
                        else{
                            switch(this.tmpComponent.inputs[ki].type){
                                case 'int':
                                    args.push(this.tmpComponent.inputs[ki].value)
                                break;
                                case 'float':
                                    args.push(this.tmpComponent.inputs[ki].value)
                                break;
                                case 'string':
                                    args.push("'"+this.tmpComponent.inputs[ki].value+"'")
                                break;
                                case 'callable':
                                    args.push("'"+this.tmpComponent.inputs[ki].value+"'")
                                break;
                                case 'bool':
                                    args.push(this.tmpComponent.inputs[ki].value)
                                break;
                                case 'array':
                                    var targs = '{}';
                                    if(!this.tmpComponent.inputs[ki].value || Object.keys(this.tmpComponent.inputs[ki].value).length > 0){
                                        targs = JSON.stringify(this.tmpComponent.inputs[ki].value);
                                    }
                                    args.push(targs)
                                break;
                                default:
                                    args.push("'"+this.tmpComponent.inputs[ki].value+"'")
                                break;
                            }
                        }
                    }
                    if(args.length > 0){
                        txt = this.tmpComponent.name+"("+args.join(' , ')+")";
                    }
                    else{
                        txt = this.tmpComponent.name+"()";
                    }
                }
                else{
                    args = Object.keys(this.tmpComponent.inputs);
                    if (args.length > 0) {
                        txt = "fp_function('" + this.tmpComponent.name + "', [" + args.join(' , ') + "])";
                    } else {
                        txt = "fp_function('" + this.tmpComponent.name + "')";
                    }
                }
                
                EventBus.$emit('set-component-template', txt);
                this.close();
            },
            close() {
                this.isVisible = this.tmpComponent = this.tmpId = false;
            }
        }
    }
</script>

<style>
    .componentDesc .el-dialog__body {
        padding-top: 20px;
    }
    
    pre {
        overflow: scroll;
    }
    
    .el-dialog {
        max-width: 550px;
    }
</style>
