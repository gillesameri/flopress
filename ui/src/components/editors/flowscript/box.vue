<template>
    <g :transform='getTransform()'>
            <svg x="0" y="0" :width="boxWidth">
        <rect x="0" :y="0" :width="boxWidth" :height="getHeight()" :fill="'#fffafa'" :filter="(selected) ? 'url(#f3)' : ''" rx="5" ry="5" :stroke="(selected) ? '#888888' : '#cccccc'" :stroke-width="(selected) ? '1' : '1'" @mousedown.left="handleMouseDown" @mouseup.left="handleMouseUp"
            @click.stop="selectComponent(id)" v-on:dblclick.stop="editComponent(id)">
        </rect>
        <rect x="1" :y="0" :width="boxWidth-2" :height="3" :fill="getBG()" rx="3" ry="3" :stroke="(selected) ? '#888888' : '#cccccc'" :stroke-width="0" @mousedown.left="handleMouseDown" @mouseup.left="handleMouseUp" @click.stop="selectComponent(id)" v-on:dblclick.stop="editComponent(id)">
        </rect>
        <g :x="0" :y="0" :width="boxWidth" style="overflow:hidden;">
            <text x="10" :y="20" :width="boxWidth">{{value.name}}</text>
            <!-- <text :class="getIcon()" class="box-icon" x="8" y="8" width="14" height="14"></text> -->
        </g>
        <g v-if="(value.type != 'hook-action') && (value.type != 'hook-filter') && (value.type != 'hook-shortcode')">
            <g v-for="(input, key, index) in value.inputs" :key="key">
                <circle 
                    @click.stop="connectorHandle(id, key,'input')" 
                    v-if="input.type != 'efct'" 
                    :stroke="getColor(input)" 
                    stroke-width="1" cx="10" 
                    :cy="getTopPosition(index,0)" 
                    r="5" 
                    fill="#ffffff" 
                    :data-test="index" />
                <circle 
                    @click.stop="connectorHandle(id, key,'input')" 
                    v-if="(input.type != 'efct') && (isInputLinked(key) || isInputValue(input))" 
                    cx="10" :cy="getTopPosition(index,0)" 
                    r="3" 
                    :fill="getColor(input)" 
                    :data-test="index" />
                <polygon 
                    v-if="input.type == 'efct'" 
                    @click.stop="connectorHandle(id, key,'input')" 
                    :transform="'translate('+5+','+(getTopPosition(index,0)-6)+')'" 
                    :fill="isInputLinked(key) ? getColor(input) : '#ffffff'" 
                    :stroke="getColor(input)" 
                    stroke-width="1" 
                    :data-test="index"
                    points="7 0, 12 6, 7 12, 0 12, 0 0" />
                <text 
                    v-if="input.label" 
                    x="20" 
                    :y="getTopPosition(index,5)">
                        {{input.label}}
                </text>
            </g>
        </g>
        <g v-for="(output, key, index) in value.outputs" :key="key" >
            <circle @click.stop="connectorHandle(id, key,'output')" v-if="output.type != 'efct'" :stroke="getColor(output)" stroke-width="1" :cx="boxWidth-10" :cy="getTopPosition(index,0)" r="5" fill="#ffffff" />
            <circle @click.stop="connectorHandle(id, key,'output')" v-if="(output.type != 'efct') && isOutputLinked(key)" :cx="boxWidth-10" :cy="getTopPosition(index,0)" r="3" :fill="getColor(output)" :data-test="index"/>
            <polygon 
                v-if="output.type == 'efct'" 
                @click.stop="connectorHandle(id, key,'output')" 
                :transform="'translate('+(boxWidth-17)+','+(getTopPosition(index,0)-6)+')'" 
                :fill="isOutputLinked(key) ? getColor(output) : '#ffffff'" 
                :stroke="getColor(output)" 
                stroke-width="1"
                :data-test="index" 
                points="7 0, 12 6, 7 12, 0 12, 0 0" />
            <text 
                v-if="output.label" 
                :x="boxWidth-20" 
                :y="getTopPosition(index,5)" 
                text-anchor="end"
                >{{output.label}}
            </text>
        </g>
            </svg>
    </g>
