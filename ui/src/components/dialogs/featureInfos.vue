<template>
    <el-dialog :visible.sync="isVisible" :title="$t('dialog.feature.title')" width="600px" :close-on-click-modal="false">
        <el-form v-loading="loading" :model="tmpFeature" :rules="rules" label-position="left" ref="tmpFeature" label-width="170px" @submit.native.prevent>
            <h4 style="margin:0 0 10px 0;">Informations</h4>
            <el-form-item :label="$t('label.title')" prop="title">
                <el-input v-model="tmpFeature.title" autofocus></el-input>
            </el-form-item>
            <el-form-item :label="$t('label.category')" prop="category">
                
                <el-select allow-create filterable multiple v-model="tmpFeature.category" clearable :placeholder="$t('label.category')" style="width:100%;">
                    <el-option v-for="(category) in categories" :key="category" :label="category" :value="category"></el-option>
                </el-select>
            </el-form-item>
            <el-form-item :label="$t('label.description')" prop="description">
                <el-input type="textarea" :autosize="{ minRows: 2, maxRows: 10}" v-model="tmpFeature.description"></el-input>
            </el-form-item>
            <div v-if="settingsFeature && Object.keys(settingsFeature).length > 0">
                <hr style="margin-bottom:10px"/>
                <h4 style="margin:0 0 10px 0;">Parameters</h4>
                <div v-for="(input,indexInput) in settingsFeature" :key="indexInput">
                    <el-form-item :label="input.content.title" v-if="'type' in input.content">
                        <el-input v-model="input.override_value" v-if="input.content.type[1] == 'textfield'"></el-input>
                        <el-input type="textarea" v-model="input.override_value" v-if="input.content.type[1] == 'textarea'"></el-input>
                        <el-input-number v-model="input.override_value" :precision="0" :step="1" v-if="input.content.type[1] == 'intfield'"></el-input-number>
                        <el-input-number v-model="input.override_value" :precision="2" :step="0.01" v-if="input.content.type[1] == 'floatfield'"></el-input-number>
                        <el-switch v-model="input.override_value" active-color="#13ce66" inactive-color="#ff4949" v-if="input.content.type[1] == 'switch'"></el-switch>
                        <el-checkbox v-model="input.override_value" v-if="input.content.type[1] == 'checkbox'"></el-checkbox>
                        <el-time-select v-model="input.override_value" placeholder="Select time" v-if="input.content.type[1] == 'timepicker'"></el-time-select>
                        <el-date-picker v-model="input.override_value" type="date" placeholder="Pick a date" v-if="input.content.type[1] == 'datepicker'"> </el-date-picker>
                        <el-color-picker v-model="input.override_value" v-if="input.content.type[1] == 'colorpicker'"></el-color-picker>
                        <fpmediapicker v-model="input.override_value" v-if="input.content.type[1] == 'media'"></fpmediapicker>
                        <fppostpicker v-model="input.override_value" v-if="input.content.type[1] == 'post'"></fppostpicker>
                        <fpmarray v-model="input.override_value" v-if="(input.content.type.indexOf('array') != -1)"></fpmarray>
                        <div>
                            <div v-if="input.content.description"><small style="line-height:16px;">{{ input.content.description }}</small></div>
                            <div v-if="input.content.value"><small v-if="input.content.value" style="line-height:16px;">{{ $t('dialog.settings.default_value') }} {{ input.content.value }}</small></div>
                        </div>
                    </el-form-item>
                </div>
            </div>
        </el-form>
        <span slot="footer" class="dialog-footer">
            <el-button v-if="settingsFeature && !(Object.keys(settingsFeature).length == 0)" type="warning" @click="clearSettings()">{{ $t('dialog.settings.clear_settings') }}</el-button>
            <el-button @click="isVisible = false">{{ $t('buttons.cancel') }}</el-button>
            <el-button type="primary" @click="setInfos()">{{ $t('buttons.saveFeature') }}</el-button>
        </span>
    </el-dialog>
</template>

<script>

import { EventBus } from '../../event';
import store from '../../Store';
import fpmediapicker from '../fields/mediaPicker';
import fppostpicker from '../fields/postPicker';
import fpmarray from '../fields/marray';

