<template>
    <el-dialog :visible.sync="isVisible" :title="(rename) ? $t('dialog.fileInfo.title.edit',{file_type:type}): $t('dialog.fileInfo.title.create',{file_type:type})" width="500px" :close-on-click-modal="false">
    
        <el-form v-if="['data','template'].includes(type)" :model="tmpFile" :rules="rules" label-position="left" ref="tmpFile" @submit.native.prevent>
            <el-form-item prop="title">
                <el-input :placeholder="$t('dialog.fileInfo.descriptions.'+type)" v-model="tmpFile.title">
                </el-input>
            </el-form-item>
        </el-form>
    
        <el-form v-if="['shortcode','function'].includes(type)" :model="tmpFile" :rules="rules" label-position="left" ref="tmpFile" @submit.native.prevent>
            <el-form-item prop="title">
                <el-input :placeholder="$t('dialog.fileInfo.descriptions.'+type)" v-model="tmpFile.title">
                </el-input>
            </el-form-item>
        </el-form>
    
        <el-form v-loading="isLoading" v-if="['action','filter','function'].includes(type)" :model="tmpFile" :rules="rules" label-position="left" ref="tmpFile" @submit.native.prevent>
    
            <el-form-item>
                
                <el-select ref="hookPicker" v-model="tmpFile" autofocus filterable clearable allow-create default-first-option remote :remote-method="remoteMethod" value-key="name" :placeholder="$t('dialog.fileInfo.descriptions.'+type)" style="width:100%;">
                    <el-option v-for="item in options5" :key="item.name" :label="item.name" :value="item">
                        <div style="width:420px;">
                            <div style="">{{ item.name }}</div>
                            <div style="color: #8492a6; font-size: 13px">{{ item.summary }}</div>
                        </div>
                    </el-option>
                </el-select>
    
            </el-form-item>
    
            <p v-html="tmpFile.summary"></p>
    
            <div v-html="tmpFile.description"></div>
    
            <template v-if="tmpFile && tmpFile.inputs && (Object.keys(tmpFile.inputs).length > 0) && (tmpFile.type != 'variable')">
                        
                <el-form-item 
                    :label="input.label" 
                    :prop="input.value" 
                    v-for="(input,indexInput) in tmpFile.inputs" 
                    :key="indexInput" 
                    v-if="(input.type != 'efct')">
                    <el-input 
                        :placeholder="input.label" 
                        v-model="input.value" 
                        v-if="(input.type == 'string')">
                    </el-input>
                </el-form-item>
                
            </template>

            <div v-if="isCustom || ['function'].includes(type)">
                <div 
                    class="d-flex" 
                    style="margin-bottom:10px;">
                    <div class="flex-full">
                        <h4 style="margin:0;">
                            {{ $t('dialog.component.arguments') }}
                        </h4>
                    </div>
                    <div 
                        class="flex-full" 
                        style="text-align:right;">
                        <el-button 
                            @click="addArgument()" 
                            size="mini">{{ $t('buttons.add_item') }}
                        </el-button>
                    </div>
                </div>
                <div 
                    class="d-flex" 
                    v-for="(output,indexOutput) in customOutputs" 
                    :key="indexOutput" 
                    v-if="(output.type != 'efct')">
                    <div class="flex-full">
                        <el-input 
                            size="mini" 
                            placeholder="Name" 
                            v-model="output.label">
                        </el-input>
                    </div>
                    <div class="">
                        <el-select 
                            size="mini" 
                            v-model="output.type" 
                            placeholder="Select" 
                            style="width:100px;">
                            <el-option
                                v-for="item in ['int','string','callable','bool','array','object','float','mixed']"
                                :key="item"
                                :label="item"
                                :value="item">
                            </el-option>
                        </el-select>
                    </div>
                    <div class="">
                        <el-button 
                            size="mini" 
                            @click="deleteArgument(indexOutput)">
                                {{ $t('buttons.delete') }}
                            </el-button>
                    </div>
                </div>
            </div>
        </el-form>
        <span 
            slot="footer" 
            class="dialog-footer">
            <el-button @click="closeDialog()">
                {{ $t('buttons.cancel') }}
            </el-button>
            <el-button 
                type="primary" 
                @click="setInfos()"
                v-html="(rename) ? $t('dialog.fileInfo.buttons.edit',{file_type:type}): $t('dialog.fileInfo.buttons.create',{file_type:type})">
            </el-button>
        </span>
    </el-dialog>