</template>

<script>
    import store from '../../../Store'
    
    export default {
        name: 'flowbox',
        props: ['value', 'id', 'selected', 'selectBoxCallback', 'editCallback', 'selectedInputs', 'selectedOutputs', 'connectorHandle'],
        data() {
            return {
                headerHeight: 25,
                boxWidth: 200,
                boxHeight: 200,
                mousePosition: {},
                coords: {
                    x: 0,
                    y: 0
                }
            }
        },
        mounted() {
    
        },
        watch: {
            
        },
        methods: {
            getBG() {
                return store.getComponentColor(this.value.type);
            },
            getIcon(){
                return store.getComponentInfos(this.value.type).icon;
            },
            selectComponent(id) {
                if (this.selectBoxCallback) this.selectBoxCallback(id);
            },
            editComponent(id) {
                if (this.editCallback) this.editCallback(id);
            },
            isInputLinked(input) {
                return (this.selectedInputs.indexOf(input) != -1) ? true : false;
            },
            isInputValue(input) {
                return (input.value) ? true : false;
            },
            isOutputLinked(output) {
                return (this.selectedOutputs.indexOf(output) != -1) ? true : false;
            },
            getTransform() {
                return 'translate(' + parseInt(this.value.x) + ' ' + parseInt(this.value.y) + ')';
            },
            getTopPosition(index, offset) {
                return this.headerHeight + (index * 20) + offset + 20;
            },
            getHeight() {
                var ho = (this.value && this.value.outputs) ? Object.keys(this.value.outputs).length : 0;
                var hi = (this.value && this.value.inputs) ? Object.keys(this.value.inputs).length : 0;
                var h = (ho > hi) ? ho : hi;
                return this.headerHeight + (h * 20) + 25;
            },
            getConnectorPosition(index) {
                return this.headerHeight + (index * 20) + 25;
            },
            getColor(io) {
                var color = "#000000";
                if (io.type in store.getConnectorColors()) {
                    color = store.getConnectorColors()[io.type];
                }
                return color;
            },
            round5(x) {
                return Math.ceil(x / 5) * 5;
            },
            handleMouseMove(e) {
                var mousePosition = this.$parent.$parent.getRelativeCoordinates(e, document.getElementById('svg'));
                var sizes = this.$parent.$parent.svgpanzoom.getSizes();
                this.value.x = this.round5(this.coords.x + (parseInt(mousePosition.x) - parseInt(this.$parent.$parent.svgpanzoom.getPan().x)) * (1 / sizes.realZoom));
                this.value.y = this.round5(this.coords.y + (parseInt(mousePosition.y) - parseInt(this.$parent.$parent.svgpanzoom.getPan().y)) * (1 / sizes.realZoom));
                this.$parent.$parent.isDrag = true;
                this.$forceUpdate();
            },
            handleMouseDown(e) {
                var mousePosition = this.$parent.$parent.getRelativeCoordinates(e, document.getElementById('svg'));
                var sizes = this.$parent.$parent.svgpanzoom.getSizes();
                this.coords = {
                    x: this.value.x - (parseInt(mousePosition.x) - parseInt(this.$parent.$parent.svgpanzoom.getPan().x)) * (1 / sizes.realZoom),
                    y: this.value.y - (parseInt(mousePosition.y) - parseInt(this.$parent.$parent.svgpanzoom.getPan().y)) * (1 / sizes.realZoom)
                };
                document.addEventListener("mousemove", this.handleMouseMove);
                this.$forceUpdate();
    
            },
            handleMouseUp() {
                this.$parent.$parent.isDrag = false;
                this.$parent.$parent.pushHistory('Move component : '+this.value.name+'');
                document.removeEventListener("mousemove", this.handleMouseMove);
                this.coords = {};
                this.$forceUpdate();
            }
        }
    }
</script>

<style>
    .svg-inline--fa {
        font-size: 1em;
    }

    .svgicon{
        font-family: FontAwesome;
    }
</style>
