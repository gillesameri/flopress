<template>
    <div class="d-flex flex-column full-absolute">
        <div class="d-flex align-center padding-10 bg-white" style="box-shadow: 0 2px 4px 0 rgba(0,0,0,.12), 0 0 6px 0 rgba(0,0,0,.04);z-index:2;">
            <div class="flex-full">
                <img src="../../assets/logo.png" style="max-height:20px;" />
            </div>
            <div class="flex-full text-center">
    
            </div>
            <div class="flex-full">
                <div style="display:inline-block;float:right;">
                    <el-button @click="backToList()" size="mini" icon="far fa-arrow-alt-circle-left"> {{ $t('buttons.back') }} </el-button>
                    <el-button @click="window.open($t('help.url'))" size="mini" type="default" icon="fas fa-question-circle"> {{ $t('label.documentation') }}</el-button>
                </div>
                <div style="clear:both"></div>
            </div>
        </div>
        <div class="flex-full d-flex flex-column" v-loading="isLoading">
            <div class="d-flex p-relative color-white padding-h-20" style="background:#409eff;">
                <div class="flex-full">
                    <h4 style="display:inline-block;">{{feature.title}}</h4>
                    <el-dropdown style="position:absolute;left:230px;z-index:999;bottom:-20px;">
                        <div style="color:white;cursor:pointer;margin-right:0px;">
                            <el-button type="success" size="mini" class="btn-icon" circle @click="addFctOverlay = true"><span class="fas fa-plus"></span></el-button>
                        </div>
                        <el-dropdown-menu slot="dropdown">
                            <el-dropdown-item v-for="(fileType, typeIndex) in fileTypes" :key="typeIndex" @click.native="addFile(typeIndex)">
                                {{ fileType.title }}
                            </el-dropdown-item>
                        </el-dropdown-menu>
                    </el-dropdown>
                </div>
                <div class="p-relative" style="padding:7px 0;">
                    <el-tooltip v-show="feature.status == 'inactive'" class="item" effect="dark" :content="$t('buttons.activate')" placement="bottom">
                        <el-button circle type="primary" @click="activateFeature(feature)"><i class="fas fa-play"></i></el-button>
                    </el-tooltip>
                    <el-tooltip v-show="feature.status == 'active'" class="item" effect="dark" :content="$t('buttons.deactivate')" placement="bottom">
                        <el-button circle type="primary" @click="deactivateFeature(feature)"><i class="fas fa-stop"></i></el-button>
                    </el-tooltip>
                    <el-tooltip class="item" effect="dark" :content="$t('buttons.settings')" placement="bottom">
                        <el-button circle type="primary" @click="settingsFeature(feature)"><i class="fas fa-cog"></i></el-button>
                    </el-tooltip>
                    <el-tooltip class="item" effect="dark" :content="$t('buttons.save')" placement="bottom">
                        <el-button @click="saveFeature()" size="mini" circle type="primary"><i class="fas fa-save" style="font-size:1.3em;"></i></el-button>
                    </el-tooltip>
                    <el-dropdown trigger="click" @command="featureCommand" :feature="feature">
                        <el-button size="mini" type="primary" circle icon="fas fa-ellipsis-h"></el-button>
                        <el-dropdown-menu slot="dropdown">
                            <el-dropdown-item command="delete"><i class="fas fa-trash-alt"></i>&nbsp;{{ $t('buttons.delete') }}</el-dropdown-item>
                            <el-dropdown-item command="export"><i class="far fa-arrow-alt-circle-down"></i>&nbsp;{{ $t('buttons.export') }}</el-dropdown-item>
                        </el-dropdown-menu>
                    </el-dropdown>
                </div>
            </div>
    
            <div class="d-flex flex-full bg-white" v-if="feature">
                <div class="d-flex flex-column" style="border-right:1px solid #eeeeee;">
                    <div class="d-flex flex-column flex-full overflow-y-scroll">
                        <div style="width:280px;">
                            <div class="" v-for="(fileType, typeIndex) in fileTypes" :key="typeIndex">
                                <div class="filesMenu" v-if="isVisible(typeIndex)">
                                    <div style="background:#fbfbfb;border-bottom:1px solid #eeeeee;border-top:1px solid #eeeeee;padding:8px 10px;">
                                        <span :class="fileType.icon"></span> {{fileType.title}}
                                    </div>
                                    <div style="padding:0 0 10px 0;">
                                        <div v-for="(file, index) in feature.content" :key="index" @click="selectFile(index)" v-show="(file.type == typeIndex)" class="link">
                                            <div @click.stop class="submenu">
                                                <el-dropdown>
                                                    <div class="cursor-pointer" style="margin-right:10px;">
                                                        <span class="el-dropdown-link">
                                                            <i class="fas fa-ellipsis-h"></i>
                                                        </span>
                                                    </div>
                                                    <el-dropdown-menu slot="dropdown">
                                                        <el-dropdown-item @click.native="renameFile(file)">{{ (['action','filter','shortcode'].includes(file.type)) ? 'Change '+file.type : $t('buttons.rename') }}</el-dropdown-item>
                                                        <el-dropdown-item @click.native="copyFile(file)">{{ $t('buttons.copy') }}</el-dropdown-item>
                                                        <el-dropdown-item @click.native="deleteFile(file)">{{ $t('buttons.delete') }}</el-dropdown-item>
                                                        <el-dropdown-item @click.native="enableFile(file)" divided v-if="(['action','filter','shortcode'].includes(file.type)) && (!file.content.autoload)">{{ $t('buttons.autoload_on') }}</el-dropdown-item>
                                                        <el-dropdown-item @click.native="disableFile(file)" divided v-if="(['action','filter','shortcode'].includes(file.type)) && (file.content.autoload)">{{ $t('buttons.autoload_off') }}</el-dropdown-item>
                                                        <el-dropdown-item @click.native="setPriority(file)" v-if="(['action','filter','shortcode'].includes(file.type)) && (file.content.autoload)">{{ $t('buttons.priority') }} : {{file.content.priority}}</el-dropdown-item>
                                                        <el-dropdown-item @click.native="exportFile(file)" divided v-if="(['action','filter','shortcode'].includes(file.type))">{{ $t('buttons.export') }}</el-dropdown-item>
                                                        <el-dropdown-item @click.native="importFile(file)" v-if="(['action','filter','shortcode'].includes(file.type))">{{ $t('buttons.import') }}</el-dropdown-item>
                                                    </el-dropdown-menu>
                                                </el-dropdown>
                                            </div>
                                            <div  draggable="true" @dragstart="onDragStart(file,index,$event)" class="linkitem" :class="(file == currentFile) ? 'selected' : ''">
                                                <span style="float:left;padding:0;color:#ccc;width:15px;"><span v-if="file.content.autoload"><i class="fas fa-bolt"></i></span>&nbsp;</span>
                                                {{file.title}}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div v-if="!feature.content || Object.keys(feature.content).length === 0" style="padding:10px; color:#ccc;">No elements</div>
                    </div>
                </div>
                <div class="d-flex flex-full flex-column p-relative bg-white">
                    <div class="d-flex flex-full p-relative" v-if="currentFile">
                        <component 
                            ref="currentEditor" 
                            v-show="currentFile" 
                            v-bind:is="currentEditorComponent" 
                            :options="currentFileTypeData.options" 
                            :file="feature.content[currentFileId]" 
                            :fileId="currentFileId" 
                            :historyClbk="historyClbk"
                            :historyBck="history[currentFileId]">
                        </component>
                    </div>
                    <div class="d-flex flex-column flex-full justify-content-center align-items-center" v-if="!currentFile" style="padding:0 10px;overflow:auto;">
                        <div class="">
                            <h1 style="color:#aaaaaa;margin-bottom:30px;text-align:center;">Add new element</h1>
                        </div>
                        <div class="d-flex justify-content-center align-items-center">
                            <div class="d-flex" v-for="(fileType, typeIndex) in fileTypes" :key="typeIndex" style="margin:5px;">
                                <el-card shadow="hover">
                                    <div class="" style="cursor:pointer;width:70px;clear:both;text-align:center;color:#aaaaaa;" @click="addFile(typeIndex)">
                                        <span :class="fileType.icon" style="font-size:2em;"></span><br />
                                        <h3 style="display:inline-block;font-size:1.2em;font-weight:300;color:#aaaaaa;margin-bottom:0;">{{fileType.title}}</h3>
                                    </div>
                                </el-card>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    
        <el-dialog :title="$t('dialog.import.title')" :visible.sync="importCodeDialog" width="40%">
            <el-input type="textarea" :rows="5" :placeholder="$t('placeholder.paste_code')" v-model="codeToImport">
            </el-input>
            <span slot="footer" class="dialog-footer">
                    <el-button @click="importCodeDialog = false">{{ $t('buttons.cancel') }}</el-button>
                    <el-button type="primary" @click="importCode()">{{ $t('buttons.confirm') }}</el-button>
                </span>
        </el-dialog>
    
        <el-dialog :title="$t('dialog.export.title')" :visible.sync="exportCodeDialog" width="50%">
            <el-input type="textarea" :rows="5" v-model="codeToExport" id="exportInput"> </el-input>
            <span slot="footer" class="dialog-footer">
                    <el-button @click="copyToClipboard()">{{ $t('buttons.copyClipboard') }}</el-button>
                    <el-button @click="exportCodeDialog = false">{{ $t('buttons.close') }}</el-button>
                </span>
        </el-dialog>
    
        <element-infos />
    </div>
