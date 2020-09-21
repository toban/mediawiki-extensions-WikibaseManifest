# Manifest Output Format

## Context

We need to determine the standard format of the WikibaseManifest output. We looked at different types of information (listed below), some of which are either listed in the [WikibaseManifest prototype](https://github.com/wmde/WikibaseManifest) or [OpenRefine's manifest spec](https://github.com/OpenRefine/wikibase-manifests) (or both).

### Entity Mapping

There are two options of interest:
- mapping between Wikidata entity ID and local Wikibase ID, e.g. `"P31": "P1"`
- using the label and the respective ID on the local wikibase, e.g. `“instance_of”: “P1”`

The goal of the manifest (in v1) is to get Wikibase users to benefit from (at least a portion of) the wealth of tools that already exist for Wikidata.
At the moment of writing this ADR, Wikidata is the only "hub" in the Wikibase ecosystem. This won't be the case forever, but for now it's reasonable and efficient to optimize the manifest for that.
Possible future extension for other mappings can be allowed by listing all the wikidata ones under "wikidata".

Map entities by using Wikidata entity ID and local Wikibase ID, e.g. `"P31" : "P1"`. The mapping will be keyed under "wikidata.org".

### Items and Properties Mappings

"instance_of" and "subclass_of" are important properties to provide in the manifest.
Full list at https://www.wikidata.org/wiki/Help:Basic_membership_properties

We won't require any. We will leave it to tool builders to ask for the ones they need from a certain Wikibase.

### Quality Constraints

OpenRefine's spec lists 17 properties and 47 items for Wikidata. It's optional, as QualityContraints is an extension and it might not be installed on some Wikibases.

We won't require any. It’s possible to include them if the user so chooses, but listed along other items and properties, and **not** in their own section.

### OAuth Extension

If the extension is available provide the URL of the registration page, e.g. https://meta.wikimedia.org/wiki/Special:OAuthConsumerRegistration/propose

### Reconciliation

The prototype has that as an external service. OpenRefine's spec requires it.  
It will be kept in the manifest.

### Edit Groups

Edit Groups is a 3rd party service to meet the needs of Wikidata.org users who want to bulk rollback or bulk inspect a set of edits
An important piece of information regarding edit groups is `url_schema`. The URL schema must contain the variable `${batch_id}`.
The first version of the WikibaseManifest will not be deployed to Wikidata.org, and currently EditGroups is only (we strongly assume) used by Wikidata.  
We won't include them in v1 of the manifest.

### RDF Namespaces

The prototype lists them and we consider them important, so they will be kept.

### MediaWiki info

Important information to list is:
- the name of the Wikibase, e.g. Wikidata
- the URL of the root, e.g. https://www.wikidata.org/wiki/
- the main page URL, e.g. https://www.wikidata.org/wiki/Wikidata:Main_Page
- API endpoints
There will be an "api" block which will contain the action API as well as the MediaWiki REST API

### Splitting up items and properties

Pros and cons of splitting up items and properties:

`-` Introduces additional effort and a change to the spec if/when a new entity type needs to be added, such as: MediaInfo, Lexemes, and potentially EntitySchemas
`+` Allows for validation of the input (the entity ID fits the expected pattern) to automatically reject incorrect input  
`+` Separates concerns and makes the readability of the output better for humans  
`-` Might not make sense if we decide to provide nothing but properties in `v1`  
`+` In [OpenRefine's manifest spec](https://github.com/OpenRefine/wikibase-manifests/blob/master/wikibase-manifest-schema-v1.json#L54) they provide **only** properties and list them in a `properties` object, not an `entities` one.  
```
"properties": {
    "type": "object",
    "properties": {
        "instance_of": {
            "type": "string",
            "description": "The 'instance of' qid of the Wikibase ('P31' for Wikidata)"
        },
        "subclass_of": {
            "type": "string",
            "description": "The 'subclass of' qid of the Wikibase ('P279' for Wikidata)"
        }
    },
    "required": [
        "instance_of",
        "subclass_of"
    ]
}
```

We will split them for readability reasons.

### Federated Properties

The WikibaseManifest will not include any information that is specific to the Federated Properties feature, such as `fedearatedPropertiesEnabled: true/false` or `federatedPropertiesSourceScriptUrl`. Information about federation scenarios and entity sources shall be added according to [T262807](https://phabricator.wikimedia.org/T262807), but likely not to the first release version of the Manifest extension.

### Entity Sources

List entity sources. We are providing only the local ones for now, but nevertheless we will list them under a "local" key, because it opens the possibility to add federated entity sources in a future version. E.g.

```
"entities": {
  "local": {
    "item": {
      "namespaceId": 120,
      "namespaceString": "Item"
    },
    "property": {
      "namespaceId": 122,
      "namespaceString": "Property"
    }
  }
}
```

### Other info

Provide `max_lag`, which is the recommended maxlag by the administrator of this Wikibase. For example Wikidata's maxlag is 5s.  
We should mention in the description or in the docs that the value of maxlag is in seconds.



