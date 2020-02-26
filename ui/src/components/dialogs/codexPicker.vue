<template>
    <el-dialog :visible.sync="isVisible" :title="$t('dialog.codex.title')" width="70%" class="codexPicker" :close-on-click-modal="false">
        <div slot="title">
            <h1 style="color:white;margin:0;" v-html="$t('dialog.codex.title')"></h1>
            <div style="padding-top:10px;">(These plugins must be installed separately in order to work).</div>
        </div>
        <div class="innerCodexPicker" v-loading="loading">
            <div class="container-fluid">
                <div class="row" style="border:1px solid #eee;">
                    <div v-for="(codex, index, k) in codexs" :key="index" class="col-lg-4 col-md-6 col-xs-12 d-flex padding-10 codex" :class="(k%2==0)?'even':'odd'">
                        <!--<div class="" style="margin-right:10px;">
                            <img :src="codex.logo" style="width:50px;"/>
                        </div>-->
                        <div class="flex-full">
                            <h3 style="margin:0;padding:0;font-size:1em;">{{codex.title}}</h3>
                            <p style="margin:0px;" v-html="codex.description"></p>
                            <span style="font-size:0.8em;">
                                <a v-if="codex.website" :href="codex.website" target="_blank">Website</a>
                                <span v-if="codex.website && codex.documentation"> - </span>
                                <a v-if="codex.documentation" :href="codex.documentation" target="_blank">Documentation</a>
                            </span>
                        </div>
                        <div class="" style="padding:10px 10px 10px 10px;">
                            <el-switch :disabled="codex.disabled" v-model="codex.enabled"></el-switch>
                            <div style="color:red;font-size:0.7em;" v-if="codex.disabled">Required</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <span slot="footer" class="dialog-footer">
            <div class="d-flex">
                <div class="flex-full">
                     <span style="display:inline-block;float:left;font-size:0.9em;color:#999;text-align:left;">
                         Definitions are provided as is and may be modified later. FloPress is neither affiliated nor necessarily endorsed by these plugins.<br />
                         <a href="mailto:support@flopress.io">Get in touch with us</a> if you would like us to support and integrate with a plugin that you use.
                    </span>
                </div>
                <div>
                    <div  style="width:200px;">
                        <el-button @click="isVisible = false">{{ $t('buttons.cancel') }}</el-button>
                        <el-button type="primary" @click="setCodexSources()">{{ $t('buttons.save') }}</el-button>
                    </div>
                </div>
            </div>
        </span>
    </el-dialog>
</template>

<script>

import { EventBus } from '../../event';
import store from '../../Store';

export default {
  name: 'featureInfos',
    components: {
    },
  data () {
        return {
            isVisible: false,
            codexs: [],
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
        EventBus.$on('codex-sources-picker',function() {
            self.isVisible = true;
            self.initCodex();
        });
    },
    methods: {
        initCodex(){
            var self = this;
            this.loading = true;
            store.getCodexSources()
                .then(codexs => {
                    self.codexs = codexs
                    self.loading = false;
                })
        },
        setCodexSources(){
            var self = this;
            this.loading = true;

            store.saveCodexSources(this.codexs)
                .then(result => {
                    self.codexs = [];
                    self.loading = false;
                    self.isVisible = false;
            })
        }
    },
    computed: {
        
    },
}
</script>

<style>
    .codexPicker .el-dialog__body{
        padding:0;
    }

    .innerCodexPicker{
        min-height: 400px;
        max-height:400px;
        padding:10px 10px;
        overflow-y:scroll;
    }
    
    .codex{
        border:1px solid #eee;
    }


</style>