</template>

<script>
    import {
        EventBus
    } from '../../event';
    import editoraction from '../editors/flowscript'
    import editorcode from '../editors/code'
    import editordata from '../editors/data'
    import store from '../../Store'
    import elementInfos from '../dialogs/elementInfos'
    import fileTypes from '../../config/editors.js'
    
    export default {
        name: 'editfeatures',
        components: {
            'editoraction': editoraction,
            'editorcode': editorcode,
            'editordata': editordata,
            'element-infos': elementInfos,
        },
        data() {
            return {
                fileTypes: {},
                currentFile: false,
                currentFileId: false,
                codeToImport: '',
                codeToExport: '',
                importCodeDialog: false,
                exportCodeDialog: false,
                filenameRegex: /^[a-zA-Z0-9_]*$/,
                //filenameRegex: /^[a-zA-Z0-9_]+(\s[a-zA-Z0-9_]+)*$/,
                feature: false,
                defaultProps: {
                    children: 'children',
                    label: 'title'
                },
                data: [],
                fileMenuVisible: false,
                window: window,
                isLoading: false,
                toResave: false,
                history:{},
            }
        },
        computed: {
            currentEditorComponent() {
                return (this.currentFile.type) ? this.fileTypes[this.currentFile.type].editor : false;
            },
            currentFileTypeData() {
                return (this.currentFile.type in this.fileTypes) ? this.fileTypes[this.currentFile.type] : {};
            },
            currentFiles(){
                return this.feature.content;
            }
        },
        mounted() {
            var id = this.$router.currentRoute.params.id;
            this.isLoading = true;
            this.fileTypes = store.editorTypes;
            
            store.loadFeature(id)
                .then(feature => {
                    this.feature = feature;
                    this.isLoading = false;
                })
                this.history = {};
        },
        beforeDestroy(){
            
        },
        watch: {
            currentFiles: {
                handler: function (val, oldVal) {
                    if(oldVal && !this.toResave){
                        this.toResave = true;
                    }
                },
                deep: true
            }
        },
        methods: {
            backToList(){
                if(this.toResave){
                    this.$confirm('Feature not saved. Back to all features list ?', 'Warning', {
                        confirmButtonText: 'Yes',
                        cancelButtonText: 'Cancel',
                    type: 'warning'
                    }).then(() => {
                        this.$router.push('/');
                    }).catch(() => {
                            return false;
                    });
                }
                else{
                    this.$router.push('/');
                }
            },
            copyToClipboard: function(){
                var copyText = document.getElementById("exportInput");
                copyText.select();
                document.execCommand("copy");
            },
            onDragStart: function(file,id,event) {
                var type = this.getFileTypes(file);

                var a = {
                    name: file.title,
                    type: file.type,
                    ref: id,
                    inputs:{},
                    outputs: {},
                    offsetX: event.offsetX,
                    offsetY: event.offsetY
                };
        
                a.outputs = type.outputs;
                a.inputs = type.inputs;
                event.dataTransfer.dropEffect = "move";
                event.dataTransfer.setData('text/plain', JSON.stringify(a))
            },
            isVisible(type) {
                var result = false;
                for (var k in this.feature.content) {
                    var file = this.feature.content[k];
                    if (file.type == type) result = true;
                }
                return result;
            },
            loadFeatureInfos() {
                var id = this.$router.currentRoute.params.id;
                store.loadFeatureInfos(id)
                    .then(feature => {
                        this.feature.title = feature.title;
                        this.feature.category = feature.category;
                        this.feature.description = feature.description;
                        this.feature.status = feature.status;
                    })
            },
            activateFeature(feature) {
                store.activateFeature(feature)
                    .then(response => {
                        this.loadFeatureInfos();
                    })
            },
            deactivateFeature(feature) {
                store.deactivateFeature(feature)
                    .then(response => {
                        this.loadFeatureInfos();
                    })
            },
            settingsFeature(feature) {
                //EventBus.$emit('settings-feature', feature);
                EventBus.$emit('edit-infos-feature', feature, this.loadFeatureInfos);
            },
            featureCommand(command, item) {
                var feature = this.feature;
                switch (command) {
                    case 'activate':
                        this.activateFeature(feature);
                        break;
                    case 'deactivate':
                        this.deactivateFeature(feature)
                        break;
                    case 'delete':
                        this.$confirm(this.$t('feature.delete'), this.$t('label.warning'), {
                            confirmButtonText: this.$t('buttons.ok'),
                            cancelButtonText: this.$t('buttons.cancel'),
                            type: 'warning'
                        }).then(() => {
                            store.deleteFeature(feature)
                                .then(response => {
                                    this.$router.push('/');
                                })
                        }).catch(() => {
    
                        });
                        break;
                    case 'settings':
                        this.settingsFeature(feature);
                        break;
                    case 'export':
                        store.exportFeature(feature)
                            .then(response => {})
                        break;
                }
    
            },
            historyClbk(history){
                this.history[this.currentFileId] = history;
            },
            selectFile(index) {
                this.currentFile = false;
                this.currentFileId = false;
                this.$nextTick(function() {
                    this.currentFileId = index;
                    if(!this.history[this.currentFileId]){
                        this.history[this.currentFileId] = [];
                    }
                    this.currentFile = this.feature.content[index];
                })
            },
            uuid() {
                function s4() {
                    return Math.floor((1 + Math.random()) * 0x10000).toString(16).substring(1);
                }
                return s4() + s4() + '' + s4() + '' + s4() + '' + s4() + '' + s4() + s4() + s4();
            },
            saveFeature() {
                var id = this.$router.currentRoute.params.id;
                store.saveFeature(id, this.feature).then(response => {
                    this.toResave = false;
                })
            },
            addFile(extension) {
                var fileType = this.fileTypes[extension];
                EventBus.$emit('add-file', extension, fileType);
            },
            renameFile(file) {
                EventBus.$emit('rename-file', file);
            },
            copyFile(file) {
                var nFile = JSON.parse(JSON.stringify(file));
                this.$set(this.feature.content, this.uuid(), nFile);
            },
            deleteFile(file) {
                this.$confirm(this.$t('file.delete'), this.$t('label.warning'), {
                    confirmButtonText: this.$t('buttons.ok'),
                    cancelButtonText: this.$t('buttons.cancel'),
                    type: 'warning'
                }).then(() => {
                    if (this.currentFile == file) {
                        this.currentFile = false;
                    }
    
                    for (var k in this.feature.content) {
                        if (this.feature.content[k] == file) {
                            this.$delete(this.feature.content, k);
                            this.$forceUpdate();
                        }
                    }
                }).catch(() => {
    
                });
            },
            exportFile(file) {
                this.codeToExport = JSON.stringify(file.content);
                this.exportCodeDialog = true;
            },
            importFile(file) {
                this.codeToImport = '';
                this.importCodeDialog = true;
            },
            enableFile(file) {
                this.$set(file.content, 'autoload', true);
                this.$forceUpdate();
            },
            disableFile(file) {
                this.$set(file.content, 'autoload', false);
                this.$forceUpdate();
            },
            setPriority(file) {
                this.$prompt(this.$t('hook.set_priority'), this.$t('hook.title'), {
                    confirmButtonText: this.$t('buttons.ok'),
                    cancelButtonText: this.$t('buttons.cancel'),
                    inputPattern: /^[0-9]*$/,
                    inputErrorMessage: this.$t('hook.errors'),
                    inputValue: file.content.priority,
                }).then(({
                    value
                }) => {
                    this.$set(file.content, 'priority', value);
                }).catch(() => {
    
                });
            },
            importCode() {
                this.currentFile.content = JSON.parse(this.codeToImport);
                this.codeToImport = '';
                this.importCodeDialog = false;
            },
            getFileTypes(file) {
                return this.fileTypes[file.type];
            }
        }
    }
