import Vue from 'vue'
import VueI18n from 'vue-i18n'

Vue.use(VueI18n);
export const i18n = new VueI18n({
    locale: 'en',
    fallbackLocale: 'en',
    messages: {
        en:{
            status:{
                all: "All",
                active: "Active",
                inactive: "Inactive",
                label: "Status",
            },
            buttons:{
                create: "Create",
                library: "Library",
                import: "Import",
                export: "Export",
                cancel: "Cancel",
                confirm: "Confirm",
                close: "Close",
                saveFeature: "Save feature",
                activate: "Activate",
                deactivate: "Deactivate",
                customize: "Customize",
                infos: "Infos",
                edit: "Edit",
                copy: "Copy",
                delete: "Delete",
                remove: "Remove",
                settings: "Settings",
                install: "Install",
                more_info: "More info",
                read_doc: "Read doc",
                full_doc: "Full documentation",
                save: "Save",
                help: "Help",
                more: "More",
                back: "Back",
                ok: "OK",
                new: "New",
                rename: "Rename",
                autoload_on: "Autoload on",
                autoload_off: "Autoload off",
                priority: "Priority",
                add_item: "Add item",
                select_media: "Select media",
                remove_media: "Remove media",
                update: 'Update',
                insert: 'Insert',
                copyClipboard: 'Copy to clipboard'
            },
            label:{
                title: "Title",
                description: "Description",
                category: "Category",
                new: "New",
                rename: "Rename",
                copy: "Copy",
                select_hook: "or select a hook below",
                documentation: "Documentation",
                codex: "Third party definitions",
                error: "Error",
                warning: "Warning",
                show: "Show",
                hide: "Hide",
                return: 'Return',
                host: 'Host',
                user: 'User',
                password: 'Password',
                port: 'Port',
                ssl: 'SSL',
                path: 'Path',
            },
            placeholder:{
                search: "Search",
                search_component: "Search component",
                paste_code: "Please paste code here",
                group: "Group",
                select_value_type: 'Select value type',
            },
            emptyScreen:{
                box1:{
                    title:"Add blank feature",
                    description:"Start building your own feature. Add actions, filters, shortcodes, templates..."
                },
                box2:{
                    title:"Time-saving features",
                    description:"One-click install time-saving features. Import, customize, deploy !"
                },
                box3:{
                    title:"Import from file",
                    description:"Import a feature from a file. Warning. Import only trusted source code."
                },
                box4:{
                    title:"Third party definitions",
                    description:"Enable definitions for third party plugins."
                }
            },
            help:{
                url: 'https://flopress.io/documentation/'
            },
            dialog:{
                feature:{
                    title:"Feature settings",
                    rules:{
                        title:"Please set feature name",
                        description:"Please set description"
                    },
                    redirectTitle: "Infos",
                    redirectMsg: "Edit new feature ?",
                },
                codex:{
                    title:"Third party plugin definitions",
                },
                import:{
                    title: "Import flow script"
                },
                export:{
                    title: "Export flow script"
                },
                settings:{
                    title: "Settings",
                    default_value: "Default value :",
                    clear_settings: "Clear settings",
                    save_settings: "Save settings",
                },
                component:{
                    title: 'Component',
                    default_name: 'Component',
                    description: 'Description',
                    return: 'Return',
                    array: 'Array',
                    key: 'Key',
                    value: 'Value',
                    parameters: 'Parameters',
                    arguments: 'Arguments',
                    value_dynamic: 'Value provided by dynamic input',
                    value_must_dynamic: 'Value must be provided by flow input.',
                    search:"Search component"
                },
                fileInfo:{
                    buttons:{
                        create: "Save new {file_type}",
                        edit: "Save {file_type}",
                    },
                    title:{
                        create: "New {file_type}",
                        edit: "Edit {file_type}",
                    },
                    descriptions:{
                        action: "Select a predefined action or create new.",
                        filter: "Select a predefined filter or create new.",
                        shortcode: "Shortcode name",
                        template: "Template name",
                        data: "Data name"
                    }
                }
            },
            feature:{
                delete: "This will permanently delete the feature. Continue?",
            },
            file:{
                title:"Please set name",
                description: 'Please set element name<br /><small>Only alpha-numeric, numeric or underscore characters.</small>',
                rules:{
                    description: "Name must contain only alpha-numeric, numeric or underscore characters.",
                    invalid: "Invalid file name",
                    exists: "A file with this name already exists. Please choose another filename.",
                },
                delete: "This will permanently delete this file. Continue?",
            },
            hook:{
                title: "Hook priority",
                set_priority: "Please set priority",
                errors: "Integer value only",
            },
            flow:{
                custom_hook: "Custom {hook}",
                select_hook: "or select a hook below",
                functions: 'Functions',
                scripts: 'Script',
                hooks: 'Hooks',
                pick_hook: 'Pick a predefined hook in the right pane (or create one).',
                start_event: 'Start event',
                delete_component: 'Delete this component. Continue ?',
                delete_link: 'Delete this link. Continue ?',
                new_hook: 'New {hook}',
                set_hook_name: 'Please set {hook} name<br /><small>Only alpha-numeric, numeric or underscore characters.</small>',
                errors:{
                    io_check: "You cannot connect two inputs or outputs together. :)",
                    component_check: "You cannot connect two connectors from the same component.",
                    efct_check: "Executable port can only connect a another executable port",
                    type_mismatch: "Type mismatch",
                    invalid_hook_name: "Invalid hook name",
                }
            },
            shortcode:{
                edit: "Edit (ctrl+e)",
                clone: "Clone (ctrl+g)",
                delete: "Delete (ctrl+d)",
                cancel_link: "Cancel link creation (esc)",
            },
            data:{
                type: "Type",
                type_data: "Type of data. Can be string, number, array, float, integer, ...",
                value: "Value",
                is_settings: "Settings",
                title: 'Title',
                description: 'Description',
                weight: "Weight",
                value_help: "The default value.",
                is_settings_help: "Allow this data to be replaced in the settings window.",
                title_help: "The title in the settings window.",
                description_help: "The description in the settings window.",
                weight_help: "Define the order of the fields in the settings dialog.",
            }
        }
    }
})