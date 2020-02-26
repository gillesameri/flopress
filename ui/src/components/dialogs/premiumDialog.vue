<template>
    <el-dialog :visible.sync="isVisible" :title="'Premium features'" width="500px" class="codexPicker" :close-on-click-modal="false">
        <div class="container cfonts text-center">
            <div class="row">
                <div class="col-md-12">
                    <h1 style="font-weight:300;">70+ Time-saving features</h1>
                    <p style="font-size:1.1em;">Get access to a great deal of ready-to-use features, directly available on your WordPress administration page.</p>
                    <hr style="margin:0;" />
                </div>
                <div class="col-md-12">
                    <h1 style="font-weight:300;">12 Plugins definitions</h1>
                    <p style="font-size:1.1em;">FloPress Pro provides hook and function definitions on some of the most popular and widely used WordPress plugins.<br />Advanced Custom Fields (free &amp; pro), Elementor (free &amp; pro), Jetpack, WPML, WPForms, Yoast, WP Security Audit Log, MetaSlider, …</p>
                    <hr style="margin:0;" />
                </div>
                <div class="col-md-12"> 
                    <h1 style="font-weight:300;">Access Anywhere</h1>
                    <p style="font-size:1.1em;">FloPress Pro lets you sync your features to your own host, via FTP.
                       Connect your own storage location, push the features in one click and share your features across all your websites.</p>
                </div>
            </div>
        </div>
        <span slot="footer" class="dialog-footer">
            <el-button type="primary" @click="getFloPressPro.goExternal()">View website</el-button>
            <el-button type="success" @click="getFloPressPro.goInternal()">Get FloPress Pro</el-button>
            <el-button @click="isVisible = false">{{ $t('buttons.close') }}</el-button>
        </span>
    </el-dialog>
</template>

<script>

import { EventBus } from '../../event';

export default {
  name: 'premiumDialog',
    components: {
    },
    data () {
        return {
            isVisible: false,
            isLoading: true,
        }
    },
    created(){
        var self = this;
        EventBus.$on('premium-dialog', this.initDialog);
    },
    beforeDestroy() {
        EventBus.$off('premium-dialog', this.initDialog);
    },
    methods: {
        initDialog(){
            this.isVisible = true;
        }
    },
}
</script>

<style>
.cfonts {
    font-family: Roboto,Ubuntu,"Helvetica Neue",sans-serif;
}
</style>
