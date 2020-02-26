<template>
    <div class="d-flex flex-column full-absolute">
        <el-upload v-show="false" class="upload-demo" ref="upload" :action="flopQ.ajaxurl+'?_ajax_nonce='+flopQ.ajax_nonce.flopress_import_local_feature+'&action=flopress_import_local_feature'" :auto-upload="true" :multiple="false" :show-file-list="false" :on-success="featureImported" :on-error="featureNotImported" style="display:inline-block;">
            <el-button size="mini" slot="trigger" ref="uploadBtn"></el-button>
        </el-upload>
        <div class="d-flex bg-white padding-10" style="box-shadow: 0 2px 4px 0 rgba(0,0,0,.12), 0 0 6px 0 rgba(0,0,0,.04);z-index:2;">
            <div class="d-flex flex-full flex align-items-center">
                <div>
                    <img src="../../assets/logo.png" style="max-height:20px;" />
                </div>
            </div>
            <div class="flex-full text-center"></div>
            <div class="flex-full text-right">
                <div style="min-width:400px;">
                    <div style="display:inline-block">
                        <el-button @click="triggerAdd('codex')" size="mini" type="default" icon="fas fa-book" style="color:blue;"> {{ $t('label.codex') }}</el-button>
                    </div>
                    <el-button @click="window.open($t('help.url'))" size="mini" type="default" icon="fas fa-question-circle"> {{ $t('label.documentation') }}</el-button>
                </div>
            </div>
        </div>
        <div class="flex-full d-flex overflow-hidden">
            <div class="d-flex flex-column flex-full">
                <div class="d-flex justify-content-center align-items-center color-white padding-h-20" style="background: #409eff;">
                    <div class="flex-full">
                        <h4 class="d-inline-block">Installed features</h4>
                    </div>
                    <div class="center">
                        <el-tooltip class="item" effect="dark" :content="$t('buttons.create')" placement="bottom">
                            <el-button type="primary" circle size="mini" @click="triggerAdd('blank')"><span class="fas fa-plus"></span></el-button>
                        </el-tooltip>
                        <el-tooltip class="item" effect="dark" :content="$t('buttons.import')" placement="bottom">
                            <el-button type="primary" circle size="mini" @click="triggerAdd('upload')"><span class="fas fa-download"></span></el-button>
                        </el-tooltip>
                        <el-tooltip class="item" effect="dark" :content="$t('buttons.library')" placement="bottom">
                            <el-button type="primary" circle size="mini" @click="triggerAdd('library')"><span class="fas fa-th-large"></span></el-button>
                        </el-tooltip>
                    </div>
                </div>
                <div class="d-flex color-white" style="padding: 10px 20px;">
                    <div class="d-flex flex-full">
                        <div class="box" style="padding-right:5px;">
                            <el-select v-model="featureStatusFilter" size="mini" clearable :placeholder="$t('status.label')" style="width:100px;">
                                <el-option :label="$t('status.active')" :value="$t('status.active')"></el-option>
                                <el-option :label="$t('status.inactive')" :value="$t('status.inactive')"></el-option>
                            </el-select>
                        </div>
                        <div class="" style="padding-right:5px;">
                            <el-select v-model="featureCategoryFilter" size="mini" clearable :placeholder="$t('label.category')" style="width:150px;">
                                <el-option v-for="category in categories" :key="category" :label="category" :value="category"></el-option>
                            </el-select>
                        </div>
                        <div class="flex-full" style="padding-right:5px;">
                            <el-input :placeholder="$t('placeholder.search')" size="mini" v-model="searchInstalled" class="input-with-select">
                            </el-input>
                        </div>
                    </div>
                </div>
                <div class="d-flex flex-column flex-full justify-content-center align-items-center justify-center overflow-auto" v-if="!features" style="padding:0 30px;">
                    <div class="">
                        <h1 style="color:#aaaaaa;margin-bottom:30px;">Loading...</h1>
                    </div>
                </div>
                <div class="d-flex flex-column flex-full justify-content-center align-items-center overflow-auto" v-if="features && !isFeatureDisplay() && (features.length > 0)" style="padding:0 30px;">
                    <div class="">
                        <h1 style="color:#aaaaaa;margin-bottom:30px;">No results</h1>
                    </div>
                </div>
                <div class="d-flex flex-column flex-full justify-content-center overflow-auto" v-if="features && !isFeatureDisplay() && (features.length <= 0)" style="padding:0 30px;">
                    <div class="">
                        <h1 style="color:#aaaaaa;margin-bottom:30px;text-align:center;">No features installed</h1>
                    </div>
                    <div class="d-flex justify-content-center ">
                        <div class="d-flex margin-5">
                            <el-card shadow="hover">
                                <div class="" style="cursor:pointer;max-width:190px;text-align:center;color:#aaaaaa;" @click="triggerAdd('blank')">
                                    <span class="fas fa-plus" style="font-size:3em;"></span><br />
                                    <h3 class="d-inline-block" style="color:#aaaaaa;">{{ $t('emptyScreen.box1.title') }}</h3>
                                    <p>{{ $t('emptyScreen.box1.description') }}</p>
                                </div>
                            </el-card>
                        </div>
                        <div class="d-flex margin-5">
                            <el-card shadow="hover">
                                <div class="cursor-pointer text-center" style="max-width:190px;color:#aaaaaa;" @click="triggerAdd('library')">
                                    <span class="fas fa-th-large" style="font-size:3em;"></span><br />
                                    <h3 class="d-inline-block" style="color:#aaaaaa;">{{ $t('emptyScreen.box2.title') }}</h3>
                                    <p>{{ $t('emptyScreen.box2.description') }}</p>
                                </div>
                            </el-card>
                        </div>
                        <div class="d-flex margin-5">
                            <el-card shadow="hover">
                                <div class="cursor-pointer text-center" style="max-width:190px;color:#aaaaaa;" @click="triggerAdd('upload')">
                                    <span class="fas fa-download" style="font-size:3em;"></span><br />
                                    <h3 class="d-inline-block" style="color:#aaaaaa;">{{ $t('emptyScreen.box3.title') }}</h3>
                                    <p>{{ $t('emptyScreen.box3.description') }}</p>
                                </div>
                            </el-card>
                        </div>
                    </div>
                </div>
                <div class="flex-full p-relative overflow-y-scroll" style="padding:0 20px 20px 20px" v-if="features && isFeatureDisplay() && (features.length > 0)">
                    <draggable v-model="features" :options="{group:'feature'}" @start="drag=true" @add="onEnd"  @end="onEnd">
                        <div v-for="feature in features" :key="feature.id" v-show="canDisplay(feature)">
                            <el-card shadow="hover" :body-style="{ padding: '0px' }" class="box-card feature" :class="feature.status">
                                <div class="padding-20 d-flex ">
                                    <div class="flex-full col-8">
                                        <strong style="font-size:1.1em;">{{ feature.title }}</strong><br />
                                        <div><i v-html="feature.category"></i></div>
                                        <div v-html="feature.description.replace(/(?:\r\n|\r|\n)/g, '<br />')"></div>
                                    </div>
                                    <div class="text-right col-4">
                                        <el-tooltip v-show="feature.status == 'inactive'" class="item" effect="dark" :content="$t('buttons.activate')" placement="bottom">
                                            <el-button class="border-none" circle type="default" @click="activateFeature(feature)"><i class="fas fa-play"></i></el-button>
                                        </el-tooltip>
                                        <el-tooltip v-show="feature.status == 'active'" class="item" effect="dark" :content="$t('buttons.deactivate')" placement="bottom">
                                            <el-button class="border-none" circle type="default" @click="deactivateFeature(feature)"><i class="fas fa-stop"></i></el-button>
                                        </el-tooltip>
                                        <el-tooltip class="item" effect="dark" :content="$t('buttons.settings')" placement="bottom">
                                            <el-button class="border-none" circle type="default" @click="settingsFeature(feature)"><i class="fas fa-cog"></i></el-button>
                                        </el-tooltip>
                                        <el-tooltip class="item" effect="dark" :content="$t('buttons.edit')" placement="bottom">
                                            <el-button class="border-none" circle type="default" @click="$router.push({name:'edit',params:{id:feature.id}})"><i class="fas fa-pencil-alt"></i></el-button>
                                        </el-tooltip>
                                        <el-dropdown trigger="click" @command="featureCommand" :feature="feature">
                                            <span class="el-dropdown-link">
                                                <el-button class="border-none" icon="el-icon-more" circle type="default"></el-button>
                                            </span>
                                            <el-dropdown-menu slot="dropdown">
                                                <el-dropdown-item command="copy"><i class="fas fa-clone"></i>&nbsp;{{ $t('buttons.copy') }}</el-dropdown-item>
                                                <el-dropdown-item command="delete"><i class="fas fa-trash-alt"></i>&nbsp;{{ $t('buttons.delete') }}</el-dropdown-item>
                                                <el-dropdown-item command="export"><i class="far fa-arrow-alt-circle-down"></i>&nbsp;{{ $t('buttons.export') }}</el-dropdown-item>
                                                <el-dropdown-item :command="'sourceadd-'+source.slug" v-for="(source,index) in editableSources" :key="index" v-show="source.can_add && (source.slug == currentSource.slug)" :divided="(index == 0)"><i :class="source.icon"></i>&nbsp;Export to {{ source.title }}</el-dropdown-item>
                                            </el-dropdown-menu>
                                        </el-dropdown>
                                    </div>
                                </div>
                            </el-card>
                        </div>
                    </draggable>
                </div>
            </div>
            <div class="d-flex flex-column overflow-hidden aside" style="background:#eee;" v-if="isFPPremium" :style="{width:(showLibrary ? '35%' : '0px')}">
                <div class="d-flex color-white" style="background: #3585d8; padding: 0 20px;min-width:300px;">
                    <div class="flex-full">
                        <h4 style="display:inline-block;">Time-saving features</h4> from 
                        <el-dropdown trigger="click" @command="changeSource">
                            <span class="el-dropdown-link" style="color:white;cursor:pointer;">
                                <span v-if="currentSource">{{currentSource.title}}</span><i class="el-icon-arrow-down el-icon--right"></i>
                            </span>
                            <el-dropdown-menu slot="dropdown">
                                <el-dropdown-item :command="source.slug" v-for="(source, index) in sources" :key="index">{{ source.title }}</el-dropdown-item>
                            </el-dropdown-menu>
                        </el-dropdown>
                    </div>
                    <div class="d-flex justify-content-center align-items-center">
                        <el-button v-if="currentSource.has_settings" class="border-none" type="text" @click="settingsSources(currentSource)">
                            <i class="fas fa-cog" style="color:white;"></i>
                        </el-button>
                    </div>
                </div>

                <div class="d-flex flex-full flex-column p-relative" v-loading="isRemoteLoading">
                    <div class="d-flex color-white" style="padding: 10px 20px;">
                        <div class="d-flex flex-full">
                            <div class="" style="padding-right:5px;">
                                <el-select v-model="searchCategory" size="mini" clearable slot="prepend" :placeholder="$t('label.category')" style="width:150px;">
                                    <el-option v-for="(remoteCat, index) in remoteCategories" :key="index" :label="remoteCat" :value="remoteCat"></el-option>
                                </el-select>
                            </div>
                            <div class="flex-full" style="padding-right:5px;">
                                <el-input :placeholder="$t('placeholder.search')" size="mini" v-model="searchRemote">
                                </el-input>
                            </div>
                        </div>
                    </div>

                    <div class="flex-full p-relative">
                        <div class="overflow-y-scroll" style="position:absolute;left:0;right:0;top:0;bottom:0;padding:0 20px 20px 20px;">
                            <div v-for="feature in remoteFeatures" :key="feature.slug">
                                <el-card shadow="hover" :body-style="{ padding: '0px' }" class="box-card">
                                    <div class="padding-20">
                                        <div class="d-flex flex-column">
                                            <div class="d-flex">
                                                <div class="flex-full">
                                                    <strong style="font-size:1.1em;">{{ feature.title }}</strong><br />
                                                    <div><i v-html="feature.category"></i></div>
                                                    <div v-html="feature.description"></div>
                                                </div>
                                                <div class="text-center" style="padding-left:10px;">
                                                    <div style="width:50px">
                                                        <el-tooltip class="item" effect="dark" :content="$t('buttons.install')" placement="bottom">
                                                            <el-button type="default" circle size="mini" @click="install(feature)"><span class="fas fa-download"></span></el-button>
                                                        </el-tooltip><br />
                                                        <el-button v-if="currentSource.can_delete" type="text" circle size="mini" style="padding:2px;" @click="deleteRemoteFeature(feature)">{{$t('buttons.remove')}}</el-button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </el-card>
                            </div>
                        </div>
                    </div>
                </div>
                
            </div>
        </div>
        <ftp-settings />
    </div>
