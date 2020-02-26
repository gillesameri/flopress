<template>
    <div style="position:absolute;top:0;left:0;right:0;bottom:0">

        <div style="background:white;position:absolute;top:0;left:0;right:0;bottom:0;" v-on:dblclick="quickAdd"  @dragover="onDragOver" @drop="onDrop" @mousemove="mouseIsMoving">

            <div class="full" style="overflow:hidden;"><div class="full grid-bg" :style="gridPosition"></div></div>
            <i data-fa-symbol="edit" class="fas fa-edit"></i>
            <i data-fa-symbol="delete" class="fas fa-ban"></i>
            <i data-fa-symbol="clone" class="fas fa-clone"></i>
            
            
            <SvgPanZoom id="svg"
                :fit="false"
                :center="true"
                :minZoom="0.1"
                :maxZoom="2"
                :dblClickZoomEnabled="false"
                :preventMouseEventsDefault="true" 
                @svgpanzoom="registerSvgPanZoom"
                @beforePan="!isDrag"
                @beforeZoom="!isDrag"
                ref="svg-pan-zoom"
                :onPan="updateGrid"
                :onZoom="updateGrid">
                <svg class="full" @click="selectedLink = selectedComponent = false">
                    <defs>
                        <filter id="f3" x="-50%" y="-50%" width="200%" height="200%">
                            <feOffset result="offOut" in="SourceAlpha" dx="0" dy="0" />
                            <feGaussianBlur result="blurOut" in="offOut" stdDeviation="10" />
                            <feBlend in="SourceGraphic" in2="blurOut" mode="normal" />
                            <feComponentTransfer>
                                <feFuncA type="linear" slope="0.2"/>
                            </feComponentTransfer>
                            <feMerge> 
                                <feMergeNode/>
                                <feMergeNode in="SourceGraphic"/> 
                            </feMerge>
                        </filter>
                        <filter id="shadow" width="1.5" height="1.5" x="-.25" y="-.25">
                            <feGaussianBlur in="SourceAlpha" stdDeviation="2.5" result="blur"/>
                            <feColorMatrix result="bluralpha" type="matrix" values=
                                    "1 0 0 0   0
                                    0 1 0 0   0
                                    0 0 1 0   0
                                    0 0 0 0.4 0 "/>
                            <feOffset in="bluralpha" dx="3" dy="3" result="offsetBlur"/>
                            <feMerge>
                                <feMergeNode in="offsetBlur"/>
                                <feMergeNode in="SourceGraphic"/>
                            </feMerge>
                        </filter>
                    </defs>
                    <g>
                        <template 
                            v-for="(link, idx) in file.content.links" 
                            >
                            <path 
                                v-if="idx"
                                :ref="'link-'+idx" 
                                :key="idx"
                                :filter="(selectedLink == idx) ? 'url(#shadow)' : ''" 
                                :d="linkPath(idx, link)" 
                                :stroke="linkColor(link)" 
                                fill="none" 
                                stroke-linecap="round" 
                                stroke-linejoin="round" 
                                :stroke-width="3" 
                                @click.stop="selectLink(idx)"/>
                        </template>

                        <path 
                            v-if="tempLink" 
                            :ref="'link-temp'" 
                            :d="linkTempPath()" 
                            :stroke="'#888888'" 
                            fill="none" 
                            stroke-linecap="round" 
                            stroke-linejoin="round" 
                            stroke-width="2" 
                            style="stroke-dasharray: 5;"/>
                    </g>
                    <g>
                        <flowbox 
                            v-for="(rect, idx) in file.content.components" 
                            :ref="'component-'+idx" 
                            :key="idx" class="rect" 
                            :value="file.content.components[idx]" 
                            :selected="(selectedComponent == idx)"
                            :id="idx"
                            :selectedInputs="getSelectedInputs(idx)"
                            :selectedOutputs="getSelectedOutputs(idx)"
                            :connectorHandle="connectorHandle"
                            :selectBoxCallback="selectBox"
                            :editCallback="editComponent"
                            >
                        </flowbox>
                    </g>
                </svg>
            </SvgPanZoom>
        </div>

        <div v-if="addFctOverlay" class="overlay d-flex flex-column gaps">
            <div class="d-flex" style="padding:10px 10px 10px 10px;margin-bottom:0;border-bottom:1px solid #eee;">
                <div class="flex-full">
                    <el-input :placeholder="$t('placeholder.search_component')" size="mini" v-model="searchTerm" class="input-with-select" style="width:100%;">
                        <el-select v-model="searchGroup" clearable size="mini" slot="prepend" style="width:130px;" :placeholder="$t('placeholder.group')">
                            <el-option v-for="(item,index) in componentsGroupsList" :key="index" :label="item" :value="item"></el-option>
                        </el-select>
                    </el-input>
                </div>
                <div class="">
                    <div style="padding:5px;cursor:pointer;margin-left:5px;" @click="addFctOverlay = false"><i class="fas fa-times"></i></div>
                </div>
            </div>
            <div class="flex-full wrap" style="overflow-y:scroll;margin:0;padding:10px 10px;">
                <div  v-for="(item, index) in componentsList" :key="index" @click="openComponentDialog(index, item)" style="cursor:pointer;">
                    <cmpt :item="item" ></cmpt>
                </div>
                <infinite-loading :identifier="infiniteId" @infinite="infiniteHandler" style="padding:10px;">
                    <div slot="spinner">Loading...</div>
                    <div slot="no-more">No more components</div>
                    <div slot="no-results">No results</div>
                </infinite-loading>
            </div>
        </div>

        <div v-if="historyOverlay" class="overlay d-flex flex-column gaps">
            <div class="d-flex" style="padding:10px;margin-bottom:0;border-bottom:1px solid #eee;">
                <div class="flex-full">
                    <div style="padding-top:5px;"><strong>History</strong></div>
                </div>
                <div class="">
                    <div>
                        <div style="display:inline-block;padding:5px;cursor:pointer;" @click="backHistory()">
                            <span class="fas fa-angle-left"></span>
                        </div>
                        <div style="display:inline-block;padding:5px;cursor:pointer;" @click="nextHistory()">
                            <span class="fas fa-angle-right"></span>
                        </div>
                        <div style="display:inline-block;padding:5px 5px 0 5px;cursor:pointer;" @click="historyOverlay = false">
                            <i class="fas fa-times"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="flex-full wrap" style="overflow-y:scroll;margin:0;padding:10px 10px;">
                <div  v-for="(item, index) in history" :key="index" @click="goHistory(index)">
                    <div :style="(historyPointer == index) ? 'background-color:#eee' : ''"  style="padding:5px;border:1px solid #eee;cursor:pointer;">
                        {{item.msg}}
                    </div>
                </div>
            </div>
        </div>

        <div style="position:absolute;top:10px;right:10px;height:40px;">
            <el-tooltip class="item" effect="dark" content="Add components" placement="left">
                <el-button type="default" class="btn-icon" circle @click="addFctOverlay = true"><span class="fas fa-project-diagram"></span></el-button>
            </el-tooltip>
        </div>

        <div style="position:absolute;top:70px;right:10px;height:40px;">
            <el-tooltip class="item" effect="dark" content="History" placement="left">
                <el-button type="default" class="btn-icon" circle @click="historyOverlay = true"><span class="fa fa-history"></span></el-button>
            </el-tooltip>
        </div>

        <div style="position:absolute;top:10px;left:10px;height:40px;">
            <el-button-group>
                <el-tooltip v-show="selectedComponent && !(selectedComponent == 'hook_args')" class="item" effect="dark" :content="$t('shortcode.edit')" placement="bottom-start">
                    <el-button size="small" type="default" icon="fas fa-edit" @click="editComponent(selectedComponent)"></el-button>
                </el-tooltip>
                <el-tooltip v-show="selectedComponent && !(selectedComponent == 'hook_args')" class="item" effect="dark" :content="$t('shortcode.clone')" placement="bottom-start">
                    <el-button size="small" type="default" icon="fas fa-clone"  @click="copyComponent(selectedComponent)"></el-button>
                </el-tooltip>
                <el-tooltip v-show="selectedComponent && !(selectedComponent == 'hook_args')" class="item" effect="dark" :content="$t('shortcode.delete')" placement="bottom-start">
                    <el-button size="small" type="default" icon="fas fa-trash"  @click="deleteComponent(selectedComponent)"></el-button>
                </el-tooltip>
            </el-button-group>
            <el-button-group>
                <el-tooltip v-show="selectedLink" class="item" effect="dark" :content="$t('shortcode.delete')" placement="bottom-start">
                    <el-button size="small" type="default" icon="fas fa-trash" @click="deleteLink()"></el-button>
                </el-tooltip>
                <el-tooltip v-show="tempLink" class="item" effect="dark" :content="$t('shortcode.cancel_link')" placement="bottom-start">
                    <el-button size="small" type="default" icon="fas fa-ban" @click="cancelLinkCreation()"></el-button>
                </el-tooltip>
            </el-button-group>
        </div>
        <componentDialog :hookType="file.type"/>
        
    </div>
