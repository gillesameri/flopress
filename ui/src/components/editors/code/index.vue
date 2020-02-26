<template>
    <div class="d-flex flex-column flex-full">
        <div class="editor-wrapper" :style="'right:'+rightEditor+'px;'">
            <editor v-model="file.content" @init="editorInit" :lang="lang" theme="chrome" class="editor" width="100%" height="100%"></editor>
        </div>
        <div style="position:absolute;top:10px;right:10px;height:40px;z-index:99;">
            <el-button type="success" class="btn-icon" circle @click="addFctOverlay = true"><span class="fas fa-project-diagram"></span></el-button>
        </div>
        <div v-if="addFctOverlay" class="overlay d-flex flex-column gaps">
             <div class="d-flex" style="padding:10px 10px 10px 10px;margin-bottom:0;border-bottom:1px solid #eee;">
                <div class="flex-full">
                    <el-input :placeholder="$t('placeholder.search_component')" size="mini" v-model="searchTerm" class="input-with-select" style="width:100%;">
                        <el-select v-model="searchGroup" size="mini" clearable slot="prepend" style="width:130px;" :placeholder="$t('placeholder.group')">
                            <el-option v-for="(item,index) in componentsGroupsList" :key="index" :label="item" :value="item"></el-option>
                        </el-select>
                    </el-input>
                </div>
                <div class="box">
                    <div style="padding:5px;cursor:pointer;margin-left:5px;" @click="addFctOverlay = false"><i class="fas fa-times"></i></div>
                </div>
            </div>
            <div class="flex-full wrap" style="overflow-y:scroll;margin:0;padding:10px 10px;">
                <div  v-for="(item, index) in componentsList" :key="index" class="" @click="openComponentDialog(index, item)" style="cursor:pointer;">
                    <cmpt :item="item" ></cmpt>
                </div>
                <infinite-loading :identifier="infiniteId" @infinite="infiniteHandler" style="padding:10px;">
                    <div slot="spinner">Loading...</div>
                    <div slot="no-more">No more components</div>
                    <div slot="no-results">No results</div>
                </infinite-loading>
            </div>
        </div>
        <componentDialog/>
    </div>
</template>

<script>

import component from './component'
import { EventBus } from '../../../event';
var debounce = require('debounce');
import store from '../../../Store'
import componentDialog from '../../dialogs/componentDescEditor'
import InfiniteLoading from 'vue-infinite-loading';

export default {
  name: 'scenario-page',
  props:[ 'id', 'options', 'content', 'file' ],
    components: {
        editor: require('./editor'),
        cmpt: component,
        componentDialog: componentDialog,
        'InfiniteLoading': InfiniteLoading,
    },
  data () {
    return {
        lang:'twig',
        addFctOverlay: true,
        editor: {},
        rightEditor: 350,
        searchTerm:'',
        searchGroup:'',
        searchPage: 0,
        totalPage: 1,
        componentsList: [],
        componentsGroupsList: [],
        infiniteId: +new Date(),
      }
  },
  mounted() {
      
  },
  created() {
        EventBus.$on('set-component-template', this.insertCode);
  },
    beforeDestroy(){
        EventBus.$off('set-component-template', this.insertCode);
    },
  watch:{
        addFctOverlay(val){
            if(val){
                this.rightEditor = 350;
            }
            else{
                this.rightEditor = 0;
            }
            this.$nextTick(function(){
                this.editor.resize()
            })
        },
        searchTerm: debounce(function (e) {
            this.componentsList = [];
            this.searchPage = 0;
            this.$nextTick(function() {
                this.infiniteId += 1;
            })
        }, 750),
        searchGroup() {
            this.componentsList = [];
            this.searchPage = 0;
            this.$nextTick(function() {
                this.infiniteId += 1;
            })
        },
  },
  methods: { 
    insertCode(val){
        this.editor.session.insert(this.editor.getCursorPosition(), val);
    },
    infiniteHandler($state) {
        var self = this;
        if (this.searchPage <= this.totalPage) {
            store.load_components_data('functions', this.searchTerm, this.searchGroup, 'fct', this.searchPage)
            .then(data => {
                self.componentsGroupsList = data['_group']
                if (self.searchPage <= data.total_pages) {
                    var res = Object.values(self.componentsList).concat(data['components'])
                    self.componentsList = res;
                    self.totalPage = data.total_pages;
                    self.searchPage += 1;
                    $state.loaded();
                } else {
                    $state.complete();
                }
            })
        } else {
            $state.complete();
        }
    },
    editorInit: function (editor) {
        require('brace/ext/language_tools') //language extension prerequsite...
        require('brace/ext/searchbox')
        require('brace/mode/html')                
        require('brace/mode/javascript')    //language
        require('brace/mode/css')
        require('brace/mode/json')
        require('brace/mode/twig')
        require('brace/mode/text')
        require('brace/theme/chrome')
        require('brace/snippets/javascript') //snippet
        require('brace/snippets/twig') //snippet
        this.editor = editor;
    },
    openComponentDialog(id, component){
        for(var i in component.inputs){
            delete component.inputs[i].description;
        }
        for(var o in component.outputs){
            delete component.outputs[o].description;
        }

        var a = {
            name: component.name,
            type: component.type,
            inputs: component.inputs,
            outputs: component.outputs
        };
        EventBus.$emit('edit-component-template', a);
    },
  }
}
</script>

<style>


.editor-wrapper{
    position:absolute;
    top:0px;
    right:350px;
    bottom:0;
    left:0;
}

.overlay{
        position:absolute;
        top:0px;
        right:0px;
        width:350px;
        bottom:0;
        background-color:rgba(255,255,255);
        z-index:999;
        border-left:1px solid #eee;
    }
</style>
 