export default {
  name: 'featureInfos',
    components: {
        fpmediapicker: fpmediapicker,
        fppostpicker: fppostpicker,
        fpmarray: fpmarray,
    },
  data () {
        return {
            isVisible: false,
            tmpFeature: {category:[]},
            categories:[],
            settingsFeature: {},
            type: false,
            loading:false,
            clbk:false,
            links: [],
            state1: '',
            state2: '',
            rules: {
                title: [
                    { required: true, message: this.$t('dialog.feature.rules.title'), trigger: 'blur' }
                ],
                description: [
                    { required: false, message: this.$t('dialog.feature.rules.description'), trigger: 'blur' }
                ]
            }
        }
    },
    created(){
        var self = this;
        EventBus.$on('create-feature',function() {
            for (var k in self.settingsFeature) { delete self.settingsFeature[k]; }
            self.initCategories();
            if(self.$refs['tmpFeature']) self.$refs['tmpFeature'].resetFields();
            self.isVisible = true;
            self.tmpFeature = {title:'',description:'', category:[]};
            self.type = 'create';
            self.settingsFeature = {};
        });
        EventBus.$on('copy-feature', (feature) => {
            for (var k in self.settingsFeature) { delete self.settingsFeature[k]; }
            self.initCategories();
            if(self.$refs['tmpFeature']) self.$refs['tmpFeature'].resetFields();
            self.isVisible = true;
            self.tmpFeature = JSON.parse( JSON.stringify( feature));
            self.tmpFeature.category = (self.tmpFeature.category == '') ? [] : self.tmpFeature.category.split(',');
            self.type = 'copy';
            self.settingsFeature = {};
        });
        EventBus.$on('edit-infos-feature', (feature, callback) => {
            for (var k in self.settingsFeature) { delete self.settingsFeature[k]; }
            self.initCategories();
            if(self.$refs['tmpFeature']) self.$refs['tmpFeature'].resetFields();
            self.loading=true;
            self.clbk = callback;
            self.isVisible = true;
            self.tmpFeature = JSON.parse( JSON.stringify( feature));
            self.tmpFeature.category = (self.tmpFeature.category == '') ? [] : self.tmpFeature.category.split(',');
            self.type = 'edit';
            store.loadSettingsData(self.tmpFeature)
                .then(settings => {
                    self.settingsFeature = settings
                    self.$forceUpdate()
                    self.loading = false;
                })
            self.$forceUpdate()
        });
    },
    methods: {
        initCategories(){
            var self = this;
            store.getCategoryTypes()
                .then(categories => {
                    self.categories = categories
                })
        },
        querySearch(queryString, cb) {
            var results = queryString ? this.categories.filter(this.createFilter(queryString)) : this.categories;
            cb();
        },
        createFilter(queryString) {
            return (link) => {
            return (link.toLowerCase().indexOf(queryString.toLowerCase()) === 0);
            };
        },
        handleSelect(item) {
        },
        setInfos(){
            var self = this;
            this.$refs['tmpFeature'].validate((valid) => {
                if (valid) {
                    var t = this.type;
                    store.setFeature(this.tmpFeature, this.type)
                        .then(featureId => {
                        if((t == 'edit') && (Object.keys(self.settingsFeature).length > 0)){
                            store.saveSettingsFeature(self.tmpFeature.id, self.settingsFeature)
                            .then(settings => {
                                self.settingsFeature = {};
                                EventBus.$emit('load-features');
                                this.isVisible = false;
                                if(this.clbk) this.clbk();
                                this.clbk = false;
                                this.$forceUpdate()
                            })
                        }
                        else{
                            self.settingsFeature = {};
                            EventBus.$emit('load-features');
                            this.isVisible = false;
                            if(this.clbk) this.clbk();
                            this.clbk = false;
                            if(t == 'create'){
                                this.$confirm(this.$t('dialog.feature.redirectMsg'), this.$t('dialog.feature.redirectTitle'), {
                                    confirmButtonText: this.$t('buttons.ok'),
                                    cancelButtonText: this.$t('buttons.cancel'),
                                    type: 'info'
                                }).then(() => {
                                    this.$router.push({ name: 'edit', params: {id: featureId}});
                                }).catch(() => {
                                    
                                });
                            }
                        }
                    })
                } else {
                    return false;
                }
            });
        },
        clearSettings() {
            for (var k in this.settingsFeature) {
                delete this.settingsFeature[k].override_value;
            }
            this.$forceUpdate();
        }
    },
    computed: {
        
    },
}
</script>

<style scoped>
</style>
