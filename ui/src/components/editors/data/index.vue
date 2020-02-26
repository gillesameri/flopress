<template>
    <div class="d-flex flex-column flex-full" style="background:white;overflow:scroll;">
        <div class="flex-full" style="padding:20px 15%;">
            <el-form label-position="left" ref="form" :model="file.content" label-width="120px">
                <el-form-item :label="$t('data.type')">
                    <el-cascader
                        :options="editorTypes"
                        v-model="file.content.type" style="width:100%;" :placeholder="$t('placeholder.select_value_type')">
                    </el-cascader><br />
                    <small style="display:block;line-height:20px;">{{ $t('data.type_data') }}</small>
                </el-form-item>
                <el-form-item :label="$t('data.value')" v-if="file.content.type">
                    <el-input v-model="file.content.value" v-if="file.content.type[1] == 'textfield'"></el-input>
                    <el-input type="textarea" v-model="file.content.value" v-if="file.content.type[1] == 'textarea'"></el-input>
                    <el-input-number v-model="file.content.value" :precision="0" :step="1" v-if="file.content.type[1] == 'intfield'"></el-input-number>
                    <el-input-number v-model="file.content.value" :precision="2" :step="0.01" v-if="file.content.type[1] == 'floatfield'"></el-input-number>
                    <el-switch v-model="file.content.value" active-color="#13ce66" inactive-color="#ff4949" v-if="file.content.type[1] == 'switch'"></el-switch>
                    <el-checkbox v-model="file.content.value" v-if="file.content.type[1] == 'checkbox'"></el-checkbox>
                    <el-time-select v-model="file.content.value" placeholder="Select time" v-if="file.content.type[1] == 'timepicker'"></el-time-select>
                    <el-date-picker v-model="file.content.value" type="date" placeholder="Pick a date" v-if="file.content.type[1] == 'datepicker'"> </el-date-picker>
                    <el-color-picker v-model="file.content.value" v-if="file.content.type[1] == 'colorpicker'"></el-color-picker>
                    <fpmediapicker v-model="file.content.value" v-if="file.content.type[1] == 'media'"></fpmediapicker>
                    <fppostpicker v-model="file.content.value" v-if="file.content.type[1] == 'post'"></fppostpicker>
                    <fpmarray v-model="file.content.value" v-if="(file.content.type.indexOf('array') != -1)"></fpmarray>
                    <small style="display:block;line-height:20px;">{{ $t('data.value_help') }}</small>
                </el-form-item>
                <el-form-item :label="$t('data.is_settings')">
                     <el-switch
                    v-model="file.content.is_settings"
                    active-color="#13ce66"
                    inactive-color="#ff4949">
                    </el-switch><br />
                    <small style="display:block;line-height:20px;">{{ $t('data.is_settings_help') }}</small>
                </el-form-item>
                <el-form-item :label="$t('data.title')" v-if="file.content.is_settings">
                    <el-input v-model="file.content.title"></el-input>
                    <small style="display:block;line-height:20px;">{{ $t('data.title_help') }}</small>
                </el-form-item>
                <el-form-item :label="$t('data.description')" v-if="file.content.is_settings">
                    <el-input v-model="file.content.description"></el-input>
                    <small style="display:block;line-height:20px;">{{ $t('data.description_help') }}</small>
                </el-form-item>
                <el-form-item :label="$t('data.weight')" v-if="file.content.is_settings">
                    <el-input v-model="file.content.weight"></el-input>
                    <small style="display:block;line-height:20px;">{{ $t('data.weight_help') }}</small>
                </el-form-item>
            </el-form>
        </div>
    </div>
</template>

<script>


import fpmediapicker from '../../fields/mediaPicker';
import fppostpicker from '../../fields/postPicker';
import fpmarray from '../../fields/marray';

export default {
  name: 'data-page',
  props:[ 'id', 'options', 'file' ],
  data () {
    return {
        exp:{
        },
        editorTypes: [
          {
            value: 'string',  label: 'String',
            children: [
              { value: 'textfield', label: 'Textfield' }, 
              { value: 'textarea', label: 'Textarea' }
            ]
          },
          {
            value: 'number', label: 'Number',
            children: [
              { value: 'floatfield', label: 'Float field' }, { value: 'intfield', label: 'Integer field' }
            ]
          },{ value: 'boolean', label: 'Boolean', children: [{ value: 'switch', label: 'Switch' }, { value: 'checkbox', label: 'Checkbox' }]
        },{
          value: 'object', label: 'Object',
          children: [{
            value: 'post', label: 'Post'
          }, {
            value: 'media', label: 'Media'
          }]
        },{
          value: 'picker', label: 'Picker',
          children: [{ value: 'timepicker', label: 'Time picker' }, { value: 'datepicker', label: 'Date picker' }, { value: 'colorpicker', label: 'Color picker' }]
        },{
          value: 'array', label: 'Array'
        }],
      }
  },

  components: {
    fpmediapicker: fpmediapicker,
    fppostpicker: fppostpicker,
    fpmarray: fpmarray,
  },
  mounted() {
        
  },
  methods: { 
    
  }
}
</script>

<style scoped>
</style>