</template>

<script>

import component from './component'
import componentDialog from '../../dialogs/componentDesc'

import flowbox from './box'
import SvgPanZoom from 'vue-svg-pan-zoom';
import 'vue-svg-pan-zoom/dist/vue-svg-pan-zoom.css';
import { Notification } from 'element-ui';
import async from 'async';
import { EventBus } from '../../../event';
import InfiniteLoading from 'vue-infinite-loading';
var debounce = require('debounce');
import store from '../../../Store'

export default {
    name: 'floweditor',
    props:['file','fileId','historyClbk','historyBck'],
    components: {
        'cmpt': component,
        'componentDialog': componentDialog,
        'flowbox': flowbox,
        'SvgPanZoom': SvgPanZoom,
        'InfiniteLoading': InfiniteLoading,
    },
    data () {
        return {
            search:'',
            category:'', 
            currentContent: {},
            addFctOverlay: false,
            historyOverlay: false,
            isDrag: false,
            svgpanzoom: false,
            selectedComponent: false,
            selectedLink: false,
            headerHeight:25,
            tempConnector:false,
            tempLink:false,
            mousePosition: {x:0,y:0},
            filenameRegex: /^[a-zA-Z0-9_/]*$/,
            searchTerm: "",
            searchGroup: "",
            searchPage: 0,
            totalPage: 1,
            componentsList: [],
            componentsGroupsList: [],
            gridPosition:{},
            infiniteId: +new Date(),
            historyPointer: 0,
            history:[],
        }
    },
    mounted() {
        this.updateGrid();
        this.initHistory('Start flow edit');
    },
    created() {
        EventBus.$on('set-component', this.setComponentData);
        document.onkeydown = evt => {
            evt = evt || window.event;
            if (evt.keyCode == 27) {
                if(this.tempLink) this.tempLink = false;
            }
            if (evt.ctrlKey && evt.which == 68) {
                if(this.selectedLink && this.file.content.links[this.selectedLink]) {
                    this.deleteLink();
                };
                if(this.selectedComponent && this.file.content.components[this.selectedComponent]) {
                    this.deleteComponent(this.selectedComponent)
                };
            }
            if (evt.ctrlKey && evt.which == 69) {
                this.editComponent(this.selectedComponent)
            }
            if (evt.ctrlKey && evt.which == 71) {
                this.copyComponent(this.selectedComponent)
            }

            if (evt.ctrlKey && evt.shiftKey && evt.which == 90) {
                this.nextHistory();
            }
            else if (evt.ctrlKey && evt.which == 90) {
                this.backHistory();
            }
        };
        this.addFctOverlay = (sessionStorage.getItem('flopress_fcts_open')) ? true : false;

    },
    beforeDestroy(){
        EventBus.$off('set-component', this.setComponentData);
    },
    computed: {
    },
    watch:{
        isDrag(val){
            (val) ? this.svgpanzoom.disablePan() : this.svgpanzoom.enablePan();
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
        addFctOverlay: function(v){
            if(v){ sessionStorage.setItem('flopress_fcts_open',v); }
            else{ sessionStorage.removeItem('flopress_fcts_open'); }
        }
    },
    methods: {
        initHistory(msg){
            if(!this.historyBck || (this.historyBck.length == 0)){
                this.history = [];
                this.pushHistory(msg);
            }
            else{
                this.history = this.historyBck;
            }
        },
        goHistory(index){
            var item = this.history[index];
            this.file = JSON.parse(item.file);  
            this.historyPointer = index;
        },
        pushHistory(msg){
            if(this.historyPointer < this.history.length){
                this.history.splice(0,this.historyPointer);
            }
            this.history.unshift({
                file: JSON.stringify(this.file),
                msg: msg
            });
            this.historyPointer = 0;
            this.historyClbk(this.history);
            this.$forceUpdate();

        },
        backHistory(){
            if(this.historyPointer < this.history.length-1){
                this.historyPointer = this.historyPointer+1;
                this.goHistory(this.historyPointer);
            }
        },
        nextHistory(){
            if(this.historyPointer > 0){
                this.historyPointer = this.historyPointer-1;
                this.goHistory(this.historyPointer);
            }
        },
        quickAdd(event){
            var sizes = this.svgpanzoom.getSizes();
            var x = (parseInt(event.layerX) - parseInt(this.svgpanzoom.getPan().x))*(1/sizes.realZoom)-10;
            var y = (parseInt(event.layerY) - parseInt(this.svgpanzoom.getPan().y))*(1/sizes.realZoom)-10;
            EventBus.$emit('edit-component', {x:x, y:y}, false);
        },
        updateGrid() {
            if(this.svgpanzoom){
                var sizes = this.svgpanzoom.getSizes();
                //item.x = (parseInt(this.mousePosition.x) - parseInt(this.svgpanzoom.getPan().x))*(1/sizes.realZoom);

                this.gridPosition = {
                    'background-position-x': this.svgpanzoom.getPan().x+'px',
                    'background-position-y': this.svgpanzoom.getPan().y+'px',
                    'background-size': 100*sizes.realZoom+'px'
                };
                this.$forceUpdate();
            }
            else{
                this.gridPosition = {
                    backgroundPositionX: 0,
                    backgroundPositionY: 0,
                }
            }
        },
        infiniteHandler($state) {
            var self = this;
            var type = (((this.file.type == 'shortcode') || (this.file.type == 'filter'))) ? 'fct' : null;
            var type = null;
            if (this.searchPage <= this.totalPage) {
                store.load_components_data('functions', this.searchTerm, this.searchGroup, type, this.searchPage)
                .then(data => {
                    self.componentsGroupsList = data['_group']
                    if (self.searchPage <= data.total_pages) {
                        var res = Object.values(self.componentsList).concat(data['components']);
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
        uuid(){
            function s4() { return Math.floor((1 + Math.random()) * 0x10000).toString(16).substring(1); }
            return s4() + s4() + '' + s4() + '' + s4() + '' +s4() + '' + s4() + s4() + s4();
        },
        registerSvgPanZoom(svgpanzoom) {
            this.svgpanzoom = svgpanzoom;
        },
        selectLink(id){
            this.selectedLink = id;
            this.selectedComponent = false;
        },
        selectBox(id){
            this.selectedComponent = id;
            this.selectedLink = false;
        },
        getRelativeCoordinates (event, element){
            var sizes = this.svgpanzoom.getSizes();

            const position = {
                x: event.pageX,
                y: event.pageY
            };

            const offset = {
                left: element.getBoundingClientRect().left,
                top: element.getBoundingClientRect().top
            };
            var r = { 
                x: (position.x - offset.left),
                y: (position.y - offset.top),
            };
            return r; 

        },
        mouseIsMoving(e){
            this.mousePosition = this.getRelativeCoordinates(e,document.getElementById('svg'));
        },
        editComponent(id){
            var component = this.file.content.components[id];
            if(['action','filter','shortcode','template','data'].includes(component.type)){
                if(component.ref){
                    this.$confirm('This component refer to a another file. Edit it ?', 'Info', {
                        confirmButtonText: 'OK',
                        cancelButtonText: 'Cancel',
                        type: 'info'
                        }).then(() => {
                            this.$parent.selectFile(component.ref);
                            this.$forceUpdate();
                        }).catch(() => {
                    });
                }
                else{
                    this.$notify({
                        title: 'Error',
                        message: 'Component not found.',
                        type: 'error',
                        position: 'bottom-right'
                    });
                }
            }
            else if(id == 'hook_args'){
                EventBus.$emit('rename-file', this.file);
            }
            else{
                EventBus.$emit('edit-component', component, id);
            }
        },
        deleteComponent(id){
            if(id == 'hook_args' ){
                this.$alert('You can\'t remove start event.', 'Error', {
                    confirmButtonText: 'OK',
                    callback: action => { }
                });
            }
            else{
                this.$confirm(this.$t('flow.delete_component'), this.$t('label.warning'), {
                    confirmButtonText: this.$t('buttons.ok'),
                    cancelButtonText: this.$t('buttons.cancel'),
                    type: 'warning'
                    }).then(() => {
                        var name = this.file.content.components[id].name;
                        delete this.file.content.components[id];
                        this.selectedComponent = false;
                        this.pushHistory('Delete component : '+name+'');
                        this.$forceUpdate();
                    }).catch(() => {
                        
                });
            }
        },
        deleteLink(){
            if(this.selectedLink){
                this.$confirm(this.$t('flow.delete_link'), this.$t('label.warning'), {
                    confirmButtonText: this.$t('buttons.ok'),
                    cancelButtonText: this.$t('buttons.cancel'),
                    type: 'warning'
                    }).then(() => {
                        delete this.file.content.links[this.selectedLink];
                        this.selectedLink = false;
                        this.pushHistory('Link deleted');
                        this.$forceUpdate();
                    }).catch(() => {
                        
                });
            }
        },
        copyComponent(id){
            if(id == 'hook_args' ){
                this.$alert('You can\'t clone start event.', 'Error', {
                    confirmButtonText: 'OK',
                    callback: action => { }
                });
            }
            else{
                var o = JSON.parse(JSON.stringify(this.file.content.components[id])); 
                o.x = o.x + 20;
                o.y = o.y + 20;

                this.$set(this.file.content.components, this.uuid(), o);
                this.pushHistory('Component '+o.name+' dupplicated');
            }
        },
        cancelLinkCreation(){
            this.tempLink = false;
        },
        connectorHandle(id, connector, type){
            if(!this.tempLink){
                this.tempLink = {id,connector,type};
            }
            else{
                var self = this;
                var fromComponentId, 
                    fromComponent, 
                    fromConnectorId,
                    fromConnectorType, 
                    toComponent, 
                    toConnectorId,
                    toComponentId, 
                    toConnectorType;

                if(self.tempLink.type == 'input'){
                    fromComponentId = id;
                    fromConnectorId = connector;
                    toComponentId = self.tempLink.id;
                    toConnectorId = self.tempLink.connector;
                }
                else if(self.tempLink.type == 'output'){
                    fromComponentId = self.tempLink.id;
                    fromConnectorId = self.tempLink.connector;
                    toComponentId = id;
                    toConnectorId = connector;
                }

                fromComponent = self.file.content.components[fromComponentId];
                fromConnectorType = fromComponent['outputs'][fromConnectorId]['type'];
                toComponent = self.file.content.components[toComponentId];
                toConnectorType = toComponent['inputs'][toConnectorId]['type'];

                async.series({
                    "io-check": function(callback) {
                        if(self.tempLink.type == type){
                            callback(self.$t('flow.errors.io_check'));
                        } 
                        else{
                            callback(null);
                        }
                    },
                    "component-check": function(callback) {
                        if(self.tempLink.id == id){
                            callback(self.$t('flow.errors.component_check'));
                        } 
                        else{
                            callback(null);
                        }
                    },
                    "efct-check": function(callback) {
                        if(((fromConnectorType == 'efct') && (fromConnectorType != toConnectorType)) || ((toConnectorType == 'efct') && (fromConnectorType != toConnectorType))){
                            callback(self.$t('flow.errors.efct_check'));
                        }
                        else{
                            callback(null);
                        }
                    },
                    "type-check": function(callback){
                        if(fromConnectorType != toConnectorType){
                            if((fromConnectorType == 'mixed') || (toConnectorType == 'mixed')){
                                callback(null);
                                return;
                            } 
                            callback(self.$t('flow.errors.type_mismatch'));
                            return;
                        } 
                        else{
                            callback(null);
                        }
                    },
                    "links-to-remove-check": function(callback){
                        if(fromConnectorType == 'efct'){
                            callback(null,'cleanOutput',fromComponentId,fromConnectorId);
                        } 
                        else{
                            callback(null,'cleanInput',toComponentId,toConnectorId);
                        }
                    }
                }, function (err, result) {
                    if(!err){
                        var uuid = self.uuid();
                        if(result['links-to-remove-check'].includes('cleanOutput')){
                            for(var k  in self.file.content.links){
                                if((self.file.content.links[k].fromComponent == result['links-to-remove-check'][1]) && (self.file.content.links[k].fromConnector == result['links-to-remove-check'][2])) {
                                    delete self.file.content.links[k];
                                }
                            }
                        }
                        if(result['links-to-remove-check'].includes('cleanInput')){
                            for(var k  in self.file.content.links){
                                if((self.file.content.links[k].toComponent == result['links-to-remove-check'][1]) && (self.file.content.links[k].toConnector == result['links-to-remove-check'][2])) {
                                    delete self.file.content.links[k];
                                }
                            }
                        }
                        self.$set(self.file.content.links, uuid, {fromComponent:fromComponentId, fromConnector:fromConnectorId, toComponent:toComponentId, toConnector: toConnectorId});
                        self.$forceUpdate();
                        self.tempLink = false;
                    }
                    else{
                        Notification.error({
                            title: self.$t('label.error'),
                            message: err,
                            position: 'bottom-right'
                        });
                        
                    }
                });
            }
        },
        linkColor(link){
            var fromComponent = this.file.content.components[link.fromComponent];
            var color = "#000000";
            if(fromComponent){
                var fromConnector = fromComponent.outputs[link.fromConnector];
                if(fromConnector && fromConnector.type in store.getConnectorColors()){
                    color = store.getConnectorColors()[fromConnector.type];
                }
            }
            return color;
        },
        linkPath(linkId,link){
            var p = [];
            var fromComponent = this.file.content.components[link.fromComponent];
            var toComponent = this.file.content.components[link.toComponent];
            if(fromComponent && toComponent){
                var fromConnector = fromComponent.outputs[link.fromConnector];
                var toConnector = toComponent.inputs[link.toConnector];
                if(fromConnector && toConnector){
                    var fromConnectorTop = this.getOutputConnectorPosition(fromComponent,link.fromConnector);
                    var toConnectorTop = this.getInputConnectorPosition(toComponent,link.toConnector);
                    return this.drawCurve(
                        parseInt(fromComponent.x+200),
                        parseInt(fromComponent.y+fromConnectorTop),
                        parseInt(toComponent.x),
                        parseInt(toComponent.y+toConnectorTop)
                    );
                }
                else{
                    delete this.file.content.links[linkId];
                    this.selectedLink = false;
                }
            }
            else{
                delete this.file.content.links[linkId];
                this.selectedLink = false;
            }
            
            
        },
        linkTempPath(){

            var sizes = this.svgpanzoom.getSizes();
            var component = this.file.content.components[this.tempLink.id];
    
            var top = 0;
            var left = 0;
            var item = {};
            if(this.tempLink.type == 'input'){
                top = this.getInputConnectorPosition(component, this.tempLink.connector);
                item.x = (parseInt(this.mousePosition.x) - parseInt(this.svgpanzoom.getPan().x))*(1/sizes.realZoom);
                item.y = (parseInt(this.mousePosition.y) - parseInt(this.svgpanzoom.getPan().y))*(1/sizes.realZoom);
       
                return this.drawCurve(
                    parseInt(item.x),
                    parseInt(item.y),
                    parseInt(component.x+left),
                    parseInt(component.y+top)
                );
            }
            else if(this.tempLink.type == 'output'){
                top = this.getOutputConnectorPosition(component, this.tempLink.connector);
                left = 200;
                item.x = (parseInt(this.mousePosition.x) - parseInt(this.svgpanzoom.getPan().x))*(1/sizes.realZoom);
                item.y = (parseInt(this.mousePosition.y) - parseInt(this.svgpanzoom.getPan().y))*(1/sizes.realZoom);
                
                return this.drawCurve(
                    parseInt(component.x+left),
                    parseInt(component.y+top),
                    parseInt(item.x),
                    parseInt(item.y),
                );
            }
          
        },
        getInputConnectorPosition(fromComponent,key){
            if(fromComponent){
                var position = Object.keys(fromComponent.inputs).indexOf(key);
                return this.headerHeight+(position*20)+20;
            }
            else{
                return 0;
            }
        },
        getOutputConnectorPosition(fromComponent,key){
            if(fromComponent){
                var position = Object.keys(fromComponent.outputs).indexOf(key);
                return this.headerHeight+(position*20)+20;
            }
            else{
                return 0;
            }
        },
        getSelectedInputs(componentId){
            var result = Object.values(this.file.content.links).filter(obj => {
                return obj.toComponent == componentId
            });
            var ports = [];
            for(var k in result){
                ports.push(result[k].toConnector);
            }
            return ports;
        },
        getSelectedOutputs(componentId){
            var result = Object.values(this.file.content.links).filter(obj => {
                return obj.fromComponent == componentId
            });
            var ports = [];
            for(var k in result){
                ports.push(result[k].fromConnector);
            }
            return ports;

        },
        drawCurve(startX, startY, endX, endY) {
            
            // M
            var AX = startX;
            var AY = startY;

            // L
            var BX = Math.abs(endX - startX) * 0.05 + startX;
            var BY = startY;
        
            // C
            var CX = startX + Math.abs(endX - startX) * 0.6;
            var CY = startY;
            var DX = endX - Math.abs(endX - startX) * 0.6;
            var DY = endY;
            var EX = - Math.abs(endX - startX) * 0.05 + endX;
            var EY = endY;

            // L
            var FX = endX;
            var FY = endY;

            // setting up the path string
            var path = 'M' + AX + ',' + AY;
            path += ' L' + BX + ',' + BY;
            path +=  ' ' + 'C' + CX + ',' + CY;
            path += ' ' + DX + ',' + DY;
            path += ' ' + EX + ',' + EY;
            path += ' L' + FX + ',' + FY;

            return path;

        },
        onDragOver: function(event){
            event.preventDefault()
        },
        openComponentDialog(id, component){
            // Get svgpanzoom sizes
            var sizes = this.svgpanzoom.getSizes();

            // Clear component inputs and outputs description
            for(var i in component.inputs) delete component.inputs[i].description;
            for(var o in component.outputs) delete component.outputs[o].description;

            // Init object for flow script insertion
            var a = {
                name: component.name,
                x: (this.svgpanzoom.getPan().x*-1*(1/sizes.realZoom))+100,
                y: (this.svgpanzoom.getPan().y*-1*(1/sizes.realZoom))+100,
                type: component.type,
                inputs: component.inputs,
                outputs: component.outputs
            };

            // Open the dialog modal
            EventBus.$emit('edit-component', a, false);
        },
        setComponentData: function(id, component){
            // Get svgpanzoom sizes
            var sizes = this.svgpanzoom.getSizes();

            var historyName = (id) ? 'Update '+component.name+' component' : 'New '+component.name+' component';
            
            // Set mouse position if not provided
            if(!component.hasOwnProperty('x')) component.x = (this.svgpanzoom.getPan().x*-1*(1/sizes.realZoom))+100;
            if(!component.hasOwnProperty('y')) component.y = (this.svgpanzoom.getPan().y*-1*(1/sizes.realZoom))+100;

            // Clean non-essentials data
            for(var i in component.inputs) delete component.inputs[i].description;
            for(var o in component.outputs) delete component.outputs[o].description;
            if(component.hasOwnProperty('description')) delete component.description;
            if(component.hasOwnProperty('return_description')) delete component.return_description;
            if(component.hasOwnProperty('summary')) delete component.summary;
            if(component.hasOwnProperty('rank')) delete component.rank;
            if(component.hasOwnProperty('group')) delete component.group;
            if(component.hasOwnProperty('since')) delete component.since;

            if((component.type != 'hook-action') && (component.type != 'hook-filter') && (component.type != 'hook-shortcode')){
                var uuid = (id) ? id : this.uuid();
                this.$set(this.file.content.components, uuid, component);
                
                if(this.tempLink){
                    if((component.type == 'efct') || (component.type == 'instruction')){
                        if(this.tempLink.type == 'input'){
                            this.connectorHandle(uuid,'ofct','output');
                        }
                        else if(this.tempLink.type == 'output'){
                            this.connectorHandle(uuid,'ifct','input');
                        }
                    }
                    else{
                        this.tempLink = false;
                    }
                }
            }
            this.pushHistory(historyName);

        },
        onDrop: function(e) {
            // Get svgpanzoom sizes
            var sizes = this.svgpanzoom.getSizes();

            // Get drag data
            let item = JSON.parse(e.dataTransfer.getData('text'));

            var offsetX = (item.offsetX) ? item.offsetX : 0;
            var offsetY = (item.offsetY) ? item.offsetY : 0;
            
            // Get drop coordinates with computed pan zone coordinates
            var x = (parseInt(e.layerX) - parseInt(this.svgpanzoom.getPan().x))*(1/sizes.realZoom) - offsetX;
            var y = (parseInt(e.layerY) - parseInt(this.svgpanzoom.getPan().y))*(1/sizes.realZoom) - offsetY;

            // Init object for flow script insertion
            var obj = {
                name: item.name,
                x: x,
                y: y,
                type: item.type,
                inputs: item.inputs,
                outputs: item.outputs
            };

            // Add reference to the external element
            if(item.ref) obj.ref = item.ref;

            // Add the component to the viewport
            this.setComponentData( false , obj );

            e.preventDefault();
            return false;
        },
    }
}
</script>

<style>
    .overlay{
        position:absolute;
        top:0px;
        right:0px;
        width:50%;
        min-width:300px;
        max-width:350px;
        bottom:0;
        background-color:rgba(255,255,255);
        z-index:999;
        border-left:1px solid #eee;
    }

    .el-input__inner{
        margin:0;
    }

    #svg{
        position:absolute;
        top:0;
        right:0;
        left:0;
        bottom:100px;
        width:100%;
        height:100%;
    }
    .full{
        position:absolute;
        top:0;
        right:0;
        left:0;
        bottom:0;
        width:100%;
        height:100%;
    }
    .grid-bg{
        background-color: #ffffff;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='100' height='100' viewBox='0 0 100 100'%3E%3Cg fill-rule='evenodd'%3E%3Cg fill='%23ececec' fill-opacity='0.58'%3E%3Cpath opacity='.5' d='M96 95h4v1h-4v4h-1v-4h-9v4h-1v-4h-9v4h-1v-4h-9v4h-1v-4h-9v4h-1v-4h-9v4h-1v-4h-9v4h-1v-4h-9v4h-1v-4h-9v4h-1v-4H0v-1h15v-9H0v-1h15v-9H0v-1h15v-9H0v-1h15v-9H0v-1h15v-9H0v-1h15v-9H0v-1h15v-9H0v-1h15v-9H0v-1h15V0h1v15h9V0h1v15h9V0h1v15h9V0h1v15h9V0h1v15h9V0h1v15h9V0h1v15h9V0h1v15h9V0h1v15h4v1h-4v9h4v1h-4v9h4v1h-4v9h4v1h-4v9h4v1h-4v9h4v1h-4v9h4v1h-4v9h4v1h-4v9zm-1 0v-9h-9v9h9zm-10 0v-9h-9v9h9zm-10 0v-9h-9v9h9zm-10 0v-9h-9v9h9zm-10 0v-9h-9v9h9zm-10 0v-9h-9v9h9zm-10 0v-9h-9v9h9zm-10 0v-9h-9v9h9zm-9-10h9v-9h-9v9zm10 0h9v-9h-9v9zm10 0h9v-9h-9v9zm10 0h9v-9h-9v9zm10 0h9v-9h-9v9zm10 0h9v-9h-9v9zm10 0h9v-9h-9v9zm10 0h9v-9h-9v9zm9-10v-9h-9v9h9zm-10 0v-9h-9v9h9zm-10 0v-9h-9v9h9zm-10 0v-9h-9v9h9zm-10 0v-9h-9v9h9zm-10 0v-9h-9v9h9zm-10 0v-9h-9v9h9zm-10 0v-9h-9v9h9zm-9-10h9v-9h-9v9zm10 0h9v-9h-9v9zm10 0h9v-9h-9v9zm10 0h9v-9h-9v9zm10 0h9v-9h-9v9zm10 0h9v-9h-9v9zm10 0h9v-9h-9v9zm10 0h9v-9h-9v9zm9-10v-9h-9v9h9zm-10 0v-9h-9v9h9zm-10 0v-9h-9v9h9zm-10 0v-9h-9v9h9zm-10 0v-9h-9v9h9zm-10 0v-9h-9v9h9zm-10 0v-9h-9v9h9zm-10 0v-9h-9v9h9zm-9-10h9v-9h-9v9zm10 0h9v-9h-9v9zm10 0h9v-9h-9v9zm10 0h9v-9h-9v9zm10 0h9v-9h-9v9zm10 0h9v-9h-9v9zm10 0h9v-9h-9v9zm10 0h9v-9h-9v9zm9-10v-9h-9v9h9zm-10 0v-9h-9v9h9zm-10 0v-9h-9v9h9zm-10 0v-9h-9v9h9zm-10 0v-9h-9v9h9zm-10 0v-9h-9v9h9zm-10 0v-9h-9v9h9zm-10 0v-9h-9v9h9zm-9-10h9v-9h-9v9zm10 0h9v-9h-9v9zm10 0h9v-9h-9v9zm10 0h9v-9h-9v9zm10 0h9v-9h-9v9zm10 0h9v-9h-9v9zm10 0h9v-9h-9v9zm10 0h9v-9h-9v9z'/%3E%3Cpath d='M6 5V0H5v5H0v1h5v94h1V6h94V5H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
        pointer-events:none;

    }
    .full-2{
        position:absolute;
        top:0;
        right:0;
        left:0;
        bottom:0;
        width:100%;
        height:100%;
    }
    
    .box-icon{
        color:#888;
    }

</style>
