<template>
    <el-dialog :title="$t('dialog.settings.title')" :visible.sync="isVisible" width="500px" @close="settingsFeature = {}" :close-on-click-modal="false">
        <el-form v-loading="loading" label-position="left" ref="form" :model="settingsFeature" label-width="120px" @submit.native.prevent>
            <div v-for="(input,indexInput) in settingsFeature" :key="indexInput" v-if="'type' in input.content">
                <hr/>
                <strong>{{input.content.title}}</strong>
                <div>
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
                </div>
                <small style="line-height:16px;">{{ input.content.description }}</small> - 
                <small v-if="input.content.value" style="line-height:16px;">{{ $t('dialog.settings.default_value') }} ({{ input.content.value }})</small>
            </div>
            <div v-if="(Object.keys(settingsFeature).length == 0) && !loading">No settings for this feature.</div>
    
        </el-form>
        <span slot="footer" class="dialog-footer">
                <el-button v-if="!(Object.keys(settingsFeature).length == 0)" type="warning" @click="clearSettings()">{{ $t('dialog.settings.clear_settings') }}</el-button>
                <el-button v-if="!(Object.keys(settingsFeature).length == 0)" @click="isVisible = false">{{ $t('buttons.cancel') }}</el-button>
                <el-button v-if="(Object.keys(settingsFeature).length == 0)" @click="isVisible = false">{{ $t('buttons.close') }}</el-button>
                <el-button v-if="!(Object.keys(settingsFeature).length == 0)" type="primary" @click="setSettings()">{{ $t('dialog.settings.save_settings') }}</el-button>
            </span>
    </el-dialog>
</template>

<script>
    import {
        EventBus
    } from '../../event';
    import fpmediapicker from '../fields/mediaPicker';
    import fppostpicker from '../fields/postPicker';
    import fpmarray from '../fields/marray';
    import store from '../../Store';
    
    export default {
        name: 'featureSettings',
        components: {
            fpmediapicker: fpmediapicker,
            fppostpicker: fppostpicker,
            fpmarray: fpmarray,
        },
        data() {
            return {
                isVisible: false,
                settingsFeature: {},
                featureId: false,
                loading:false,
            }
        },
        created() {
            var self = this;
            EventBus.$on('settings-feature', this.initDialog);
        },
        beforeDestroy() {
            EventBus.$off('settings-feature', this.initDialog);
        },
        methods: {
            initDialog(feature) {
                var self = this;
                self.isVisible = true;
                self.featureId = feature.id;
                this.loading=true;
                store.loadSettingsData(feature)
                    .then(settings => {
                        self.settingsFeature = settings
                        self.$forceUpdate()
                        self.loading = false;
                    })
                self.$forceUpdate()
            },
            setSettings() {
                store.saveSettingsFeature(this.featureId, this.settingsFeature)
                    .then(settings => {
                        this.settingsFeature = settings;
                        this.isVisible = false;
                        this.$forceUpdate()
                    })
            },
            clearSettings() {
                for (var k in this.settingsFeature) {
                    delete this.settingsFeature[k].override_value;
                }
                this.$forceUpdate();
            }
        }
    }
</script>

<style scoped>
    
</style>
