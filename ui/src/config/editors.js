export default {
    "action": {
        "editor": "editoraction",
        "title": "Action",
        "options": {
            "mode": "action"
        },
        "icon": "fas fa-cogs",
        "inputs": {},
        "outputs": {
            "name": {
                "type": "string",
                "label": "name"
            },
            "callable": {
                "type": "callable",
                "label": "callable"
            }
        },
        "content": {
            "components": {},
            "links": {},
            "autoload": true,
            "priority": 10
        }
    },
    "filter": {
        "editor": "editoraction",
        "title": "Filter",
        "options": {
            "mode": "filter"
        },
        "icon": "fas fa-filter",
        "inputs": {},
        "outputs": {
            "name": {
                "type": "string",
                "label": "name"
            },
            "callable": {
                "type": "callable",
                "label": "callable"
            }
        },
        "content": {
            "components": {},
            "links": {},
            "autoload": true,
            "priority": 10
        }
    },
    "shortcode": {
        "editor": "editoraction",
        "title": "Shortcode",
        "options": {
            "mode": "shortcode"
        },
        "icon": "fas fa-code",
        "inputs": {},
        "outputs": {
            "name": {
                "type": "string",
                "label": "name"
            },
            "callable": {
                "type": "callable",
                "label": "callable"
            }
        },
        "content": {
            "components": {},
            "links": {},
            "autoload": true,
            "priority": 10
        }
    },
    "template": {
        "editor": "editorcode",
        "title": "Template",
        "options": {
            "mode": "twig"
        },
        "icon": "fas fa-file-code",
        "inputs": {
            "args": {
                "type": "array",
                "label": "arguments"
            }
        },
        "outputs": {
            "content": {
                "type": "string",
                "label": "content"
            }
        },
        "content": ""
    },
    "data": {
        "editor": "editordata",
        "title": "Data",
        "options": {
            "mode": "json"
        },
        "icon": "fas fa-table",
        "inputs": {},
        "outputs": {
            "content": {
                "type": "mixed",
                "label": "output"
            }
        },
        "content": {
            "weight": 0
        }
    }
}