</template>

<script>
    import {
        EventBus
    } from '../../event';
    import featureInfos from '../dialogs/featureInfos'
    import ftpSettings from '../dialogs/ftpSettings'
    import store from '../../Store'
    var debounce = require('debounce');
    import draggable from 'vuedraggable'

    export default {
        name: 'localfeatures',
        components: {
            featureInfos,
            ftpSettings,
            draggable,
        },
        data() {
            return {
                featureStatusFilter: '',
                featureCategoryFilter: '',
                ajaxUrl: ajaxurl,
                features: false,
                window: window,
                searchInstalled: '',
                searchRemote: '',
                searchCategory: '',
                searchSource:'local',
                remoteFeatures: [],
                remoteCategories: [],
                showLibrary: false,
                categories:[],
                sources:[],
                isRemoteLoading: false
            }
        },
        created() {
            EventBus.$on('load-features', this.loadFeature);
            this.showLibrary = (sessionStorage.getItem('flopress_library_open')) ? true : false;
        },
        beforeDestroy() {
            EventBus.$off('load-features', this.loadFeature);
        },
        mounted() {
            var self = this;
            if(this.isFPPremium){
                store.getSourceTypes()
                    .then(sources => {
                        self.sources = sources;
                        self.loadRemoteFeatures();
                    })
            }
            this.loadFeature();
        },
        watch: {
            showLibrary: function(v){
                if(this.isFPPremium){
                    if(v){ sessionStorage.setItem('flopress_library_open',v); }
                    else{ sessionStorage.removeItem('flopress_library_open'); }
                }
            },
            searchInstalled: debounce(function(e) {
                this.$nextTick(function() {
    
                })
            }, 1000),
            searchRemote: debounce(function(e) {
                if(this.isFPPremium){
                    this.loadRemoteFeatures();
                }
            }, 1000),
            searchCategory: function(v) {
                if(this.isFPPremium){
                    this.loadRemoteFeatures();
                }
            },
            searchSource: function(v) {
                if(this.isFPPremium){
                    this.loadRemoteFeatures();
                }
            },
        },
        computed: {
            remoteFeaturesList() {
                return (this.isFPPremium) ? this.remoteFeatures : [];
            },
            currentSource(){
                var self = this;
                var title = '';
                var el = this.sources.find(function(source){
                    return (self.searchSource == source.slug) ? true : false;
                })
                return (el) ? el : false;
            },
            editableSources(){
                var list = [];
                this.sources.forEach(function(source){
                    if(source.can_add) list.push(source);
                })
                return list;
            }
        },
        methods: {
            changeCodexSources: function(){
                EventBus.$emit('codex-sources-picker');
            },
            deleteRemoteFeature(feature){
                this.$confirm('Remove remote feature ?', 'Warning', {
                    confirmButtonText: 'Yes',
                    cancelButtonText: 'Cancel',
                    type: 'warning'
                }).then(() => {
                    var self = this;
                    this.isRemoteLoading = true;
                    store.sourceRemove(feature,this.currentSource.slug)
                    .then(response => {
                        self.loadRemoteFeatures();
                    })
                }).catch(() => {
                             
                });
                
            },
            changeSource(source){
                this.searchSource = source;
            },
            settingsSources: function(source){
                EventBus.$emit(source.has_settings,source, this.loadRemoteFeatures);
            },
            onEnd(event){
                var ids = [];
                this.features.forEach(function(feature){
                    ids.push(feature.id);
                })
                store.updateFeaturesOrder(ids)
                    .then(response => {})
            },
            canDisplay(feature){
                var is = true;
                var self = this;
                if (self.featureStatusFilter) {
                    if (feature.status && (feature.status.toLowerCase() != self.featureStatusFilter.toLowerCase())) {
                        is = false;
                    }
                }

                if (self.featureCategoryFilter) {
                    if (feature.category && (!feature.category.split(',').includes(self.featureCategoryFilter))) {
                        is = false;
                    }
                }

                if (self.searchInstalled != '') {
                    if ((feature.title.indexOf(self.searchInstalled) == -1) && (feature.description.indexOf(self.searchInstalled) == -1)) {
                        is = false;
                    }
                }
                return is;
            },
            isFeatureDisplay(){
                var is = true;
                var self = this;
                if(this.features){
                    is = false;
                    this.features.forEach(function(feature){
                        if (self.canDisplay(feature)) {
                            is = true;
                        }
                    })
                }
                return is;
            },
            loadFeature() {
                store.getFeatures()
                    .then(features => {
                        this.features = features;
                        var self = this;
                        var categories = [];
                        features.forEach(function(item){
                            var cats = item.category.split(',');
                            cats.forEach(function(cat){
                                if(!categories.includes(cat) && cat != '')
                                    categories.push(cat);
                            })
                            
                        })
                        self.categories = categories;
                    })
            },
            activateFeature(feature) {
                store.activateFeature(feature)
                    .then(response => {
                        this.loadFeature();
                    })
            },
            deactivateFeature(feature) {
                store.deactivateFeature(feature)
                    .then(response => {
                        this.loadFeature();
                    })
            },
            settingsFeature(feature) {
                //EventBus.$emit('settings-feature', feature);
                EventBus.$emit('edit-infos-feature', feature, false);
            },
            featureCommand(command, item) {
                var feature = item.$parent.$parent.$attrs.feature;
                switch (command) {
                    case 'edit':
                        this.$router.push({
                            name: 'edit',
                            params: {
                                id: feature.id
                            }
                        });
                        break;
                    case 'copy':
                        EventBus.$emit('copy-feature', feature);
                        break;
                    case 'delete':
                        this.$confirm(this.$t('feature.delete'), this.$t('label.warning'), {
                            confirmButtonText: this.$t('buttons.ok'),
                            cancelButtonText: this.$t('buttons.cancel'),
                            type: 'warning'
                        }).then(() => {
                            store.deleteFeature(feature)
                                .then(response => {
                                    this.loadFeature();
                                })
                        }).catch(() => {
    
                        });
                        break;
                    case 'export':
                        store.exportFeature(feature)
                            .then(response => {})
                        break;
                    default:
                        var source = command.split('-');
                        var self = this;
                        if(source[0] == 'sourceadd'){
                            this.$confirm('Push to library ?', 'Info', {
                                confirmButtonText: 'Yes',
                                cancelButtonText: 'Cancel',
                                type: 'info'
                            }).then(() => {
                                var self = this;
                                this.isRemoteLoading = true;
                                store.sourceAdd(feature,source[1])
                                    .then(response => {
                                        if(source[1] == self.currentSource.slug){
                                            self.loadRemoteFeatures();
                                        }
                                    })
                            }).catch(() => {
                                        
                            });
                        }
                        break;
                }
    
            },
            triggerAdd(val) {
                switch (val) {
                    case 'library':
                        if(this.isFPPremium){
                            this.showLibrary = !this.showLibrary;
                        }
                        else{
                            this.showLibrary = false;
                            sessionStorage.removeItem('flopress_library_open');
                            EventBus.$emit('premium-dialog');
                        }
                        break;
                    case 'codex':
                        if(this.isFPPremium){
                            this.changeCodexSources();
                        }
                        else{
                            EventBus.$emit('premium-dialog');
                        }
                        break;
                    case 'blank':
                        EventBus.$emit('create-feature');
                        break;
                    case 'upload':
                        this.$refs.uploadBtn.$el.click();
                        break;
                }
            },
            loadRemoteFeatures() {
                var self = this;
                if(this.isFPPremium){
                    this.isRemoteLoading = true;
                    store.getRemoteFeatures(this.searchSource, this.searchRemote, this.searchCategory)
                        .then(response => {
                            var data = response.data;
                            self.remoteCategories = data.categories;
                            self.remoteFeatures = data.features;
                            self.isRemoteLoading = false;
                        })
                }
            },
            featureImported() {
                this.$notify({
                    title: 'Success',
                    message: 'Feature imported.',
                    type: 'success',
                    position: 'bottom-right'
                });
                this.loadFeature();
            },
            featureNotImported() {
                this.$notify({
                    title: 'Error',
                    message: 'Feature not imported.',
                    type: 'error',
                    position: 'bottom-right'
                });
            },
            installCurrent() {
                if(this.isFPPremium){
                    this.install(this.remoteFeatureDialog);
                }
            },
            install(feature) {
                if(this.isFPPremium){
                    var self = this;
                    store.installRemoteFeature(feature, self.currentSource.slug)
                        .then(id => {
                            this.remoteFeatureDialog = false;
                            self.loadFeature();
                            //this.$router.push('/');
                        })
                }
            }
        }
    }
</script>

<style>
    .card-footer {
        border-top: 1px solid #eee;
        padding: 20px;
        text-align: center;
    }
    
    .card-footer .el-button+.el-button {
        margin-left: 0px;
    }
    
    .feature .el-card__header {
        background: white;
        color: #111111;
        -webkit-transition: background-color .5s ease, color .5s ease;
        /* Safari */
        transition: background-color .5s ease, color .5s ease;
    }
    
    .feature.active {
        border-left: 3px solid #409eff;
    }
    .aside{
        -webkit-transition: width 0.5s; /* Safari */
        transition: width 0.5s;
    }

    .d-none{
        display:none;
    }
</style>
