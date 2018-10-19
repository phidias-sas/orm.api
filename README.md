# ORM.api

Este modulo nos permite crear entidades (agrupadas en "modulos")
y construir endpoints para interactuar con ellas


## Crear un modulo
POST /orm/modules
{
    "id": "test1",
    "name": "Test Module No. 1"
}

## Crear entidades
POST /orm/modules/test1/entities
[
    {
        "name": "Phidias\\Store\\Credit\\Entity",
        "keys": ["person"],

        "attributes": {
            "person": {
                "entity": "Phidias\\Core\\Person\\Entity"
            },

            "dateCreated": {
                "type": "date"
            },

            "credit": {
                "type": "decimal",
                "unsigned": false,
                "length": "12,2",
                "default": 0
            }
        }
    },

    {
        "name": "Phidias\\Store\\Transaction\\Entity",
        "keys": ["id"],

        "attributes": {
            "id": {
                "type": "uuid"
            },

            "person": {
                "entity": "Phidias\\Store\\Credit\\Entity"
            },

            "timestamp": {
                "type": "date"
            },

            "date": {
                "type": "date"
            },

            "value": {
                "type": "decimal",
                "unsigned": false,
                "length": "12,2"
            },

            "description": {
                "type": "mediumtext",
                "acceptNull": true,
                "default": null
            },

            "itemId": {
                "type": "tinytext",
                "acceptNull": true,
                "default": null
            },

            "quantity": {
                "type": "integer",
                "acceptNull": true,
                "default": null
            }
        },

        "triggers": {
            "insert": "UPDATE {Phidias\\Store\\Credit\\Entity} SET credit = credit + NEW.value WHERE person = NEW.person;"
        }
    }
]


## Instalar el modulo (crear tablas en la base de datos para cada entidad)
PUT /orm/modules/test1/installation

## Crear un recurso de consulta
POST /orm/modules/test1/resources
{
    "url": "/people/{personId}/store/credit",

    "get": {
        "dispatcher": "collection",
        "settings": {
            "collection": {
                "entity": "Phidias\\Store\\Credit\\Entity",

                "select": {
                    "person": {
                        "entity": "Phidias\\Core\\Person\\Entity",
                        "select": {
                            "id": true,
                            "firstName": true,
                            "lastName": true,
                            "gender": true
                        }
                    },

                    "credit": true
                },

                "match": {
                    "person": "${url.personId}"
                },

                "limit": 1
            }
        }
    },

    "put": {
        "dispatcher": "insert",
        "settings": {
            "entity": "Phidias\\Store\\Credit\\Entity",
            "attributes": {
                "person": "${url.personId}",
                "dateCreated": "${now}",
                "credit": 0
            }
        }
    }
}

## Crear un recurso de escritura de datos
POST /orm/modules/test1/resources
{
    "url": "/people/{personId}/store/transactions",

    "get": {
        "dispatcher": "collection",
        "settings": {
            "collection": {
                "entity": "Phidias\\Store\\Transaction\\Entity",

                "select": {
                    "date": true,
                    "value": true
                },

                "match": {
                    "person": "${url.personId}"
                }
            }
        }
    },

    "post": {
        "dispatcher": "insert",
        "settings": {
            "entity": "Phidias\\Store\\Transaction\\Entity",

            "attributes": {
                "person": "${url.personId}",
                "timestamp": "${now}",
                "date": "${record.date}",
                "value": "${record.value}",
                "description": "${record.description}",
                "itemId": "${record.itemId}",
                "quantity": "${record.quantity}"
            }
        }
    }
}


GET /people/1/store/transactions

POST /people/1/store/transactions
{
    "date": "1536917994",
    "value": 1000,
    "description": "recarga $1,000.00",
    "itemId": null,
    "quantity": 1
}










#### --------------------   ESPECIFICACION ANTERIOR

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