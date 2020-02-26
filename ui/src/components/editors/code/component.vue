<template>

    <div draggable="true" @dragstart="onDragStart" style="margin-bottom:10px;">
        <el-card shadow="hover" class="box-card">
            <div v-html="(item.name) ? item.name : 'Component'" style="font-weight:bold;font-size:1.1em;padding-bottom:5px;"></div>
            <div v-html="item.summary" style=""></div>
        </el-card>
    </div>

</template>

<script>

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
            event.dataTransfer.dropEffect = "move";
            var args = Object.keys(this.item.inputs);
            
            if(this.isFPPremium){
                if(args.length > 0){
                    var txt = this.item.name+"("+args.join(' , ')+")";
                }
                else{
                    var txt = this.item.name+"()";
                }
            }
            else{
                if(args.length > 0){
                    var txt = "fp_function('"+this.item.name+"', ["+args.join(' , ')+"])";
                }
                else{
                    var txt = "fp_function('"+this.item.name+"')";
                }
            }

            event.dataTransfer.setData('text/plain', txt)
        }
    }
}
</script>

<style scoped>
</style>
