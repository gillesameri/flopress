<template>
    <div>
        <el-select
            v-model="postID"
            filterable
            remote
            reserve-keyword
            :placeholder="$t('placeholder.search')"
            :remote-method="remoteMethod"
            :visible-change="selectPost()"
            :loading="loading"
            clearable>
                <el-option
                v-for="item in posts"
                :key="item.ID"
                :label="item.post_title"
                :value="item.ID">
                    {{item.post_title}} ({{item.post_status}})
                </el-option>
        </el-select>
        <div v-if="post">
            {{post}}
        </div>
    </div>
</template>

<script>

var debounce = require('debounce');

export default {
  name: 'scenario-page',
  props:[ 'multi', 'value' ],
  data () {
    return {
        loading: false,
        posts:[],
        postID:{}
      }
  },
  mounted() {
      var self = this;
      if(self.value){
        $.ajax({
                url: flopQ.ajaxurl, method: 'GET', data: { _ajax_nonce: flopQ.ajax_nonce['flopress_post_by_id'] ,action : 'flopress_post_by_id', id: self.value },
                success: function (response) {
                    if(response){
                        var data = response.data;
                        if(data)
                            self.posts.push({ID:self.postID, post_title: data.post_title, post_status: data.post_status});
                    }
                },
                error: function (error) {
                    var data = error.responseJSON.data;
                    self.notify(data);
                }
            });
      }
  },
watch: { 
},
  methods: { 
      remoteMethod(search) {
          var self = this;
        if (search !== '') {
          this.loading = true;
          $.ajax({
                url: ajaxurl, method: 'GET', data: { _ajax_nonce: flopQ.ajax_nonce['flopress_post_picker'] ,action : 'flopress_post_picker', search: search},
                success: function (response) {
                    if(response){
                        var data = response.data;
                        self.posts = data;
                        self.loading = false;
                        self.$forceUpdate();
                    }
                },
                error: function (error) {
                    var data = error.responseJSON.data;
                    self.notify(data);
                }
            });
        } else {
          this.posts = [];
        }
      },
    selectPost(){
        this.$emit('input', this.postID);
    }
    
  }
}
</script>

<style scoped>
</style>
