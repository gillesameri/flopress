import axios from 'axios'
import router from './router'
import { Notification } from 'element-ui';

import componentTypes from './config/components.js';
import connectorTypes from './config/connectors.js';
import editorTypes from './config/editors.js';

const Api = function() {
    var engine = axios.create({
        baseURL: flopQ.ajaxurl
        /*params:{_ajax_nonce: flopQ.ajax_nonce},*/
    });
    engine.interceptors.request.use((config) => {
        if(config.method == 'get'){
            var act = config.params.action;
            if(act in flopQ.ajax_nonce) config.params['_ajax_nonce'] = flopQ.ajax_nonce[act];
        }
        else if(config.method == 'post'){
            var act = config.data.get('action');
            if(act in flopQ.ajax_nonce) config.baseURL += '?_ajax_nonce='+flopQ.ajax_nonce[act];
        }
        return config
    })
    return engine;
}

export default {
    componentTypes: componentTypes,
    connectorTypes: connectorTypes,
    editorTypes: editorTypes,

    getSourceTypes(){
        return  Api().get('', {params:{ action : 'flopress_source_types'}})
        .then((response) => {
            return response.data.data;
        })
    },

    getCodexSources(){
        return  Api().get('', {params:{ action : 'flopress_codex_sources'}})
        .then((response) => {
            return response.data.data;
        }).catch(error => {
            Notification({
                title: 'Errors',
                message: 'Can\'t load codexs.',
                type: 'error',
                position: 'bottom-right'
            });
        });
    },

    saveCodexSources(sources){
        let form_data = new FormData;
          form_data.append('action', 'flopress_codex_sources_save');
          form_data.append('sources', JSON.stringify(sources));

        return Api().post('', form_data)
        .then((response) => {
            Notification({
                title: 'Success',
                message: 'Codexs saved.',
                type: 'success',
                position: 'bottom-right'
            });
        }).catch(error => {
            Notification({
                title: 'Info',
                message: 'Definitions already up-to-date.',
                type: 'info',
                position: 'bottom-right'
            });
        });
    },

    getCategoryTypes(){
        return  Api().get('', {params:{ action : 'flopress_category_types'}})
        .then((response) => {
            return response.data.data;
        })
    },

    getRemoteFeatures(source,search,category){
        return Api().get('', {params:{ action : 'flopress_filter_remote_features', o:source, s:search, c:category }})
        .then((response) => {
            return response.data
        }).catch(error => {
            Notification({
                title: 'Error',
                message: error.response.data.data,
                type: 'error',
                position: 'bottom-right'
            });
        });
    },
    installRemoteFeature(feature, source){
        return Api().get('', {params:{ action : 'flopress_import_remote_feature', slug: feature.slug, source:source }})
        .then((response) => {
            Notification({
                title: 'Success',
                message: 'Feature successfully imported.',
                type: 'success',
                position: 'bottom-right'
            });
            return response.data.data
        }).catch(error => {
            Notification({
                title: 'Error',
                message: error.response.data.data,
                type: 'error',
                position: 'bottom-right'
            });
        });
    },
    getSourceSettings(source){
        return Api().get('', {params:{ action : 'flopress_get_source_settings', source: source }})
        .then((response) => {
            return response.data.data
        }).catch(error => {
            Notification({
                title: 'Error',
                message: error.response.data.data,
                type: 'error',
                position: 'bottom-right'
            });
        });
    },
    saveSourceSettings(settings, source){

        let form_data = new FormData;
        form_data.append('action', 'flopress_save_source_settings');
        form_data.append('settings', JSON.stringify(settings));
        form_data.append('source', source);
        return Api().post('', form_data)
        .then((response) => {
            Notification({
                title: 'Success',
                message: 'Settings saved',
                type: 'success',
                position: 'bottom-right',
            });
            return response.data.data;
        }).catch(error => {
            Notification({
                title: 'Error',
                message: error.response.data.data,
                type: 'error',
                position: 'bottom-right'
            });
        });
    },
    sourceAdd(feature, source){
        return Api().get('', {params:{ action : 'flopress_add_to_source', feature: feature.id, source: source }})
        .then((response) => {
            Notification({
                title: 'Success',
                message: 'Feature successfully imported.',
                type: 'success',
                position: 'bottom-right'
            });
            return response.data.data
        }).catch(error => {
            Notification({
                title: 'Error',
                message: error.response.data.data,
                type: 'error',
                position: 'bottom-right'
            });
        });
    },
    sourceRemove(feature, source){
        return Api().get('', {params:{ action : 'flopress_remove_to_source', feature: feature.slug, source: source }})
        .then((response) => {
            Notification({
                title: 'Success',
                message: 'Feature successfully removed from source.',
                type: 'success',
                position: 'bottom-right'
            });
            return response.data.data
        }).catch(error => {
            Notification({
                title: 'Error',
                message: error.response.data.data,
                type: 'error',
                position: 'bottom-right'
            });
        });
    },

    getFeatures(){
        return Api().get('', {params:{ action : 'flopress_load_installed_features' }})
        .then((response) => {
            return response.data.data;
        }).catch(error => {
            Notification({
                title: 'Error',
                message: error.response.data.data,
                type: 'error',
                position: 'bottom-right'
            });
        });
    },

    updateFeaturesOrder(ids){
        return Api().get('', {params:{ action : 'flopress_order_installed_features', ids: ids }})
        .then((response) => {
            Notification({
                title: 'Success',
                message: 'Features successfully organized.',
                type: 'success',
                position: 'bottom-right'
            });
            return response.data.data;
        }).catch(error => {
            Notification({
                title: 'Error',
                message: error.response.data.data,
                type: 'error',
                position: 'bottom-right'
            });
        });
    },
    activateFeature(feature){
        return Api().get('', {params:{ action : 'flopress_activate_feature', id: feature.id }})
        .then((response) => {
            Notification({
                title: 'Success',
                message: 'Feature successfully activated.',
                type: 'success',
                position: 'bottom-right'
            });
            return response.data.data;
        }).catch(error => {
            Notification({
                title: 'Error',
                message: error.response.data.data,
                type: 'error',
                position: 'bottom-right'
            });
        });
    },
    deactivateFeature(feature){
        return Api().get('', {params:{ action : 'flopress_deactivate_feature', id: feature.id }})
        .then((response) => {
            Notification({
                title: 'Success',
                message: 'Feature successfully deactivated.',
                type: 'success',
                position: 'bottom-right'
            });
            return response.data.data;
        }).catch(error => {
            Notification({
                title: 'Error',
                message: error.response.data.data,
                type: 'error',
                position: 'bottom-right'
            });
        });
    },
    setFeature(feature, action){
        let form_data = new FormData;
        form_data.append('id', feature.id);
        form_data.append('title', feature.title);
        form_data.append('category', feature.category);
        form_data.append('description', feature.description);
        var message = 'Feature updated.';
        switch(action){
            case 'create':
                message =  'Feature successfully created.';
                form_data.append('action', 'flopress_create_feature');
            break;
            case 'copy':
                message =  'Feature successfully copied.';
                form_data.append('action', 'flopress_copy_feature');
            break;
            case 'edit':
                message =  'Feature successfully updated.';
                form_data.append('action', 'flopress_edit_feature');
            break;
        }
        return Api().post('', form_data)
        .then((response) => {
            Notification({
                title: 'Success',
                message: message,
                type: 'success',
                position: 'bottom-right',
            });
            return response.data.data;
        }).catch(error => {
            Notification({
                title: 'Error',
                message: error.response.data.data,
                type: 'error',
                position: 'bottom-right'
            });
        });
    },
    deleteFeature(feature){
        return Api().get('', {params:{ action : 'flopress_delete_feature', id: feature.id }})
        .then((response) => {
            Notification({
                title: 'Success',
                message: 'Feature successfully deleted.',
                type: 'success',
                position: 'bottom-right'
            });
            return response.data.data;
        }).catch(error => {
            Notification({
                title: 'Error',
                message: error.response.data.data,
                type: 'error',
                position: 'bottom-right'
            });
        });
    },
    exportFeature(feature){
        return Api().get('', {params:{ action : 'flopress_export_feature', id: feature.id }})
        .then((response) => {
            var iframe = $("<iframe/>").attr({
                src: flopQ.ajaxurl+'?_ajax_nonce='+flopQ.ajax_nonce.flopress_export_feature+'&action=flopress_export_feature&id='+feature.id,
                style: "visibility:hidden;display:none"
            }).appendTo($('#uploadZone'));
        }).catch(error => {
            Notification({
                title: 'Error',
                message: error.response.data.data,
                type: 'error',
                position: 'bottom-right'
            });
        });
    },
    loadFeature(id){
        return Api().get('', {params:{ action : 'flopress_load_feature', id: id }})
        .then((response) => {
            var feature = response.data.data;
            if(!feature.content) feature.content = {};
            return feature;
        }).catch(error => {
            Notification({
                title: 'Error',
                message: error.response.data.data,
                type: 'error',
                position: 'bottom-right'
            });
        });
    },
    loadFeatureInfos(id){
        return Api().get('', {params:{ action : 'flopress_load_feature_infos', id: id }})
        .then((response) => {
            var feature = response.data.data;
            return feature;
        }).catch(error => {
            Notification({
                title: 'Error',
                message: error.response.data.data,
                type: 'error',
                position: 'bottom-right'
            });
        });
    },
    saveFeature(id, content, settings){
        let form_data = new FormData;
          form_data.append('action', 'flopress_save_feature');
          form_data.append('id', id);
          form_data.append('content', JSON.stringify(content));

        return Api().post('', form_data)
        .then((response) => {
            Notification({
                title: 'Success',
                message: 'Feature successfully saved.',
                type: 'success',
                position: 'bottom-right'
            });
            if(content.status == 'active') this.buildFeature(id);
        }).catch(error => {
            Notification({
                title: 'Info',
                message: 'Feature already up to date.',
                type: 'info',
                position: 'bottom-right'
            });
        });
    },
    buildFeature(id){
        return Api().get('', {params:{ action : 'flopress_build_feature', id: id  }})
        .then((response) => {
            Notification({
                title: 'Success',
                message: 'Feature successfully builded.',
                type: 'success',
                position: 'bottom-right'
            });
            return response.data.data;
        }).catch(error => {
            Notification({
                title: 'Error',
                message: error.response.data.data,
                type: 'error',
                position: 'bottom-right'
            });
        });
    },
    loadSettingsData(feature){
        return Api().get('', {params:{ action : 'flopress_get_settings_feature', id: feature.id }})
        .then((response) => {
            return response.data.data;
        }).catch(error => {
            Notification({
                title: 'Error',
                message: error.response.data.data,
                type: 'error',
                position: 'bottom-right'
            });
        });
    },
    saveSettingsFeature(id, settings){
        let form_data = new FormData;
        form_data.append('action', 'flopress_set_settings_feature');
        form_data.append('id', id);
        form_data.append('settings',JSON.stringify(settings));
        return Api().post('', form_data)
        .then((response) => {
            Notification({
                title: 'Success',
                message: 'Feature settings successfully saved.',
                type: 'success',
                position: 'bottom-right'
            });
            
            return response.data.data;
        }).catch(error => {
            Notification({
                title: 'Error',
                message: error.response.data.data,
                type: 'error',
                position: 'bottom-right'
            });
        });
    },

    load_components_data(store, search, group, type, page){
        return Api().get('', {params:{ action : 'flopress_load_components', store:store, search:search, group: group, type:type, page:page }}).then((response) => {
            return response.data.data;
        }).catch(error => {
            Notification({
                title: 'Error',
                message: error.response.data.data,
                type: 'error',
                position: 'bottom-right'
            });
        });
    },

    load_component_infos(component){
        return Api().get('', {params:{ action : 'flopress_load_component', component: component.name, type: component.type }}).then((response) => {
            return response.data.data;
        }).catch(error => {
            // Just component not exists
            /*Notification({
                title: 'Error',
                message: error.response.data.data,
                type: 'error',
                position: 'bottom-right'
            });*/
        });
    },

    getConnectorColors() {
        return this.connectorTypes;
    },

    getComponentColor(type) {
        return (type in this.componentTypes) ? this.componentTypes[type].bgcolor : '#eeeeee';
    },

    getComponentInfos(type) {
        return (type in this.componentTypes) ? this.componentTypes[type] : false;
    }
}