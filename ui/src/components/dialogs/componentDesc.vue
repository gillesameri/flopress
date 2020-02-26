<template>
    <el-dialog :visible.sync="isVisible" :title="$t('dialog.component.name')" width="500px" class="componentDesc" :class="(canChange) ? 'hide-body' : ''" @close="close()" :close-on-click-modal="false">
        <div slot="title">
            <el-select 
                v-if="canChange" 
                class="fctSearch" 
                ref="fctselect" 
                v-model="searchComponent" 
                autofocus 
                filterable 
                default-first-option 
                remote 
                :remote-method="remoteMethod" 
                value-key="name" 
                :placeholder="$t('dialog.component.search')" 
                style="width:100%;margin-bottom:10px;">
                <el-option v-for="item in options5" :key="item.name" :label="item.name" :value="item">
                    <div style="width:420px;">
                        <div style="">{{ item.name }}</div>
                        <div style="color: #8492a6; font-size: 13px">{{ item.summary }}</div>
                    </div>
                </el-option>
            </el-select>
            <div v-if="!canChange" v-html="(tmpComponent) ? tmpComponent.name : $t('dialog.component.default_name')" style="font-weight:bold;font-size:1.5em;padding-bottom:5px;"></div>
            <div v-if="componentInfos && componentInfos.summary" v-html="componentInfos.summary" style=""></div>
        </div>

        <div v-loading="isLoading">

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
        
            <el-form label-position="top" ref="form" :model="tmpComponent" label-width="120px" @submit.native.prevent>
                
                <div v-if="(tmpComponent.type == 'instruction') && (tmpComponent.name == 'cascader')">
        
                    <div class="d-flex" style="margin-bottom:10px;">
                        <div class="flex-full">
                            <h4>Executable connectors</h4>
                        </div>
                        <div class="flex-full" style="text-align:right;">
                            <el-button type="primary" @click="addCascaderKey()" size="mini">{{ $t('buttons.add_item') }}</el-button>
                        </div>
                    </div>
                    <div class="d-flex" v-for="(output,indexOutput) in tmpComponent.outputs" :key="indexOutput" v-if="(output.type == 'efct')">
                        <div class="flex-full">
                            <el-input size="mini" :placeholder="$t('dialog.component.key')" v-model="output.label"></el-input>
                        </div>
                        <div class="">
                            <el-button size="mini" @click="deleteCascaderKey(indexOutput)">{{ $t('buttons.delete') }}</el-button>
                        </div>
                    </div>
                </div>

                <div v-if="(tmpComponent.type == 'variable') && (tmpComponent.name == 'array')">
        
                    <div class="d-flex" style="margin-bottom:10px;">
                        <div class="flex-full">
                            <h4>{{ $t('dialog.component.array') }}</h4>
                        </div>
                        <div class="flex-full" style="text-align:right;">
                            <el-button type="primary" @click="addArrayKey()" size="mini">{{ $t('buttons.add_item') }}</el-button>
                        </div>
                    </div>
                    <div class="d-flex" v-for="(input,indexInput) in tmpComponent.inputs" :key="indexInput" v-if="(input.type != 'efct')">
                        <div class="flex-full">
                            <el-input size="mini" :placeholder="$t('dialog.component.key')" v-model="input.label"></el-input>
                        </div>
                        <div class="flex-full">
                            <el-input size="mini" :placeholder="$t('dialog.component.value')" v-model="input.value"></el-input>
                        </div>
                        <div class="">
                            <el-button size="mini" @click="deleteArrayKey(indexInput)">{{ $t('buttons.delete') }}</el-button>
                        </div>
                    </div>
                </div>
                <div v-if="(tmpComponent.type == 'variable') && (tmpComponent.name == 'string')">
                    <el-input v-model="tmpComponent.value"></el-input>
                </div>
                <div v-if="(tmpComponent.type == 'variable') && (tmpComponent.name == 'integer')">
                    <el-input-number v-model="tmpComponent.value"></el-input-number>
                </div>
                <div v-if="(tmpComponent.type == 'variable') && (tmpComponent.name == 'float')">
                    <el-input-number v-model="tmpComponent.value" :precision="4" :step="0.1"></el-input-number>
                </div>
                <div v-if="(tmpComponent.type == 'variable') && (tmpComponent.name == 'boolean')">
                    <el-switch v-model="tmpComponent.value"></el-switch>
                </div>
        
                <div v-if="tmpComponent.inputs && (Object.keys(tmpComponent.inputs).length > 0) && (tmpComponent.type != 'variable') && (tmpComponent.name != 'cascader')">
                    <strong>{{ $t('dialog.component.parameters') }}</strong>
                    <el-button type="text" size="mini" @click="isParameters = !isParameters" v-html="(isParameters) ? $t('label.hide') :  $t('label.show')"></el-button>
                    <el-tabs tab-position="left" v-show="isParameters">
                        <el-tab-pane :label="input.label" v-for="(input,indexInput) in tmpComponent.inputs" :key="indexInput" v-if="(input.type != 'efct')" style="padding:0 30px;">
                            <div v-if="isConnectorLinked(indexInput)">{{ $t('dialog.component.value_dynamic') }}</div>
                            <div v-if="!isConnectorLinked(indexInput)">
                                <div class="d-flex">
                                    <div class="">
                                        <small v-if="componentInfos && componentInfos.inputs && componentInfos.inputs.hasOwnProperty(indexInput)" style="line-height:20px;" v-html="componentInfos.inputs[indexInput].description"></small>
                                        <div v-show="!input.types || (input.types.length <= 1)" style="margin-top:10px;">
                                            <span style="display:block;font-weight:bold;"><small>({{ input.type }})</small></span>
                                        </div>
                                        <div v-show="input.types && (input.types.length > 1)" style="margin-top:10px;">
                                            <el-select v-show="input.types && (input.types.length > 1)" placeholder="Select" size="mini" v-if="input.types" v-model="input.type">
                                                <el-option v-for="item in input.types" :key="item" :label="item" :value="item"></el-option>
                                            </el-select>
                                        </div>
                                    </div>
                                </div>
                                <div style="margin-top:10px;">
                                    <el-input-number v-model="input.value" v-if="(input.type == 'int')"></el-input-number>
                                    <el-input-number v-model="input.value" v-if="(input.type == 'float')" :precision="4" :step="0.1"></el-input-number>
                                    <el-input v-model="input.value" v-if="(input.type == 'string')"></el-input>
                                    <el-input v-model="input.value" v-if="(input.type == 'callable')"></el-input>
                                    <el-switch v-model="input.value" v-if="(input.type == 'bool')"></el-switch>
                                    <el-input v-model="input.value" v-if="((input.type != 'bool') && (input.type == 'string') && (input.type == 'int'))"></el-input>
                                    <fpmarray v-model="input.value" v-if="((input.type == 'array') || input.type == 'object')"></fpmarray>
                                    <span v-if="(input.type == 'mixed')">{{ $t('dialog.component.value_must_dynamic') }}</span>
                                </div>
                            </div>
                        </el-tab-pane>
                    </el-tabs>
                </div>
        
            </el-form>
        </div>
        
        <span v-if="!canChange" slot="footer" class="dialog-footer">
            <el-button @click="close">{{ $t('buttons.close') }}</el-button>
            <el-button v-if="(typeof tmpComponent === 'object') && (tmpComponent.hasOwnProperty('name'))" type="primary" @click="updateData()" v-html="(tmpId) ? $t('buttons.update') : $t('buttons.create')"></el-button>
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
                canChange: false,
                tmpComponent: {},
                searchComponent:{},
                tmpId: false,
                componentInfos: {},
                isVisible: false,
                isReturn: true,
                isDescription: true,
                isParameters: true,
                isLoading: false,
                rules: {
                    title: [{
                            required: true,
                            pattern: /^[a-zA-Z0-9_]*$/,
                            message: this.$t('file.rules.description'),
                            trigger: 'blur'
                        },
    
                    ],
                },
                options5: [],
            }
        },
        created() {
            EventBus.$on('edit-component', this.initDialog);
        },
        beforeDestroy() {
            EventBus.$off('edit-component', this.initDialog);
        },
        watch:{
            searchComponent(val){
                if(val && (typeof val === 'object') && val.hasOwnProperty('name')){
                    this.isLoading = true;
                    this.tmpComponent.name = val.name;
                    this.tmpComponent.type = val.type;
                    this.tmpComponent.inputs = val.inputs;
                    this.tmpComponent.outputs = val.outputs;
                    this.isLoading = false;
                    this.updateData();
                }
            }
        },
        methods: {
            initDialog(component, id) {
                if((typeof component === 'object') && (component.hasOwnProperty('name'))){
                    this.canChange = false;
                    this.componentInfos = {};
                    this.isVisible = true;
                    this.tmpComponent = JSON.parse(JSON.stringify(component));
                    this.tmpId = id;
                    this.isLoading = true;
                    store.load_component_infos(this.tmpComponent)
                    .then(component => {
                        this.componentInfos = component;
                        this.isLoading = false;
                        this.$forceUpdate()
                    })
                }
                else{
                    this.canChange = true;
                    this.options5 = [];
                    this.componentInfos = {};
                    this.isVisible = true;
                    this.tmpComponent = JSON.parse(JSON.stringify(component));
                    this.tmpId = false;
                    this.isLoading = false;
                    this.$nextTick(function(){
                        this.$refs['fctselect'].focus();
                    })
                }
            },
            remoteMethod(query) {
                var self = this;
                if (query !== '') {
                    this.loading = true;
                    store.load_components_data('functions', query)
                        .then(data => {
                            this.options5 = [];
                            for (var k in data.components) {
                                var c = data.components[k];
                                var o = {
                                    value: k,
                                    label: c.name
                                }
                                self.options5.push(c);
                            }
                        })
                } else {
                    this.options5 = [];
                }
            },
            addArrayKey() {
                this.$set(this.tmpComponent.inputs, this.uuid(), {
                    label: '',
                    type: 'mixed'
                })
                this.$forceUpdate();
            },
            deleteArrayKey(index) {
                this.$delete(this.tmpComponent.inputs, index);
                this.$forceUpdate();
            },
            addArgument() {
                this.$set(this.tmpComponent.outputs, this.uuid(), {
                    label: '',
                    type: 'mixed'
                })
                this.$forceUpdate();
            },
            deleteArgument(index) {
                this.$delete(this.tmpComponent.outputs, index);
                this.$forceUpdate();
            },
            addCascaderKey() {
                this.$set(this.tmpComponent.outputs, this.uuid(), {
                    label: '',
                    type: 'efct'
                })
                this.$forceUpdate();
            },
            deleteCascaderKey(index) {
                this.$delete(this.tmpComponent.outputs, index);
                this.$forceUpdate();
            },
            uuid() {
                function s4() {
                    return Math.floor((1 + Math.random()) * 0x10000).toString(16).substring(1);
                }
                return s4() + '' + s4() + '' + s4() + '' + s4() + '' + s4() + '' + s4() + '' + s4() + '' + s4();
            },
            isConnectorLinked() {
                return false;
            },
            updateData() {
                EventBus.$emit('set-component', this.tmpId, JSON.parse(JSON.stringify(this.tmpComponent)));
                this.close();
            },
            close() {
                this.isVisible = this.tmpId = false;
                this.tmpComponent = {};
                this.searchComponent = {};
            }
        }
    }
</script>

<style>
    .componentDesc {}
    
    .componentDesc .el-dialog__body {
        padding-top: 20px;
    }

    .componentDesc.hide-body .el-dialog__body {
        display:none;
    }
    
    pre {
        overflow: scroll;
    }

    .fctSearch{
        
    }

    .fctSearch input.el-input__inner{
        background: transparent !important;
        border: none !important;
        color: white;
        font-weight:bold;
        font-size:1.5em;
        padding: 0;
        -shadow: none;
    }
    .el-select-dropdown__item {
        height: auto;
        line-height: 20px;
        word-break: break-all;
        white-space: normal;
    }
</style>