</script>

<style>
    .bg-white {
        background: white;
    }
    
    .padding-5 {
        padding: 5px;
    }
    
    .padding-10 {
        padding: 10px;
    }
    
    .position-relative {
        position: relative;
    }
    
    .link {
        cursor: pointer;
    }
    
    .link:hover {
        background: #f7f6f6;
        color: #222222;
    }
    
    .link:hover .submenu {
        opacity: 1;
    }
    
    .link .submenu {
        padding: 5px 0 0 0;
        opacity: 0;
        float: right
    }
    
    .link .submenu.show {
        opacity: 1;
    }
    
    .btn-icon>span {
        width: 18px;
        height: 18px;
        line-height: 18px;
        display: inline-block;
    }
    
    .box.fileTypes {
        width: 70px;
    }
    
    .box.fileTypes .filesMenu {
        display: none;
        z-index: 99;
    }
    
    .box.fileTypes:hover .filesMenu {
        display: block;
    }
    
    .fileTypeButton {
        text-align: center;
        border-bottom: 1px solid #eee;
        font-size: 1.5em;
        color: #555;
        width: 70px;
    }
    
    .fileTypeButton:hover {
        background: #fcfcfc;
    }
    .selected{
        background-color: #f5f7fa;
        color: #32373c;
    }

    .linkitem{
        padding:5px 10px;
    } 
</style>
