<template>
    <el-dialog :visible.sync="isVisible" :title="'FTP settings'" width="600px" :close-on-click-modal="false">
        <el-form v-loading="loading" :model="ftpSettings" label-position="left" ref="ftpSettings" label-width="170px" @submit.native.prevent>
            <el-form-item :label="$t('label.host')" prop="host">
                <el-input v-model="ftpSettings.host" autofocus></el-input>
            </el-form-item>
            <el-form-item :label="$t('label.user')" prop="user">
                <el-input v-model="ftpSettings.user"></el-input>
            </el-form-item>
            <el-form-item :label="$t('label.password')" prop="password">
                <el-input v-model="ftpSettings.password"></el-input><br />
                <el-checkbox v-model="ftpSettings.change_password">Change password</el-checkbox>
            </el-form-item>
            <el-form-item :label="$t('label.ssl')" prop="ssl">
                <el-switch
                    v-model="ftpSettings.ssl"
                    active-color="#13ce66"
                    inactive-color="#ff4949">
                </el-switch>
            </el-form-item>
            <el-form-item :label="$t('label.port')" prop="port">
                <el-input v-model="ftpSettings.port"></el-input>
            </el-form-item>
            <el-form-item :label="$t('label.path')" prop="path">
                <el-input v-model="ftpSettings.path"></el-input>
            </el-form-item>
        </el-form>
        <span slot="footer" class="dialog-footer">
            <el-button type="warning" @click="clearSettings()">{{ $t('dialog.settings.clear_settings') }}</el-button>
            <el-button @click="closeSettings()">{{ $t('buttons.cancel') }}</el-button>
            <el-button type="primary" @click="setSettings()">{{ $t('buttons.saveFeature') }}</el-button>
        </span>
    </el-dialog>
</template>

<script>

import { EventBus } from '../../event';
import store from '../../Store';

export default {
    name: 'ftpSettings',
    components: {
    },
    data () {
        return {
            isVisible: false,
            ftpSettings: {},
            source:false,
            type: false,
            loading:false,
            clbk:false,
        }
    },
    created(){
        var self = this;
        EventBus.$on('ftp-settings', this.initFTPSettings);
    },
    beforeDestroy() {
        EventBus.$off('ftp-settings', this.initFTPSettings);
    },
    methods: {
        initFTPSettings(source, clbk){
            this.isVisible = true;
            this.loading = true;
            this.ftpSettings = {change_password:false};
            this.source = source;
            var self = this;
            this.clbk = clbk;
            this.ftpSettings.change_password = false;
            store.getSourceSettings(source.slug).then(
                settings => {
                    self.ftpSettings = settings;
                    self.loading = false;
                }
            )
        },
        setSettings(){
            var self = this;
            this.$refs['ftpSettings'].validate((valid) => {
                var t = this.type;
                store.saveSourceSettings(this.ftpSettings, this.source.slug)
                    .then(featureId => {
                        self.ftpSettings = {change_password:false};
                        if(self.clbk) self.clbk(self.ftpSettings);
                        self.isVisible = false;
                })
            });
        },
        clearSettings() {
            for (var k in this.ftpSettings) {
                delete this.ftpSettings[k];
            }
            this.$forceUpdate();
        },
        closeSettings() {
            delete this.ftpSettings[k];
            this.isVisible = false;
            this.$forceUpdate();
        }
    },
    computed: {
        
    },
}
</script>

<style scoped>
</style>
