<template>
    <div>
        <el-button size="mini" :disabled="attachmentUrl" @click="selectMedia()">{{ $t('buttons.select_media') }}</el-button>
        <el-button size="mini" :disabled="!attachmentUrl" @click="unselectMedia()">{{ $t('buttons.remove_media') }}</el-button>
        <div v-if="attachmentUrl">
            <img :src="attachmentUrl" style="max-width:100%;"/>
        </div>
    </div>
</template>

<script>

export default {
  name: 'scenario-page',
  props:[ 'multi', 'value' ],
  data () {
    return {
        fileframe:false,
        attachmentUrl:false
      }
  },
  watch:{
      value(val){
          if(val == null){
                this.attachmentUrl = null;
                this.$emit('input', null);
          }
      }
  },
  mounted() {
        var self = this;
        this.fileframe = wp.media.frames.file_frame = wp.media({
            title: 'Select a image to upload',
            button: { text: 'Use this image', },
            multiple: this.multi
        });
        this.fileframe.on( 'select', function() {
            var attachment = self.fileframe.state().get('selection').first().toJSON();
            self.attachmentUrl = attachment.url;
            self.$emit('input', attachment.id)
            //wp.media.model.settings.post.id = wp_media_post_id;
        });
        wp.media.attachment(self.value).fetch().then(function (data) {
            self.attachmentUrl = wp.media.attachment(self.value).get('url');
        });
  },
  methods: { 
    selectMedia(){
        this.fileframe.open();
    },
    unselectMedia(){
        this.attachmentUrl = false;
        this.$emit('input', false);
    },
    
  }
}
</script>

<style scoped>
 .vue-codemirror{
    position:absolute;
    left:0;
    right:0px;
    bottom:0px;
    top:42px;
}
</style>
