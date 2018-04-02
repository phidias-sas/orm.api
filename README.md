# Module to dynamically manage entities
# and create functional URLs

/orm/entities
/orm/entities/{entityId}

/orm/endpoints
/orm/endpoints/{endpointId}

PUT /orm/entities/work
{
    "attributes": [
        {
            "name": "id",
            "mysql": {
            	"type": "uuid"
            }
        },

        {
            "name": "title",
            "mysql": {
            	"type": "varchar",
            	"length": 255
            }
        },

		{
            "name": "width",
            "mysql": {
            	"type": "decimal",
            	"length": "10,2"
            }
        },

		{
            "name": "height",
            "mysql": {
            	"type": "decimal",
            	"length": "10,2"
            }
        },

        {
            "name": "author",
            "mysql": {
            	"entity": "Phidias\\Core\\Person\\Entity"
            }

        },

        {
            "name": "post",
            "mysql": {
            	"entity": "Phidias\\Post\\Entity",
                "acceptNull": true
            }
        }
    ]
}



POST /orm/endpoints
{
    "path": "artists",
    "controller": "collection",
    "settings": {
        "entity": "Phidias\\Core\\Person\\Entity",
        "query": {
            "attributes": [
                "id",
                "firstName",
                "lastName",
                "birthDay"
            ],
            "where": {
                "type": "attributes",
                "model": {
                    "avatar": null
                }
            }
        }
    }
}


POST /orm/endpoints
{
    "path": "/artists/{artistId}",
    "controller": "single",
    "settings": {
        "entity": "Phidias\\Core\\Person\\Entity",
        "id": "{artistId}",
        "query": {
            "attributes": ["id", "document", "firstName", "lastName", "birthDay"]
        }
    }
}


POST /orm/endpoints
{
    "path": "works",
    "controller": "collection",
    "settings": {
        "entity": "work",
        "query": {
            "attributes": [
                "id",
                "title",
                "width",
                "height"
            ]
        }
    }
}

POST /orm/endpoints
{
    "path": "/works/{workId}",
    "controller": "single",
    "settings": {
        "entity": "work",
        "id": "{workId}"
    }
}


POST /orm/endpoints
{
    "path": "/artists/{artistId}/works",
    "controller": "collection",
    "settings": {
        "entity": "work",
        "query": {
            "attributes": [
                "id",
                "title",
                "width",
                "height"
            ],
            "where": {
                "type": "attributes",
                "model": {
                    "author": "{artistId}"
                }
            }
        }
    }
}





# Crear una obra
POST /orm/endpoints
{
    "path": "/artists/{artistId}/works/",
    "method": "post",
    "type": "create",
    "entity": "work",
    "input": {
        "id": {
            "source": "constant",
            "value": null
        },
        "title": {
            "source": "input"
        },
        "author": {
            "source": "constant",
            "value": "{artistId}"
        },
        "post": {
            "source": "ignore"
        }
    }
}

# Actualizar una obra
POST /orm/endpoints
{
    "path": "/artists/{artistId}/works/{workId}",
    "method": "post",
    "type": "update",
    "entity": "work",
    "entityId": "{workId}",
    "input": {
        "title": {
            "source": "input"
        }
    }
}


# Eliminar una obra
DELETE /orm/endpoints
{
    "path": "/artists/{artistId}/works/{workId}",
    "method": "delete",
    "type": "update",
    "entity": "work",
    "entityId": "{workId}",
    "input": {

        "deleteDate": {
            "source": "constant",
            "value": "{timestamp}"
        }
    }
}




/v1/artists/
/v1/artists/{artistId}
/v1/artists/{artistId}/works/




# Gestion de personas
{
    "path": "/artists",

    "methods": {
        "get": {
            "controller": {
                "function": "collection",
                "settings": {
                    "entity": "Phidias\\Core\\Person\\Entity",
                    "query": {
                        "attributes": [
                            "id",
                            "firstName",
                            "lastName",
                            "birthDay"
                        ],
                        "where": {
                            "type": "attributes",
                            "model": {
                                "avatar": null
                            }
                        }
                    }
                }
            }
        },

        "post": {
            "validation": {
                "input": {
                    "entity": "Phidias\\Core\\Person\\Entity",
                    "attributes": ["firstName", "lastName", "birthDay"]
                }
            },

            "controller": {
                "function": "add",
                "settings": {
                    "entity": "Phidias\\Core\\Person\\Entity",
                    "attributes": [
                        "firstName",
                        "lastName",
                        "birthDay"
                    ]
                }
            }
        }
    }
}

{
    "path": "/artists/{artistId}",

    "methods": {
        "get": {
            "controller": {
                "function": "single",
                "settings": {
                    "entity": "Phidias\\Core\\Person\\Entity",
                    "id": "{artistId}",
                    "query": {
                        "attributes": [
                            "id",
                            "firstName",
                            "lastName",
                            "birthDay"
                        ]
                    }
                }
            }
        },

        "put": {
            "validation": {
                "input": {
                    "entity": "Phidias\\Core\\Person\\Entity",
                    "attributes": ["firstName", "lastName", "birthDay"]
                }
            },

            "controller": {
                "function": "update",
                "settings": {
                    "entity": "Phidias\\Core\\Person\\Entity",
                    "id": "{artistId}",
                    "attributes": [
                        "firstName",
                        "lastName",
                        "birthDay"
                    ]
                }
            }
        },

        "delete": {
            "controller": {
                "function": "delete",
                "settings": {
                    "id": "{artistId}"
                }
            }
        }
    }
}





##########
"query": {
    "attributes": [
        "id",
        "title",
        "width",
        "height",
        {
            "author": {
                "attributes": ["id", "firstName", "lastName"]
            }
        }
    ],

    "where": [
        {
            "type": "attributes",
            "model": [...]
        }
    ]
}


"endpoint": {
    "path": "/artists",

    "methods": {
        "get": {
            "validation": {},
            "authentication": {},
            "authorization": {},

            "controller": {
                "function": "collection",
                "settings": {
                    "entity": "Phidias\\Core\\Person\\Entity",
                    "query": {
                        "attributes": [
                            "id",
                            "firstName",
                            "lastName",
                            "birthDay"
                        ],
                        "where": {
                            "type": "attributes",
                            "model": {
                                "avatar": null
                            }
                        }
                    }
                }
            }
        },

        "post": {
            "validation": {
                "input": {
                    "type": "object",
                    "attributes": {
                        "firstName": {"type": "string"},
                        "lastName": {"type": "string"},
                        "birthDay": {"type": "integer"},
                    }
                },

                "input(?)": {
                    "entity": "work",
                    "attributes": ["firstName", "lastName", "birthDay"]  // you know if the validations by looking at "work" entity
                }
            },

            "controller": {
                "function": "add",
                "settings": {
                    "entity": "Phidias\\Core\\Person\\Entity",
                    "attributes": [
                        "firstName",
                        "lastName",
                        "birthDay"
                    ]
                }
            }
        },

        "put": {

        },

        "delete": {

        }
    }

}