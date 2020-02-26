<template>
    <div draggable="true" @dragstart="onDragStart" style="margin-bottom:10px;">
        <el-card shadow="hover" class="box-card" :style="'border-top:3px solid '+getBG()+';'">
            <div v-html="(item.name) ? item.name : 'Component'" style="font-weight:bold;font-size:1.1em;padding-bottom:5px;"></div>
            <div v-html="item.summary" style=""></div>
        </el-card>
    </div>
</template>

<script>

import store from '../../../Store'

export default {
    name: 'flowbox',
    props: ['item'],
    data () {
        return {
            style:{}
        }
    },
    methods: {
        onDragStart: function(event) {
            var it = JSON.parse(JSON.stringify(this.item));
            it.offsetX = event.offsetX;
            it.offsetY = event.offsetY;

            event.dataTransfer.dropEffect = "move";
            event.dataTransfer.setData('text/plain', JSON.stringify(it));
        },
        getBG(){
            return store.getComponentColor(this.item.type);
        },
    }
}
</script>

<style scoped>
</style>