</template>

<script>
    import {
        EventBus
    } from '../../event';
    import store from '../../Store';
    
    export default {
        name: 'hookPicker',
        data() {
            return {
                isVisible: false,
                currentFile: {},
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
                tmpFile: {},
                type: false,
                fileType: false,
                rename: false,
                customOutputs: {},
                isLoading: false
            }
        },
        created() {
            var self = this;
            EventBus.$on('add-file', this.addFile);
            EventBus.$on('rename-file', this.renameFile);
        },
        beforeDestroy() {
            EventBus.$off('add-file', this.addFile);
            EventBus.$off('rename-file', this.renameFile);
        },
        methods: {
            str_replace($f, $r, $s) {
                return $s.replace(new RegExp("(" + $f.map(function(i) {
                    return i.replace(/[.?*+^$[\]\\(){}|-]/g, "\\$&")
                }).join("|") + ")", "g"), function(s) {
                    return $r[$f.indexOf(s)]
                });
            },
            setInfos() {
                this.$refs['tmpFile'].validate((valid) => {
    
                    if (valid) {
                        if (['filter', 'action'].includes(this.type)) {
                            if (this.isCustom) {
                                var tmpFileSave = {};
                                tmpFileSave.title = this.tmpFile;
                                tmpFileSave.inputs = [];
                                tmpFileSave.type = 'hook-' + this.type;
                                tmpFileSave.outputs = Object.assign({
                                    ofct: {
                                        type: 'efct'
                                    }
                                }, JSON.parse(JSON.stringify(this.customOutputs)));
                            } else {
                                var tmpFileSave = this.tmpFile;
                                tmpFileSave.title = tmpFileSave.name;
                                delete tmpFileSave.name;
                            }
    
                            var title = '';
                            if (tmpFileSave.inputs && (Object.keys(tmpFileSave.inputs).length > 0)) {
                                var searchParts = [];
                                var replaceParts = [];
                                for (var i in tmpFileSave.inputs) {
                                    searchParts.push('{$' + i + '}');
                                    replaceParts.push(tmpFileSave.inputs[i].value);
                                }
                                title = this.str_replace(searchParts, replaceParts, tmpFileSave.title);
                            } else {
                                title = tmpFileSave.title;
                            }
    
                            for (var i in tmpFileSave.inputs) delete tmpFileSave.inputs[i].description;
                            for (var o in tmpFileSave.outputs) delete tmpFileSave.outputs[o].description;
    
                            if (this.rename) {
                                /*
                                // clean bad old outputs for hook_args (handle auto by linkpath)
                                for (var k in this.currentFile.content.links) {
                                    if ((this.currentFile.content.links[k].fromComponent == 'hook_args') && (this.currentFile.content.links[k].fromConnector != 'ofct')) {
                                        delete this.currentFile.content.links[k];
                                    }
                                }
                                */
                                this.currentFile.title = title;
                                this.currentFile.content.components['hook_args'].outputs = tmpFileSave.outputs;
                                this.currentFile.content.components['hook_args'].inputs = tmpFileSave.inputs;
                                this.currentFile.content.components['hook_args'].name = tmpFileSave.title;
                                this.$forceUpdate();
                            } else {
                                var content = (this.fileType) ? JSON.parse(JSON.stringify(this.fileType.content)) : '';
    
                                var cmpt = {
                                    name: tmpFileSave.title,
                                    inputs: tmpFileSave.inputs,
                                    outputs: tmpFileSave.outputs,
                                    type: tmpFileSave.type,
                                    x: 0,
                                    y: 0
                                }
                                content.components['hook_args'] = cmpt;
                                var uuid = this.$parent.uuid();
                                var obj = {
                                    title: title,
                                    type: this.type,
                                    content: content
                                };
                                this.$set(this.$parent.feature.content, uuid, obj);
                                this.$parent.selectFile(uuid);
                                tmpFileSave = {};
                            }
                        } else if (['shortcode'].includes(this.type)) {
                            var tmpFileSave = this.tmpFile;
                            if (this.rename) {
                                this.currentFile.title = tmpFileSave.title;
                                this.currentFile.content.components['hook_args'].name = tmpFileSave.title;
                            } else {
                                var hook = {
                                    name: tmpFileSave.title,
                                    type: 'hook-shortcode',
                                    x: 0,
                                    y: 0,
                                    inputs: {},
                                    outputs: {
                                        ofct: {
                                            type: 'efct'
                                        },
                                        args: {
                                            label: 'args',
                                            type: 'array'
                                        },
                                        content: {
                                            label: 'content',
                                            type: 'string'
                                        }
                                    }
                                };
                                var content = (this.fileType) ? JSON.parse(JSON.stringify(this.fileType.content)) : '';
                                content.components['hook_args'] = hook;
    
                                var obj = {
                                    title: tmpFileSave.title,
                                    type: this.type,
                                    content: content
                                };
                                var uuid = this.$parent.uuid();
                                this.$set(this.$parent.feature.content, uuid, obj);
                                this.$parent.selectFile(uuid);
                            }
                        } else {
                            var tmpFileSave = this.tmpFile;
                            if (this.rename) {
                                this.currentFile.title = tmpFileSave.title;
                            } else {
                                var content = (this.fileType) ? JSON.parse(JSON.stringify(this.fileType.content)) : '';
                                var uuid = this.$parent.uuid();
                                var obj = {
                                    title: tmpFileSave.title,
                                    type: this.type,
                                    content: content
                                };
                                this.$set(this.$parent.feature.content, uuid, obj);
                                this.$parent.selectFile(uuid);
                            }
                        }
                        this.tmpFile = '';
                        this.tmpFile = {};
                        this.currentFile = {};
                        this.isVisible = false;
                    } else {
                        return false;
                    }
                });
    
            },
            remoteMethod(query) {
                var self = this;
                if (query !== '') {
                    this.loading = true;
                    store.load_components_data(this.type + 's', query, 0)
                        .then(data => {
                            this.options5 = [];
                            for (var k in data.components) {
                                var c = data.components[k];
                                var o = {
                                    value: k,
                                    label: c.title
                                }
                                self.options5.push(c);
                            }
                        })
                } else {
                    this.options5 = [];
                }
            },
            closeDialog(){
                this.isVisible = false;
            },
            addFile(type, fileType) {
                for(var k in this.customOutputs) delete this.customOutputs[k];
                this.type = type;
                this.fileType = fileType;
                this.tmpFile = '';
                this.tmpFile = {};
                this.currentFile = {};
                this.options5 = [];
                this.isVisible = true;
                this.rename = false;
            },
            renameFile(file) {
                for(var k in this.customOutputs) delete this.customOutputs[k];
                var self = this;
                this.type = file.type;
                this.tmpFile = '';
                this.tmpFile = {};
                this.currentFile = file;
                this.rename = true;
                this.options5 = [];
    
                if (['filter', 'action'].includes(this.type)) {
                    this.isLoading = true;
                    store.load_component_infos(file.content.components['hook_args'])
                        .then(component => {
                            if (component) {
                                self.options5 = [component];
                                self.tmpFile = component;
                                self.tmpFile.inputs = JSON.parse(JSON.stringify(file.content.components['hook_args'].inputs));
                            } else {
                                self.tmpFile = file.content.components['hook_args'].name;
                                self.customOutputs = JSON.parse(JSON.stringify(file.content.components['hook_args'].outputs));
                            }
                            this.componentInfos = component;
                            this.isLoading = false;
                            //this.isLoading = false;
                        })
                } else {
                    self.tmpFile = {
                        title: file.title
                    };
                }
    
                this.isVisible = true;
            },
            uuid() {
                function s4() {
                    return Math.floor((1 + Math.random()) * 0x10000).toString(16).substring(1);
                }
                return s4() + '' + s4() + '' + s4() + '' + s4() + '' + s4() + '' + s4() + '' + s4() + '' + s4();
            },
            addArgument() {
                this.$set(this.customOutputs, this.uuid(), {
                    label: '',
                    type: 'mixed'
                })
                this.$forceUpdate();
            },
            deleteArgument(index) {
                this.$delete(this.customOutputs, index);
                this.$forceUpdate();
            },
        },
        computed: {
            isCustom() {
                return (typeof this.tmpFile == 'string') ? true : false;
            },
        },
    }
</script>

<style scoped>
    .el-select-dropdown__item {
        height: auto;
        line-height: 20px;
        word-break: break-all;
        white-space: normal;
    }
</style>
