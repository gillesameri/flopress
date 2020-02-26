<template>
    <div>
        <div class="d-flex flex-column flex-full">
            <div class="d-flex" style="margin-bottom:10px;">
                <div class="flex-full">
                    <el-button type="primary" @click="addItem()" size="mini">{{ $t('buttons.add_item') }}</el-button>
                </div>
                <div class="flex-full" style="text-align:right;">
                </div>
            </div>
            <div v-for="(item,index) in items" :key="index" clas="d-flex flex-full">
                <div class="d-flex flex-full">
                    <div class="flex-full">
                        <el-input size="mini" :placeholder="index" v-model="item.k"></el-input>
                    </div>
                    <div class="flex-full">
                        <el-input size="mini" placeholder="Value" v-model="item.v"></el-input>
                    </div>
                    <div class="">
                        <el-button size="mini" @click="deleteItem(index)">{{ $t('buttons.delete') }}</el-button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>

export default {
  name: 'marray',
  props:[ 'value' ],
  data () {
    return {
            items:[],
        }
    },
    mounted() {
        var self = this;
        for(var k in self.value){
            this.items.push({k:k, v:self.value[k]});
        }
    },
    watch: { 
        items: {
            handler: function (val, before) {
                var r = {};
                var c = 0;
                var nkey;
                for(var k in val){
                    if(!val[k].k){
                        nkey = c;
                        c++;
                    }
                    else if((val[k].k.length == 0)){
                        nkey = c;
                        c++;
                    }
                    else if(val[k].k === parseInt(val[k].k, 10)){
                        nkey = parseInt(val[k].k);
                        c = parseInt(val[k].k)+1;
                    }
                    else{
                        var nkey = val[k].k+"";
                    }
                    r[nkey] = val[k].v;
                }
                r = (Object.keys(r).length > 0) ? r : null;
                this.$emit('input', r);
            },
            deep: true,
        }
    },
    methods: { 
        addItem(){
            this.items.push({k:'',v:''})
            this.$forceUpdate();
        },
        deleteItem(index){
            this.$delete(this.items, index);
            this.$forceUpdate();
        },
    
    }
}
</script>

<style scoped>
</style>
